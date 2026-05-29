var path =  url_params();



var ctrl_page 		= path.route.split('/');



var lot_details 	= [];



var tax_details 	= [];



var tag_stones 		= [];



var tag_materials 	= [];



var tag_design_stones 		= [];



var tag_design_materials 	= [];



var stone_details 	= [];



var stones 			= [];



var stone_types 			= [];



var uom_details 			= [];



var pre_img_files=[];



var pre_img_resource=[];



var dublicate_tag_details =[];



var pro_designs = [];



var modalStoneDetail = [];



var modalChargeDetail  = [];



var modalAttributeDetail  = [];



var modalOtherMetalDetail = [];



var wast_settings_details=[];



var lot_inward_detail=[];



var metalDetails    =[];



var purityDetails   =[];



var charges_list = [];



var attributes_list = [];



var prod_details = [];



var current_po_details = [];



var metal_rate_details = [];



var metal_rates = [];



var total_files=[];



var quality_code=[];



var qulaity_diamond_rates = [];



var stone_rate_settings = [];



var modalHuidDetail  = [];



var loose_product_rate = [];



var retageditRow = '';

var dupPrintLog = [];


$(document).ready(function() {



    if((ctrl_page[2]=='add' || ctrl_page[2]=='bulk_edit') && ctrl_page[1] != 'retagging' )



	{



		   Webcam.set({



			width: 290,



			height: 190,



			image_format: 'jpg',



			jpeg_quality: 90,

			

			constraints: {                     //Kyc Tab

				facingMode: 'environment'

			}



		});



        Webcam.attach( '#my_camera' );



		Webcam.on('error', function(err) {



			console.log('Error accessing webcam:', err);



		});



        $("#imageModal").setShortcutKey(17 , 73 , function() {



            take_snapshot('pre_images');



        } );



	}



    if(ctrl_page[1] == "tagging" && (ctrl_page[2] == "add" || ctrl_page[2] == "bulk_edit")){



        $("#cus_stoneModal").on("hidden.bs.modal", function () {



          $("#tag_wast_perc").focus();



        });



		$("#other_metalmodal").on("hidden.bs.modal", function () {



          $("#tag_wast_perc").focus();



        });



        $("#cus_chargeModal").on("hidden.bs.modal", function () {



          $('#addTagToPreview').focus();



        });



        $(document).keyup(function(e) {



            if(e.keyCode == 9) {    // TAB KEY



                if($('#tag_lwt').is(':focus')){



                    //alert('Less wt focused '+e.keyCode);



                    openStoneModal();



                }



                else if($('#tag_charge').is(':focus')){



                    //alert('Charge focused : '+e.keyCode);



                    openChargeModal();



                }



				/*else if($('#tag_attribute').is(':focus')) {



                    //alert('Charge focused : '+e.keyCode);



                    openAttributeModal();



                }*/



				else if($('#other_metal_charges').is(':focus')){



					open_other_metal_modal();



				}else if($('#tag_huid_model').is(':focus')) {



                        //alert('Charge focused : '+e.keyCode);



                	openHuidModal();



                }



            }



        });



        $(document).on('focus', '.select2-selection.select2-selection--single', function (e) {



          $(this).closest(".select2-container").siblings('select:enabled').select2('open');



        });



        // steal focus during close - only capture once and stop propogation



        $('select.select2').on('select2:closing', function (e) {



          $(e.target).data("select2").$selection.one('focus focusin', function (e) {



            e.stopPropagation();



          });



        });



       $(document).on('click', '.create_stone_item_details', function (e) {



             if(validateStoneCusItemDetailRow()){



        			create_new_stone_row();



        		}else{



        			alert("Please fill required stone fields");



        		}



         });



        $('#cus_stoneModal  #close_stone_details').on('click', function(){



        	$('#cus_stoneModal .modal-body').find('#estimation_stone_cus_item_details tbody').empty();



        });



    }



    $('.dateRangePicker').daterangepicker({



	    //startDate:  moment().subtract(6, 'days'),



	    endDate: moment(),



	});



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



     prod_info = [];



	 switch(ctrl_page[1])



	 {



	 	case 'tagging':



				 switch(ctrl_page[2]){



				 	case 'list':



					 var date = new Date();



					 var firstDay  = new Date(date.getFullYear(), date.getMonth(),date.getDate() - 0, 1);



					 var from_date =  firstDay.getFullYear()+'-'+(firstDay.getMonth()+1)+'-'+firstDay.getDate();



					 var to_date= (date.getFullYear()+"-"+(date.getMonth() + 1)+"-"+date.getDate());





						   $('#tag_date1').html(from_date);



						   $('#tag_date2').html(to_date);







				 			get_tagging_list(from_date,to_date);



				 			get_stones();



							 $('#tag_lot_no').select2({



								placeholder:'Select Lot No',



								allowClear:true



							});



							$('#tag_po_ref_no').select2({



								placeholder:'Select Po REf No',



								allowClear:true



							});



							$('#tag_karigar').select2({



								placeholder:'Select Karigar',



								allowClear:true



							});



							getTaggedLot();



							getTaggedRefNo();



							get_ActiveKarigar();



				 		break;



				 	case 'tag_link':



				 	    get_CustomerOrders();



				 	break;



				 	case 'add':



				 	        get_wastage_settings_details();



				 			get_received_lots();



							//get_tag_types();



							//get_tag_taxgroups();



							get_tag_stones();



							get_tag_materials();



							get_stones();



							get_stone_types();



							get_ActiveUOM();



							get_taxgroup_items();



							get_charges();



							get_activeAttributes();



							get_ActiveMetals();



        	         		get_ActivePurity();



        	         		get_metal_rate_purities();



							 getActive_quality_code();



							 getQualityDiamondRates();



							 getStoneRateSettings();



							 getLooseStoneProductRateSettings();



							 $('#tag_gwt').focus();





							 $("#remarks").on("input", function() {



								 var inputValue = $(this).val();

								 var sanitizedValue = inputValue.replace(/\s/g, '');

								 $(this).val(sanitizedValue);



							 });



        	         		//get_ActiveSections($('#id_branch').val());



							$("#tag_lt_prod").on("keyup",function(e){



								var prod = $("#tag_lt_prod").val();



								if(prod.length == 3) {



									get_lot_products(prod);



				                }



							});



							$("#tag_lt_design").on("keyup",function(e){



								var des = $("#tag_lt_design").val();



								if(des.length == 3) {



									get_lot_designs(des);



				                }



							});



							$("#des_select").select2(



							{



								placeholder:"Select Design",



								allowClear: true



							});



							$("#sub_des_select").select2(



							{



								placeholder:"Select Sub Design",



								allowClear: true



							});



							$(document).on('change', '.chargesType', function (e) {



								var row = $(this).closest('tr');



								add_charges_value(row);



							});



							$(document).on('keyup', '.chargesValue', function (e) {



								console.log("chargesValueOnChange");



								calculateTagFormSaleValue();



							});



							$(document).on("click",".add_tag_attribute, .add_attributes",function() {



								add_tag_attribute();



							});



							$(document).on('click', '.remove_tag_attribute', function() {



								remove_tag_attribute($(this));



							});



							$(document).on('change', '.tag_upd_attr_name', function() {



								let attribute_changed = $(this).val();



								let attr_values_list = get_attribute_values_from_attribute(attribute_changed);



								let attr_value_obj = $(this).closest("tr").find('.tag_upd_attr_value');



								load_attribute_values(attr_values_list, attr_value_obj);



							});



							$("#attribute_modal").on('shown.bs.modal', function() {



								$('#table_attribute_detail .attr_row:last-child').find('.tag_upd_attr_name').focus();



							});



							$("#attribute_modal").on('hidden.bs.modal', function() {



								$('#cert_no').focus();



							});



							$(document).on("blur","#tag_huid", function() {



								let huid = $.trim($(this).val().toUpperCase());



								$(this).val(huid);



								if(huid != "") {



									if(!huid_validation(huid)) {



										$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Invalid HUID Number!'});



										$(this).focus();



									}



								}



							});



							$(document).on("blur","#tag_huid2", function() {



								let huid2 = $.trim($(this).val().toUpperCase());



								$(this).val(huid2);



								if(huid2 != "") {



									if(!huid_validation(huid2)) {



										$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Invalid HUID Number!'});



										$(this).focus();



									}



								}



							});



							$(document).on('click', '.remove_other_metal', function() {



								remove_other_metal($(this));



							});



							$(document).on('change', '.select_metal, .select_purity', function() {



								console.log("select_metal");



								let closest_tr = $(this).closest("tr");



								let metal_obj 	= closest_tr.find('.select_metal');



								let purity_obj	= closest_tr.find('.select_purity');



								if(metal_obj.val() > 0 && purity_obj.val() > 0) {



									get_rate_from_metal_and_purity(metal_obj.val(), purity_obj.val()).then((rate) => {



										rate = isNaN(rate) ? 0 : parseFloat(rate);



										if(rate > 0) {



											closest_tr.find('.rate_per_gram').val(rate);



										}



									});



								}



							});



							$("#tag_wast_perc").on("keyup",function () {



								calc_wastage("WV");



							});

							$('#tag_pur_wast_perc').on('keyup', function() {
								calc_pur_wastage("WV");
							});
							$('#tag_pur_wast_value').on('keyup', function() {
								calc_pur_wastage("WP");
							});


							$("#tag_nwt").on("input", function() {



								calc_wastage("WV");



							});



							$("#tag_wast_value").on("change",function () {



								calc_wastage("WP");



								calculateTagFormSaleValue();



							});



							$(document).on("click",".add_tag_huid",function() {



                            	add_tag_huid();



                            });



                            $(document).on('click', '.remove_tag_huid', function() {



                            	remove_tag_huid($(this));



                            });



				 		break;



				 	case 'edit':



				 			get_received_lots();







							//get_tag_types();



							//get_tag_taxgroups();



							get_tag_stones();



							get_tag_materials();



							//get_employee();



							$("#tag_lt_prod").on("keyup",function(e){



								var prod = $("#tag_lt_prod").val();



								if(prod.length == 3) {



									getSearchProd(prod);



				                }



							});



						//	calculateSaleValue();



				 			//load_tag_stone_list_on_edit();



				 			//load_tag_materials_list_on_edit();



							break;



				 	case 'bulk_edit':      //coaded by karthik



				 		 //get_received_lots();



				 		 get_stones();



						 get_stone_types();



						 get_ActiveUOM();



						 getActive_quality_code();



						 getQualityDiamondRates();



						// getQualityDiamondRates();

						 get_ActiveKarigar();



						 getStoneRateSettings();



						 getLooseStoneProductRateSettings();



						 get_taxgroup_items();



				 		 $("#tag_no").on('keyup',function(e) {



								var tag_no = this.value;



									get_tag_number(tag_no);



							});



				 		 	$("#prod_name").on("keyup",function(e){



								var prod = $("#prod_name").val();



								if(prod.length == 3) {



									getSearchProd(prod);



				                }



							});



							$("#des_name").on("keyup",function(e){



								var des = $("#des_name").val();



								if(des.length == 3) {



									getSearchDes(des);



				                }



							});



							$('#sub_des_select').select2();



							$('#sub_des_select').select2({placeholder:'Select Sub Design',allowClear: true});



							$('#bulkedit_sub_des_update').select2({placeholder:'Select Sub Design',allowClear: true});



							$('#mc_type').select2();



							$('#mc_type').select2({placeholder:'Select MC type',allowClear: true});



							$('#mc_type').select2("val",($('#id_mc_type').val()!='' && $('#id_mc_type').val()>0?$('#id_mc_type').val():''));



							$("#wastage_percent, #mc_value").on('keyup', function(e){



								var  retail_max_wastage_percent=$('#wastage_percent').val();



								var  tag_mc_value=$('#mc_value').val();



								var  design_id=$('#design_id').val();



							});



							get_ActiveProduct();



							get_activeAttributes();



							get_charges();



							// getBranchName();



							$(document).on('click',"#tagging_list tbody tr a.btn-bulk-charges-view", function(){



								id=$(this).data('id');



								 bluk_edit_charges_view(id);



							 }) ;



							 $(document).on('click',"#tagging_list tbody tr a.btn-bulk-attribute-view", function(){



								id=$(this).data('id');



								bulk_edit_attribute_view(id);



							 }) ;



							 $(document).on("click","input[name='tag_update_options']",function() {



								let tag_update_options = $(this).val();



								if(tag_update_options == 1) {



									$("#attribute_block").css("display","none");



									$(".mcva_filters").css("display","block");



									$("#mc_va_block").css("display","block");



								} else if(tag_update_options == 2) {



									$(".mcva_filters").css("display","none");



									$("#mc_va_block").css("display","none");



									$("#attribute_block").css("display","block");



								}



						   });



						   $(document).on("change","#attribute_type",function() {



								if($("#attribute_type").val() == 1) {



									$("#update_attribute_block").css("display","block");



								} else {



									$("#update_attribute_block").css("display","none");



								}



							});



							$(document).on("click",".bulk_tag_upd_add_attribute, #update_tag_add_attribute",function() {



								bulk_tag_upd_add_attribute();



							});



							$(document).on('click', '.bulk_tag_upd_remove_attribute', function() {



								bulk_tag_upd_remove_attribute($(this));



							});



							$(document).on("click",".bulk_tag_upd_add_charges, #update_update_add_charges",function() {



								bulk_tag_upd_add_charges();



							});



							$(document).on('change', '.bulk_tag_upd_attr_name',function() {



								let attr_selected = $(this).val();



								if(attr_selected > 0) {



									let attribute_values = get_attribute_values_from_attribute(attr_selected);



									let attrValObj = $(this).closest('tr').find('.bulk_tag_upd_attr_value');



									bulk_edit_attribute_values(attribute_values, attrValObj);



								} else {



									let _parent = $(this).closest('tr');



									$(_parent).find(".bulk_tag_upd_attr_value").empty();



									$(_parent).find(".bulk_tag_upd_attr_value").select2("val",'');



								}



							});



							$(document).on('click', '.delete_tag_attribute', function(e) {



								e.preventDefault();



								let delete_href = $(this).attr('data-href');



								let parent_id = $(this).data('parent');



								let del_status = delete_tag_attribute(delete_href);



								if(del_status) {



									let bulk_edit_attr_rows = $("#bulk_edit_table_attr_detail tbody tr").length;



									if(bulk_edit_attr_rows == 1) {



										$(this).closest('tr').remove();



										$("#tagging_list tbody tr").find(`[data-id='${parent_id}'].btn-attribute-view`).remove();



										$('#bulk_edit_attributes_modal').modal('hide');



									} else {



										$(this).closest('tr').remove();



									}



								}



							});



							$(document).on("click",".add_tag_huid",function() {



                            	add_tag_huid();



                            });



                            $(document).on('click', '.remove_tag_huid', function() {



                            	remove_tag_huid($(this));



                            });



				 		break;



    				 	case 'duplicate_print':



    						/*$("#tag_no").on('keyup',function(e) {



    						var tag_no = this.value;



    						get_tag_number(tag_no);



    						});*/


							get_daterange();

							// Initialize lot dropdown
							$('#lot_id').select2({placeholder:'Select Lot No', allowClear: true});

							// For single-branch logins, loggedInBranch > 0 and the branch dropdown
							// is pre-selected/disabled so its onChange never fires. We explicitly call
							// get_received_lots() here on page init so lots load automatically.
							// For multi-branch logins (loggedInBranch == 0), the user picks a branch
							// and the onChange fires to load lots.
							if(typeof loggedInBranch !== 'undefined' && loggedInBranch > 0) {
								get_received_lots(loggedInBranch);
							}

    				 	 	get_ActiveProduct();



    				 	 	$('#sub_des_select').select2();



							$('#sub_des_select').select2({placeholder:'Select Sub Design',allowClear: true});



							$('#lot_id').on('change',function(){
							if(this.value!=''){
								$('#tag_lot_id').val(this.value);

								// Reload product dropdown filtered by selected lot
								$('#prod_select option').remove();
								$('#des_select option').remove();
								$('#sub_des_select option').remove();
								$('#des_select').select2("val",'');
								$('#sub_des_select').select2("val",'');
								$('#id_product').val('');

								$.ajax({
									type: 'POST',
									url: base_url+'index.php/admin_ret_tagging/get_products_by_lot',
									data: { 'lot_no': this.value, 'id_branch': $('#id_branch').val() },
									dataType: 'json',
									success: function(data){
										$.each(data, function(key, item){
											$('#prod_select').append(
												$('<option></option>')
												.attr('value', item.pro_id)
												.text(item.product_name)
											);
										});
										$('#prod_select').select2({
											placeholder: 'Select Product',
											allowClear: true
										});
										$('#prod_select').select2("val",'');
									}
								});

							}else{
								$('#tag_lot_id').val('');
								$('#id_product').val('');
								// Lot cleared — reload all products
								get_ActiveProduct();
							}
						});



    				 	 break;



    				 	 case 'tag_mark':



    				 	 	//set_tag_marking();



							get_ActiveMetals();



							$("#prod_select").select2(



								{



									placeholder:"Select Product",



									allowClear: true



								});





							$("#category").select2(



								{



									placeholder:"Select Category",



									allowClear: true



								});



							 $("#metal").select2(



									{



										placeholder:"Select Metal",



										allowClear: true



								});







    				 	 break;



    				 	 case 'tag_edit':



    				 	    get_tag_edit_lots();



    				 	 	get_ActiveProduct();



    				 	 	$("#sub_des_select").select2(



							{



								placeholder:"Select Sub Design",



								allowClear: true



							});



							$("#select_size").select2(



							{



								placeholder:"Select Size",



								allowClear: true



							});



							$("#sub_des_filter").select2(



							{



								placeholder:"Select Sub Design",



								allowClear: true



							});



    				 	 break;



				 }



	 		break;



	 		case 'retagging':



	 		     switch(ctrl_page[2]){



	 		         case 'list':



	 		             get_stock_process_list();



	 		          break;



				 	case 'add':



				 	        get_ActiveNontagProduct();



            		 	 	get_category();



						    get_ActiveKarigar();



							get_tag_Sections();



            		 	 	$("#prod_select").select2(



            				{



            					placeholder:"Select Product",



            					allowClear: true



            				});



							$("#des_select").select2(



								{



									placeholder:"Select Design",



									allowClear: true



								});



            				$("#sub_des_select").select2(



            				{



            					placeholder:"Select Sub Design",



            					allowClear: true



            				});







							$("#select_purity").select2(



								{







									placeholder:"Select Purity",







									allowClear: true







							    });



							$("#tag_karigar").select2(



								{







									placeholder:"Select Karigar",







									allowClear: true







								});







            				$("#tag_process").select2(



            				{



            					placeholder:"Select Process",



            					allowClear: true



            				});



            				$("#report_type").select2(



            				{



            					placeholder:"Select Report Type",



            					allowClear: true



            				});



							get_stones();



							get_stone_types();



							get_ActiveUOM();



				 	break;



	 		     }



		 	 break;



		 	 case 'collection_mapping':



		 	    $("#select_collection").select2(



				{



					placeholder:"Select Collection",



					allowClear: true



				});



				get_ActiveCollection();



				if(ctrl_page[1]=='collection_mapping')



				{



				    set_collection_mapping_list();



				}



		     break;



			 case 'bulk_tag_edit_log':



				$('#bulkedit_log_date1').text(moment().clone().subtract(30, 'days').format('YYYY-MM-DD'));



				$('#bulkedit_log_date2').text(moment().format('YYYY-MM-DD'));



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



				$('#bulkedit_log_date1').text(start.format('YYYY-MM-DD'));



				$('#bulkedit_log_date2').text(end.format('YYYY-MM-DD'));



				}



				);



				bulk_tag_edit_log_list();



				get_employee("");



				$(document).on("change", "#branch_select", function() {



					get_employee(this.value);



				});



				$(document).on("click", "#search", function() {



					bulk_tag_edit_log_list();



				});



			break;





	}



	if(ctrl_page[1]=='get_tag_detail_list')



	{



	    lot_tag_detail('','');



	    get_branchwise_emp();


		getActive_quality_code();


		get_stones();



		get_stone_types();



		get_ActiveUOM();

	}



	// BTtagData



	if(ctrl_page[1]=='bt_tag_list')



	{



	    printBTtagData();



	}



	$('#tag-dt-btn').daterangepicker(



	{



	 ranges: {



		'Today'  : [moment(), moment()],



		'Yesterday'  : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],



		'Last 7 Days' : [moment().subtract(6, 'days'), moment()],



		'Last 30 Days': [moment().subtract(29, 'days'), moment()],



		'This Month'  : [moment().startOf('month'), moment().endOf('month')],



		'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]



		 },



		 startDate: moment().subtract(0, 'days'),



		 endDate: moment()



		},



		function (start, end) {



		$('#tag_date1').text(moment().startOf('month').format('YYYY-MM-DD'));



		$('#tag_date2').text(moment().endOf('month').format('YYYY-MM-DD'));



		get_tagging_list(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));



		 }



	);



	/* Lot Received At */



	$('#lt_rcvd_branch_sel').on('change',function(e){



		if(this.value != '')



		{



			$("#id_branch").val(this.value);



		}



		else



		{



			$("#id_branch").val('');



		}



	});



	/* Get Lot inward ids*/



	/* $('#tag_lot_id').on('change',function(e){



		if( $('#tag_lot_id').length > 3){



			get_received_lots();



		}



	}); */



	$('#tag_datetime').datepicker({ dateFormat: 'yyyy-mm-dd' });



	$("#tag_lot_received_id,#lot_id").select2({



	 	placeholder: "Select Lot ID",



	 	allowClear: true



 	});



	$("#select_tag_type").select2({



	 	placeholder: "Select Tag Type",



	 	allowClear: true



 	});



	$("#select_tax_group_id").select2({



	 	placeholder: "Select Tax Group",



	 	allowClear: true



 	});



	$("#select_tag_type").on('change', function(e){



		if(this.value!='')



		{



			$("#tag_type").val(this.value);



		}else{



			$("#tag_type").val('');



		}



	});



	$("#des_select").on('change', function(e){



		if(this.value !='')



		{



		    var prodesId = this.value;



		    console.log(prodesId);



		    $.each(pro_designs, function (key, item) {



		        if(item.design_no == prodesId){



		            if(item.sales_mode == 2){



		                $('#tag_wast_perc').val(item.wastag_value);



		                calc_wastage('WV');



		                $('#tag_id_mc_type').val(item.mc_cal_type);



		                $('#tag_mc_value').val(item.mc_cal_value);



		                $('#tag_wast_perc').attr("disabled", false);



                        //$('#tag_id_mc_type').attr("disabled", true);



                        $('#tag_mc_value').attr("disabled", true);



						if($("#tag_cat_type").val() == 3) {



							$('#tag_sell_rate').attr("disabled", false);



						} else {



                        	$('#tag_sell_rate').attr("disabled", true);



						}



		            }else{



		                $('#tag_sell_rate').attr("disabled", false);



		                 $('#tag_wast_perc').attr("disabled", false);



		                 calc_wastage('WV');



                        //$('#tag_id_mc_type').attr("disabled", true);



                        $('#tag_mc_value').attr("disabled", false);



		            }



		        }



		    });



		}



		if(ctrl_page[1]=='tagging' && ctrl_page[2]=='add')



		{



		    calculateTagFormSaleValue();



		}



	});



	$("#select_tax_group_id").on('change', function(e){



		if(this.value != '')



		{



			$("#tax_group_id").val(this.value);



		}else{



			$("#tax_group_id").val('');



		}



	});



	$('#tag_lot_received_id').on('change', function(e){



		$('#des_select option').remove();



		$('#tag_lt_prod option').remove();



		$('#sub_des_select option').remove();



		//$('#reset_tag_form').trigger('click');



		if(this.value != '')



		{



			var selected_lot_no = this.value;



			$('#tag_lot_id').val(selected_lot_no);



			$.each(lot_details.lot_inward, function (key, item) {



				if(selected_lot_no == item.lot_no)



				{



					$('#lt_category').html(item.category_name);



					$('#lt_date').html(item.lot_date);



					$('#lt_metal').html(item.metal+' - '+item.purity_name);



					$('#lt_wast').html(item.wastage_percentage+' %');



					$('#lt_mc').html(item.making_charge+' '+item.mc_type);



					$('#lt_tax_group').html(item.tgrp_name);



					$('#lt_id_tax_group').html(item.tgrp_id);



					$('#tax_percentage').val(item.tax_percentage);



					$('#tgi_calculation').val(item.tgi_calculation);



					$('#purity').val(item.id_purity);



					$('#lt_karigar_name').html(item.karigar_name);



					if(item.is_lot_split==1)



					{



						$('#is_lot_split').val(item.is_lot_split);



						$('.emp').show();



						$('#id_employee').val('');



						get_employee();



					}



					else



					{



						$('#is_lot_split').val(item.is_lot_split);



						$('.emp').hide();



						get_lot_products(selected_lot_no);



					}



					if(ctrl_page[2]!='bulk_edit')



					{



					   get_metal_rates_by_branch(item.lot_received_at);



					}



				}



			});



			if(ctrl_page[2]=='bulk_edit')



			{



				var  id_mc_type=$('#id_mc_type').val();



				var  id_branch=$('#id_branch').val();



				$("#tag_id").val('');



				$("#tag_no").val('');



				$("#prod_name" ).val('');



				$('#lot_product').val('');



			}//coaded by karthik



		}else{



			$('#lt_product').html("-");



			$('#lt_prod_code').val("");



			$('#lt_category').html("-");



			$('#lt_date').html("-");



			$('#lt_design').html("-");



			$('#lt_purity').html("-");



			$('#lt_metal').html("-");



			$('#lt_wast').html("-");



			$('#lt_mc').html("-");



			$('#lt_tax_group').html("-");



			$('#lt_id_tax_group').val("");



			$('#lt_date').html("-");



			$('#lt_karigar_name').html("-");



			//$('#get_tag_details').prop('disabled',true);



		}



	});



	$("#select_tax_group_id").on('change', function(e){



		if(this.value != ''){



			var taxgroupid = this.value;



			my_Date = new Date();



			$.ajax({



				url: base_url+'index.php/admin_ret_tagging/getAvailableTaxGroupItems/?nocache=' + my_Date.getUTCSeconds(),



				dataType: "json",



				method: "POST",



				data: { 'taxGroupId': taxgroupid },



				success: function ( data ) {



					console.log(data);



					tax_details = data;



					calculateSaleValue();



				}



			 });



		}



	});



	/* Design No. - Starts */



	$("#tag_design_no").on("keyup",function(e){



		var design = $("#tag_design_no").val();



		if(design.length >= 3) {



			getSearchDesignNo(design,$("#tag_design_no").val());



			/* $("#create_stn_details").prop('disabled', true);



			$("#create_material_details").prop('disabled', true); */



		}else if($("#design_id").val() == "" || $("#tag_design_no").val() == ""){



			/* $("#create_stn_details").prop('disabled', false);



			$("#create_material_details").prop('disabled', false); */



		}



	});



	/* Ends - Design No. */



	$("input[name='product[product_stone]']").on('change',function(){



		if ($("input[name='product[product_stone]'][value='2']").prop("checked")){



			$('#tot_stone_diamond').attr('disabled','disabled');



			$('#stone_name').prop("disabled",'disabled');



			  $("#diamond_block").hide();



		}else{



			$('#tot_stone_diamond').prop("disabled", false);



			 $("#diamond_block").show();



			 if ($("input[name='product[product_stone]'][value='0']").prop("checked")){



				$('#stone_name').prop("disabled",'disabled');



			 }



			 else{



				$('#stone_name').prop("disabled", false);



			 }



		}



	});



	$(".add_tag_lwt").on('click',function()



	{



	    openStoneModal();



	});



	$("#create_stn_details").on('click',function(){



		if(validateStoneDetailRow()){



			var temp_tag_stones = tag_stones;



			if($("#design_id").val() != ""){



				temp_tag_stones = tag_design_stones;



			}



			var html = "";



			var row_id = $('#tagging_stone_details tbody tr').length;



			var select_op = '<select class="form-control select_stn_det" id="tagstone_'+row_id+'" name="tagstone[stone_id][]"><option value=""> - Select Stone - </option>';



			var selected_stones = [];



			var op_length = 0;



			$('#tagging_stone_details > tbody  > tr').each(function(index, tr) {



				selected_stones.push({ "st_id" : $(this).find('td:first   .select_stn_det').val()});



			});



			$.each(tag_stones, function(key, item){



				var $exist_flag = false;



				$.each(selected_stones, function(stkey, stval){



					if(stval.st_id == item.stone_id){



						$exist_flag = true;



					}



				});



				if(!$exist_flag){



					select_op += '<option value="'+item.stone_id+'">'+item.stone_name+'</option>';



					op_length++;



				}



			});



			select_op += '</select>';



			if(op_length > 0){



				html += '<tr><td>'+select_op+'</td><td><div class="input-group"> <input class="form-control tagstone_pcs" type="number" step="any" name="tagstone[pcs][]" value="" required /></div></td><td><div class="input-group"><input type="number" class="form-control tagstone_wt" step="any" name="tagstone[weight][]" value="" required /></div></td><td><input type="hidden" name="tagstone[uom_id][]" value="" /><div class="stn_uom"></div></td><td><div class="input-group"> <input class="form-control tagstone_amt" type="number" step="any" name="tagstone[amount][]" value="" required /></div></td></tr>';



				$('#tagging_stone_details tbody').append(html);



			}else{



				alert("There is no more stone details are available");



			}



		}else{



			alert("Please fill required stone fields");



		}



	});



	$("#create_material_details").on('click',function(){



		if(validateMaterialDetailRow()){



			var html = "";



			var row_id = $('#tagging_material_details tbody tr').length;



			var select_op = '<select class="form-control select_mat_det" id="tagmat_'+row_id+'" name="tagmaterials[material_id][]"><option value=""> - Select Material - </option>';



			var selected_materials = [];



			var op_length = 0;



			$('#tagging_material_details > tbody  > tr').each(function(index, tr) {



				selected_materials.push({ "mat_id" : $(this).find('td:first .select_mat_det').val()});



			});



			$.each(tag_materials, function(key, item){



				var $exist_flag = false;



				$.each(selected_materials, function(stkey, stval){



					if(stval.mat_id == item.material_id){



						$exist_flag = true;



					}



				});



				if(!$exist_flag){



					select_op += '<option value="'+item.material_id+'">'+item.material_name+'</option>';



					op_length++;



				}



			});



			select_op += '</select>';



			if(op_length > 0){



				html += '<tr><td>'+select_op+'</td><td><div class="input-group"> <input class="form-control tagmat_wt" type="number" step="any" name="tagmaterials[weight]" value="" required /></div></td><td><input type="hidden" name="tagmaterials[uom_id]" value="" /><div class="stn_uom"></div></td><td><div class="input-group"> <input class="form-control tagmat_amt" type="number" step="any" name="tagmaterials[amount][]" value="" required /></div></td></tr>';



				$('#tagging_material_details tbody').append(html);



			}else{



				alert("There is no more materials details are available");



			}



		}else{



			alert("Please fill required material fields");



		}



	});



	$(document).on('change',".select_stn_det", function(){



		//alert(this.value);



		var selectId = this.value;



		//var row = $(this).parent().parent();



		var row = $(this).closest('tr');



		if(selectId != ""){



			$.each(tag_stones, function(key, item){



				if(item.stone_id == selectId){



					row.find('td:eq(3) .stn_uom').html(item.uom_short_code);



					//row.find('td eq(3) .stn_uom').html(item.uom_short_code);



					$(row).find("td:eq(3) input[type='hidden']").val(item.uom_id);



				}



			});



		}else{



			row.find('td:eq(3) .stn_uom').html("");



			$(row).find("td:eq(3) input[type='hidden']").val("");



		}



		console.log(row);



	});



	$(document).on('change',".select_mat_det", function(){



		var selectedId = this.value;



		var row = $(this).closest('tr');



		if(selectedId != ""){



			$.each(tag_materials, function(key, item){



				if(item.material_id == selectedId){



					row.find('td:eq(2) .stn_uom').html(item.uom_short_code);



					$(row).find("td:eq(2) input[type='hidden']").val(item.uom_id);



				}



			});



		}else{



			row.find('td:eq(2) .stn_uom').html("");



			$(row).find("td:eq(2) input[type='hidden']").val("");



		}



		console.log(row);



	});



// add new metal information



	$("#add_metal_info").on('click',function(){



		var html = "";



		var a = $("#m_increment").val();



		var i = ++a;



		$("#m_increment").val(i);



			html+="<tr id='detail"+i+"'><td>"+i+"</td><td><div class='col-sm-2'>Min wgt</div><div class='col-sm-4'><input type='text' class='form-control' placeholder='min metal weight' name='detail["+i+"][min_metal_weight]' required='true'/></div><div class='col-sm-2'>Max wgt</div><div class='col-sm-4'><input type='text' class='form-control' placeholder='max metal weight' name='detail["+i+"][max_metal_weight]' required='true'/></div></td>"+"<td><select name='detail["+i+"][id_color]' style='width:100%;' id='color"+i+"' class='form-control'></select></td>"+"<td><select style='width:100%;' name='detail["+i+"][id_purity]' id='purity"+i+"' class='form-control'></select></td>"+"<td><button type='button' class='btn btn-danger' onclick='m_remove("+i+")'><i class='fa fa-trash'></i></button></td>";



					$(".overlay").css('display','block');



					$.ajax({



						type: 'GET',



						url: base_url+'index.php/get/active_color',



						dataType:'json',



						success:function(data){



							console.log(data);



						  var id_color =  $('#id_color').val();



						   $.each(data.color, function (key, item) {



							   		$('#color'+i+'').append(



										$("<option></option>")



										  .attr("value", item.id_color)



										  .text(item.color)



									);



							});



								$('#color'+i+'').select2({



							    placeholder: "Select color",



							    allowClear: true



							});



							 $('#color'+i+'').select2("val",(id_color!='' && id_color>0?id_color:''));



							 $(".overlay").css("display", "none");



						}



					});



					$(".overlay").css('display','block');



					$.ajax({



						type: 'GET',



						url: base_url+'index.php/get/active_purity',



						dataType:'json',



						success:function(data){



							console.log(data);



						  var id_purity =  $('#id_purity').val();



						   $.each(data.purity, function (key, item) {



							   		$('#purity'+i+'').append(



										$("<option></option>")



										  .attr("value", item.id_purity)



										  .text(item.purity)



									);



							});



							$('#purity'+i+'').select2({



							    placeholder: "Select purity",



							    allowClear: true



							});



							$('#purity'+i+'').select2("val",(id_purity!='' && id_purity>0?id_purity:''));



							 $(".overlay").css("display", "none");



						}



					});



					 $('#prod_metal_detail tbody').append(html);



	});



//add new diamond information



	$("#add_diamond_info").on('click',function(){



		var html = "";



		var a = $("#d_increment").val();



		var i = ++a;



		$("#d_increment").val(i);



			html+="<tr id='d_detail"+i+"'><td>"+i+"</td>"+"<td><select name='d_detail["+i+"][id_cut]' style='width:100%;' id='id_cut"+i+"' class='form-control'></select></td>"+"<td><select style='width:100%;' name='d_detail["+i+"][id_dia_color]' id='id_dia_color"+i+"' class='form-control'></select></td>"+"<td><select style='width:100%;' name='d_detail["+i+"][id_clarity]' id='id_clarity"+i+"' class='form-control'></select></td>"+"<td><select style='width:100%;' name='d_detail["+i+"][id_carat]' id='id_carat"+i+"' class='form-control'></select></td>"+"<td><button type='button' class='btn btn-danger' onclick='dia_remove("+i+")'><i class='fa fa-trash'></i></button></td>";



					$(".overlay").css('display','block');



					$.ajax({



						type: 'GET',



						url: base_url+'index.php/get/active_color',



						dataType:'json',



						success:function(data){



						// load diamnd carat options



						   $.each(data.color, function (key, item) {



							   		$('#id_dia_color'+i+'').append(



										$("<option></option>")



										  .attr("value", item.id_color)



										  .text(item.color)



									);



							});



								$('#id_dia_color'+i+'').select2({



							    placeholder: "Select color",



							    allowClear: true



							});



							 $('#id_dia_color'+i+'').select2("val","");



							 $(".overlay").css("display", "none");



							}



					});



					$(".overlay").css('display','block');



					$.ajax({



						type: 'GET',



						url: base_url+'index.php/get/active_masters',



						dataType:'json',



						success:function(data){



						// load diamnd cut options



						   $.each(data.cut, function (key, item) {



							   		$('#id_cut'+i+'').append(



										$("<option></option>")



										  .attr("value", item.id_cut)



										  .text(item.cut)



									);



							});



							$('#id_cut'+i+'').select2({



							    placeholder: "Select Cut",



							    allowClear: true



							});



							 $('#id_cut'+i+'').select2("val","");



						// load diamnd clarity options



						   $.each(data.clarity, function (key, item) {



							   		$('#id_clarity'+i+'').append(



										$("<option></option>")



										  .attr("value", item.id_clarity)



										  .text(item.clarity)



									);



							});



							$('#id_clarity'+i+'').select2({



							    placeholder: "Select clarity",



							    allowClear: true



							});



							$('#id_clarity'+i+'').select2("val","");



						// load diamnd carat options



						   $.each(data.carat, function (key, item) {



							   		$('#id_carat'+i+'').append(



										$("<option></option>")



										  .attr("value", item.id_carat)



										  .text(item.carat)



									);



							});



							$('#id_carat'+i+'').select2({



							    placeholder: "Select carat",



							    allowClear: true



							});



							 $('#id_carat'+i+'').select2("val","");



							 $(".overlay").css("display", "none");



						}



					});



					 $('#prod_dia_detail tbody').append(html);



	});



});



// to load metal info list



function prodInfo_list()



{



	my_Date = new Date();



	$("div.overlay").css("display", "block");



	var id_prod = $('#id_product').val();



		$.ajax({



				  type: 'GET',



				  url:  base_url+'index.php/get/metal_info_list/'+id_prod+'?nocache='+ my_Date.getUTCSeconds(),



				  dataType: 'json',



				  success: function(data) {



				  	prod_info = data;



				  	var html = "";



				  	var dhtml = "";



				  	//load metal information



				  	 $.each(data.prod_mInfo, function (key, item) {



						var a = $("#m_increment").val();



						var i = ++a;



						$("#m_increment").val(i);



					   html += "<tr id='detail"+i+"'><td>"+i+"</td><td><div class='col-sm-2'>Min wgt</div><div class='col-sm-4'><input type='text' class='form-control' placeholder='metal weight' name='detail["+i+"][min_metal_weight]' value='"+item.min_metal_weight+"' required='true'/></div><div class='col-sm-2'>Max wgt</div><div class='col-sm-4'><input type='text' class='form-control' placeholder='metal weight' name='detail["+i+"][max_metal_weight]' value='"+item.max_metal_weight+"' required='true'/></div></td>"+"<td><select name='detail["+i+"][id_color]' style='width:100%;' class='form-control' id='color_select"+i+"'></td>"+"<td><select style='width:100%;' name='detail["+i+"][id_purity]' id='purity_select"+i+"' class='form-control'></select></td>"+"<td><button type='button' class='btn btn-danger' onclick='m_remove("+i+","+item.id_metal_details+")'><i class='fa fa-trash'></i></button></td>";



						 var m_id = 'color_select'+i;



						 get_color_options(m_id,item.metal_color);



					 });



					     $('#prod_metal_detail tbody').append(html);



					   //load diamond information



					      $.each(data.prod_dInfo, function (key, item) {



							var a = $("#d_increment").val();



							var i = ++a;



							$("#d_increment").val(i);



						   dhtml += "<tr id='d_detail"+i+"'><td>"+i+"</td><td><select name='d_detail["+i+"][id_cut]' style='width:100%;' class='form-control' id='cut_select"+i+"'></td>"+"<td><select name='d_detail["+i+"][id_dia_color]' style='width:100%;' class='form-control' id='dcolor_select"+i+"'></td>"+"<td><select name='d_detail["+i+"][id_clarity]' style='width:100%;' class='form-control' id='clarity_select"+i+"'></td>"+"<td><select style='width:100%;' name='d_detail["+i+"][id_carat]' id='carat_select"+i+"' class='form-control'></select></td>"+"<td><button type='button' class='btn btn-danger' onclick='dia_remove("+i+","+item.id_metal_details+")'><i class='fa fa-trash'></i></button></td>";



							var d_id = 'dcolor_select'+i;



							 get_color_options(d_id,item.diamond_color);



							 get_cut_options('cut_select'+i,item.cut);



							 get_clarity_options('clarity_select'+i,item.clarity);



							 get_carat_options('carat_select'+i,item.carat);



						 });



					     $('#prod_dia_detail tbody').append(dhtml);



				  },



			  	  error:function(error)



				  {



					 $("div.overlay").css("display", "none");



				  }



	        });



}



	function dia_remove(i,id = ""){



		var	rowId= "d_detail"+i;



		$('#'+rowId+'').remove();



		if(id){



			deleteProdDetail(id);



		}



	}



	function m_remove(i,id = ""){



		var	rowId= "detail"+i;



		$('#'+rowId+'').remove();



		if(id){



			deleteProdDetail(id);



		}



	}



	function deleteProdDetail(id){



		my_Date = new Date();



			$("div.overlay").css("display", "block");



			$.ajax({



				 url:base_url+"index.php/product/delect_prodDetail/"+id+"?nocache=" + my_Date.getUTCSeconds(),



				 type:"POST",



				 success:function(data){



				 			//	window.location.reload();



			   			 $("div.overlay").css("display", "none");



					  },



					  error:function(error)



					  {



					  	alert('error');



						 $("div.overlay").css("display", "none");



					  }



			      });



		 }



	function remove_img(file) {



		var id =  $('#id_product').val();



				$("div.overlay").css("display", "block");



			$.ajax({



				   url:base_url+"index.php/admin_catalog/remove_img/"+file+"/"+id,



				   type : "POST",



				   success : function(result) {



				   	$("div.overlay").css("display", "none");



					  window.location.reload();



				   },



				   error : function(error){



					$("div.overlay").css("display", "none");



				   }



				});



			}



$('#tag_lot_search').on('click',function()



{



	get_tagging_list($('#tag_date1').html(),$('#tag_date2').html())



})



function get_tagging_list(from_date,to_date)



{



	my_Date = new Date();



	$("div.overlay").css("display", "block");



	$.ajax({



		 url:base_url+"index.php/admin_ret_tagging/get_tagging_details?nocache=" + my_Date.getUTCSeconds(),



		 dataType:"JSON",



		 type:"POST",



		 data:{'from_date':from_date,'to_date':to_date,'lot_no':$('#tag_lot_no').val(),'po_ref_no':$('#tag_po_ref_no').val(),'id_karigar':$('#tag_karigar').val()},



		 success:function(data){



   			set_tag_list(data);



   			 $("div.overlay").css("display", "none");



		  },



		  error:function(error)



		  {



			 $("div.overlay").css("display", "none");



		  }



	});



}



function set_tag_list(data)



{



    $("div.overlay").css("display", "none");



    var oTable = $('#tag_list').DataTable();



    oTable.clear().draw();



   	 if (data!= null && data.length > 0)



	 {



	        oTable = $('#tag_list').dataTable({



			"bDestroy": true,



			"bInfo": true,



			"bFilter": true,



			"order": [[ 0, "desc" ]],



			"bSort": true,



			"dom": 'lBfrtip',



			"columnDefs": [



				{



					targets: [7,8,9,10,11],



					className: 'dt-body-right'



				},



			],



			"buttons" : ['excel','print'],



			 "lengthMenu": [ [ 10, 25, 50, -1], [10, 25, 50, "All"] ],



			"tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },



			"aaData": data,



			"aoColumns": [



			           { "mDataProp": function ( row, type, val, meta ) {



					                	var url = base_url+'index.php/admin_ret_tagging/get_tag_detail_list/'+row.tag_lot_id;



					                	action = '<a href="'+url+'" target="_blank">'+row.tag_lot_id+'</a>';



					                	return action;



					                	}



					   },



					   { "mDataProp": "old_tag_id" },



					   { "mDataProp": "branch_name" },



					   	{ "mDataProp": "po_ref_no" },



						{ "mDataProp": "section_name" },



					   	{ "mDataProp": "karigar" },



						{ "mDataProp": "tag_date" },



						{ "mDataProp": function ( row, type, val, meta ) {



							var piece = money_format_india(parseFloat(row.piece).toFixed(0));







							return piece;



						}



						},



						{ "mDataProp": function ( row, type, val, meta ) {



							var gross_wt =money_format_india(parseFloat(row.gross_wt).toFixed(3));







							return gross_wt;



						}



						},



						



						{ "mDataProp": function ( row, type, val, meta ) {



							var tag_stn_wt =money_format_india(parseFloat(row.tag_stn_wt).toFixed(3));







							return tag_stn_wt;



						}



						},



						{ "mDataProp": function ( row, type, val, meta ) {



							var tag_dia_wt =money_format_india(parseFloat(row.tag_dia_wt).toFixed(3));







							return tag_dia_wt;



						}



						},

						{ "mDataProp": function ( row, type, val, meta ) {



							var net_wt =money_format_india(parseFloat(row.net_wt).toFixed(3));







							return net_wt;



						}



						},



						],



						"footerCallback": function (row, data, start, end, display) {



							var cshtotal = 0;



							if (data.length > 0) {



								var api = this.api(), data;



								for (var i = 0; i <= data.length - 1; i++) {



									var intVal = function (i) {



										return typeof i === 'string' ?



											i.replace(/[\$,]/g, '') * 1 :



											typeof i === 'number' ?



												i : 0;



									};



									$(api.column(0).footer()).html('Total');



									pieces = api



										.column(7)



										.data()



										.reduce(function (a, b) {



											return intVal(a) + intVal(b);



										}, 0);



									$(api.column(7).footer()).html(money_format_india(parseFloat(pieces).toFixed(0)));



									gross_wt = api



										.column(8)



										.data()



										.reduce(function (a, b) {



											return intVal(a) + intVal(b);



										}, 0);



									$(api.column(8).footer()).html(money_format_india(parseFloat(gross_wt).toFixed(3)));



									net_wt = api



										.column(9)



										.data()



										.reduce(function (a, b) {



											return intVal(a) + intVal(b);



										}, 0);



									$(api.column(9).footer()).html(money_format_india(parseFloat(net_wt).toFixed(3)));



									stn_wt = api



										.column(10)



										.data()



										.reduce(function (a, b) {



											return intVal(a) + intVal(b);



										}, 0);



									$(api.column(10).footer()).html(money_format_india(parseFloat(stn_wt).toFixed(3)));



									dia_wt = api



										.column(11)



										.data()



										.reduce(function (a, b) {



											return intVal(a) + intVal(b);



										}, 0);



									$(api.column(11).footer()).html(money_format_india(parseFloat(dia_wt).toFixed(3)));







								}



							} else {



								var api = this.api(), data;







								$(api.column(7).footer()).html('');



								$(api.column(8).footer()).html('');



								$(api.column(9).footer()).html('');



								$(api.column(10).footer()).html('');



								$(api.column(11).footer()).html('');



							}



						}



		});



    }



}



function lot_tag_detail(from_date="",to_date="")



{



	my_Date = new Date();



    var tag_lot_id=ctrl_page[2];



	$("div.overlay").css("display", "block");



	$.ajax({



		 url:base_url+"index.php/admin_ret_tagging/lot_tag_detail?nocache=" + my_Date.getUTCSeconds(),



		 dataType:"JSON",



		 type:"POST",



		 data:{'from_date':from_date,'to_date':to_date,'tag_lot_id':tag_lot_id,'id_employee':$('#id_employee').val()},



		 success:function(data){



   			set_tagging_list(data);



   			 $("div.overlay").css("display", "none");



		  },



		  error:function(error)



		  {



			 $("div.overlay").css("display", "none");



		  }



	});



}



$('#select_all').click(function(event) {



	$("tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));

	event.stopPropagation();

	calculate_average_purity_and_rate();



});



$('#print_all').on('click',function(){



	window.open(base_url+'index.php/admin_ret_tagging/tagging/generate_all_qrcode?lot_id='+ctrl_page[2],'_blank');



	window.location.reload();



});



$("#tag_print").click(function(){



	if($("input[name='tag_id[]']:checked").val())



	{



		var selected = [];



		var tag_id='';



		$("#tagging_list tbody tr").each(function(index, value){



		if($(value).find("input[name='tag_id[]']:checked").is(":checked"))



		{



		tag_id+= $(value).find(".tag_id").val()+',';



		transData = {



		'tag_id': $(value).find(".tag_id").val(),



		}



		}



		});



		req_data = tag_id;



		tagging_print(req_data);



	}



	else{



		alert('Please Select Atleast One Tag');



	}



});



function tagging_print(req_data, dupPrintLog =[])



{

	//var tag = JSON.stringify(req_data);
	let printer_selected = $("#printer_selected").val();


	window.open(base_url+'index.php/admin_ret_tagging/tagging/generate_barcode?tag='+req_data+'&printer_selected='+printer_selected,'_blank');
	if (dupPrintLog != []){
		ret_duplicate_print_log_save(dupPrintLog);
	}



	window.location.reload();



}





function set_tagging_list(data)



{



   $("div.overlay").css("display", "none");



   var tagging = data.list;



   var access = data.access;



   var view_access_permission = data.image_shown;



   var oTable = $('#tagging_list').DataTable();



   $("#total_tagging").text(tagging.length);



    if(access.add == '0')



	 {



		$('#add_product').attr('disabled','disabled');



	 }



	 oTable.clear().draw();



   	 if (tagging!= null && tagging.length > 0)



	 {



	 	oTable = $('#tagging_list').dataTable({



			"bDestroy": true,



			"bInfo": true,



			"bFilter": true,



			"order": [[ 1, "desc" ]],

			"bSort": true,



			"dom": 'lBfrtip',



			 "lengthMenu": [ [ 10, 25, 50, -1], [10, 25, 50, "All"] ],



			"buttons" : ['excel','print'],



			"tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },



			"aaData": tagging,



			"aoColumns": [


					   { "mDataProp": function (row, type, val, meta) {
								 return meta.row + 1;  
								}
						},

			           { "mDataProp": function ( row, type, val, meta ){



		                	chekbox='<input type="checkbox" class="tag_id" name="tag_id[]" value="'+row.tag_id+'"/><input type="hidden" class="net_wt" value="'+row.net_wt+'"><input type="hidden" class="product_name" value="'+row.product_name+'"><input type="hidden" class="size" value="'+row.size+'"><input type="hidden" class="short_code" value="'+row.short_code+'"><input type="hidden" class="product_id" value="'+row.product_id+'"><input type="hidden" class="tag_code" value="'+row.tag_code+'"><input type="hidden" class="code_karigar" value="'+row.code_karigar+'"><input type="hidden" class="tot_print_taken" value="'+row.tot_print_taken+'">'



		                	if(row.tot_print_taken==0)



		                	{



		                		return chekbox+" "+row.tag_id;



		                	}else{



		                		return row.tag_id;



		                	}



		                }},


						{ "mDataProp": "lot_no" },

						{ "mDataProp": "po_ref_no" },

						{ "mDataProp": "vendor_name" },

						{ "mDataProp": function ( row, type, val, meta ){



                            if(row.tag_default_image!='' && row.tag_default_image!=null)



                             {



                                 img_src = base_url+'assets/img/tag/'+row.tag_default_image;



                             }else{



                                 img_src=base_url+'assets/img/no_image.png';



                             }



                             return '<img src='+img_src+' width="50" height="55"><br><a  class="btn btn-secondary tag_img"  id="edit" data-toggle="modal" data-id='+row.tag_id+'><i class="fa fa-eye" ></i></a>';



                         },



                         },

						{ "mDataProp": "tag_code" },

						{ "mDataProp": "old_tag_code" },

						{ "mDataProp": "tag_date" },

						{ "mDataProp": "tag_status" },

						{ "mDataProp": "category_name" },

						{ "mDataProp": "purity" },

						{ "mDataProp": "product" },

						{ "mDataProp": "design" },

						{ "mDataProp": "sub_design" },

						{ "mDataProp": "section" },

						{ "mDataProp": "huid" },

						{ "mDataProp": function ( row, type, val, meta ) {

								return 1;

							}

						},

						{ "mDataProp": function ( row, type, val, meta ) {

								let value = 0 ;

								value = ((row.stone_type == 0 || (row.stone_type == 1  && row.uom_gross_wt == 1)) ? row.piece : 0)

								return value;

							}

						},


						// { "mDataProp": "tag_lot_id" },

						{ "mDataProp": function ( row, type, val, meta ) {

							var unit = row.uom_short_code;

								//return (row.gross_wt ? row.gross_wt+' '+unit : '');
								let value = 0;
								value = ((row.stone_type == 1 || row.stone_type == 2) ? 0 : row.gross_wt);

								return parseFloat(value).toFixed(3);

							}

						},


						{ "mDataProp": function ( row, type, val, meta ) {

								let value = 0;

								value = ((row.stone_type == 1 || row.stone_type == 2) ? 0 : row.net_wt);

								return parseFloat(value).toFixed(3);

							}

						},

						{ "mDataProp": function ( row, type, val, meta ) {

							let stoneData = JSON.stringify(row.stone_details || []);

							return '<a href="#" class="less_wt">' + row.less_wt + '</a>' +
								'<input type="hidden" class="stone_details" value=\'' + stoneData + '\'>';

							}

						},

						{ "mDataProp": function ( row, type, val, meta ) {

							let value = 0;

							value = (row.stone_type == 0  ? row.dia_pcs : (row.stone_type == 2 ? row.piece : 0));

							return value;

							}

						},
						{
							"mDataProp": function (row, type, val, meta) {
								let value = 0;
								if (row.stone_type == 0) {
								value = parseFloat(row.dia_wgt || 0);
								} else if (row.stone_type == 2 ) {
								value = parseFloat(row.gross_wt || 0);
								}
								return value.toFixed(4); 
							}
						},



						{ "mDataProp": function ( row, type, val, meta ) {

							let value = 0;

							value = (row.stone_type == 0 ? row.dia_amt : (row.stone_type == 2 ? row.sales_value : 0));

							return parseFloat(value).toFixed(2);

							}

						},

						{ "mDataProp": function ( row, type, val, meta ) {

							let value = 0;

							value = ((row.stone_type == 1 && (row.uom_gross_wt == 1 || row.uom_gross_wt == '')) ? row.gross_wt : 0.000)

							return parseFloat(value).toFixed(3);

							}

						},
						{ "mDataProp": function ( row, type, val, meta ) {
							let value = 0;

							value = (row.stone_type == 1 && row.uom_gross_wt == 6 ? row.gross_wt : 0.000)

							return parseFloat(value).toFixed(3);
							}

						},

						{ "mDataProp": "size_name" },

						{ "mDataProp": "wastage_weight" },

						{ "mDataProp": "wastage_per" },

						{ "mDataProp": function ( row, type, val, meta ) {
								
								if (row.mc_type == 'Per Gram') {
									return row.mc_value;
								}
								return "-"; 
							}

						},
						{ "mDataProp": function ( row, type, val, meta ) {
								
								if (row.mc_type == 'Per Piece') {
									return row.mc_value;
								}
								return "-"; 
							}

						},

						{ "mDataProp": "mc_type" },

						{ "mDataProp": "sales_value" },

						{ "mDataProp": "tagged_emp" },

						{ "mDataProp": "age" },


						{ "mDataProp": "narration" },



					// 	{ "mDataProp": function ( row, type, val, meta ) {



					// 		 id= row.tag_id;



					// 		 edit_url=(access.edit=='1' ? base_url+'index.php/admin_ret_tagging/tagging/edit/'+id : '#' );



					// 		 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_tagging/tagging/delete/'+id : '#' );



					// 		 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');



					// 		 print_url=(access.edit=='1' ? base_url+'index.php/admin_ret_tagging/tagging/generate_barcode/'+id : '#' );



					// 		 action_content='<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a><a  href="'+print_url+'" target="_blank" class="btn btn-info btn-print" ><i class="fa fa-print" ></i></a>';



					// 		return action_content;



					// 	}



					// }
					],

					footerCallback: function (row, data, start, end, display) {
					let api = this.api();

					 let numVal = i => {
						if (typeof i === 'string') {
							let match = i.match(/[-+]?[0-9]*\.?[0-9]+/);
							return match ? parseFloat(match[0]) : 0;
						}
						return typeof i === 'number' ? i : 0;
					};

					let totalsMap = {
						17: 'no_tags',
						18: 'pieces',
						19: 'gross_wt',
						20: 'net_wt',
						21: 'less_wt',
						22: 'dia_pcs',
						23: 'dia_wt',
						24: 'dia_value',
						25: 'gram_wt',
						26: 'ct_wt',
						33: 'sales_value'
					};

					  Object.keys(totalsMap).forEach(idx => {
							let total = api.column(idx).data() 
								.reduce((a, b) => a + numVal(b), 0);

						let formatted = (parseInt(idx) === 17 || parseInt(idx) === 18 || parseInt(idx) === 22) 
							? total   
							: parseInt(idx) === 24 ? money_format_india(total.toFixed(2)) : (parseInt(idx) === 23 ?  money_format_india(total.toFixed(4)) : money_format_india(total.toFixed(3))); 

						$(api.column(idx).footer()).html(formatted);
					});
				}



		});



}
}



$(document).on('click', "#tagging_list a.tag_img", function(e) {



     //alert('hi');



        e.preventDefault();



        id=$(this).data('id');



        $("#edit-id").val(id);



         view_dup_tag_history_imgs(id);



});



function view_dup_tag_history_imgs(tag_id_img1)



{



     update_tag_img_id1 = tag_id_img1;



      data = [];



     var tag_codeimage1 =  base_url+'assets/img/tag';



     $('#imageModal_bulk_edit').modal('show');



     $(".overlay").css('display',"none");



     $.ajax({



        data: ( {'tag_id':tag_id_img1}),



              url:base_url+ "index.php/admin_ret_tagging/get_img_by_id?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),



              dataType:"JSON",



              type:"POST",



              success:function(data){



                  retrive_img = data;



                  console.log(retrive_img);



                  console.log(data.length);



                  for (i = 0; i < data.length; i++)



                  {



                    img_src = data[i].image;



                    var preview = $('#order_images');



                    var img = tag_codeimage1 + '/' + img_src;



                    if (img_src) {



                    div = document.createElement("div");



                    div.setAttribute('class', 'col-md-3 images');



                    div.setAttribute('id', 'order_img_edit_' + [i]);



                    $('.images').css('margin-right','25px');



                    key = [i];



                    param = img_src;



                    console.log(param);



                    div.innerHTML += "<div class='form-group'><div class='image-input image-input-outline' id='kt_image_'><div class='image-input-wrapper'><img class='thumbnail' src='" + img + "'" + "style='width: 300px;height: 250px;'/></div></div>";



                    preview.append(div);



                }



            }



              },



              error:function(error)



                {



                    $("div.overlay").css("display", "none");



                }



          });



}



function get_metal()



{



	$(".overlay").css('display','block');



	$.ajax({



		type: 'GET',



		url: base_url+'index.php/get/active_metals',



		dataType:'json',



		success:function(data){



		  var id_metal =  $('#id_metal').val();



		  console.log(data);



		   $.each(data, function (key, item) {



			   		$('#metal_select').append(



						$("<option></option>")



						  .attr("value", item.id_metal)



						  .text(item.metal)



					);



			});



			$("#metal_select").select2({



			    placeholder: "Select metal",



			    allowClear: true



			});



			$("#metal_select").select2("val",(id_metal!='' && id_metal>0?id_metal:''));



			 $(".overlay").css("display", "none");



		}



	});



}



 //on selecting subcategory



   $('#metal_select').select2()



        .on("change", function(e) {



          if(this.value!='')



          {



          	 $("#id_metal").val(this.value);



		  }



   });



function get_received_lots(id_branch){

	// Check if on duplicate_print page and include closed lots
	var include_closed = '';
	var branch_param = '';
	if(typeof ctrl_page !== 'undefined' && ctrl_page[2] == 'duplicate_print') {
		include_closed = 1;
	}
	if(typeof id_branch !== 'undefined' && id_branch != '' && id_branch != null) {
		branch_param = id_branch;
	} else if(typeof ctrl_page !== 'undefined' && ctrl_page[2] == 'duplicate_print' && typeof loggedInBranch !== 'undefined' && loggedInBranch > 0) {
		// Single-branch login: auto-use the session branch so lots are filtered correctly
		// without needing the user to change the (disabled) branch dropdown
		branch_param = loggedInBranch;
	}

	var query_params = [];
	if(include_closed) query_params.push('include_closed=1');
	if(branch_param) query_params.push('id_branch=' + branch_param);
	var query_string = query_params.length > 0 ? '?' + query_params.join('&') : '';

	$.ajax({



	 	type: 'GET',



	 	url : base_url + 'index.php/admin_ret_tagging/get_lot_ids' + query_string,



	 	dataType : 'json',

		async:false,

	 	success  : function(data){



		

		lot_details = data;

		if(ctrl_page[1]=='tagging' && ctrl_page[2]=='add')
		{
			// Clear both lot dropdowns before populating to prevent stale options
			// from a previous branch selection from accumulating in the list.
			$("#tag_lot_received_id").empty().append('<option value=""></option>');
			if($("#lot_id").length) {
				if($("#lot_id").data('select2')) { $("#lot_id").select2("destroy"); }
				$("#lot_id").empty().append('<option value=""></option>');
			}
		}

	 	var id =  $('#tag_lot_id').val();



		 	$.each(data.lot_inward, function (key, item) {



	    	    if(item.is_closed==0 || (typeof ctrl_page !== 'undefined' && ctrl_page[2] == 'duplicate_print'))



    		 	{



    		 		$("#tag_lot_received_id").append(



    	    	 	$("<option></option>")



    	    	 	.attr("value", item.lot_no)



    	    	 	.attr("data-lotfrom", item.lot_from)



    	    	 	.text(item.lot_no)



    	    	 	);



    		 	}



	     	});



	     	$.each(data.lot_inward, function (key, item) {

	    	    if(ctrl_page[2]=='duplicate_print' || item.is_closed==0)

    		 	{

    		 		$("#lot_id").append(



    	    	 	$("<option></option>")



    	    	 	.attr("value", item.lot_no)



    	    	 	.attr("data-lotfrom", item.lot_from)



    	    	 	.text(item.lot_no)



    	    	 	);

    		 	}

	     	});



	     	console.log(id);



	     	$("#tag_lot_received_id").select2("val",(id!='' && id>0?id:''));



	     	if($("#lot_id").length)



	     	{



	     	    $("#lot_id").select2({placeholder: 'Select Lot No', allowClear: true});



	     	    $("#lot_id").select2("val",'');



	     	}



	     	$(".overlay").css("display", "none");



	 	}



	});



}



function get_tag_types(){



	$.ajax({



	 	type: 'GET',



	 	url : base_url + 'index.php/admin_ret_tagging/get_tag_types',



	 	dataType : 'json',



	 	success  : function(data){



		 	var id =  $('#tag_type').val();



		 	$.each(data, function (key, item) {



	    	 	$("#select_tag_type").append(



	    	 	$("<option></option>")



	    	 	.attr("value", item.tag_id)



	    	 	.text(item.tag_name)



	    	 	);



	     	});



	     	$("#select_tag_type").select2("val",(id !='' && id > 0 ? id : ''));



	     	$(".overlay").css("display", "none");



	 	}



	});



}



function get_tag_taxgroups(){



	$.ajax({



	 	type: 'GET',



	 	url : base_url + 'index.php/admin_ret_tagging/getAvailableTaxGroups',



	 	dataType : 'json',



	 	success  : function(data){



		 	var id =  $('#tax_group_id').val();



		 	$.each(data, function (key, item) {



	    	 	$("#select_tax_group_id").append(



	    	 	$("<option></option>")



	    	 	.attr("value", item.tgrp_id)



	    	 	.text(item.tgrp_name)



	    	 	);



	     	});



	     	$("#select_tax_group_id").select2("val",(id !='' && id > 0 ? id : ''));



	     	$(".overlay").css("display", "none");



	 	}



	});



}



function get_tag_stones(){



	$.ajax({



	 	type: 'GET',



	 	url : base_url + 'index.php/admin_ret_tagging/getStoneItems',



	 	dataType : 'json',



	 	success  : function(data){



		 	tag_stones = data;



			if(ctrl_page[2] == "edit"){



				load_tag_stone_list_on_edit();



			}



			console.log("tag_stones:", tag_stones);



	 	}



	});



}



function get_tag_materials(){



	$.ajax({



	 	type: 'GET',



	 	url : base_url + 'index.php/admin_ret_tagging/getAvailableMaterials',



	 	dataType : 'json',



	 	success  : function(data){



		 	tag_materials = data;



			if(ctrl_page[2] == "edit"){



				load_tag_material_list_on_edit();



			}



			console.log("tag_materials", tag_materials);



	 	}



	});



}



function getSearchDesignNo(searchTxt){



	my_Date = new Date();



	$.ajax({



        url: base_url+'index.php/admin_ret_tagging/getDesignNosBySearch/?nocache=' + my_Date.getUTCSeconds(),



        dataType: "json",



        method: "POST",



        data: {'searchTxt': searchTxt},



        success: function (data) {



			$( "#tag_design_no" ).autocomplete(



			{



				source: data,



				select: function(e, i)



				{



					e.preventDefault();



					$("#tag_design_no").val(i.item.label);



					$("#design_id").val(i.item.value);



					if(ctrl_page[2]!='bulk_edit')



					{



						getDesignStoneDetails(i.item.value);



						getDesignMaterialsByDesignId(i.item.value);



					}



					if(ctrl_page[2]=='bulk_edit')



					{



						var  retail_max_wastage_percent=$('#wastage_percent').val();



						var  tag_mc_value=$('#mc_value').val();



						var  design_id=$('#design_id').val();



					}



				},



				change: function (event, ui) {



					if (ui.item === null) {



						$(this).val('');



						$('#tag_design_no').val('');



						$("#design_id").val("");



						if($("#design_id").val() == "" || $("#tag_design_no").val() == ""){



							/* $("#create_stn_details").prop('disabled', false);



							$("#create_material_details").prop('disabled', false); */



						}



					}



			    },



				response: function(e, i) {



		            // ui.content is the array that's about to be sent to the response callback.



		            if(searchTxt != ""){



						if (i.content.length === 0) {



						   $("#designAlert").html('<p style="color:red">Enter a valid design code</p>');



						   //$("#tag_design_no" ).val("");



						   if($("#design_id").val() == "" || $("#tag_design_no").val() == ""){



								/* $("#create_stn_details").prop('disabled', false);



								$("#create_material_details").prop('disabled', false); */



							}



						}else{



						   $("#designAlert").html('');



						   /* $("#create_stn_details").prop('disabled', true);



						   $("#create_material_details").prop('disabled', true); */



						}



					}else{



						/* $("#create_stn_details").prop('disabled', false);



						$("#create_material_details").prop('disabled', false); */



					}



		        },



				 minLength: 3,



			});



        }



     });



}



function getDesignStoneDetails(designId)



{



	my_Date = new Date();



	$.ajax({



        url: base_url+'index.php/admin_ret_tagging/getDesignStonesByDesignId/?nocache=' + my_Date.getUTCSeconds(),



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



function getDesignMaterialsByDesignId(designId)



{



	my_Date = new Date();



	$.ajax({



        url: base_url+'index.php/admin_ret_tagging/getDesignMaterialsByDesignId/?nocache=' + my_Date.getUTCSeconds(),



        dataType: "json",



        method: "POST",



        data: { 'designId': designId },



        success: function ( data ) {



			tag_design_materials = data;



			/* console.log(data);



			var html = '';



			$.each(data, function(key, item){



				html += '<tr><td><input type="hidden" name="tagmaterials[material_id][]" value="'+item.material_id+'" required />'+item.material_name+'</td><td><div class="input-group"> <input class="form-control" type="number" step="any" name="tagmaterials[weight][]" value="" required /></div></td><td><input type="hidden" name="tagmaterials[uom_id][]" value="'+item.uom_id+'" />'+item.uom_short_code+'</td><td><div class="input-group"> <input class="form-control" type="number" step="any" name="tagmaterials[amount][]" value="" required /></div></td><td></td></tr>';



			});



			$('#tagging_material_details tbody').append(html); */



        }



     });



}



function load_tag_stone_list_on_edit()



{



	my_Date = new Date();



	$.ajax({



        url: base_url+'index.php/admin_ret_tagging/getTagStoneByTagId/?nocache=' + my_Date.getUTCSeconds(),



        dataType: "json",



        method: "POST",



        data: { 'tagId': $("#tag_id").val() },



        success: function ( data ) {



			console.log(data);



			var html = '';



			$.each(data, function(ekey, eitem){



				var select_op = '<select class="form-control select_stn_det" id="tagstone_'+(ekey+1)+'" name="tagstone[stone_id][]"><option value=""> - Select Stone - </option>';



			var selected_stones = [];



			var op_length = 0;



			$('#tagging_stone_details > tbody  > tr').each(function(index, tr) {



				selected_stones.push({ "st_id" : $(this).find('td:first   .select_stn_det').val()});



			});



			$.each(tag_stones, function(key, item){



				var $exist_flag = false;



				$.each(selected_stones, function(stkey, stval){



					if(stval.st_id == item.stone_id){



						$exist_flag = true;



					}



				});



				if(!$exist_flag){



					var selected = "";



					if(item.stone_id == eitem.stone_id){



						selected = "selected='selected'";



					}



					select_op += '<option value="'+item.stone_id+'" '+selected+'>'+item.stone_name+'</option>';



					op_length++;



				}



			});



			select_op += '</select>';



			html += '<tr><td>'+select_op+'</td><td><div class="input-group"> <input class="form-control tagstone_pcs" type="number" step="any" name="tagstone[pcs][]" value="'+eitem.pieces+'" required /></div></td><td><div class="input-group"><input type="number" class="form-control tagstone_wt" step="any" name="tagstone[weight][]" value="'+eitem.wt+'" required /></div></td><td><input type="hidden" name="tagstone[uom_id][]" value="'+eitem.uom_id+'" /><div class="stn_uom">'+eitem.uom_name+'</div></td><td><div class="input-group"> <input class="form-control tagstone_amt" type="number" step="any" name="tagstone[amount][]" value="'+eitem.amount+'" required /></div></td></tr>';



				$('#tagging_stone_details tbody').append(html);



			});



        }



     });



}



function load_tag_material_list_on_edit()



{



	my_Date = new Date();



	$.ajax({



        url: base_url+'index.php/admin_ret_tagging/getTagMaterialByTagId/?nocache=' + my_Date.getUTCSeconds(),



        dataType: "json",



        method: "POST",



        data: { 'tagId': $("#tag_id").val() },



        success: function ( data ) {



			console.log(data);



			$.each(data, function(ekey, eitem){



				var html = "";



				var select_op = '<select class="form-control select_mat_det" id="tagmat_'+(ekey+1)+'" name="tagmaterials[material_id][]"><option value=""> - Select Material - </option>';



			var selected_materials = [];



			var op_length = 0;



			$('#tagging_material_details > tbody  > tr').each(function(index, tr) {



				selected_materials.push({ "mat_id" : $(this).find('td:first .select_mat_det').val()});



			});



			$.each(tag_materials, function(key, item){



				var $exist_flag = false;



				$.each(selected_materials, function(stkey, stval){



					if(stval.mat_id == item.material_id){



						$exist_flag = true;



					}



				});



				if(!$exist_flag){



					var selected = "";



					if(item.material_id == eitem.material_id){



						selected = "selected='selected'";



					}



					select_op += '<option value="'+item.material_id+'" '+selected+'>'+item.material_name+'</option>';



					op_length++;



				}



			});



			select_op += '</select>';



				html += '<tr><td>'+select_op+'</td><td><div class="input-group"> <input class="form-control tagmat_wt" type="number" step="any" name="tagmaterials[weight]" value="'+eitem.wt+'" required /></div></td><td><input type="hidden" name="tagmaterials[uom_id]" value="'+eitem.uom_id+'" /><div class="stn_uom">'+eitem.uom_name+'</div></td><td><div class="input-group"> <input class="form-control tagmat_amt" type="number" step="any" name="tagmaterials[amount][]" value="'+eitem.price+'" required /></div></td></tr>';



				$('#tagging_material_details tbody').append(html);



			});



        }



     });



}



function validateStoneDetailRow(){



	var st_validate = true;



	$('#tagging_stone_details > tbody  > tr').each(function(index, tr) {



		if($(this).find('td:first .select_stn_det').val() == "" || $(this).find('td:eq(4) div:eq(0) .tagstone_amt').val() == "" || $(this).find('td:eq(1) div:eq(0) .tagstone_pcs').val() == "" || $(this).find('td:eq(2) div:eq(0) .tagstone_wt').val() == "" ){



			st_validate = false;



		}



	});



	return st_validate;



}



function validateMaterialDetailRow(){



	var mt_validate = true;



	$('#tagging_material_details > tbody  > tr').each(function(index, tr) {



		if($(this).find('td:first .select_mat_det').val() == "" || $(this).find('td:eq(1) div:eq(0) .tagmat_wt').val() == "" || $(this).find('td:eq(3) div:eq(0) .tagmat_amt').val() == "" ){



			mt_validate = false;



		}



	});



		return mt_validate;



}



//bulk edit



$('#branch_select').on('change',function(){



    if(this.value!='')



    {



    	if(ctrl_page[2]=='duplicate_print' || (ctrl_page[1]=='tagging' && ctrl_page[2]=='add'))

    	{

    		$('#id_branch').val(this.value);

    		// Clear and reload lot dropdown filtered by the newly selected branch
    		$("#lot_id").empty().append('<option value=""></option>');
    		$('#tag_lot_id').val('');
    		if($("#lot_id").data('select2')) { $("#lot_id").select2("destroy"); }
    		$("#lot_id").select2({placeholder:'Select Lot No', allowClear: true});
    		get_received_lots(this.value);

    	}

    	else if(ctrl_page[2]=='tag_mark')

    	{



    		if(this.value!='')



    		{



    			$('#id_branch').val(this.value);



    		}else{



    			$('#id_branch').val('');



    		}



    	}



    	else if(ctrl_page[2]=='tag_link' && this.value!='')



    	{



    	    get_CustomerOrders();



    	}



    	else{



    		$("#tag_id").val('');



    		$("#tag_no").val('');



    		if(this.value!='')



    		{



    				$('#id_branch').val(this.value);



    		}



    		else



    		{



    			$('#id_branch').val();



    		}



    		if(this.value!='' )



    		{//&& $('#id_mc_type').val()!=''



    			$('#get_tag_details').prop('disabled',false);



    		}



    		else



    		{



    			//$('#get_tag_details').prop('disabled',true);



    		}



    	}



    }



});



$('#mc_type').on('change',function(){



if(this.value!='')



{



	$('#id_mc_type').val(this.value);



}



else



{



	$('#id_mc_type').val('');



}



if($('#id_branch').val() != '')



	{// && $('#id_mc_type').val()!=''



		//$('#get_tag_details').prop('disabled',false);



	}



	else



	{



		//$('#get_tag_details').prop('disabled',true);



	}



});



function get_tag_number(tag_id)



{



	my_Date = new Date();



	var tag_lot_id=$('#tag_lot_id').val();



	var id_branch=$('#id_branch').val();



	$.ajax({



        url: base_url+'index.php/admin_ret_tagging/get_tag_number?nocache=' + my_Date.getUTCSeconds(),



        dataType: "json",



        method: "POST",



        data:{'tag_lot_id':tag_lot_id,'id_branch':id_branch,'tag_id':tag_id},



        success: function (datas) {



			$( "#tag_no" ).autocomplete(



			{



				source: datas,



				select: function(e, i)



				{



					e.preventDefault();



					console.log(i);



					$("#tag_id").val(i.item.value);



					$("#designAlert").html('');



				},



				change: function (event, ui) {



					if(ui.item==null)



					{



							$("#tag_id").val('');



							$("#designAlert").html('');



					}



			    },



				response: function(e, i) {



					if(i.content.length==0)



					{



					   $("#designAlert").html('<p style="color:red">Enter a valid Tag No</p>')



					}



		            // ui.content is the array that's about to be sent to the response callback.



		        },



			});



        }



     });



}



function getSearchProd(searchTxt){



	var tag_lot_id=$('#tag_lot_id').val();



	var id_branch=$('#id_branch').val();



	var tag_id=$('#tag_id').val();



	my_Date = new Date();



	$.ajax({



        url: base_url+'index.php/admin_ret_tagging/get_prod_by_tagno?nocache=' + my_Date.getUTCSeconds(),



        dataType: "json",



        method: "POST",



        data: {'prod_name':searchTxt,'tag_lot_id':tag_lot_id,'id_branch':id_branch,'tag_id':tag_id},



        success: function (data) {



			$( "#prod_name" ).autocomplete(



			{



				source: data,



				select: function(e, i)



				{



					e.preventDefault();



					$("#prod_name" ).val(i.item.label);



					$('#lot_product').val(i.item.value);



				},



				response: function(e, i) {



		            // ui.content is the array that's about to be sent to the response callback.



		            console.log(i);



		            if (i.content.length === 0) {



		               $("#prodAlert").html('<p style="color:red">Enter a valid Product</p>');



		            }else{



						$("#prodAlert").html('');



					}



		        },



				 minLength: 0,



			});



        }



     });



}



function getSearchDes(searchTxt){



	my_Date = new Date();



	$.ajax({



        url: base_url+'index.php/admin_ret_tagging/getDesignNosBySearch?nocache=' + my_Date.getUTCSeconds(),



        dataType: "json",



        method: "GET",



        success: function (data) {



			$( "#des_name" ).autocomplete(



			{



				source: data,



				select: function(e, i)



				{



					e.preventDefault();



					$("#des_name" ).val(i.item.label);



					$("#id_design" ).val(i.item.value);



				},



				response: function(e, i) {



		            // ui.content is the array that's about to be sent to the response callback.



		            console.log(i);



		            if (i.content.length === 0) {



		               $("#desdAlert").html('<p style="color:red">Enter a valid Design</p>');



		            }else{



						$("#desdAlert").html('');



					}



		        },



				 minLength: 0,



			});



        }



     });



}



$('#get_tag_details').on('click',function(){



	$('#tagging_list').DataTable().clear().draw();



	$('#tag_img').val('');

	$('#tag_images').val('');



	var tag_lot_id=$('#tag_lot_id').val();



	var id_branch=$('#id_branch').val();



	var to_branch=$('#branch_to').val();





	var tag_id=$('#tag_id').val();



	if($('#lot_product').length > 0){



	    var lot_product= $('#lot_product').val();



	}else if($('#prod_select').length > 0){



	    var lot_product= $('#prod_select').val();



	}



	var from_weight=$('#from_weight').val();



	var to_weight=$('#to_weight').val();



	var id_mc_type=$('#id_mc_type').val();



	var mc_value=$('#old_mc_value').val();



	var making_per=$('#old_mc_per').val();



	var making_per=$('#old_mc_per').val();



	var tag_code=$.trim($('#be_tag_code').val());



	var karigar=$('#tag_karigar').val();



	var lot_no=$('#lot_no').val();





	var old_tag_code=$.trim($('#be_old_tag_code').val());



	var bulk_edit_options = $('#bulk_edit_options').val();



	if($('#id_design').length > 0){



    	var id_design=$('#id_design').val();



	}else if($('#des_select').length > 0){



		var id_design=$('#des_select').val();



	}



	var id_sub_design=$('#sub_des_select').val();



	if((bulk_edit_options == 3  || bulk_edit_options == 9 ) ? tag_code == "" : false) {



		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Tag Code required!"});



		$('#be_tag_code').focus();



		return;



	} else if(bulk_edit_options == 5 || bulk_edit_options == 8 ? ((lot_product == "" || lot_product == null) && tag_code == "") : false) {



		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Product or Tag code required!"});



		$('#be_tag_code').focus();



		return;



	}



	else if(bulk_edit_options == 15 && id_branch == "") {



		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select branch!"});



		return;



	}



	my_Date = new Date();



	$("div.overlay").css("display", "block");



	$.ajax({



        url: base_url+'index.php/admin_ret_tagging/get_tag_details?nocache=' + my_Date.getUTCSeconds(),



        dataType: "json",



        method: "POST",



        data: {'id_branch':id_branch,'tag_id':tag_id,'old_tag_code':old_tag_code,

		'id_mc_type':id_mc_type,'lot_product':lot_product,'from_weight':from_weight,

		'to_weight':to_weight,'mc_value':mc_value,'making_per':making_per,'id_design':id_design,

		'id_sub_design':id_sub_design,'tag_code':tag_code,'bulk_edit_options':bulk_edit_options,

	     'karigar':karigar,'lot_no':lot_no,'to_branch':to_branch},



        success: function (data) {



			if(data.tag_details !== null && Object.keys(data.tag_details).length > 0) {



				$("#be_purity").empty();



				$("#be_size").empty();



				if(bulk_edit_options == 5) {



					$("#be_purity").append(



						$("<option></option>")



						.attr("value", '')



						.text("--Select--")



					);



					$.each(data.purities, function (key, item) {



						$("#be_purity").append(



							$("<option></option>")



							.attr("value", item.id_purity)



							.text(item.purity)



						);



					});



				} else if(bulk_edit_options == 8) {



					$("#be_size").append(



						$("<option></option>")



						.attr("value", '')



						.text("--Select--")



					);



					$.each(data.sizes, function (key, item) {



						$("#be_size").append(



							$("<option></option>")



							.attr("value", item.id_size)



							.text(item.value+'-'+item.name)



						);



					});



				} else if(bulk_edit_options == 3) {



					if(Object.keys(data.tag_details).length == 1) {



						$("#be_gross_wt").val(data.tag_details[0].gross_wt);



						$("#be_less_wt").val(data.tag_details[0].less_wt);



						$("#be_net_wt").val(data.tag_details[0].net_wt);



						$("#tag_edit_stone_details").val(JSON.stringify(data.tag_details[0].stone_details));



					}



				}



                     else if(bulk_edit_options == 11) {



                    	if(data.tag_details.length>0){



                    	console.log(data.tag_details);



                    		$.each(data.tag_details, function(skey, sitem){



                    			console.log(sitem.huid);



                    			modalHuidDetail =sitem.huid



                    			$('#other_huid_details').val(JSON.stringify(sitem.huid))



                    		});



                    	}



                    } else if(bulk_edit_options == 14) {



						if(Object.keys(data.tag_details).length == 1) {



							$("#bulk_mc").val(data.tag_details[0].tag_mc_value);



							$("#bulk_mc_type").val(data.tag_details[0].tag_mc_type);



							$("#bulk_pcs").val(data.tag_details[0].piece);



							$("#bulk_wastage").val(data.tag_details[0].retail_max_wastage_percent);



							$("#bulk_net").val(data.tag_details[0].net_wt);



							$("#bulk_gross").val(data.tag_details[0].gross_wt);



							$("#bulk_charges").val(data.tag_details[0].charge_value);



							$("#bulk_othermetal").val(data.tag_details[0].other_metal_amt);



							$("#bulk_stones").val(data.tag_details[0].stone_price);



							//$("#bulk_rate_per_gram").val(data.tag_details[0].bulk_rate_per_gram);



							$("#tag_edit_stone_details").val(JSON.stringify(data.tag_details[0].stone_details));



						}



					} else if(bulk_edit_options == 16) {



						if($.trim($("#be_tag_code").val()) != "") {



							var product_id = data.tag_details[0].product_id !== undefined && data.tag_details[0].product_id > 0 ? data.tag_details[0].product_id : 0;



							if (product_id > 0) {



								$("#id_product").val(product_id);



								$("#prod_select").val(product_id);



							} else {



								$("#id_product").val("");



								$("#prod_select").val("");



							}



						}



					}



				BulkEditDetails();



				if(bulk_edit_options == 16 && $.trim($("#be_tag_code").val()) != "") {



					$.when(get_Activedesign($("#prod_select").val())).done(function(desData) {



						setTimeout(function(){



							set_tagedit_list(data.tag_details);



							$("div.overlay").css("display", "none");



						  },750);



					});



				} else {



					set_tagedit_list(data.tag_details);



					$("div.overlay").css("display", "none");



				}



			} else {



				$.toaster({ priority : 'warning', title : 'Warning!', message : ''+"</br>No records found!"});



				$("div.overlay").css("display", "none");



			}



        }



     });



});



$('#select_all').click(function(event) {



	$("tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));



    event.stopPropagation();



});



function BulkEditDetails()



{



	$('.editable_block').css("display","block");



	var bulk_edit_options = $('#bulk_edit_options').val();

    $(".mc_type_block,.mc_value_block, .va_block, .mrp_value_block, .gross_wt_block, .less_wt_block, .net_wt_block, .pcs_block, .update_lot_block, .purity_block, .attribute_block,.size_block,.image_block,.charges_block,.huid_block, .old_tag_id_block, .cal_type_block,.purchase_cost_block,.branch_edit_block,.design_sub_design_block,.narration_block").css("display","none");



    $('#select_all').prop('disabled', false);

    $('input[name="tag_id[]"]').prop('disabled', false);

    $('.save_edit_tag').prop('disabled', false);



    if(bulk_edit_options == 1) {



    	$(".mc_type_block, .mc_value_block").css("display","block");



    } else if(bulk_edit_options == 2) {



    	$(".bulk_edit_wastage").css("display","block");



    }



    else if(bulk_edit_options == 3 || bulk_edit_options == 4 || bulk_edit_options == 6)



    {



    	$(".bulk_edit_tag_code").css("display","block");



    	if(bulk_edit_options == 3) {



    		$(".gross_wt_block, .less_wt_block, .net_wt_block").css("display","block");



    	}



    	else if(bulk_edit_options == 4) {



    		$(".pcs_block").css("display","block");



    	}



    	else if(bulk_edit_options == 6) {



    		$(".mrp_value_block").css("display","block");



    	}



    }



    else if(bulk_edit_options == 5 || bulk_edit_options == 7 || bulk_edit_options == 8) {



    	if(bulk_edit_options == 5)



    	{



    		$(".purity_block").css("display","block");



    	}



    	else if(bulk_edit_options == 7) {



    		$(".attribute_block").css("display","block");



    	}



    	else if(bulk_edit_options == 8) {



    		$(".size_block").css("display","block");



    	}



    }



    else if(bulk_edit_options==9)



    {



        $('.image_block').css("display","block");



    }



	else if(bulk_edit_options==10)



    {



        $('.charges_block').css("display","block");



    }



	else if(bulk_edit_options==11)

    {

        $('.huid_block').css("display","block");
        $('input[name="tag_id[]"]').prop('checked', false);
        $('#select_all').prop('checked', false).prop('disabled', true);
        $('input[name="tag_id[]"]').prop('disabled', false);
    }



	else if(bulk_edit_options==12)



    {



        $('.old_tag_id_block').css("display","block");



    }else if(bulk_edit_options==13)



    {



        $('.cal_type_block').css("display","block");



    }else if(bulk_edit_options==14)



    {



        $('.purchase_cost_block').css("display","block");



    }



	else if(bulk_edit_options==15)



    {



        $('.branch_edit_block').css("display","block");



    }



	else if(bulk_edit_options==16)



    {



        $('.design_sub_design_block').css("display","block");



    }
	else if(bulk_edit_options==17)

    {
		$('.narration_block').css("display","block");

    }

}



function set_tagedit_list(data)



{

	var bulk_edit_options = $('#bulk_edit_options').val();



   var oTable = $('#tagging_list').DataTable();



	 oTable.clear().draw();



	 	oTable = $('#tagging_list').dataTable({



			"bDestroy": true,



			"bInfo": true,



			"order": [[ 0, "desc" ]],



			"bFilter": true,



			"bSort": true,



			"dom": 'lBfrtip',



			"buttons" : ['excel','print'],



			"lengthMenu": [ [ 10, 25, 50, -1], [10, 25, 50, "All"] ],



			"tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },



			"aaData": data,



			"aoColumns": [ { "mDataProp": function ( row, type, val, meta ){



		                	chekbox='<input type="checkbox" class="tag_id" name="tag_id[]" value="'+row.tag_id+'"/><input type="hidden" class="tag_mc_value" value="'+row.tag_mc_value+'"/><input type="hidden" class="retail_max_wastage_percent" value="'+row.retail_max_wastage_percent+'"/><input type="hidden" class="calculation_based_on" value="'+row.calculation_based_on+'"><input type="hidden" class="gross_wt" value="'+row.gross_wt+'"/><input type="hidden" class="net_wt" value="'+row.net_wt+'"><input type="hidden" class="less_wt" value="'+row.less_wt+'"/><input type="hidden" class="no_of_piece" value="'+row.piece+'"/><input type="hidden" class="id_mc_type" value="'+row.tag_mc_type+'"/><input type="hidden" class="stone_price" value="'+row.stone_price+'"><input type="hidden" class="tgi_calculation" value="'+row.tax_group_id+'"><input type="hidden" class="sell_rate" value="'+row.sales_value+'"><input type="hidden" class="metal_rate" value="'+row.metal_rate['goldrate_22ct']+'"><input type="hidden" class="charge_value" value="'+row.charge_value+'">'



		                	return chekbox+" "+row.tag_id;



		                }},



					    { "mDataProp": "tag_code" },



						{ "mDataProp": "old_tag_id" },



                        { "mDataProp":function (row,type,val,meta)



                            {



                            var type  = "";



                            id       = row.tag_id;



                            if (row.tag_default_image)



                            {



                            type = base_url + 'assets/img/tag/' + row.tag_default_image;



                            return '<img src=' + type + ' width="40" height="35"><br><i class="bi bi-eye" ></i><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" onclick = "view_tag_imgs(' + row.tag_id + ')" viewBox="0 0 16 16"><path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/><path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/></svg>';



                            }



                            else



                            {



                            type = base_url + 'assets/img/no_image.png';



                            return '<img src=' + type + ' width="40" height="35"><br><i class="bi bi-eye" ></i><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" onclick = "view_tag_imgs(' + row.tag_id + ')" viewBox="0 0 16 16"><path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/><path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/></svg>';



                            }



                            }



                        },



					    { "mDataProp": "product_name" },



						{ "mDataProp": "design_name" },



						{ "mDataProp": "sub_design_name" },



						{ "mDataProp": "tag_datetime" },



						{ "mDataProp": "piece" },



						{ "mDataProp": "gross_wt" },



						{ "mDataProp": "less_wt" },



						{ "mDataProp": "net_wt" },



						{ "mDataProp": "retail_max_wastage_percent" },



						{ "mDataProp": "mc_type" },



						{ "mDataProp": "tag_mc_value" },



						{ "mDataProp": function ( row, type, val, meta ) {



							return '<button  class="btn btn-primary add_tag_lwt_pur" ><i>Stones</i></button>';



						} },

						{ "mDataProp": function ( row, type, val, meta ) {

							if(bulk_edit_options == 14){



							    return '<select class="form-control lot_calc_type" ><option  '+( row.lot_calc_type == 1 ?'selected':'')+' value="1"> Weight x Rate </option>  <option value="2" '+( row.lot_calc_type == 2 ?'selected':'')+'>Purchase Touch</option><option value="3" '+( row.lot_calc_type == 3 ?'selected':'')+' >Weight x Wastage %</option></select>';



							}else{



								return '<span>'+row.lot_calc_type_name+'</span>';



							}

						} },

						{ "mDataProp": function ( row, type, val, meta ) {



							if(row.lot_wastage_percentage < 100){



							if(bulk_edit_options == 14){



								return '<input type="number" class="form-control lot_wastage_percentage" value="'+row.lot_wastage_percentage+'">';



							}else{



								return '<span>'+row.lot_wastage_percentage+'</span>';



							}



							}else{



								if(bulk_edit_options == 14){



									return '<input type="number" class="form-control lot_wastage_percentage">';



								}else{



									return '<span></span>';



								}

							}

						}},

						{ "mDataProp": function ( row, type, val, meta ) {



							if(bulk_edit_options == 14){



								return '<select class="form-control lot_mc_type" ><option value="1" '+( row.lot_mc_type == 1 ?'selected':'')+' >Per Gram</option><option '+( row.lot_mc_type == 2 ?'selected':'')+' value="2">Per Piece</option></select>';



							}else{



								return '<span>'+row.lot_mc_type_name+'</span>';



							}





						}},

						{ "mDataProp": function ( row, type, val, meta ) {



							if(bulk_edit_options == 14){



								return '<input type="number" class="form-control lot_making_charge" value="'+row.lot_making_charge+'">';



							}else{



								return '<span>'+row.lot_making_charge+'</span>';



							}





						}},

						{ "mDataProp": function ( row, type, val, meta ) {



							if(bulk_edit_options == 14){



								return '<input type="number" class="form-control lot_purchase_touch" value="'+row.lot_purchase_touch+'">';



							}else{





								return '<span>'+row.lot_purchase_touch+'</span>';



							}





						}},

						{ "mDataProp": function ( row, type, val, meta ) {



							if(bulk_edit_options == 14){



								return '<div class="input-group"  style="width:200px;" ><input style="width:60%;" type="number" class="form-control lot_rate" value="'+row.lot_rate+'" ><select  style="width:40%;"  class="form-control lot_rate_calc_type" >  <option value="1" '+( row.lot_rate_calc_type == 1 ?'selected':'')+'>Gram</option><option '+( row.lot_rate_calc_type == 2 ?'selected':'')+' value="2">Pcs</option></select></div>';



							}else{



								return '<span>'+row.lot_rate_name+'</span>';



							}





						}},

						{ "mDataProp": function ( row, type, val, meta ) {



							action_content ='<input type="number" class="form-control tag_purchase_cost" readonly value="'+row.tag_purchase_cost+'">'

							                +'<input type="hidden" class="form-control tag_pcs" value="'+row.piece+'">'

											+'<input type="hidden" class="form-control stone_details" value=\''+JSON.stringify(row.stones_details)+'\'>'

							                +'<input type="hidden" class="form-control tag_net_wt" value="'+row.net_wt+'">'

											+'<input type="hidden" class="form-control tag_less_wt" value="'+row.less_wt+'">'

											+'<input type="hidden" class="form-control charge_value" value="'+row.charge_value+'">'

											+'<input type="hidden" class="form-control tag_gross_wt" value="'+row.gross_wt+'">'

											+'<input type="hidden" class="form-control purchase_tgrp" value="'+row.purchase_tgrp+'">'

											+'<input type="hidden" class="form-control calculation_based_on" value="'+row.calculation_based_on+'">'

											+'<input type="hidden" class="form-control other_metal" value="'+row.other_metal_amt+'">'

											+'<input type="hidden" class="form-control tag_purchase_taxable" value="'+row.tag_purchase_taxable+'">'

											+'<input type="hidden" class="form-control tag_purchase_tax" value="'+row.tag_purchase_tax+'">'

											+'<input type="hidden" class="form-control stone_price" value="'+row.stone_price+'">'

											+'<input type="hidden" class="form-control karigar_type" value="'+row.karigar_type+'">'

											+'<input type="hidden" class="form-control tax_type" value="'+row.tax_type+'">'

											+'<input type="hidden" class="form-control metal_type" value="'+row.metal_type+'">'

											+'<input type="hidden" class="form-control lot_rate" value="'+row.lot_rate+'">'

											+'<input type="hidden" class="form-control lot_purchase_touch" value="'+row.lot_purchase_touch+'">'

											+'<input type="hidden" class="form-control lot_calc_type" value="'+row.lot_calc_type+'">'

											+'<input type="hidden" class="form-control lot_mc_type" value="'+row.lot_mc_type+'">'

											+'<input type="hidden" class="form-control lot_making_charge" value="'+row.lot_making_charge+'">'

											+'<input type="hidden" class="form-control lot_wastage_percentage" value="'+row.lot_wastage_percentage+'">'

											+'<input type="hidden" class="form-control tagid" value="'+row.tag_id+'">';



							 return action_content;



						}},



						{ "mDataProp": "sales_value" },



						{ "mDataProp": function ( row, type, val, meta ) {



								let id = row.tag_id;



								if(row.charges.length > 0) {



									let action_content = '<a href="#" class="btn btn-primary btn-bulk-charges-view" id="edit" role="button" data-toggle="modal" data-id='+id+' data-target="#bulk_edit_charges_modal"><i>view</i></a>';



									return action_content;



								}	else {



									return '';



								}



							}



						},



						{ "mDataProp": function ( row, type, val, meta ) {



								let id = row.tag_id;



								if(row.attributes.length > 0) {



									let action_content = '<a href="#" class="btn btn-primary btn-bulk-attribute-view" id="edit" role="button" data-toggle="modal" data-id='+id+' data-target="#bulk_edit_attributes_modal"><i>View</i></a>';



									return action_content;



								} else {



									return '';



								}



							},





						},


						{ "mDataProp": "narration" },
						{ "mDataProp": function ( row, type, val, meta ) {

							return '<button class="form-control save_edit_tag" >Save</button>'

						}

						}



					]



		});



		if($('#select_all').is(":checked")==false)



		{



			$('#select_all').trigger('click');



		}



		else



		{



			$("input[name='tag_id[]']").prop('checked',true);



		}



}



function view_tag_imgs(tag_id_img)



{



    my_Date = new Date();



    update_tag_img_id = tag_id_img;



    data = [];



    var tag_codeimage = base_url + 'assets/img/tag';



    $('#imageModal_bulk_edit').modal('show');



    $(".overlay").css('display', "none");



    $.ajax({



        data: ({ 'tag_id': tag_id_img }),



        url: base_url + "index.php/admin_ret_tagging/get_img_by_id?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),



        dataType: "JSON",



        type: "POST",



        success: function (data) {



            retrive_img = data;



            for (i = 0; i < data.length; i++) {



                img_src = data[i].image;



                var preview = $('#order_images');



                var img = tag_codeimage + '/' + img_src;



                if (img_src) {



                    div = document.createElement("div");



                    div.setAttribute('class', 'col-md-3 images');



                    div.setAttribute('id', 'order_img_edit_' + [i]);



                    key = [i];



                    param = img_src;



                    div.innerHTML += "<div id='imgid_"+data[i].id_tag_img+"' class='form-group'><div class='image-input image-input-outline' id='kt_image_'><div class='image-input-wrapper'><a onclick='remove_order_images_new(" + JSON.stringify(param) + "," + JSON.stringify([i]) + ")'><i class='fa fa-trash'></i>Delete</a><img class='thumbnail' src='" + img + "'" + "style='width: 115px;height: 115px;'/><span><input type='checkbox' "+(data[i].is_default == 1 ? 'checked' : '')+" class='tag_default_"+key+"' name='' value='' onclick='default_stn_set(" + JSON.stringify(param) + "," + JSON.stringify([i]) + ")' data-toggle='tooltip' data-placement='bottom' title='' style='float:left;margin-right:20px;' data-original-title='Click Here To Set Default Image'><b>Is Default<b></span></div></div>";



                    preview.append(div);



                }



            }



    },



    error: function(error) {



        $("div.overlay").css("display", "none");



    }



});



}



function remove_order_images_new(param, key) {



	// var index = retrive_img.indexOf(param);



	 retrive_img.splice(key, 1);



	 $('#order_img_edit_' + key).remove();



 }



  $('#update_img_bulk').on('click', function () {



	let id_branch = $("#branch_select").val();



	console.log(retrive_img);



	let no_defaults = 0;



	$.each(retrive_img, function (imgkey, imgitem) {



		let is_default = $("#imgid_" + imgitem.id_tag_img).find(':checkbox').is(':checked') ? 1 : 0;



		no_defaults = is_default == 1 ? ++no_defaults : no_defaults;



		retrive_img[imgkey].is_default = is_default;



	});



	if(no_defaults == 1) {



		my_Date = new Date();



		$.ajax({



			data: ({ 'id_branch' : id_branch, 'image': retrive_img, 'tag_id': update_tag_img_id }),



			url: base_url + "index.php/admin_ret_tagging/update_tag_img_by_id?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),



			dataType: "JSON",



			type: "POST",



			success: function (data) {



				if (data || data==null) {



					$.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>"+'Image Updated Successfully..'});



					$('#imageModal_bulk_edit').modal('toggle');



					window.location.reload();



				}



			},



			error: function (error) {



				$("div.overlay").css("display", "none");



			}



		});



	} else {



		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Choose 1 default image for this tag'});



	}



});



$("#imageModal_bulk_edit").on("hidden.bs.modal", function () {



    $('#order_images').empty();



});



/*$('#bulk_edit').on('click',function() {



    if($("input[name='tag_id[]']:checked").val()) {



		let bulk_edit_options = $("input[name='tag_update_options']:checked").val();



		var selected = [];



		$("#bulk_edit").prop('disabled',true);



		$(".overlay").css('display','none');



		if(bulk_edit_options == 1) {



			$("#tagging_list tbody tr").each(function(index, value){



			if($(value).find("input[name='tag_id[]']:checked").is(":checked")) {



				var disc_limit_type=$('#disc_limit_type').val();



				var total_price = 0;



				var base_value_price = 0;



				var arrived_value_price = 0;



				var base_value_tax = 0;



				var arrived_value_tax = 0;



				var base_rate_tax = 0;



				var arrived_rate_tax = 0;



				var total_tax_per = 0;



				var total_tax_rate = 0;



				var gross_wt            =  (($(value).find(".gross_wt").val() == '')  ? 0 : $(value).find(".gross_wt").val());



				var less_wt             =  (($(value).find(".less_wt").val() == '')  ? 0 : $(value).find(".less_wt").val());



				var net_wt              =  (($(value).find(".net_wt").val() == '')  ? 0 : $(value).find(".net_wt").val());



				var id_mc_type          =  (($(value).find(".id_mc_type").val() == '')  ? 0 : $(value).find(".id_mc_type").val());



				var no_of_piece         =  (($(value).find(".no_of_piece").val() == '')  ? 0 : $(value).find(".no_of_piece").val());



				var stone_price         =  $(value).find(".stone_price").val();



				var calculation_type    =  $(value).find(".calculation_based_on").val();



				var rate_per_grm        =  $(value).find(".metal_rate").val();



				var sell_rate           =  $(value).find(".sell_rate").val();



				var wastage_percent     =  $(value).find(".retail_max_wastage_percent").val();



				var tag_mc_value     	=  $(value).find(".tag_mc_value").val();



				var retail_max_mc        = $('#mc_value').val();



				var edit_wast_per        = $('#wastage_percent').val();



				var edit_mc_type         = $('#update_mc_type').val();



				var total_charges        = $(value).find(".charge_value").val();



				var mc_type              = (edit_mc_type!='' && edit_mc_type!=null && edit_mc_type!=undefined ? edit_mc_type:id_mc_type);



				var tot_wastage          = (edit_wast_per!='' && edit_wast_per!=null && edit_wast_per!=undefined ? edit_wast_per:wastage_percent);



				var mc_value              = (retail_max_mc!='' && retail_max_mc!=null && retail_max_mc!=undefined ? retail_max_mc:tag_mc_value);



				if(calculation_type == 0){



					var wast_wgt      = parseFloat(parseFloat(gross_wt) * parseFloat(tot_wastage/100)).toFixed(3);



					if(mc_type != 3){



						var making_charge       =  parseFloat(mc_type == 2 ? parseFloat(mc_value * gross_wt ) : parseFloat(mc_value * no_of_piece));



						// Metal Rate + Stone + OM + Wastage + MC



						rate_with_mc = parseFloat(parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(making_charge)+parseFloat(stone_price));



					}else{



						var making_charge  = parseFloat((parseFloat(gross_wt) * parseFloat(rate_per_grm)) * parseFloat(mc_value/100)).toFixed(3);



						rate_with_mc       =  parseFloat(parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(making_charge)+parseFloat(stone_price));



					}



				}



				else if(calculation_type == 1){



					var wast_wgt      = parseFloat(parseFloat(net_wt) * parseFloat(tot_wastage/100)).toFixed(3);



					if(mc_type != 3){



						var making_charge       =  parseFloat(mc_type == 2 ? parseFloat(mc_value * net_wt ) : parseFloat(mc_value * no_of_piece));



						// Metal Rate + Stone + OM + Wastage + MC



						rate_with_mc = parseFloat(parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(making_charge)+parseFloat(stone_price));



					}else{



						var making_charge  = parseFloat((parseFloat(net_wt) * parseFloat(rate_per_grm)) * parseFloat(mc_value/100)).toFixed(3);



						rate_with_mc       =  parseFloat(parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(making_charge)+parseFloat(stone_price));



					}



				}



				else if(calculation_type == 2){



					var wast_wgt      = parseFloat(parseFloat(net_wt) * parseFloat(tot_wastage/100)).toFixed(3);



					if(mc_type != 3){



						var making_charge       =  parseFloat(mc_type == 2 ? parseFloat(mc_value * gross_wt ) : parseFloat(mc_value * no_of_piece));



						// Metal Rate + Stone + OM + Wastage + MC



						rate_with_mc = parseFloat((parseFloat(rate_per_grm) * (parseFloat(wast_wgt) + parseFloat(net_wt)))+ parseFloat(making_charge))+parseFloat(stone_price);



					}else{



						var making_charge  = parseFloat((parseFloat(gross_wt) * parseFloat(rate_per_grm)) * parseFloat(mc_value/100)).toFixed(3);



						rate_with_mc = parseFloat((parseFloat(rate_per_grm) * (parseFloat(wast_wgt) + parseFloat(net_wt)))+ parseFloat(making_charge))+parseFloat(stone_price);



					}



				}



				else if(calculation_type == 3 || calculation_type == 4){



						var sell_rate  = (isNaN(sell_rate) || sell_rate == '')  ? 0 : sell_rate;



						var adjusted_item_rate  = (isNaN($('.adjusted_item_rate').val()) || $('.adjusted_item_rate').val() == '')  ? 0 : curRow.find('.adjusted_item_rate').val();



						caculated_item_rate = (parseFloat(sell_rate)*parseFloat(net_wt))*parseFloat(no_of_piece);



						$("#caculated_item_rate").val(caculated_item_rate);



						rate_with_mc = parseFloat(parseFloat(adjusted_item_rate) > 0 ? parseFloat(adjusted_item_rate) : caculated_item_rate );



				}



				console.log("total_charge",total_charges);



				console.log("rate_with_mc",rate_with_mc);



				var rate_with_charges = parseFloat(parseFloat(rate_with_mc) + parseFloat(total_charges)).toFixed(2);



				//calculate sale value



				transData = {



				'tag_id'   		:  $(value).find(".tag_id").val(),



				'sales_value'   :  rate_with_charges,



				'tot_wastage'   :  wast_wgt,



				'retail_max_wastage_percent' : tot_wastage,



				'tag_mc_value'  :  mc_value,



				'id_mc_type'    :  mc_type,



				'bulk_edit_options'  : bulk_edit_options



				}



				selected.push(transData);



			}



			})



			req_data = selected;



			update_tagging_data(req_data);



		} else if(bulk_edit_options == 2) {



			let attribute_type = $("#attribute_type").val();



			let attribute_validated = false;



			if(attribute_type == 1)  {



				let _attr_rows = $("#bulk_edit_attribute_detail tbody tr").length;



				if(_attr_rows > 0)



				{



					if(validate_bulk_edit_attr_row())



					{



						attribute_validated = true;



					}



					else



					{



						attribute_validated = false;



					}



				}



				else



				{



					attribute_validated = false;



				}



			}



			else



			{



				attribute_validated = true;



			}



			if(attribute_validated) {



				let attributes = [];



				if(attribute_type == 1)  {



					let _attr_rows = $("#bulk_edit_attribute_detail tbody tr");



					$.each(_attr_rows, function (attrkey, attritem) {



						let attr_name = $(attritem).find('.bulk_tag_upd_attr_name').val();



						let attr_value = $(attritem).find('.bulk_tag_upd_attr_value').val();



						let has_attr_name = false;



						$.each(attributes, function (attrkey, attritem) {



							if(attr_name == attritem['attr_name'])  {



								has_attr_name = true;



							}



						});



						if (!has_attr_name) {



							var attrs = {



								"attr_name" :  attr_name,



								"attr_value" :  attr_value



							};



							attributes.push(attrs);



						}



					});



				}



				console.log("attributes",attributes);



				$("#tagging_list tbody tr").each(function(index, value)	{



					if($(value).find("input[name='tag_id[]']:checked").is(":checked")) {



						transData = {



							'tag_id'   		:  $(value).find(".tag_id").val(),



							'bulk_edit_options'   :  bulk_edit_options,



							'attribute_type'  :  attribute_type,



							'attributes' : attributes



						}



						selected.push(transData);



					}



				});



				update_tagging_data(selected);



			} else {



				alert('Please Select The Attributes');



				$("#otp_submit").prop('disabled',false);



			}



		}



	} else {



		alert('Please Select Tag');



		$("#otp_submit").prop('disabled',false);



	}



});*/



function validate_bulk_edit() {



	let is_validated = true;



	let bulk_edit_options = $("#bulk_edit_options").val();



	let id_branch = $("#branch_select").val();



	if(!(id_branch > 0)) {



		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Branch required!"});



		is_validated = false;



	} else if(bulk_edit_options == 1) {



		if($.trim($('#update_mc_type').val()) == '') {



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>MC type required!"});



			is_validated = false;



		} else if($.trim($('#mc_value').val()) == '') {



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>MC value required!"});



			is_validated = false;



		}



	} else if(bulk_edit_options == 2) {



		if($.trim($('#wastage_percent').val()) == '') {



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Wastage required!"});



			is_validated = false;



		}



	} else if(bulk_edit_options == 3) {



		if($.trim($('#be_gross_wt').val()) == '') {



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Gross wt required!"});



			is_validated = false;



		} else if($.trim($('#be_net_wt').val()) == '') {



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Net wt required!"});



			is_validated = false;



		} else if(parseFloat($.trim($('#be_gross_wt').val())) < 0) {



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Gross wt cannot be negative!"});



			is_validated = false;



		} else if(parseFloat($.trim($('#be_net_wt').val())) < 0) {



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Net wt cannot be negative!"});



			is_validated = false;



		}



	} else if(bulk_edit_options == 4) {



		if(parseFloat($.trim($('#be_pcs').val())) == '') {



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Pieces required!"});



			is_validated = false;



		} else if(parseFloat($.trim($('#be_pcs').val())) < 0) {



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Pieces cannot be negative!"});



			is_validated = false;



		}



	} else if(bulk_edit_options == 5) {



		if($.trim($('#be_purity').val()) == '') {



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Purity required!"});



			is_validated = false;



		}



	} else if(bulk_edit_options == 6) {



		if($.trim($('#be_mrp_value').val()) == '') {



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>MRP price required!"});



			is_validated = false;



		}



	} else if(bulk_edit_options == 7) {



		let attribute_type = $("#attribute_type").val();



		let attribute_validated = false;



		if(attribute_type == 1)  {



			let _attr_rows = $("#bulk_edit_attribute_detail tbody tr").length;



			if(_attr_rows > 0) {



				if(validate_bulk_edit_attr_row()) {



					attribute_validated = true;



				} else {



					attribute_validated = false;



				}



			} else {



				attribute_validated = false;



			}



		} else {



			attribute_validated = true;



		}



		is_validated = attribute_validated;



	} else if(bulk_edit_options == 10) {



		let charge_type = $("#charge_type").val();



		let charges_validated = false;



		if(charge_type == 1)  {



			let _charge_rows = $("#bulk_edit_charge_detail tbody tr").length;



			if(_charge_rows > 0) {



				charges_validated = true;



			} else {



				charges_validated = false;



			}



		} else {



			charges_validated = true;



		}



		if(!charges_validated) {



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Any one charges required!"});



		}



		is_validated = charges_validated;



	} else if(bulk_edit_options == 12) {



		let total_tags = 0



		$("#tagging_list tbody tr").each(function(index, value) {



			if($(value).find("input[name='tag_id[]']:checked").is(":checked")) {



				total_tags = total_tags +1;



			}



		});



		if(total_tags > 1) {



			is_validated = false;



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Only one tag can be selected for old tag update"});



		}



	}else if(bulk_edit_options == 14 ) {



		if(! validateStoneBulkItemDetailRow()) {



			is_validated = false;



		}



		let total_tags = 0



		$("#tagging_list tbody tr").each(function(index, value) {



			if($(value).find("input[name='tag_id[]']:checked").is(":checked")) {



				total_tags = total_tags +1;



			}



		});



		if(total_tags > 1) {



			is_validated = false;



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Only one tag can be selected for  tag update"});



		}



	} else if(bulk_edit_options == 16) {



		if(!($('#bulkedit_des_update').val() > 0) || !($('#bulkedit_sub_des_update').val() > 0)) {



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Design and sub design required!"});



			is_validated = false;



		}



	}

	// else if(bulk_edit_options == 15) {



	// 	if($.trim($('#pur_mc_value').val()) == '') {



	// 		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Purchase MC  required!"});



	// 		is_validated = false;



	// 	}else if($.trim($('#purchase_wastage').val()) == '') {



	// 		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Purchase Wastage  required!"});



	// 		is_validated = false;



	// 	}else if($.trim($('#purchase_touch').val()) == '') {



	// 		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Purchase Touch required!"});



	// 		is_validated = false;



	// 	}else if($.trim($('#rate_per_gram').val()) == '') {



	// 		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Purchase Rate required!"});



	// 		is_validated = false;



	// 	}



	// }



	return is_validated;



}



$('#bulk_edit').on('click',function() {



	let bulk_edit_validate = validate_bulk_edit();



	let id_branch = $("#branch_select").val();



	let to_branch = $("#branch_to").val();





	if(bulk_edit_validate) {



		if($("input[name='tag_id[]']:checked").val()) {



			let bulk_edit_options = $("#bulk_edit_options").val();



			var selected = [];



			let bulk_edit_mc 		= (($('#update_mc_type').val() == '')  ? 0 : $('#update_mc_type').val());



			let bulk_edit_mc_value 	= (($('#mc_value').val() == '')  ? 0 : $('#mc_value').val());



			let bulk_edit_wastage 	= (($('#wastage_percent').val() == '')  ? 0 : $('#wastage_percent').val());



			let bulk_edit_gross_wt 	= (($('#be_gross_wt').val() == '')  ? 0 : $('#be_gross_wt').val());



			let bulk_edit_net_wt 	= (($('#be_net_wt').val() == '')  ? 0 : $('#be_net_wt').val());



			let bulk_edit_less_wt 	= (($('#be_less_wt').val() == '')  ? 0 : $('#be_less_wt').val());



			let bulk_edit_pcs 		= (($('#be_pcs').val() == '')  ? 0 : $('#be_pcs').val());



			let update_in_lot 		= $('#be_update_lot').is(":checked") ? 1 : 0;



			let bulk_edit_mrp		= (($('#be_mrp_value').val() == '')  ? 0 : $('#be_mrp_value').val());



			var edit_purity			= $('#be_purity').val();



			let bulk_edit_size		= $('#be_size').val();



			let attribute_type 		= $("#attribute_type").val();



			let charge_type 		= $("#charge_type").val();



			let blk_huid 			= $.trim($("#blk_huid").val());



			let blk_huid2 			= $.trim($("#blk_huid2").val());



			let blk_old_tag			= $.trim($("#blk_old_tag").val());



			let tag_edit_stone_details 		= $("#tag_edit_stone_details").val();



			let edit_caltype 		= $("#be_caltype").val();



			let stone_details =[];



			let attributes 			= [];



			var rate = $("#rate_per_gram").val() ==''?0:$("#rate_per_gram").val();



			var ratecaltype = $("#rate_calc_type").val();



			var purchase_touch = $("#purchase_touch").val() ==''?0:$("#purchase_touch").val();



			var karigar_calc_type = $("#karigar_calc_type").val() ;



			var purchase_mc_type= $("#pur_mc_type").val() ;



			var purchase_mc = $("#pur_mc_value").val() ==''?0:$("#pur_mc_value").val();



			var pur_wastage =  $("#purchase_wastage").val() ==''?0:$("#purchase_wastage").val();



			var purchase_cost =  $("#purchase_cost").val() ==''?0:$("#purchase_cost").val();



			var id_design = $("#bulkedit_des_update").val() == '' ? 0 : $("#bulkedit_des_update").val();



			var id_sub_design = $("#bulkedit_sub_des_update").val() == '' ? 0 : $("#bulkedit_sub_des_update").val();

			var blk_narration = $("#blk_narration").val() == '' ? '' : $('#blk_narration').val();

			if(attribute_type == 1) {



				let _attr_rows = $("#bulk_edit_attribute_detail tbody tr");



				$.each(_attr_rows, function (attrkey, attritem) {



					let attr_name = $(attritem).find('.bulk_tag_upd_attr_name').val();



					let attr_value = $(attritem).find('.bulk_tag_upd_attr_value').val();



					let has_attr_name = false;



					$.each(attributes, function (attrkey, attritem) {



						if(attr_name == attritem['attr_name'])  {



							has_attr_name = true;



						}



					});



					if (!has_attr_name) {



						var attrs = {



							"attr_name" :  attr_name,



							"attr_value" :  attr_value



						};



						attributes.push(attrs);



					}



				});



			}



			let huid_edit 		= JSON.parse($("#other_huid_details").val());



            let bulk_huid 		= [];



            console.log(huid_edit);



            if(huid_edit!=''){



            	var huid_update =[];



            	$.each(huid_edit,function(key, huid){



            		huid_update.push(huid)



            	});



            		bulk_huid.push(huid_update);



            }



			let blk_charges = [];



			if(charge_type == 1) {



				let _charge_rows = $("#bulk_edit_charge_detail tbody tr");



				$.each(_charge_rows, function (attrkey, attritem) {



					let charge_id = $(attritem).find('.bulk_tag_upd_charge_name').val();



					let charge_value = $(attritem).find('.bulk_tag_upd_charge_value').val();



					var chrgs = {



						"charge_id" :  charge_id,



						"charge_value" :  charge_value



					};



					blk_charges.push(chrgs);



				});



			}



			$("#tagging_list tbody tr").each(function(index, value) {



				if($(value).find("input[name='tag_id[]']:checked").is(":checked")) {



					if(bulk_edit_options == 14) {

						caculate_bulk_edit_purchase_sale_row($(value));

					}



					var total_price = 0;



					if(bulk_edit_options == 1) {



						var mc_type            =   bulk_edit_mc;



						var mc_value           =   bulk_edit_mc_value;



					} else {



						var mc_type            =   (($(value).find(".id_mc_type").val() == '')  ? 0 : $(value).find(".id_mc_type").val());



						var mc_value           =   (($(value).find(".tag_mc_value").val() == '')  ? 0 : $(value).find(".tag_mc_value").val());



					}



					if(bulk_edit_options == 2) {



						var tot_wastage            =   bulk_edit_wastage;



					} else {



						var tot_wastage            =   (($(value).find(".retail_max_wastage_percent").val() == '')  ? 0 : $(value).find(".retail_max_wastage_percent").val());



					}



					if(bulk_edit_options == 3) {

						var gross_wt            =  bulk_edit_gross_wt;

						var net_wt              =  bulk_edit_net_wt;

					}else if(bulk_edit_options == 14){

						var gross_wt            =  (($(value).find(".tag_gross_wt").val() == '')  ? 0 : $(value).find(".tag_gross_wt").val());

						var net_wt              =  (($(value).find(".tag_net_wt").val() == '')  ? 0 : $(value).find(".tag_net_wt").val());

					} else {

						var gross_wt            =  (($(value).find(".gross_wt").val() == '')  ? 0 : $(value).find(".gross_wt").val());

						var net_wt              =  (($(value).find(".net_wt").val() == '')  ? 0 : $(value).find(".net_wt").val());

					}



					if(bulk_edit_options == 4) {



						var no_of_piece         =  bulk_edit_pcs;



					} else {



						var no_of_piece         =  (($(value).find(".no_of_piece").val() == '')  ? 0 : $(value).find(".no_of_piece").val());



					}



					if(bulk_edit_options == 6) {



						var sell_rate           =  bulk_edit_mrp;



					} else {



						var sell_rate           =  $(value).find(".sell_rate").val();



					}







					var stone_price         =  $(value).find(".stone_price").val();



					var calculation_type    =  $(value).find(".calculation_based_on").val();



					var rate_per_grm        =  $(value).find(".metal_rate").val();



					var total_charges       = $(value).find(".charge_value").val();



					var metal_type    =  $(value).find(".metal_type").val();



					if(metal_type==1)

					{

						var rate_per_grm = $(value).find(".metal_rate").val();

					}else if(metal_type==2){

						var rate_per_grm = $(value).find(".silver_rate").val();

					}

					else if(metal_type==3){

						var rate_per_grm = $(value).find(".platinum_rate").val();

					}



					if(calculation_type == 0)	{



						var wast_wgt      = parseFloat(parseFloat(gross_wt) * parseFloat(tot_wastage/100)).toFixed(3);



						if(mc_type != 3){



							var making_charge       =  parseFloat(mc_type == 2 ? parseFloat(mc_value * gross_wt ) : parseFloat(mc_value * 1));



							// Metal Rate + Stone + OM + Wastage + MC



							rate_with_mc = parseFloat(parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(making_charge)+parseFloat(stone_price));



						}else{



							var making_charge  = parseFloat((parseFloat(gross_wt) * parseFloat(rate_per_grm)) * parseFloat(mc_value/100)).toFixed(3);



							rate_with_mc       =  parseFloat(parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(making_charge)+parseFloat(stone_price));



						}



					}



					else if(calculation_type == 1)	{



						var wast_wgt      = parseFloat(parseFloat(net_wt) * parseFloat(tot_wastage/100)).toFixed(3);



						if(mc_type != 3){



							var making_charge       =  parseFloat(mc_type == 2 ? parseFloat(mc_value * net_wt ) : parseFloat(mc_value * 1));



							// Metal Rate + Stone + OM + Wastage + MC



							rate_with_mc = parseFloat(parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(making_charge)+parseFloat(stone_price));



						}else{



							var making_charge  = parseFloat((parseFloat(net_wt) * parseFloat(rate_per_grm)) * parseFloat(mc_value/100)).toFixed(3);



							rate_with_mc       =  parseFloat(parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(making_charge)+parseFloat(stone_price));



						}



					}



					else if(calculation_type == 2)	{



						var wast_wgt      = parseFloat(parseFloat(net_wt) * parseFloat(tot_wastage/100)).toFixed(3);



						if(mc_type != 3){



							var making_charge       =  parseFloat(mc_type == 2 ? parseFloat(mc_value * gross_wt ) : parseFloat(mc_value * 1));



							// Metal Rate + Stone + OM + Wastage + MC



							rate_with_mc = parseFloat((parseFloat(rate_per_grm) * (parseFloat(wast_wgt) + parseFloat(net_wt)))+ parseFloat(making_charge))+parseFloat(stone_price);



						}else{



							var making_charge  = parseFloat((parseFloat(gross_wt) * parseFloat(rate_per_grm)) * parseFloat(mc_value/100)).toFixed(3);



							rate_with_mc = parseFloat((parseFloat(rate_per_grm) * (parseFloat(wast_wgt) + parseFloat(net_wt)))+ parseFloat(making_charge))+parseFloat(stone_price);



						}



					}



					else if(calculation_type == 3)	{



						var sell_rate  = (isNaN(sell_rate) || sell_rate == '')  ? 0 : sell_rate;



						var adjusted_item_rate  = 0;



						caculated_item_rate = parseFloat(sell_rate);



						$('.caculated_item_rate').val(caculated_item_rate);



						rate_with_mc = parseFloat(parseFloat(adjusted_item_rate) > 0 ? parseFloat(adjusted_item_rate) : caculated_item_rate );



					}



					else if(calculation_type == 4)	{



						var sell_rate  = (isNaN(sell_rate) || sell_rate == '')  ? 0 : sell_rate;



						var adjusted_item_rate  = 0;



						caculated_item_rate = parseFloat((parseFloat(sell_rate)*parseFloat(net_wt)));



						$('.caculated_item_rate').val(caculated_item_rate);



						rate_with_mc = parseFloat(parseFloat(adjusted_item_rate) > 0 ? parseFloat(adjusted_item_rate) : caculated_item_rate );



					}



					console.log("total_charge",total_charges);



					console.log("rate_with_mc",rate_with_mc);



					total_price = parseFloat(parseFloat(rate_with_mc) + parseFloat(total_charges)).toFixed(2);



					if(bulk_edit_options == 14) {

						curRow=$(value);

						if(validate_purchase_cost(curRow)){

						transData ={

							'gross_wt' 				 : curRow.find('.tag_gross_wt').val(),

							'net_wt'				 : curRow.find('.tag_net_wt').val(),

							'less_wt' 				 : curRow.find('.tag_less_wt').val(),

							'lot_calc_type' 		 : curRow.find('.lot_calc_type').val(),

							'lot_wastage_percentage' : curRow.find('.lot_wastage_percentage').val(),

							'lot_mc_type'       	 : curRow.find('.lot_mc_type').val(),

							'lot_making_charge' 	 : curRow.find('.lot_making_charge').val(),

							'lot_purchase_touch'	 : curRow.find('.lot_purchase_touch').val(),

							'lot_rate' 				 : curRow.find('.lot_rate').val(),

							'lot_rate_calc_type' 	 : curRow.find('.lot_rate_calc_type').val(),

							'tag_purchase_cost'		 : curRow.find('.tag_purchase_cost').val(),

							'tag_purchase_tax'		 : curRow.find('.tag_purchase_tax').val(),

							'tag_purchase_taxable'	 : curRow.find('.tag_purchase_taxable').val(),

							'stone_details'		     : JSON.parse(curRow.find('.stone_details').val()),

							'tag_id'		     	 : curRow.find('.tagid').val(),

							'sales_value'		     : total_price,

						}

					  }

					}else{

						transData = {

							'tag_id'   					:  	$(value).find(".tag_id").val(),

							'sales_value'   			:  	total_price

						}

					}



					selected.push(transData);



				}



			});



			req_data =  {



							"id_branch"					:   id_branch,



							"tags" 						: 	selected,



							"bulk_edit_options"			:	bulk_edit_options,



							'retail_max_wastage_percent': 	bulk_edit_wastage,



							'id_mc_type'    			:  	bulk_edit_mc,



							'tag_mc_value'  			:  	bulk_edit_mc_value,



							'piece'						: 	bulk_edit_pcs,



							'gross_wt'					: 	bulk_edit_gross_wt,



							'net_wt'					: 	bulk_edit_net_wt,



							'less_wt'					: 	bulk_edit_less_wt,



							'update_in_lot'				:	update_in_lot,



							'purity'					: 	edit_purity,



							'mrp_price'					: 	bulk_edit_mrp,



							'size'						:	bulk_edit_size,



							'attribute_type'  			:  	attribute_type,



							'attributes' 				: 	attributes,



							'charge_type' 				: 	charge_type,



							'blk_charges' 				: 	blk_charges,



							'blk_huid'					: 	blk_huid,



							'blk_huid2'					: 	blk_huid2,



							'blk_old_tag'				: 	blk_old_tag,



							'stone_details' 	        : 	tag_edit_stone_details,



							'huid' 						: 	huid_edit,



							'calc_type'                 :   edit_caltype,



							'lot_wastage_percentage'    :   pur_wastage,



							'lot_making_charge'         :   purchase_mc,



							'lot_mc_type'               :   purchase_mc_type,



							'lot_calc_type'             :   karigar_calc_type,



							'lot_rate'                  :   rate,



							'lot_rate_calc_type'        :   ratecaltype,



							'purchase_touch'            :   purchase_touch,



							'purchase_cost'             :   purchase_cost,



							'to_branch'		        	:   to_branch,



							'id_design'					:	id_design,



							'id_sub_design'				:	id_sub_design,

							'narration'					: 	blk_narration

						}



			update_tagging_data(req_data);



		} else {



			alert('Please Select Tag');



			$("#otp_submit").prop('disabled',false);



		}



	} else {



		$("#otp_submit").prop('disabled',false);



		$("div.overlay").css("display", "none");



	}



});



function update_tagging_data(req_data)



{



	console.log(req_data);



	my_Date = new Date();



	var design_id = $('#design_id').val();



	var tag_otp = $('#tag_otp').val();



	$.ajax({



			 url:base_url+ "index.php/admin_ret_tagging/update_tagging_data?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),



			 //data:  {'req_data':req_data,'tag_otp':tag_otp},



			 data:  {'req_data':req_data,'tag_img':$('#tag_img').attr("data-img"),'tag_otp':tag_otp},



			 type:"POST",



			 dataType: "json",



			 async:false,



			 	  success:function(data){



			 	  	if(data.status)

			 	  	{

			 	  			$.toaster({ priority : 'success', title : 'Success!', message : ''+"</br>"+data.msg});

			 	  			$('#otp_modal').modal('hide');

			 	  			$('#otp_submit').prop('disabled', false);

			 	  			$("div.overlay").css("display", "none");

			 	  			// Refresh the DataTable with current filters (no page reload)
			 	  			$('#get_tag_details').trigger('click');

			 	  	}

			 	  	else

			 	  	{

			 	  		$.toaster({ priority : 'danger', title : 'Error!', message : ''+"</br>"+data.msg});

			 	  		$('#bulk_edit').prop('disabled',false);

			 	  		$('#otp_submit').prop('disabled', false);

			 	  		$("div.overlay").css("display", "none");

			 	  		$('#tag_otp').val('');

			 	  	}



				  },



				  error:function(error)



				  {



					 $("div.overlay").css("display", "none");



				  }



		  });



}



/*



$(".add_tag_edit_lwt").on('click',function(){



   $('#cus_stoneModal').modal('show');



    if($('#cus_stoneModal tbody >tr').length == 0)



    {



         create_new_empty_stone_item();



    }



});



$('#update_tag_edit_stone_details').on('click',function(){



   if(validateStoneCusItemDetailRow())



    {



    	var stone_details=[];



    	var stone_price=0;



    	var certification_price=0;



    	var tag_less_wgt = 0;



    	var gross_wt = $('#be_gross_wt').val();



    	modalStoneDetail = []; // Reset Old Value of stone modal



    	$('#cus_stoneModal .modal-body #estimation_stone_cus_item_details> tbody  > tr').each(function(index, tr) {



    		stone_price+=parseFloat($(this).find('.stone_price').val());



    		if($(this).find('.show_in_lwt :selected').val() == 1){



    		    tag_less_wgt+=parseFloat($(this).find('.stone_wt').val());



    		}



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



    		            'stone_uom_name'      : $(this).find('.stone_uom_id :selected').text(),



    		            'stone_name'        : $(this).find('.stone_id :selected').text()



    		});



    	});



    	if(gross_wt  < tag_less_wgt)



    	{



    	    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Entered Weight is More Than The Gross Wt.."});



    	}else



    	{



    	    $('#be_net_wt').val(parseFloat(parseFloat(gross_wt)-parseFloat(tag_less_wgt)).toFixed(3));



    	    modalStoneDetail = stone_details;



            console.log(modalStoneDetail);



        	$("#be_less_wt").val(tag_less_wgt);



        	$("#tag_edit_stone_details").val(JSON.stringify(stone_details));



            $('#cus_stoneModal').modal('hide');



    	}



    }



    else



    {



    	alert('Please Fill The Required Details');



    }



});



*/



$('#be_gross_wt').on('keyup',function(){



    var gross_wt = $('#be_gross_wt').val();



    var less_wt = $('#be_less_wt').val();



    $('#be_net_wt').val(parseFloat(parseFloat(gross_wt)-parseFloat(less_wt)).toFixed(3));



});



function get_metal_rates_by_branch(id_branch)



{



	my_Date = new Date();



	$.ajax({



			 url:base_url+ "index.php/admin_ret_tagging/get_metal_rates_by_branch?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),



			 data:  {'id_branch':id_branch},



			 type:"POST",



			 dataType: "json",



			 async:false,



			 	  success:function(data){



			 	    metal_rates = data;



			 	  	$('.tag-per-grm-sale-value').html(data.goldrate_22ct);



			 	  	$('#metal_rate').val(data.goldrate_22ct);



			 	  	$('#silverrate_1gm').val(data.silverrate_1gm);



			 	  	$('#platinum_1g').val(data.platinum_1g);



			 	  	setTimeout(function(){



			 	  	  //  calculateSaleValue();



			 	  	},1000);



				  },



				  error:function(error)



				  {



					 $("div.overlay").css("display", "none");



				  }



		  });



}



$('#otp_submit').on('click',function(){



    if($("input[name='tag_id[]']:checked").val())



    {



		let bulk_edit_validate = validate_bulk_edit();



		if(bulk_edit_validate) {



            $(".overlay").css('display','block');



            $('#otp_submit').prop('disabled',true);



            	my_Date = new Date();



            	$.ajax({



            			 url:base_url+ "index.php/admin_ret_tagging/admin_approval?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),



            			 type:"POST",



            			 dataType: "json",



            			 async:false,



            			 	  success:function(data){



            			 	  	/*if(data.status)



            			 	  	{



            			 	  	    if(data.sms_req==1)



            			 	  	    {



            			 	  	        $('#otp_modal').modal('show');



            			 	  	    }else{



            			 	  	        $("#bulk_edit").trigger('click');



            			 	  	    }



            			 	  	}



            			 	  	else



            			 	  	{



            			 	  		$('#tag_otp').prop('disabled',true);



            			 	  	    alert(data.msg);



            			 	  	}*/



            			 	  	$("#bulk_edit").trigger('click');



            				  },



            				  error:function(error)



            				  {



            					 $(".overlay").css('display',"none");



            				  }



            		  });



		} else {



			$("#otp_submit").prop('disabled',false);



			$("div.overlay").css("display", "none");



		}



    }else{



        alert('Please Select Any One Tag');



    }



});



$('#close_modal').on('click',function(){



    $('#otp_modal').modal('toggle');



    $('#otp_submit').prop('disabled',false);



});



$('#tag_otp').on('keyup',function(){



	$("#otp_alert").html('');



	if(this.value.length>6)



	{



		$("#otp_alert").html('<p style="color:red">Please Enter Valid Number</p>');



		$('#bulk_edit').prop('disabled',true);



	}



	else



	{



		$('#bulk_edit').prop('disabled',false);



	}



});



$('#resendotp').on('click',function(){



$(".overlay").css('display','block');



$('#otp_submit').prop('disabled',true);



	my_Date = new Date();



	$.ajax({



			 url:base_url+ "index.php/admin_ret_tagging/resendotp?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),



			 type:"POST",



			 dataType: "json",



			 async:false,



			 	  success:function(data){



			 	    alert(data.msg);



				  },



				  error:function(error)



				  {



					 $(".overlay").css('display',"none");



				  }



		  });



});



$(document).on("change","#bulk_edit_options",function() {



	$('.editable_block').css("display","none");



	$('.purchase_filter').css("display","none");



	// Only toggle edit field visibility — do NOT clear the DataTable
	// so that loaded tags persist when switching between MC/VA/Charges etc.
	BulkEditDetails();



    let bulk_edit_options = $(this).val();



    if(bulk_edit_options == 3 || bulk_edit_options == 4) {



    	$(".tag_update_text").html("*Only current day HO items can be updated");



    } else if(bulk_edit_options == 5 || bulk_edit_options == 8 || bulk_edit_options == 16) {



    	$(".tag_update_text").html("*Product or Tag Code should be given");



    } else if(bulk_edit_options == 6) {



    	$(".tag_update_text").html("*Only MRP products can be updated");



    }else if(bulk_edit_options == 14) {



    	$('.purchase_filter').css("display","block");



    }

	 else {



    	$(".tag_update_text").html("");



    }



});



// DataTable no longer auto-clears when filter dropdowns change.
// User must click Apply Filter to refresh the table.




//bulk edit



$(document).on('keyup',".tagstone_amt",function(){



    calculateSaleValue();



});



//Employee Filter



	function get_employee(id_branch)



	{



		$('#emp_select option').remove();



		my_Date = new Date();



		$.ajax({



		url:base_url+ "index.php/admin_ret_estimation/get_employee?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),



        data: {'id_branch':id_branch},



        type:"POST",



        dataType:"JSON",



        success:function(data)



        {



           var id_employee=$('#id_employee').val();



           emp_details=data;



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



	$('#emp_select').on('change',function(){



		if(this.value!='')



		{



			$('#id_employee').val(this.value);



			if(ctrl_page[1]=='get_tag_detail_list')



			{



			    lot_tag_detail();



			}



			else if(ctrl_page[1]=='tagging' && ctrl_page[2]=='add')



			{



				get_lot_split_products(this.value);



			}



		}



		else



		{



			$('#id_employee').val('');



		}



	});



//Employee Filter



//Branch filter



$("#branch_select,#current_branch").on('change',function(){



	if(this.id == "branch_select"){



	    var id_branch = this.value;



		if(this.value!='')



		{



			$('#id_branch').val(id_branch);



			if(ctrl_page[1]=='tagging' && ctrl_page[2]=='add')



            {



                $('#current_branch').select2("val",id_branch);



            }



			get_ActiveSections(id_branch);



		}



		else



		{



			$('#id_branch').val('');



		}



	}else{



		if(id_branch!='')



		{



			//$('#current_branch_id').val(this.value);



			//$('#current_branch').select2("val",id_branch);



		}



		else



		{



			//$('#current_branch_id').val('');



		}



	}



});



//Branch filter



//Lot Products



function get_lot_products(searchTxt)



{



	prod_details = [];



	var tag_lot_id=$('#tag_lot_id').val();



	$('#tag_lt_prod').html('');



	$('#tag_lt_design').html('');



	my_Date = new Date();



		$.ajax({



		url:base_url+ "index.php/admin_ret_tagging/get_lot_products?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),



        data: {'lot_no':tag_lot_id,'searchTxt':searchTxt},



        type:"POST",



        dataType:"JSON",



        success:function(data)



        {



       		if(data[0].order_no=='')



       		{



       				$(".overlay").css("display", "block");



       				$('#tag_lt_prod').prop('disabled',false);



					$('#tag_lt_design').prop('disabled',false);



					var tag_lt_prodId=$('#tag_lt_prodId').val();



					prod_details = data;



					var gross_wt=0;



					var gorss_pcs=0;



					var precious_st_wt=0;



					var precious_st_pcs=0;



					var semi_precious_st_wt=0;



					var semi_precious_st_pcs=0;



					var normal_st_wt=0;



					var normal_st_pcs=0;

					var product_pcs =0;

					$.each(data, function (key, item) {



					    gross_wt +=parseFloat(item.gross_wt);



					    gorss_pcs += parseFloat(item.no_of_piece);



					    precious_st_wt +=parseFloat(item.precious_st_wt);



					    precious_st_pcs +=parseFloat(item.precious_st_pcs);



					    semi_precious_st_wt +=parseFloat(item.semi_precious_st_wt);



					    semi_precious_st_pcs +=parseFloat(item.semi_precious_st_pcs);



					    normal_st_wt +=parseFloat(item.normal_st_wt);



					    normal_st_pcs +=parseFloat(item.normal_st_pcs);



					    product_division = item.product_division;

						product_pcs = item.product_pcs;

						$('#tag_order_no').val(item.order_no);



					$("#tag_lt_prod").append(



					$("<option></option>")



					.attr("value", item.lot_product)



					.attr("data-rate", item.rate)



					.attr("data-touch", item.purchase_touch)



					.attr("data-rate_calc_type", item.rate_calc_type)



					.attr("data-calc_type", item.calc_type)



					.attr("data-wastage_percentage", item.wastage_percentage)



					.attr("data-making_charge", item.making_charge)



					.attr("data-mc_type", item.mc_type)



					.attr("data-id_lot_inward_detail", item.id_lot_inward_detail)



					.text(item.product_name)



					);



					});



					$("#tag_lt_prod").select2({



					placeholder: "Select Product",



					allowClear: true



					});



					$("#tag_lt_prod").select2("val",(tag_lt_prodId!='' && tag_lt_prodId>0?tag_lt_prodId:''));



					$("#tag_product_division").val((product_division!='' && product_division>0?product_division:''));



					$(".overlay").css("display", "none");



					$('#lot_bal_wt').val(gross_wt);



					$('#lot_bal_pcs').val(gorss_pcs);



					$('#lot_bal_prec_wt').val(precious_st_wt);



					$('#lot_bal_prec_pcs').val(precious_st_pcs);



					$('#lot_bal_semi_pre_wt').val(semi_precious_st_wt);



					$('#lot_bal_semi_pre_pcs').val(semi_precious_st_pcs);



					$('#lot_bal_normal_wt').val(normal_st_wt);



					$('#lot_bal_normal_pcs').val(normal_st_pcs);


					$('#product_pcs').val(product_pcs);




		}



			else



			{



				$(".overlay").css("display", "block");



				my_Date = new Date();



				$.ajax({



				url: base_url+'index.php/admin_ret_tagging/get_order_details?nocache=' + my_Date.getUTCSeconds(),



				dataType: "json",



				method: "POST",



				data: {'lot_no': tag_lot_id},



				success: function (data) {



				if(data)



				{



				$('#tag_lt_prod').prop('disabled',true);



				$('#tag_lt_design').prop('disabled',true);



				//$('#add_more_tag').prop('disabled',true);



				var row='';



				var gross_wt=0;



				var lot_pcs=0;



				var id_orderdetails='';



				row_exist=false;



				var total_row=$('#lt_item_list tbody > tr').length;



					$.each(data,function(key,item){



						$('#lt_item_list> tbody  > tr').each(function(index, tr) {



								if($(this).find('.id_orderdetails').val() == item.id_orderdetails){



									row_exist = true;



									alert('Tag Already Exists');



									return false;



								}



								});



						});



						if(!row_exist)



						{



						$.each(data,function(key,item){



							gross_wt+=parseFloat(item.gross_wt);



							lot_pcs+=parseFloat(item.no_of_piece);



								row += '<tr id='+item.id_lot_inward_detail+' class='+total_row+'>'



								+'<td width="5%">'+item.lot_no+'<input type="hidden" name="lt_item[lot_no][]" value="'+item.lot_no+'" class="lot_no" /><input type="hidden" name="lt_item[id_lot_inward_detail][]" id="id_lot_inward_detail" value="'+item.id_lot_inward_detail+'" class="id_lot_inward_detail"><input type="hidden" name="lt_item[sales_mode][]" id="sales_mode" value="'+item.sales_mode+'" class="sales_mode"><input type="hidden" name="lt_item[id_orderdetails][]" class="id_orderdetails" value="'+item.id_orderdetails+'"><input type="hidden" class="stn_amt" value="'+item.stn_amt+'"></td>'



								+'<td width="10%">'+item.product_name+'<input type="hidden" name="lt_item[lot_product][]" value="'+item.id_product+'" class="lot_product" /><input type="hidden" name="lt_item[product_short_code][]" value="'+item.product_short_code+'" class="product_short_code" /></td>'



								+'<td width="10%">'+item.design_name+'<input type="hidden" name="lt_item[lot_id_design][]" value="'+item.design_no+'" class="lot_id_design" /></td>'



								+'<td width="10%">'+(item.design_for==1 ? 'Men' :(item.design_for==2 ? 'Female':'Unisex'))+'<input type="hidden" name="lt_item[design_for][]" value="'+item.design_for+'" class="design_for" /></td>'



								+'<td width="10%"><select class="calculation_based_on" name="lt_item[calculation_based_on][]"><option value="0" '+(item.calculation_based_on == 0 ? "selected":"")+' '+(item.calculation_based_on >= 3 ? "disabled":"")+'>Mc & Wast On Gross</option><option value="1" '+(item.calculation_based_on == 1 ? "selected":"")+' '+(item.calculation_based_on >= 3 ? "disabled":"")+'>Mc & Wast On Net</option><option value="2" '+(item.calculation_based_on == 2 ? "selected":"")+' '+(item.calculation_based_on >= 3 ? "disabled":"")+'>Mc on Gross,Wast On Net</option><option value="3" '+(item.calculation_based_on == 3 ? "selected":"")+' '+(item.calculation_based_on == 4 ? "disabled":"")+'>Fixed Rate</option><option value="4" '+(item.calculation_based_on == 4 ? "selected":"")+' '+(item.calculation_based_on == 3 ? "disabled":"")+'>Fixed Rate based on Weight</option></select></td>'



								+'<td width="5%"><input type="number" step="any" name="lt_item[no_of_piece][]"   class="no_of_piece" value="1" style="width:80px;" read/><span class="blc_pcs"></span><input type="hidden" disabled class="act_blc_pcs" value="'+item.no_of_piece+'" style="width:80px;" readonly></td>'



								+'<td width="10%"><input type="number" step="any" name="lt_item[gross_wt][]"   class="gross_wt" style="width:80px;" value="'+item.gross_wt+'"/></span><input type="hidden" class="act_gross_blc" value="'+item.gross_wt+'"><input type="hidden" class="gross_wt_blc" value="'+item.gross_wt+'"></td>'



								+'<td width="10%"><input type="number" step="any" name="lt_item[less_wt][]"  class="less_wt" style="width:80px;" readonly/></td>'



								+'<td width="10%"><input type="number" step="any" name="lt_item[net_wt][]"  class="net_wt" value="'+item.net_wt+'" style="width:80px;" readonly/></td>'



								+'<td width="5%"><input type="text" name="lt_item[wastage_percentage][]" value="'+item.wastage_percentage+'" class="order_wastage_percentage" style="width:80px;"/></td>'



								+'<td><select class="id_mc_type" value='+item.mc_type+'><option value="1">Per Gram</option><option value="2" selected>Per Piece</option></select><input type="hidden" value="'+item.mc_type+'" name="lt_item[id_mc_type][]" class="id_mc_type"></td>'



								+'<td width="10%"><input type="number" step="any" name="lt_item[making_charge][]"  class="order_making_charge" value="'+item.making_charge+'" style="width:80px;"/></td>'



								+'<td width="10%"><input type="number" step="any" name="lt_item[sell_rate][]"  class="order_sell_rate" value="'+item.sell_rate+'" style="width:80px;" '+(item.calculation_based_on < 3 ? "readonly":"")+'/>'+(item.calculation_based_on == 3 ? "per piece":(item.calculation_based_on == 4 ? "per gram":""))+'</td>'



								+'<td width="10%"><input type="number" step="any" name="lt_item[size][]"  class="order_size" style="width:70px;"/></td>'



								// +'<td><a href="#" onClick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));" class="btn btn-default btn-sm"><i class="fa fa-plus"></i></a></td>'



								+'<td><a href="#" onClick="update_image_upload($(this).closest(\'tr\'));" class="btn btn-default btn-sm"><i class="fa fa-plus"></i></a></td>'



								+'<td width="5%"><input type="number" step="any" name="lt_item[caculated_item_rate][]"  class="order_caculated_item_rate" value="'+item.caculated_item_rate+'" style="width:70px;" readonly/></td>'



								+'<td width="5%"><input type="number" step="any" name="lt_item[adjusted_item_rate][]"  class="order_adjusted_item_rate" value="'+item.adjusted_item_rate+'" style="width:80px;" '+(item.calculation_based_on < 3 ? "readonly":"")+'/></td>'



								+'<td width="10%"><input type="number" name="lt_item[sale_value][]"  class="sale_value" readonly style="width:80px;" /><input type="hidden" class="tax_group_id" value="'+item.tax_group_id+'"><input type="hidden" class="stone_details" name="lt_item[stone_details][]"><input type="hidden" class="stone_price" name="lt_item[stone_price][]"><input type="hidden" class="price" name="lt_item[price][]"><input type="hidden" class="tag_img" name="lt_item[tag_img][]"><input type="hidden" class="tag_img_copy" name="lt_item[tag_img_copy][]" value="0"><input type="hidden" class="tag_img_default" name="lt_item[tag_img_default][]" value=""><input type="hidden" class="normal_st_certif" value="'+item.normal_st_certif+'"><input type="hidden" class="semiprecious_st_certif" value="'+item.semiprecious_st_certif+'"><input type="hidden" class="precious_st_certif" value="'+item.precious_st_certif+'"></td>'



								+'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'



								+'</tr>';



						});



						}



						console.log(lot_pcs);



						console.log(gross_wt);



					$('#lot_bal_wt').val(gross_wt);



					$('#lot_bal_pcs').val(lot_pcs);



					$('#lt_item_list tbody').append(row);



					calculateOrderTagSaleValue();



					$(".overlay").css("display", "none");



				}



				else



				{



				$('#errorMsg').css("display","block");



				$('#errorMsg').html(data.message);



				}



				}



				});



			}



        },



        error:function(error)



        {



        }



    	});



}



$(document).on('keyup', '.gross_wt,.less_wt,.order_making_charge,.order_wastage_percentage,.no_of_piece', function(e){



		var row = $(this).closest('tr');



		var gross_wt = parseFloat((isNaN(row.find('.gross_wt').val()) || row.find('.gross_wt').val() == '')  ? 0 : row.find('.gross_wt').val()).toFixed(3);



		var less_wt  = (isNaN(row.find('.less_wt').val()) || row.find('.less_wt').val() == '')  ? 0 : row.find('.less_wt').val();



		var net_wt = parseFloat(parseFloat(gross_wt) - parseFloat(less_wt)).toFixed(3);



		row.find('.net_wt').val(net_wt);



		calculateOrderTagSaleValue();



});



$(document).on('change','.calculation_based_on',function(){



    var row = $(this).closest('tr');



    row.find('.calc_type').val(this.value);



});



$(document).on('change','.mc_type',function(){



    var row = $(this).closest('tr');



    row.find('.making_type').val(this.value);



});



$('#tag_lt_prod').on('change',function(){



		if(this.value!='')



		{



            var proId = this.value;



			$('#tag_lt_prodId').val(this.value);



			$('#tag_lt_designId').val('');



			$('#tag_size').val('');







			var tag_lot_id=$('#tag_lot_id').val();



			var tag_lt_prodId=$('#tag_lt_prodId').val();



			if(tag_lot_id!='' && tag_lt_prodId!='')



			{



			      get_lot_inwards_detail(tag_lot_id,tag_lt_prodId,'')



			}



			if(ctrl_page[2]=='add')



			{



			    get_ActiveSize();



			}



			$.each(prod_details,function(key,item){



			    if(item.lot_product == proId){



				    $('.issuspensestock').val(item.is_suspense_stock);



					$("#tag_cat_type").val(item.cat_type);



					$('#tag_product_stone_type').val(item.stone_type);



					if(item.stone_type != 0) {



						$(".tag_calc").css("display","none");



						$(".stone_calc").css("display","block");



						$("#gwt_uom_id").css("display","inline-block");



						$("#tag_gwt").css("width","74%");



						$('.quality').show();



					} else {



						$(".tag_calc").css("display","block");



						$(".stone_calc").css("display","none");



						$("#gwt_uom_id").css("display","none");



						$("#tag_gwt").css("width","100%");



						$('.quality').hide();



					}



			    }



			});



			get_Activedesign(this.value);



		}



		else



		{



			$('#tag_lt_prodId').val('');



			$('.issuspensestock').val(0);



		}



	});



$('#tag_lt_prod').on('select2:select', function (e) {



	get_productCharges();



});



//Lot Products



//Lot designs



function get_lot_designs(searchTxt)



{



	$('#tag_lt_design').html('');



	var lot_no=$('#tag_lot_id').val();



	var lot_product=$('#tag_lt_prodId').val();



	my_Date = new Date();



		$.ajax({



		url:base_url+ "index.php/admin_ret_tagging/get_lot_designs?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),



        data: {'lot_no':lot_no,'lot_product':lot_product,'searchTxt':searchTxt},



        type:"POST",



        dataType:"JSON",



        success:function(data)



        {



			/*$( "#tag_lt_design" ).autocomplete(



			{



				source: data,



				select: function(e, i)



				{



					e.preventDefault();



					$("#tag_lt_design" ).val(i.item.label);



					$("#tag_lt_designId" ).val(i.item.value);



				},



				response: function(e, i) {



		            // ui.content is the array that's about to be sent to the response callback.



		            console.log(i);



		            if (i.content.length === 0) {



		               $("#prodAlert").html('<p style="color:red">Enter a valid Design</p>');



		               //$('#lt_product').val('');



		            }else{



						$("#prodAlert").html('');



					}



		        },



				 minLength: 0,



			});*/



			var tag_lt_designId=$('#tag_lt_designId').val();



			$.each(data, function (key, item) {



			$("#tag_lt_design").append(



			$("<option></option>")



			.attr("value", item.lot_id_design)



			.text(item.design_name)



			);



			});



			$("#tag_lt_design").select2({



			placeholder: "Select Design",



			allowClear: true



			});



			$("#tag_lt_design").select2("val",(tag_lt_designId!='' && tag_lt_designId>0?tag_lt_designId:''));



        },



        error:function(error)



        {



        }



    	})



}



$('#tag_lt_design').on('change',function(){



		if(this.value!='')



		{



			$('#tag_lt_designId').val(this.value);



			$('#designAlert').html('');



		}



		else



		{



			$('#tag_lt_designId').val('');



		}



	});



//Lot designs



$('#add_more_tag').on('click',function(){



	var tag_lot_id=$('#tag_lot_id').val();



	var tag_lt_prodId=$('#tag_lt_prodId').val();



	//var tag_lt_designId=$('#tag_lt_designId').val();



	if(tag_lt_prodId=='')



	{



		$('#productAlert').html('Please Select Product');



	}else{



		$('#productAlert').html('');



	}



/*	if(tag_lt_designId=='')



	{



		$('#designAlert').html('Please Select Design');



	}else{



		$('#designAlert').html('');



	}*/



	if(tag_lot_id!='' && tag_lt_prodId!='')



	{



	    if(validateTagDetailRow())



	    {



	        	get_lot_inwards_detail(tag_lot_id,tag_lt_prodId,'')



	    }



	}



});



$('#tag_submit').on('click',function(){



    $('#tag_submit').prop('disabled', true);



    if(validateTagDetailRow())



    {



        $('#tag_form').submit();



    }else{



       return false;



       $('#tag_submit').prop('disabled', false);



    }



});



function validateTagDetailRow(){



	var row_validate = true;



	$('#lt_item_list > tbody  > tr').each(function(index, tr) {



	    if($(this).find('.sales_mode').val()==1)



	    {



	        if($(this).find('.no_of_piece').val() == "" && $(this).find('.lot_id_design').val() == "")



	        {



	            row_validate = false;



	            alert('Please Fill Required Fields');



	             $('#tag_submit').prop('disabled', false);



	        }



	    }



	    else if($(this).find('.sales_mode').val()!=1)



	    {



    	    if($(this).find('.gross_wt').val() == "" || $(this).find('.no_of_piece').val() == "" || $(this).find('.lot_id_design').val() == ""){



    			row_validate = false;



    			alert('Please Fill Required Fields');



    			 $('#tag_submit').prop('disabled', false);



    		}



	    }



	});



	return row_validate;



}



$(document).on('keyup',	".cus_design", function(e){



		//var row = $(this).parent().parent();



		var row = $(this).closest('tr');



		var lot_product = row.find(".lot_product").val();



		var design = row.find(".cus_design").val();



		getSearchCusDesign(lot_product,design, row);



	});



function getSearchCusDesign(lot_product,searchTxt, curRow){



	my_Date = new Date();



	$.ajax({



        url: base_url+'index.php/admin_ret_estimation/getProductDesignBySearch/?nocache=' + my_Date.getUTCSeconds(),



        dataType: "json",



        method: "POST",



        data: { 'searchTxt': searchTxt, 'ProCode' : lot_product},



        success: function (data) {



			$( ".cus_design" ).autocomplete(



			{



				source: data,



				select: function(e, i)



				{



					e.preventDefault();



					curRow.find('.cus_design').val(i.item.label);



					curRow.find('.lot_id_design').val(i.item.value);



				},



				change: function (event, ui) {



					if (ui.item === null) {



					}else{



					}



			    },



				response: function(e, i) {



		            // ui.content is the array that's about to be sent to the response callback.



		            if(searchTxt != ""){



						if (i.content.length !== 0) {



						   //console.log("content : ", i.content);



						}



					}else{



						curRow.find('.cus_design').val("");



						curRow.find('.lot_id_design').val("");



					}



		        },



				 minLength: 1,



			});



        }



     });



}



function get_lot_inwards_detail(lot_no,lot_product,lot_id_design)



{



	//$(".overlay").css("display", "block");



	var id_emp = $('#emp_select').val();



	my_Date = new Date();



		$.ajax({



		url:base_url+ "index.php/admin_ret_tagging/get_lot_inward_details?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),



        data: {'lot_no':lot_no,'lot_product':lot_product,'lot_id_design':lot_id_design,'is_split':$('#is_lot_split').val(),'id_employee':id_emp,'id_lot_inward_detail':$('#tag_lt_prod option:selected').attr('data-id_lot_inward_detail')},



        type:"POST",



		async : false,



        dataType:"JSON",



        success:function(data)



        {



        	if(data.lot_inward_detail.length>0)



        	{



					$('#lt_tax_group').html(data.tax_percentage.tax_name);



					$('#tax_percentage').val(data.tax_percentage.tax_percentage);



					$('#tgi_calculation').val(data.tax_percentage.tgi_calculation);



        		var row = "";



        		var i=1;



        		var allow_row_create=false;



        		lot_inward_detail=data.lot_inward_detail;



        		checking_lot_availability();



        		$.each(data.lot_inward_detail, function (key, item) {



        		    $('#id_metal').val(item.id_metal);



					// console.log('PIECES : '+item.pieces);



					console.log('ORDER NO : '+item.order_no);





					$('#tag_pcs').val(item.pieces);



					$('#tag_order_no').val(item.order_no);



        		    $('#tag_id_lot_inward_detail').val(item.id_lot_inward_detail);



        		    $('#calculation_based_on').val(item.calculation_based_on);



        		    $('#tag_calculation_based_on').val(item.calculation_based_on);



        		    $('#tag_design_for').val(item.design_for);



        		    $('#tag_lot_no').val(item.lot_no);



        		    $('#tax_group_id').val(item.tax_group_id);



        		    $('#tag_tax_type').val(item.tax_type);



        		    $('#tag_sales_mode').val(item.sales_mode);



        		    $('#id_purity').val(item.id_purity);



        		    $('#tag_product_short_code').val(item.product_short_code);



        		    $('#has_size').val(item.has_size);



					$('#id_design').val(item.lot_id_design);



					$('#id_sub_design').val(item.id_sub_design);



					$('#tag_sell_rate').val(item.sell_rate);



					$('#lot_pur_stone_details').val(JSON.stringify(item.stone_details));



        		    if($("#tag_calculation_based_on").val() == 3 || $("#tag_calculation_based_on").val() == 4 ){



						$('#tag_sell_rate').attr("disabled", false);



					}else{



						$('#tag_sell_rate').attr("disabled", true);

					}

					$('#tag_pur_id_mc_type').val(item.pur_mc_type);
					$('#tag_pur_mc_value').val(item.pur_mc_value);
					pur_va = parseFloat(item.pur_wastage_perc) || 0;
					pur_mc_va_edit_access = parseFloat($('#pur_mc_va_edit_access').val());
					// Auto-fill purchase wastage when it is zero but purchase touch is available
					if (pur_va === 0 && parseFloat(item.purchase_touch) > 0) {
						var selling_purity_val = 0;
						var selected_id_purity = $('#id_purity').val();
						$.each(purityDetails, function (pk, pd) {
							if (pd.id_purity == selected_id_purity) {
								selling_purity_val = parseFloat(pd.purity) || 0;
								return false; // break
							}
						});
						var auto_wast = parseFloat(item.purchase_touch) - parseFloat(selling_purity_val);
						if (auto_wast > 0) {
							pur_va = parseFloat(auto_wast.toFixed(4));
						}
					}

					$('#tag_pur_wast_perc').val(pur_va > 0 ? pur_va : 0);
					calc_pur_wastage("WV");

					pur_mc_va_edit_access = parseFloat($('#pur_mc_va_edit_access').val());
					if (parseFloat(item.pur_wastage_perc) > 0 && pur_mc_va_edit_access == 0) {
						// Original wastage from supplier bill - lock the field
						$('#tag_pur_wast_perc').prop('readonly', true);
						$('#tag_pur_wast_value').prop('readonly', true);
						$('#tag_pur_id_mc_type').css('pointer-events', 'none');
						$('#tag_pur_id_mc_type').prop('disabled', true);
						$('#tag_pur_mc_value').prop('readonly', true);
					} else {
						// Zero or auto-filled wastage - allow manual override
						$('#tag_pur_wast_perc').prop('readonly', false);
						$('#tag_pur_wast_value').prop('readonly', false);
						$('#tag_pur_id_mc_type').css('pointer-events', 'auto');
						$('#tag_pur_id_mc_type').prop('disabled', false);
						$('#tag_pur_mc_value').prop('readonly', false);
					}



					if (item.calculation_based_on==4){

						$("#tag_sale_value").prop("readonly", false);

					}else{

						$("#tag_sale_value").prop("readonly", true);

					}



					var curr_used_gross = 0;



					var curr_used_pcs = 0;



					var lot_bal_wt=0;



					var blc_lot_wt=0;



					var blc_gross_wt=0;



					var act_gross_blc=0;



					var weight_per=$('#weight_per').val();



					var sales_mode=item.sales_mode;



					var size_sel = "<option value=''>- Select Size-</option>";



					$.each(item.size_details, function (mkey, mitem) {



                		size_sel += "<option  value='"+mitem.id_size+"'>"+mitem.value+'-'+mitem.name+"</option>";



                	});



					$('.disp_lot_wt').html(item.gross_wt);



					$('.disp_lot_pcs').html(item.no_of_piece);



					$('.disp_lot_tag_wt').html(parseFloat(parseFloat(item.gross_wt) - parseFloat(item['lot_blc'].lot_bal_wt)).toFixed(3));



					$('.disp_lot_tag_pcs').html(parseFloat(item.no_of_piece) - parseFloat(item['lot_blc'].lot_bal_pcs));



					$('.disp_lot_bal_wt').html(parseFloat(item['lot_blc'].lot_bal_wt).toFixed(3));



					$('.disp_lot_bal_pcs').html(item['lot_blc'].lot_bal_pcs);



					$('#lot_bal_wt').val(item['lot_blc'].lot_bal_wt);



					$('#lot_bal_pcs').val(item['lot_blc'].lot_bal_pcs);



					$('.disp_lot_nwt').html(item.net_wt);



					$('.disp_lot_tag_nwt').html(parseFloat(parseFloat(item.net_wt) - parseFloat(item['lot_blc'].lot_tag_net_wt)).toFixed(3));



					$('.disp_lot_bal_nwt').html(parseFloat(item['lot_blc'].lot_tag_net_wt).toFixed(3));



					$('.disp_lot_dwt').html(item['stone_details'].lot_dia_wt);



					$('.disp_lot_tag_dwt').html(item['stone_details'].tag_dia_wt);



					$('.disp_lot_bal_dwt').html(item['stone_details'].bal_dia_wt);



					$('.disp_lot_stwt').html(item['stone_details'].lot_stn_wt);



					$('.disp_lot_tag_stwt').html(item['stone_details'].tag_stn_wt);



					$('.disp_lot_bal_stwt').html(item['stone_details'].bal_stn_wt);



					$('#lot_bal_stone_wt').val(item['stone_details'].bal_stn_wt);



					$('#lot_bal_dia_wt').val(item['stone_details'].bal_dia_wt);



					if(item.is_multimetal == 1)



						$(".multimetal").css("display","block");



					else



						$(".multimetal").css("display","none");



					console.log($('#lot_bal_wt').val());



					if($('.disp_lot_bal_pcs').html()==1)



					{



						alert("Balance Piece : "+$('.disp_lot_bal_pcs').html()+" and Balance Weight : "+$('.disp_lot_bal_wt').html()+" gm");



					}



					/*$('#lt_item_list> tbody  > tr').each(function(index, tr) {



						if($(this).find('.id_lot_inward_detail').val() == item.id_lot_inward_detail)



						{



							curr_used_gross+=parseFloat(($(this).find('.gross_wt').val()=='' ?0 :$(this).find('.gross_wt').val()));



							curr_used_pcs+=parseFloat(($(this).find('.no_of_piece').val()=='' ?0 :$(this).find('.no_of_piece').val()));



							act_gross_blc =parseFloat(($(this).find('.act_gross_blc').val()=='' ?0 :$(this).find('.act_gross_blc').val()));



						}



					});*/



        		});



        	}



        	else



        	{



        	    $('#lot_bal_wt').val(0);



				$('#lot_bal_pcs').val(0);



				$('.disp_lot_wt').html(0);



				$('.disp_lot_pcs').html(0);



				$('.disp_lot_tag_wt').html(0);



				$('.disp_lot_tag_pcs').html(0);



				$('.disp_lot_bal_wt').html(0);



				$('.disp_lot_bal_pcs').html(0);



				$('#tag_act_gross_blc').val(0);



				$('#tag_blc_gross').val(0);



				$('.disp_lot_nwt').html(0);



				$('.disp_lot_tag_nwt').html(0);



				$('.disp_lot_bal_nwt').html(0);



				$('.disp_lot_dwt').html(0);



				$('.disp_lot_tag_dwt').html(0);



				$('.disp_lot_bal_dwt').html(0);



				$('.disp_lot_stwt').html(0);



				$('.disp_lot_tag_stwt').html(0);



				$('.disp_lot_bal_stwt').html(0);



        	}



         	$(".overlay").css("display", "none");



        },



        error:function(error)



        {



        }



    	});



}



//Gross Weight



$(document).on('keyup', '.gross_wt,.less_wt,.making_charge,.wastage_percentage,.sell_rate,.adjusted_item_rate,.no_of_piece', function(e){



		var row = $(this).closest('tr');



		var gross_wt = parseFloat((isNaN(row.find('.gross_wt').val()) || row.find('.gross_wt').val() == '')  ? 0 : row.find('.gross_wt').val()).toFixed(3);



		var less_wt  = (isNaN(row.find('.less_wt').val()) || row.find('.less_wt').val() == '')  ? 0 : row.find('.less_wt').val();



		var net_wt = parseFloat(parseFloat(gross_wt) - parseFloat(less_wt)).toFixed(3);



		row.find('.net_wt').val(net_wt);



		if(this.className == "no_of_piece"){



			if(row.find('.calculation_based_on').val() >= 3 ){



				calculateTagSaleValue(row);



			}



		}else{



			calculateTagSaleValue(row);



		}



	});



/*$(document).on('change','.gross_wt',function(e){



	var curr_used_gross = 0;



	var act_gross_blc = 0;



	var row = $(this).closest('tr');



	var trid = $(this).closest('tr').attr('id'); // table row ID



	if(row.find('.calculation_based_on').val() != 3 ){



		$('#lt_item_list> tbody  > tr').each(function(index, tr) {



			var id_lot_inward_detail=$(this).find('.id_lot_inward_detail').val();



			if(id_lot_inward_detail==trid)



			{



				act_gross_blc = $(this).find('.act_gross_blc').val();



				curr_used_gross+=parseFloat(($(this).find('.gross_wt').val()=='' ?0 :$(this).find('.gross_wt').val()));



			}



		});



		if(act_gross_blc < curr_used_gross)



		{



			row.find('.gross_wt').val('');



			row.find('.gross_wt').focus();



			alert("Entered gross weight greater than available weight.");



		}else{



			row.find('.blc_gross').html(act_gross_blc-curr_used_gross);



		}



	}



});*/



$(document).on('change','.gross_wt',function(e){



	var curr_used_gross = 0;



	var act_gross_blc = 0;



	var blc_gross = 0;



	var gross_wt_blc = 0;



	var row = $(this).closest('tr');



	var trid = $(this).closest('tr').attr('id'); // table row ID



	if(row.find('.calculation_based_on').val() != 3 ){



		$('#lt_item_list> tbody  > tr').each(function(index, tr) {



			var id_lot_inward_detail=$(this).find('.id_lot_inward_detail').val();



			if(id_lot_inward_detail==trid)



			{



				act_gross_blc = parseFloat($(this).find('.act_gross_blc').val());



				blc_gross 	  = $(this).find('.act_gross_blc').html();



				gross_wt_blc  = $(this).find('.gross_wt_blc').val();



				curr_used_gross+=parseFloat(($(this).find('.gross_wt').val()=='' ?0 :$(this).find('.gross_wt').val()));



			}



		});



	    console.log(act_gross_blc);



	    console.log(curr_used_gross);



		if(parseFloat(act_gross_blc) < parseFloat(curr_used_gross))



		{



			row.find('.gross_wt').val('');



			row.find('.gross_wt').focus();



			row.find('.net_wt').val('');



			alert("Entered gross weight greater than available weight.");



		}



		else{



			if(act_gross_blc<=curr_used_gross)



			{



				row.find('.blc_gross').html(0);



			}else{



				row.find('.blc_gross').html(parseFloat(gross_wt_blc-curr_used_gross).toFixed(3));



			}



		}



	}



});



// Piece



$(document).on('change','.no_of_piece',function(e){



	var curr_used_pcs = 0;



	var act_blc_pcs = 0;



	var row = $(this).closest('tr');



	var trid = $(this).closest('tr').attr('id'); // table row ID



	$('#lt_item_list> tbody  > tr').each(function(index, tr) {



		var id_lot_inward_detail=$(this).find('.id_lot_inward_detail').val();



		if(id_lot_inward_detail==trid)



		{



			act_blc_pcs = $(this).find('.act_blc_pcs').val();



			curr_used_pcs+=parseFloat(($(this).find('.no_of_piece').val()=='' ?0 :$(this).find('.no_of_piece').val()));



		}



	});



	if(act_blc_pcs < curr_used_pcs)



	{



		row.find('.no_of_piece').val('');



		row.find('.no_of_piece').focus();



		alert("Entered pieces greater than available pieces.");



	}else{



		row.find('.blc_pcs').html(act_blc_pcs-curr_used_pcs);



	}



});



//pieces



$(document).on('change', '.calculation_based_on', function(e){



	var row = $(this).closest('tr');



	calculateTagSaleValue(row);



});



$(document).on('change','.mc_type ',function(e){



		var row = $(this).closest('tr');



		if(this.value!='')



		{



			row.find('.id_mc_type').val(this.value);



		}



		else



		{



			row.find('.id_mc_type').val(this.value);



		}



		calculateTagSaleValue(row);



	});



function calculateTagSaleValue(row){



    $('#lt_item_list > tbody tr').each(function(idx, row){



    curRow = $(this);



	var disc_limit_type=$('#disc_limit_type').val();



	var total_price = 0;



	var base_value_price = 0;



	var arrived_value_price = 0;



	var base_value_tax = 0;



	var arrived_value_tax = 0;



	var base_rate_tax = 0;



	var arrived_rate_tax = 0;



	var total_tax_per = 0;



	var total_tax_rate = 0;



	var rate_with_mc = 0;



	//curRow.find('td:eq(1) .cat_design').val(i.item.label);



	//(isNaN(row.find('td:eq(6) .cat_gwt').val()) || row.find('td:eq(6) .cat_gwt').val() == '')  ? 0 : row.find('td:eq(6) .cat_gwt').val();



	var gross_wt = (isNaN(curRow.find('.gross_wt').val()) || curRow.find('.gross_wt').val() == '')  ? 0 : curRow.find('.gross_wt').val();



	var less_wt  = (isNaN(curRow.find('.less_wt').val()) || curRow.find('.less_wt').val() == '')  ? 0 : curRow.find('.less_wt').val();



	var stone_price  = (isNaN(curRow.find('.stone_price').val()) || curRow.find('.stone_price').val() == '')  ? 0 : curRow.find('.stone_price').val();



	var material_price  = (isNaN(curRow.find('.material_price').val()) || curRow.find('.material_price').val() == '')  ? 0 : curRow.find('.material_price').val();



	var certification_price  = (isNaN(curRow.find('.price').val()) || curRow.find('.price').val() == '')  ? 0 : curRow.find('.price').val();



	var net_wt = (isNaN(curRow.find('.net_wt').val()) || curRow.find('.net_wt').val() == '')  ? 0 : curRow.find('.net_wt').val();



	var calculation_type = (isNaN(curRow.find('.calculation_based_on').val()) || curRow.find('.calculation_based_on').val() == '')  ? 0 : curRow.find('.calculation_based_on').val();



	var metal_type = (isNaN(curRow.find('.id_metal').val()) || curRow.find('.id_metal').val() == '')  ? 1 : curRow.find('.id_metal').val();



	var sales_mode = (isNaN(curRow.find('.sales_mode').val()) || curRow.find('.sales_mode').val() == '')  ? 1 : curRow.find('.sales_mode').val();



    if(metal_type==1)



	{



		var rate_per_grm = $('#metal_rate').val();//Gold



	}else if(metal_type==2){



		var rate_per_grm = $('#silverrate_1gm').val();//Silver



	}



	else if(metal_type==3){



		var rate_per_grm = $('#platinum_1g').val();//Platinum



	}



	var tgi_calculation_type=$('#tgi_calculation').val().split(",");



	var tax_percentage=$('#tax_percentage').val().split(",");



	var retail_max_mc = (isNaN(curRow.find('.making_charge').val()) || curRow.find('.making_charge').val() == '')  ? 0 : curRow.find('.making_charge').val();



	var tot_wastage   = (isNaN(curRow.find('.wastage_percentage').val()) || curRow.find('.wastage_percentage').val() == '')  ? 0 : curRow.find('.wastage_percentage').val();



	var no_of_piece   = (isNaN(curRow.find('.no_of_piece').val()) || curRow.find('.no_of_piece').val() == '')  ? 0 : curRow.find('.no_of_piece').val();



	/**



	*	Amount calculation based on settings (without discount and tax )



	*   0 - Wastage on Gross weight And MC on Gross weight



	*   1 - Wastage on Net weight And MC on Net weight



	*   2 - Wastage On Netwt And MC On Grwt



	*   rate_with_mc = Metal Rate + Stone + OM + Wastage + MC



	*/



	if(calculation_type == 0){



		var wast_wgt      = parseFloat(parseFloat(gross_wt) * parseFloat(tot_wastage/100)).toFixed(3);



		var mc_type       =  parseFloat(curRow.find('.id_mc_type').val() == 1 ? parseFloat(retail_max_mc * gross_wt ) : parseFloat(retail_max_mc * curRow.find('.no_of_piece').val()));



		// Metal Rate + Stone + OM + Wastage + MC



		rate_with_mc = parseFloat(parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(mc_type)+parseFloat(stone_price)+parseFloat(material_price)+parseFloat(certification_price));



	}



	else if(calculation_type == 1){



		var wast_wgt      = parseFloat(parseFloat(net_wt) * parseFloat(tot_wastage/100)).toFixed(3);



		var mc_type       =  parseFloat(curRow.find('.id_mc_type').val() == 1 ? parseFloat(retail_max_mc * net_wt ) : parseFloat(retail_max_mc * curRow.find('.no_of_piece').val()));



		// Metal Rate + Stone + OM + Wastage + MC



		rate_with_mc = parseFloat(parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(mc_type)+parseFloat(stone_price)+parseFloat(material_price)+parseFloat(certification_price));



	}



	else if(calculation_type == 2){



		var wast_wgt      = parseFloat(parseFloat(net_wt) * parseFloat(tot_wastage/100)).toFixed(3);



		var mc_type       =  parseFloat(curRow.find('.id_mc_type').val() == 1 ? parseFloat(retail_max_mc * gross_wt ) : parseFloat(retail_max_mc * curRow.find('.no_of_piece').val()));



		// Metal Rate + Stone + OM + Wastage + MC



	    rate_with_mc = parseFloat((parseFloat(rate_per_grm) * (parseFloat(wast_wgt) + parseFloat(net_wt)))+ parseFloat(mc_type))+parseFloat(stone_price)+parseFloat(material_price)+parseFloat(certification_price);



	}



	else if(calculation_type == 3){



		var sell_rate  = (isNaN(curRow.find('.sell_rate').val()) || curRow.find('.sell_rate').val() == '')  ? 0 : curRow.find('.sell_rate').val();



		var adjusted_item_rate  = (isNaN(curRow.find('.adjusted_item_rate').val()) || curRow.find('.adjusted_item_rate').val() == '')  ? 0 : curRow.find('.adjusted_item_rate').val();



	    caculated_item_rate = parseFloat(parseFloat(sell_rate)*parseFloat(no_of_piece));



	    curRow.find('.caculated_item_rate').val(caculated_item_rate);



	    rate_with_mc = parseFloat(parseFloat(adjusted_item_rate) > 0 ? parseFloat(adjusted_item_rate) : caculated_item_rate );



	}



	else if(calculation_type == 4){



		var sell_rate  = (isNaN(curRow.find('.sell_rate').val()) || curRow.find('.sell_rate').val() == '')  ? 0 : curRow.find('.sell_rate').val();



		var adjusted_item_rate  = (isNaN(curRow.find('.adjusted_item_rate').val()) || curRow.find('.adjusted_item_rate').val() == '')  ? 0 : curRow.find('.adjusted_item_rate').val();



	    caculated_item_rate = parseFloat((parseFloat(sell_rate)*parseFloat(net_wt)));



	    curRow.find('.caculated_item_rate').val(caculated_item_rate);



	    rate_with_mc = parseFloat(parseFloat(adjusted_item_rate) > 0 ? parseFloat(adjusted_item_rate) : caculated_item_rate );



	}



	console.log('Calculation : '+calculation_type);



	console.log('Wastage : '+wast_wgt);



	console.log('Total Wastage : '+tot_wastage);



	console.log('MC : '+mc_type);



	console.log('Rate with MC : '+rate_with_mc);



	console.log(' MC TYPE : '+mc_type);



	console.log(' Rate Per Gram : '+rate_per_grm);



	// Tax Calculation



	if(sales_mode==2)



	{



	 $.each(tgi_calculation_type,function(ckey,citem)



	{



		if(citem==1) //Base value



		{



			$.each(tax_percentage,function(key,item){



				if(ckey==key)



				{



					base_value_tax+=(parseFloat(item));



				}



			});



			base_rate_tax	 = parseFloat(rate_with_mc*parseFloat(( base_value_tax / 100)));



			base_value_price = parseFloat(rate_with_mc +base_rate_tax).toFixed(2);



		}



		if(citem==2) //arrived value



		{



				$.each(tax_percentage,function(key,item){



				if(ckey==key)



				{



					arrived_value_tax+=(parseFloat(item));



				}



				});



			arrived_rate_tax    = parseFloat(parseFloat(base_value_price)*parseFloat(( arrived_value_tax / 100)));



			arrived_value_price = parseFloat(parseFloat(base_value_price)+arrived_rate_tax).toFixed(2);



		}



	});



	total_tax_rate=parseFloat(parseFloat(base_rate_tax)+parseFloat(arrived_rate_tax)).toFixed(2);



	total_tax_per=parseFloat(parseFloat(base_value_tax)+parseFloat(arrived_value_tax)).toFixed(2);



	total_price=parseFloat(arrived_value_price!=0 ? arrived_value_price:base_value_price).toFixed(2);



    }else



    {



        total_price=rate_with_mc;



    }



	curRow.find('.sale_value').val(Math.round(total_price));



	console.log('Amount : '+total_price);



	console.log('Tax Rate : '+total_tax_rate);



	console.log('Arrived value : '+arrived_value_price);



	console.log('*************************');



    });



}



function calculateOrderTagSaleValue(){



	$('#lt_item_list > tbody tr').each(function(idx, row){



	curRow = $(this);



	var disc_limit_type=$('#disc_limit_type').val();



	var total_price = 0;



	var base_value_price = 0;



	var arrived_value_price = 0;



	var base_value_tax = 0;



	var arrived_value_tax = 0;



	var base_rate_tax = 0;



	var arrived_rate_tax = 0;



	var total_tax_per = 0;



	var total_tax_rate = 0;



	var rate_with_mc = 0;



	//curRow.find('td:eq(1) .cat_design').val(i.item.label);



	//(isNaN(row.find('td:eq(6) .cat_gwt').val()) || row.find('td:eq(6) .cat_gwt').val() == '')  ? 0 : row.find('td:eq(6) .cat_gwt').val();



	var gross_wt = (isNaN(curRow.find('.gross_wt').val()) || curRow.find('.gross_wt').val() == '')  ? 0 : curRow.find('.gross_wt').val();



	var less_wt  = (isNaN(curRow.find('.less_wt').val()) || curRow.find('.less_wt').val() == '')  ? 0 : curRow.find('.less_wt').val();



	var stone_price  = (isNaN(curRow.find('.stone_price').val()) || curRow.find('.stone_price').val() == '')  ? 0 : curRow.find('.stone_price').val();



	var material_price  = (isNaN(curRow.find('.material_price').val()) || curRow.find('.material_price').val() == '')  ? 0 : curRow.find('.material_price').val();



	var certification_price  = (isNaN(curRow.find('.price').val()) || curRow.find('.price').val() == '')  ? 0 : curRow.find('.price').val();



	var net_wt = (isNaN(curRow.find('.net_wt').val()) || curRow.find('.net_wt').val() == '')  ? 0 : curRow.find('.net_wt').val();



	var calculation_type = (isNaN(curRow.find('.calculation_based_on').val()) || curRow.find('.calculation_based_on').val() == '')  ? 0 : curRow.find('.calculation_based_on').val();



	var metal_type = (isNaN(curRow.find('.id_metal').val()) || curRow.find('.id_metal').val() == '')  ? 1 : curRow.find('.id_metal').val();



	var sales_mode = (isNaN(curRow.find('.sales_mode').val()) || curRow.find('.sales_mode').val() == '')  ? 1 : curRow.find('.sales_mode').val();



	var stn_amt = (isNaN(curRow.find('.stn_amt').val()) || curRow.find('.stn_amt').val() == '')  ? 0 : curRow.find('.stn_amt').val();



    if(metal_type==1)



	{



		var rate_per_grm = $('#metal_rate').val();//Gold



	}else if(metal_type==2){



		var rate_per_grm = $('#silverrate_1gm').val();//Silver



	}



	else if(metal_type==3){



		var rate_per_grm = $('#platinum_1g').val();//Platinum



	}



	var tgi_calculation_type=$('#tgi_calculation').val().split(",");



	var tax_percentage=$('#tax_percentage').val().split(",");



	var retail_max_mc = (isNaN(curRow.find('.order_making_charge').val()) || curRow.find('.order_making_charge').val() == '')  ? 0 : curRow.find('.order_making_charge').val();



	var tot_wastage   = (isNaN(curRow.find('.order_wastage_percentage').val()) || curRow.find('.order_wastage_percentage').val() == '')  ? 0 : curRow.find('.order_wastage_percentage').val();



	var no_of_piece   = (isNaN(curRow.find('.no_of_piece').val()) || curRow.find('.no_of_piece').val() == '')  ? 0 : curRow.find('.no_of_piece').val();



	var tax_group = curRow.find('.tax_group_id').val();



	var mc_type = curRow.find('.id_mc_type').val();



	/**



	*	Amount calculation based on settings (without discount and tax )



	*   0 - Wastage on Gross weight And MC on Gross weight



	*   1 - Wastage on Net weight And MC on Net weight



	*   2 - Wastage On Netwt And MC On Grwt



	*   rate_with_mc = Metal Rate + Stone + OM + Wastage + MC



	*/



	var wast_wgt = parseFloat(parseFloat(net_wt) * parseFloat(tot_wastage/100)).toFixed(3);



	var retail_max_mc       =  parseFloat(curRow.find('.id_mc_type').val() == 1 ? parseFloat(retail_max_mc * net_wt ) : parseFloat(retail_max_mc * curRow.find('.no_of_piece').val()));



	rate_with_mc = parseFloat(parseFloat(parseFloat(rate_per_grm) * (parseFloat(wast_wgt) + parseFloat(net_wt))) +parseFloat(retail_max_mc)+parseFloat(stn_amt)).toFixed(2);



	//console.log('Calculation : '+calculation_type);



	console.log('retail_max_mc : '+retail_max_mc);



	console.log('Wastage : '+wast_wgt);



	console.log('Total Wastage : '+tot_wastage);



	console.log('Rate with MC : '+rate_with_mc);



	console.log(' Rate Per Gram : '+rate_per_grm);



	// Tax Calculation



	if(sales_mode==2)



	{



   	var base_value_tax=parseFloat(calculate_base_value_tax(rate_with_mc,tax_group)).toFixed(2);



	var base_value_amt=parseFloat(parseFloat(rate_with_mc)+parseFloat(base_value_tax)).toFixed(2);



	var arrived_value_tax=parseFloat(calculate_arrived_value_tax(base_value_amt,tax_group)).toFixed(2);



	var arrived_value_amt=parseFloat(parseFloat(base_value_amt)+parseFloat(arrived_value_tax)).toFixed(2);



	var total_tax_rate=parseFloat(parseFloat(base_value_tax)+parseFloat(arrived_value_tax)).toFixed(2);



	total_price=parseFloat(parseFloat(rate_with_mc)+parseFloat(total_tax_rate)).toFixed(2);



    }else



    {



        total_price=rate_with_mc;



    }



	curRow.find('.sale_value').val(Math.round(total_price));



	console.log('Amount : '+total_price);



	console.log('Tax Rate : '+total_tax_rate);



	console.log('Arrived value : '+arrived_value_price);



	console.log('*************************');



	});



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



function calculate_base_value_tax(taxcallrate, taxgroup){



	var totaltax = 0;



	console.log(tax_details);



	$.each(tax_details, function(idx, taxitem){



		if(taxitem.tgi_tgrpcode == taxgroup){



			if(taxitem.tgi_calculation == 1){



				if(taxitem.tgi_type == 1){



					totaltax += parseFloat(taxcallrate)	* (parseFloat(taxitem.tax_percentage)/100);



				}else{



					totaltax -= parseFloat(taxcallrate)	* (parseFloat(taxitem.tax_percentage)/100);



				}



			}



		}



	});



	return totaltax;



}



function calculate_arrived_value_tax(taxcallrate, taxgroup){



	var totaltax = 0;



	console.log(tax_details);



	$.each(tax_details, function(idx, taxitem){



		if(taxitem.tgi_tgrpcode == taxgroup){



			if(taxitem.tgi_calculation == 2){



				if(taxitem.tgi_type == 1){



					totaltax += parseFloat(taxcallrate)	* (parseFloat(taxitem.tax_percentage)/100);



				}else{



					totaltax -= parseFloat(taxcallrate)	* (parseFloat(taxitem.tax_percentage)/100);



				}



			}



		}



	});



	return totaltax;



}



//custom



$(document).on('change',".stones_type",function(){



   var row = $(this).closest('tr');



   var stone_type=this.value;



   /*if(stone_type == 1)



   {



        row.find('.quality_id').val('');



        row.find('.quality_id').prop('disabled', false);



   }



   else



   {



        row.find('.quality_id').val('');



        row.find('.quality_id').prop('disabled', true);



   }*/



   row.find('.quality_id').val('');



   row.find('.quality_id').prop('disabled', false);



   row.find('.stone_id').html('');



   	var stones_list = "<option value=''>-Select Stone-</option>";



   $.each(stones, function (pkey, pitem) {



        if(pitem.stone_type==stone_type)



        {



            /*if($('#tag_lot_received_id option:selected').attr('data-lotfrom') == 2){



                $.each(current_po_details[0].stonedetail, function (spkey, spitem) {



        	        if(pitem.stone_id == spitem.po_stone_id){



                        stones_list += "<option value='"+pitem.stone_id+"'>"+pitem.stone_name+"</option>";



        	        }



                });



            }else{



                stones_list += "<option value='"+pitem.stone_id+"'>"+pitem.stone_name+"</option>";



            }*/



			stones_list += "<option value='"+pitem.stone_id+"'>"+pitem.stone_name+"</option>";



        }



	});



	 row.find('.stone_id').append(stones_list);



	 set_minmaxStone_rates(row);



});


document.addEventListener("DOMContentLoaded", function () {

    document.getElementById("purchase_touch").addEventListener("input", function () {

        let value = this.value;

        value = value.replace(/[^0-9.]/g, '');

        if ((value.match(/\./g) || []).length > 1) {
            value = value.substring(0, value.length - 1);
        }

        if (value.indexOf('.') !== -1) {
            let parts = value.split('.');
            parts[1] = parts[1].substring(0, 2);
            value = parts[0] + '.' + parts[1];
        }

        this.value = value;
    });

});



$(document).on('change',".stone_id",function(){



	let stone_id_val = $(this).val();



	var row = $(this).closest('tr');



	$.each(stones, function (pkey, stItem) {



		if(stone_id_val == stItem.stone_id)



		{



			if(stItem.st_id == 0) {



				console.log("Diamond");



				row.find('.show_in_lwt').prop('checked',true);



				row.find('.show_in_lwt').val(1);



			} else if(stItem.st_id == 1) {



				console.log("Others");



				row.find('.show_in_lwt').prop('checked',false);



				row.find('.show_in_lwt').val(0);



			}



			row.find('.stone_uom_id').val(stItem.uom_id);



		}



	});



	set_minmaxStone_rates(row);



 });



 $(document).on('change','.quality_id',function(){



    curRow = $(this).closest('tr');



    var stone_quality_id = curRow.find('.quality_id').val();



    $.each(quality_code, function (pkey, pitem) {



        if(pitem.quality_id == stone_quality_id)



        {



             curRow.find('.stone_clarity').html(pitem.clarity);



             curRow.find('.stone_color').html(pitem.color);



             curRow.find('.stone_cut').html(pitem.cut);



             curRow.find('.stone_shape').html(pitem.shape);



        }



    });



	set_minmaxStone_rates(curRow);



});



function create_new_stone_row()



{



	var stones_list = "<option value=''>-Select Stone-</option>";



	var stones_type = "<option value=''>-Stone Type-</option>";



	var uom_list = "<option value=''>-UOM-</option>";



	var quality_list = "<option value=''>-Quality-</option>";



	var clarity="";



    var color ="";



    var cut ="";



    var shape ="";

	var pur_rate="";



    var length=(($('#estimation_stone_cus_item_details tbody tr').length)+1);



    console.log('length:'+length);



	$.each(stones, function (pkey, pitem) {



		stones_list += "<option value='"+pitem.stone_id+"'>"+pitem.stone_name+"</option>";



	});



	$.each(stone_types, function (pkey, pitem) {



		stones_type += "<option value='"+pitem.id_stone_type+"'>"+pitem.stone_type+"</option>";



	});



	$.each(uom_details, function (pkey, pitem) {



		uom_list += "<option value='"+pitem.uom_id+"'>"+pitem.uom_name+"</option>";



	});



	$.each(quality_code, function (pkey, pitem) {



		quality_list += "<option value='"+pitem.quality_id+"'>"+pitem.code+"</option>";



	});



	var row='';



        row += '<tr id="'+length+'">'



        	+'<td><select class="show_in_lwt form-control" name="est_stones_item[show_in_lwt][]" style="width:80px;"><option value="">-Select-</option><option value=1 selected>Yes</option><option value=0>No</option></select></td>'



        	+'<td><select class="stones_type form-control" name="est_stones_item[stones_type][]" style="width:100px;">'+stones_type+'</select></td>'



			+'<td><select class="stone_id form-control" name="est_stones_item[stone_id][]" style="width:100px;">'+stones_list+'</select><input type="hidden" class="stone_type" value=""></td>'



			+'<td><select class="quality_id form-control" name="est_stones_item[quality_id][]" disabled >'+quality_list+'</select></td>'



			+'<td><input type="number" class="stone_pcs form-control" name="est_stones_item[stone_pcs][]" value="1" style="width: 60px;"/></td>'



			+'<td><div class="input-group" style="width:159px;"><input class="stone_wt form-control" type="number" name="est_stones_item[stone_wt][]" value="" style="width: 78px;"/><span class="input-group-btn" style="width: 138px;"><select class="stone_uom_id form-control" name="est_stones_item[uom_id][]">'+uom_list+'</select></span></div></td>'



		    +'<td><div class="form-group" style="width: 100px;"><input class="stone_cal_type" type="radio" name="est_stones_item[cal_type]['+length+']" value="1" checked>Wt&nbsp;<input type="radio" name="est_stones_item[cal_type]['+length+']" class="stone_cal_type" value="2" >Pcs</div></td>'



			+'<td><span class="stone_cut">'+cut+'</span></td>'



			+'<td><span class="stone_color">'+color+'</span></td>'



			+'<td><span class="stone_clarity">'+clarity+'</span></td>'



			+'<td><span class="stone_shape">'+shape+'</span></td>'

			+'<td><input type="number" class="pur_stone_rate form-control" name="est_stones_item[pur_stone_rate][]" value="'+pur_rate+'" style="width: 100px"/></td>'



			+'<td><input type="number" class="stone_rate form-control" name="est_stones_item[stone_rate][]" value="" style="width: 100px"/></td>'



			+'<td><input type="number" class="stone_price form-control" name="est_stones_item[stone_price][]" value="" " style="width: 100px"/></td>'



			+'<td style="width: 100px;"><button type="button" class="btn btn-success btn-xs create_stone_item_details"><i class="fa fa-plus"></i></button><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-xs btn-del"><i class="fa fa-trash"></i></a></td></tr>';



	$('#cus_stoneModal .modal-body').find('#estimation_stone_cus_item_details tbody').append(row);



}



function create_new_empty_est_cus_stone_item(curRow,id)



{



	if(curRow!=undefined)



	{



		$('#custom_active_id').val(curRow.closest('tr').attr('class'));



		$('#stone_active_row').val(curRow.closest('tr').attr('class'));



	}



	var row='';



	var catRow=$('#custom_active_id').val();



	var active_row=$('#stone_active_row').val();



	var row_st_details=$('.'+catRow).find('.stone_details').val();



	console.log(row_st_details);



	if(row_st_details.length>0)



	{



		var stone_details=JSON.parse(row_st_details);



		console.log(stone_details);



		$.each(stone_details, function (pkey, pitem) {



 			var stones_list='';



 			var stones_type_list='';



 			var uom_list='';



 			var html='';



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



            	//+'<td><input class="show_in_lwt" type="checkbox"name="est_stones_item[show_in_lwt][]" value="'+(pitem.show_in_lwt==1 ? 1:0)+'" '+(pitem.show_in_lwt==1 ? 'checked' :'')+' ></td>'



            	+'<td><select class="show_in_lwt form-control" name="est_stones_item[show_in_lwt][]"><option value="">-Select-</option><option value=1 '+(pitem.show_in_lwt==1 ? 'selected' :'')+'>Yes</option><option value=0 '+(pitem.show_in_lwt==0 ? 'selected' :'')+'>No</option></select></td>'



            	+'<td><select class="stones_type form-control" name="est_stones_item[stones_type][]" >'+stones_type_list+'</select></td>'



				+'<td><select class="stone_id form-control" name="est_stones_item[stone_id][]" >'+stones_list+'</select></td>'



				+'<td><input type="number" class="stone_pcs form-control" name="est_stones_item[stone_pcs][]" value="'+pitem['stone_pcs']+'" style="width: 100%;"/></td>'



				+'<td><div class="input-group"><input class="stone_wt form-control" type="number" name="est_stones_item[stone_wt][]" value="'+pitem['stone_wt']+'" style="width:100%;"/><span class="input-group-btn" style="width: 100px;"><select class="stone_uom_id form-control" name="est_stones_item[uom_id][]" >'+uom_list+'</select></span></div></td>'



                +'<td><div class="form-group" style="width: 100px;"><input class="stone_cal_type" type="radio" name="est_stones_item[cal_type]['+pkey+']" value="1" "'+(pitem.stone_cal_type==1 ? 'checked' :'')+'">Wt&nbsp;<input type="radio" name="est_stones_item[cal_type]['+pkey+']" class="stone_cal_type" value="2" "'+(pitem.stone_cal_type==2 ? 'checked' :'')+'" >Pcs</div></td>'



				+'<td><input type="number" class="stone_rate form-control" name="est_stones_item[stone_rate][]" value="'+pitem['stone_rate']+'"  style="width:80%;"/></td>'



				+'<td><input type="number" class="stone_price form-control" name="est_stones_item[stone_price][]" value="'+pitem['stone_price']+'"  style="width:100%;" readonly/></td>'



				+'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td></tr>';



		});



	}



	else



	{



		var stones_list = "<option value=''>-Select Stone-</option>";



		var stones_type = "<option value=''>-Stone Type-</option>";



		var uom_list = "<option value=''>-Stone Type-</option>";



		$.each(stones, function (pkey, pitem) {



			stones_list += "<option value='"+pitem.stone_id+"'>"+pitem.stone_name+"</option>";



		});



		$.each(stone_types, function (pkey, pitem) {



			stones_type += "<option value='"+pitem.id_stone_type+"'>"+pitem.stone_type+"</option>";



		});



		$.each(uom_details, function (pkey, pitem) {



			uom_list += "<option value='"+pitem.uom_id+"'>"+pitem.uom_name+"</option>";



		});



            row += '<tr id="'+active_row+'">'



            	//+'<td><input class="show_in_lwt" type="checkbox"name="est_stones_item[show_in_lwt][]" value="1" checked></td>'



            	+'<td><select class="show_in_lwt form-control" name="est_stones_item[show_in_lwt][]"><option value="">-Select-</option><option value=1>Yes</option><option value=0>No</option></select></td>'



            	+'<td><select class="stones_type form-control" name="est_stones_item[stones_type][]">'+stones_type+'</select></td>'



				+'<td><select class="stone_id form-control" name="est_stones_item[stone_id][]"></select></td>'



				+'<td><input type="number" class="stone_pcs form-control" name="est_stones_item[stone_pcs][]" value="" style="width: 100%;"/></td>'



				+'<td><div class="input-group"><input class="stone_wt form-control" type="number" name="est_stones_item[stone_wt][]" value="" style="width:100%;"/><span class="input-group-btn" style="width: 100px;"><select class="stone_uom_id form-control" name="est_stones_item[uom_id][]">'+uom_list+'</select></span></div></td>'



                +'<td><div class="form-group" style="width: 100px;"><input class="stone_cal_type" type="radio" name="est_stones_item[cal_type]['+pkey+']" value="1" checked="true"> By Wt&nbsp;<input type="radio" name="est_stones_item[cal_type]['+pkey+']" class="stone_cal_type" value="2">Pcs</div></td>'



				+'<td><input type="number" class="stone_rate form-control" name="est_stones_item[stone_rate][]" value=""  style="width:80%;"/></td>'



				+'<td><input type="number" class="stone_price form-control" name="est_stones_item[stone_price][]" value=""  style="width:100%;" readonly/></td>'



				+'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td></tr>';



	}



	$('#cus_stoneModal .modal-body').find('#estimation_stone_cus_item_details tbody').append(row);



	$('#cus_stoneModal').modal('show');



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



$(document).on('input',".stone_pcs,.stone_wt",function()



{



    var curRow = $(this).closest('tr');



	set_minmaxStone_rates(curRow);



});



$(document).on('change',".stone_price",function(){



    var curRow = $(this).closest('tr');



    var stone_amt=0;



    var stone_pcs    = (isNaN(curRow.find('.stone_pcs').val()) || curRow.find('.stone_pcs').val() == '')  ? 0 : curRow.find('.stone_pcs').val();



    var stone_wt     = (isNaN(curRow.find('.stone_wt').val()) || curRow.find('.stone_wt').val() == '')  ? 0 : curRow.find('.stone_wt').val();



    var stone_price  = (isNaN(curRow.find('.stone_price').val()) || curRow.find('.stone_price').val() == '')  ? 0 : curRow.find('.stone_price').val();



     if(curRow.find('input[type=radio]:checked').val() == 1)



     {



        stone_amt = parseFloat(parseFloat(stone_price) / parseFloat(stone_wt)).toFixed(2);



     }



     else



     {



       stone_amt = parseFloat(parseInt(stone_price) / parseFloat(stone_pcs )).toFixed(2);



     }



     curRow.find('.stone_rate').val(stone_amt);



	 check_min_max_stone_rate(curRow);



});



$(document).on('change',".stone_cal_type",function(){



    var curRow = $(this).closest('tr');



  var stone_amt=0;



     var stone_pcs  = (isNaN(curRow.find('.stone_pcs').val()) || curRow.find('.stone_pcs').val() == '')  ? 0 : curRow.find('.stone_pcs').val();



     var stone_wt  = (isNaN(curRow.find('.stone_wt').val()) || curRow.find('.stone_wt').val() == '')  ? 0 : curRow.find('.stone_wt').val();



     var stone_rate  = (isNaN(curRow.find('.stone_rate').val()) || curRow.find('.stone_rate').val() == '')  ? 0 : curRow.find('.stone_rate').val();



     if(curRow.find('input[type=radio]:checked').val() == 1){



        stone_amt = parseFloat(parseFloat(stone_wt)*parseFloat(stone_rate)).toFixed(2);



     }else{



       stone_amt = parseFloat(parseInt(stone_pcs)*parseFloat(stone_rate)).toFixed(2);



     }



     //stone_amt = parseFloat(parseFloat(stone_wt)*parseFloat(stone_rate)).toFixed(2);



     curRow.find('.stone_price').val(stone_amt);



});



function calculate_stone_amount()



{



     $('#estimation_stone_cus_item_details > tbody tr').each(function(idx, row){



         curRow = $(this);



         var stone_amt=0;



         var stone_pcs  = (isNaN(curRow.find('.stone_pcs').val()) || curRow.find('.stone_pcs').val() == '')  ? 0 : curRow.find('.stone_pcs').val();



         var stone_wt  = (isNaN(curRow.find('.stone_wt').val()) || curRow.find('.stone_wt').val() == '')  ? 0 : curRow.find('.stone_wt').val();



         var stone_rate  = (isNaN(curRow.find('.stone_rate').val()) || curRow.find('.stone_rate').val() == '')  ? 0 : curRow.find('.stone_rate').val();



         if(curRow.find('input[type=radio]:checked').val() == 1){



            stone_amt = parseFloat(parseFloat(stone_wt)*parseFloat(stone_rate)).toFixed(2);



         }else{



           stone_amt = parseFloat(parseInt(stone_pcs)*parseFloat(stone_rate)).toFixed(2);



         }



         //stone_amt = parseFloat(parseFloat(stone_wt)*parseFloat(stone_rate)).toFixed(2);



         curRow.find('.stone_price').val(stone_amt);



     });



	 calculate_total_stone_amount();



}



function calculate_total_stone_amount(){



    var stone_pcs   = 0;



    var stone_wt    = 0;



    var stone_price     = 0;



    $('#estimation_stone_cus_item_details > tbody tr').each(function(idx, row){



        curRow = $(this);



        stone_pcs+= parseFloat((isNaN(curRow.find('.stone_pcs').val()) || curRow.find('.stone_pcs').val() == '')  ? 0 : curRow.find('.stone_pcs').val());



        if(curRow.find('.stone_uom_id').val()==6) // for Diamnond Stones



        {



            stone_wt +=parseFloat((isNaN(curRow.find('.stone_wt').val()) || curRow.find('.stone_wt').val() == '')  ? 0 : curRow.find('.stone_wt').val() / 5);



        }



        else



        {



            stone_wt +=parseFloat((isNaN(curRow.find('.stone_wt').val()) || curRow.find('.stone_wt').val() == '')  ? 0 : curRow.find('.stone_wt').val());



        }



        stone_price  +=parseFloat((isNaN(curRow.find('.stone_price ').val()) || curRow.find('.stone_price ').val() == '')  ? 0 : curRow.find('.stone_price ').val());



    });



    $('.stn_tot_pcs').html(parseFloat(stone_pcs));



    $('.stn_tot_weight').html(parseFloat(stone_wt).toFixed(3));



    $('.stn_tot_amount').html(parseFloat(stone_price ).toFixed(3));



}



/*$(document).on('change',".show_in_lwt",function(){



    if($(this).is(":checked"))



    {



        $(this).closest('tr').find('.show_in_lwt').val(1);



    }else{



        $(this).closest('tr').find('.show_in_lwt').val(0);



    }



});*/



$('#cus_stoneModal  #update_stone_details').on('click', function(){



	if(validateStoneCusItemDetailRow())



    {



    	var stone_details=[];



    	var stone_price=0;



    	var certification_price=0;



    	var tag_less_wgt = 0;



		var stn_pur_cost = 0;



    	var stn_pur_rate = 0;



    	modalStoneDetail = []; // Reset Old Value of stone modal



		lot_pur_stone_details = $('#lot_pur_stone_details').val() != ''? JSON.parse($('#lot_pur_stone_details').val()):[];



    	$('#cus_stoneModal .modal-body #estimation_stone_cus_item_details> tbody  > tr').each(function(index, tr) {



    		stone_price+=parseFloat($(this).find('.stone_price').val());



    		if($(this).find('.show_in_lwt :selected').val() == 1){



    		    tag_less_wgt+=parseFloat($(this).find('.stone_wt').val());



    		}



			$.each(lot_pur_stone_details, function (key, item) {

				if(item.stone_id == curRow.find('.stone_id').val() ){

					stn_pur_rate = item.rate_per_gram;

					stn_pur_cost = item.stone_cal_type == 1 ? parseFloat(curRow.find('.stone_wt').val()) * parseFloat(stn_pur_rate)  : parseFloat(curRow.find('.stone_pcs').val()) * parseFloat(stn_pur_rate)  ;

				}



			});



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



    		            'stone_uom_name'      : $(this).find('.stone_uom_id :selected').text(),



    		            'stone_name'        : $(this).find('.stone_id :selected').text(),



						'stone_quality_id'  : $(this).find('.quality_id').val(),



						// 'stn_pur_rate'        : stn_pur_rate == 0 ? $(this).find('.stone_rate').val():stn_pur_rate,



						'stn_pur_cost'        : stn_pur_cost == 0 ? $(this).find('.stone_price').val():stn_pur_cost,

						'stn_pur_rate'		  : $(this).find(".pur_stone_rate").val(),



    		});



    	});



    	modalStoneDetail = stone_details;



        console.log(modalStoneDetail);



    	// Preview Table Update



    	/*



    	$('#cus_stoneModal').modal('toggle');



    	var catRow=$('#custom_active_id').val();



    	$('.'+catRow).find('.stone_details').val(JSON.stringify(stone_details));



       	$('.'+catRow).find('.stone_price').val(stone_price);



       	$('.'+catRow).find('.price').val(certification_price);



    	var row = $('.'+catRow).closest('tr');



    	calculateTagPreviewSaleValue();



    	*/



    	// Update Stone Summary



    	$('#stone-det tbody').empty();



    	var stnRow = "";



    	$.each(modalStoneDetail, function (key, item) {



            stnRow +='<tr id='+item.stone_id+'>'



                    +'<td>'+item.stone_name+'</td>'



                    +'<td>'+item.stone_pcs+'</td>'



                    +'<td>'+item.stone_wt+' '+item.stone_uom_name+'</td>'



                    +'<td>'+item.stone_price+'</td>'



               +'</tr>';



        })



        $('#stone-det tbody').append(stnRow);



    	$("#tag_lwt").val(tag_less_wgt);



    	$("#tag_stone_details").val(JSON.stringify(stone_details));



    	calculateTagFormSaleValue();



        $('#cus_stoneModal .modal-body').find('#estimation_stone_cus_item_details tbody').empty();



        $('#cus_stoneModal').modal('hide');



        $('#tag_wast_perc').focus();



		set_tagging_wastage_and_mc();

    }



    else



    {



    	alert('Please Fill The Required Details');



    }



});



$(document).on('change','.stone_id',function(e){



	var stone_id=this.value;



	var stone_type='';



	var catRow=$('#custom_active_id').val();



	var row_st_details=$('.'+catRow).find('.stone_details').val();



	$.each(stones, function (pkey, item)



	{



		if(item.stone_id==stone_id)



		{



			stone_type = item.stone_type;



		}



	});



	var row = $(this).closest('tr');



	row.find('.stone_type').val(stone_type);



	if(stone_id!='')



	{



		$('#update_stone_details').prop('disabled',false);



	}



	else



	{



		$('#update_stone_details').prop('disabled',true);



	}



});



$(document).on('change',".is_certification",function()



{



	var catRow=$('#custom_active_id').val();



	var row_st_details=$('.'+catRow).find('.stone_details').val();



	if($(this).is(":checked"))



	{



		$(this).closest('tr').find('.is_certification').val(1);



		$(this).closest('tr').find('.price').prop('disabled',false);



		var stone_type=$(this).closest('tr').find('.stone_type').val();



		if(stone_type==1)



		{



			var image=$('.'+catRow).find('.precious_st_certif').val();



		}



		else if(stone_type==2)



		{



			var image=$('.'+catRow).find('.semiprecious_st_certif').val();



		}



		else if(stone_type==3)



		{



			var image=$('.'+catRow).find('.normal_st_certif').val();



		}



	    if(image!='' && image!=undefined)



		{



			var img_src=image.split('#');



			var html='';



			$.each(img_src,function(key,item){



				if(item!='')



				{



					var tag_lot_received_id=$('.'+catRow).find('.lot_no').val();



					var src=base_url+'/assets/img/lot/'+tag_lot_received_id+'/certificates/'+item;



					html+= "<div class='col-md-6'><input type='checkbox' class='img_select' name='image' value="+item+">&nbsp;select<img class='thumbnail' src='" + src + "'" +



					"style='width:70px;height:70px;'/></div>";



				}



			});



		}



		$(this).closest('tr').find('td:eq(6)').append(html);



	}



	else



	{



		var image='';



		$(this).closest('tr').find('td:eq(6)').html('');



		$(this).closest('tr').find('.is_certification').val(0);



		$(this).closest('tr').find('.price').prop('disabled',true);



		//$(this).closest('tr').find('.lwt').prop('disabled',true);



	}



});



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



	 	},



		complete: function (data) {



			load_uom("gwt_uom_id");



		}



	});



}



 //Update Tag list



 function update_tagging_details(id)



 {



	my_Date = new Date();



		$.ajax({



		url:base_url+ "index.php/admin_ret_tagging/tagging/edit/"+id+"?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),



        data: {'tag_id':id},



        type:"POST",



        dataType:"JSON",



        success:function(data)



        {



			$("#tagEditModal").modal({



			backdrop: 'static',



			keyboard: false



			});



			var row='';



        	var tagging=data.tagging;



        	var tag_balance=data.tag_balance;



        	var tax_percentage=data.tax_percentage;



        	var metal_rate=data.metal_rate;



        	var stone_details=data.stone_details;



        	var img_source =data.tagging.img_source;



        	if(img_source.length>0)



        	{



        		$.each(img_source,function(ckey,citem)



				{



					pre_img_resource.push({'src':citem.src,'name':citem});



					$('#img_source').val(JSON.stringify(pre_img_resource));



					var div = document.createElement("div");



					div.setAttribute('class','col-md-4');



					div.setAttribute('id',+ckey);



					param = {"key":ckey,"preview":preview};



					div.innerHTML+= "<a><i onclick='remove_tag_img("+JSON.stringify(param)+")' class='fa fa-trash'></i>Delete</a><img class='thumbnail' src='" +citem.src+ "'" +



					"style='width: 100px;height: 100px;'/>";



					$('#preview').append(div);



				});



        	}



        	$('#tag_id').val(tagging.tag_id);



        	$('#branch').html(tagging.name);



        	$('#lt_design_id').html(tagging.design_name);



        	$('#lot_no').html(tagging.tag_lot_id);



        	$('#tag_code').html(tagging.tag_code);



        	$('#net_wt').val(tagging.net_wt);


        	$('#purchase_touch').val(tagging.purchase_touch);



        	$('#gross_wt').val(tagging.gross_wt);



        	$('#cur_gross_wt').val(tagging.cur_gross_wt);



        	$('#less_wt').val(tagging.less_wt);



        	$('#piece').val(tagging.piece);



        	$('#cur_pieces').val(tagging.piece);



        	$('#size').val(tagging.size);



        	$('#sales_value').val(tagging.sales_value);



        	$('#lot_bal_wt').html(tag_balance.lot_bal_wt);



        	$('#lot_bal_pcs').html(tag_balance.lot_bal_pcs);



        	$('#tax_percentage').val(tax_percentage.tax_percentage);



        	$('#tgi_calculation').val(tax_percentage.tgi_calculation);



        	$('#wastage_percentage').val(tagging.retail_max_wastage_percent);



        	$('#making_charge').val(tagging.tag_mc_value);



        	$('#metal_rate').val(metal_rate.goldrate_22ct);



        	$('#tag_mc_type').val(tagging.tag_mc_type);



        	$('#sell_rate').val(tagging.sell_rate);



        	if(tagging.calculation_based_on == 3){



				$('#sell_rate_type').html("Per Piece");



			}



			else if(tagging.calculation_based_on == 4){



				$('#sell_rate_type').html("Per Gram");



			}



        	$('#adjusted_item_rate').val(tagging.adjusted_item_rate);



        	if(tagging.calculation_based_on==0)



        	{



        		$('#type0').prop('checked',true)



        	}



        	else if(tagging.calculation_based_on==1)



        	{



        		$('#type1').prop('checked',true)



        	}



        	else if(tagging.calculation_based_on==2)



        	{



        		$('#type2').prop('checked',true)



        	}



        	else if(tagging.calculation_based_on==3)



        	{



        		$('#type3').prop('checked',true)



        	}



        	else if(tagging.calculation_based_on==4)



        	{



        		$('#type4').prop('checked',true)



        	}



        	else



        	{



        		$('#type2').prop('checked',true)



        	}



        	if(stone_details.length>0)



        	{



					$.each(stone_details, function (pkey, pitem) {



					var stones_list='';



					$.each(stones, function (pkey, item)



					{



						var selected = "";



						if(item.stone_id == pitem.stone_id)



						{



						selected = "selected='selected'";



						}



						stones_list += "<option value='"+pitem.stone_id+"' "+selected+">"+item.stone_name+"</option>";



					});



					row+='<tr><td><select class="stone_id" name="est_stones_item[stone_id][]">'+stones_list+'</select></td><td><input type="number" class="stone_pcs" name="est_stones_item[stone_pcs][]" value="'+pitem['pieces']+'" style="width:80px;"/></td><td><input class="stone_wt" type="number" name="est_stones_item[stone_wt][]" value="'+pitem['wt']+'" style="width:80px;"/></td><td><input type="number" class="stone_price" name="est_stones_item[stone_price][]" value="'+pitem['amount']+'" style="width:80px;"/></td><td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td></tr>';



					});



        			$('#tagEditModal .modal-body').find('#tagging_stone_details tbody').append(row);



        	}



        	calculateTagEditSaleValue();



			$(".overlay").css("display", "none");



        },



        error:function(error)



        {



        }



    	});



 }



  $('#tagEditModal #close').click(function (e) {



  	     $('#tagEditModal .modal-body').find('#tagging_stone_details tbody').empty();



  	     $('#tagEditModal .modal-body').find('#preview').empty();



  	     $('#tagEditModal').modal('toggle');



 });



 $('#gross_wt').on('change',function(){



		var lot_bal_wt=parseInt($('#lot_bal_wt').html());



		var cur_gross_wt=$('#cur_gross_wt').val();



		var gross_wt=parseFloat($('#gross_wt').val()).toFixed(4);



		if(lot_bal_wt==0)



		{



			if(gross_wt>cur_gross_wt)



			{



				$('#gross_wt').val('');



				$('#gross_wt').focus();



			}



		}else if(lot_bal_wt<gross_wt)



		{



			$('#gross_wt').val('');



			$('#gross_wt').focus();



		}



	});



 $('#gross_wt, #less_wt,#making_charge,#wastage_percentage,#sell_rate,#adjusted_item_rate').on('keyup', function(e){



		var gross_wt = (isNaN($('#gross_wt').val()) || $('#gross_wt').val() == '')  ? 0 : $('#gross_wt').val();



		var less_wt  = (isNaN($('#less_wt').val()) || $('#less_wt').val() == '')  ? 0 : $('#less_wt').val();



		var net_wt = parseFloat(gross_wt) - parseFloat(less_wt);



		$('#net_wt').val(net_wt);



		calculateTagEditSaleValue();



	});



 $('#piece').on('change',function(){



	var pieces=$('#piece').val();



	var cur_pieces=$('#cur_pieces').val();



	var lot_bal_pcs=$('#lot_bal_pcs').html();



	if(lot_bal_pcs==0)



	{



		if(pieces>cur_pieces)



		{



			$('#piece').val('');



			$('#piece').focus();



		}



	}else if(lot_bal_pcs<pieces)



	{



		$('#piece').val('');



		$('#piece').focus();



	}



});



$("#tag_mc_value,#piece").on('keyup', function(e){



	calculateTagEditSaleValue();



});



$('input[type=radio][name="calculation_based_on"]').change(function() {



	calculateTagEditSaleValue();



});



$('#tag_mc_type').change(function() {



	calculateTagEditSaleValue();



});



function calculateTagEditSaleValue(){



	var total_price = 0;



	var base_value_price = 0;



	var arrived_value_price = 0;



	var base_value_tax = 0;



	var arrived_value_tax = 0;



	var base_rate_tax = 0;



	var arrived_rate_tax = 0;



	var total_tax_per = 0;



	var total_tax_rate = 0;



	var stone_price=0;



	var material_price  = 0;



	var rate_with_mc  = 0;



	var gross_wt = (isNaN($('#gross_wt').val()) || $('#gross_wt').val() == '')  ? 0 : $('#gross_wt').val();



	var less_wt  = (isNaN($('#less_wt').val()) || $('#less_wt').val() == '')  ? 0 : $('#less_wt').val();



	var net_wt = (isNaN($('#net_wt').val()) || $('#net_wt').val() == '')  ? 0 : $('#net_wt').val();



	var no_of_piece = (isNaN($(".piece").val()) || $('#piece').val() == '')  ? 0 : $('#piece').val();



	var calculation_type =$('input[name="calculation_based_on"]:checked').val();



	var rate_per_grm = $('#metal_rate').val();



	var tgi_calculation_type=$('#tgi_calculation').val().split(",");



	var tax_percentage=$('#tax_percentage').val().split(",");



	var retail_max_mc = (isNaN($('#making_charge').val()) || $('#making_charge').val() == '')  ? 0 : $('#making_charge').val();



	var tot_wastage   = (isNaN($('#wastage_percentage').val()) || $('#wastage_percentage').val() == '')  ? 0 : $('#wastage_percentage').val();



	$('#tagging_stone_details> tbody  > tr').each(function(index, tr) {



		stone_price+=(isNaN(parseFloat($(this).find('.stone_price').val())) ?0:parseFloat($(this).find('.stone_price').val()))



	});



	/**



	*	Amount calculation based on settings (without discount and tax )



	*   0 - Wastage on Gross weight And MC on Gross weight



	*   1 - Wastage on Net weight And MC on Net weight



	*   2 - Wastage On Netwt And MC On Grwt



	*   rate_with_mc = Metal Rate + Stone + OM + Wastage + MC



	*/



	if(calculation_type == 0){



		var wast_wgt      = parseFloat(parseFloat(gross_wt) * parseFloat(tot_wastage/100)).toFixed(3);



		var mc_type       =  parseFloat($('#tag_mc_type').val() == 1 ? parseFloat(retail_max_mc * gross_wt ) : parseFloat(retail_max_mc * $('#piece').val()));



		// Metal Rate + Stone + OM + Wastage + MC



		rate_with_mc = parseFloat(parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(mc_type)+parseFloat(stone_price)+parseFloat(material_price));



	}



	else if(calculation_type == 1){



		var wast_wgt      = parseFloat(parseFloat(net_wt) * parseFloat(tot_wastage/100)).toFixed(3);



		var mc_type       =  parseFloat($('#tag_mc_type').val() == 1 ? parseFloat(retail_max_mc * net_wt ) : parseFloat(retail_max_mc * $('#piece').val()));



		// Metal Rate + Stone + OM + Wastage + MC



		rate_with_mc = parseFloat(parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(mc_type)+parseFloat(stone_price)+parseFloat(material_price));



	}



	else if(calculation_type == 2){



		var wast_wgt      = parseFloat(parseFloat(net_wt) * parseFloat(tot_wastage/100)).toFixed(3);



		var mc_type       =  parseFloat($('#tag_mc_type').val() == 1 ? parseFloat(retail_max_mc * gross_wt ) : parseFloat(retail_max_mc * $('#piece').val()));



		// Metal Rate + Stone + OM + Wastage + MC



	    rate_with_mc = parseFloat((parseFloat(rate_per_grm) * (parseFloat(wast_wgt) + parseFloat(net_wt)))+ parseFloat(mc_type))+parseFloat(stone_price)+parseFloat(material_price);



	}



	else if(calculation_type == 3){



		var sell_rate  = (isNaN($('.sell_rate').val()) || $('.sell_rate').val() == '')  ? 0 : $('.sell_rate').val();



		var adjusted_item_rate  = (isNaN($('.adjusted_item_rate').val()) || $('.adjusted_item_rate').val() == '')  ? 0 : $('.adjusted_item_rate').val();



	    caculated_item_rate = parseFloat(parseFloat(sell_rate)*parseFloat(no_of_piece));



	    $("#caculated_item_rate").val(caculated_item_rate);



	    rate_with_mc = parseFloat(parseFloat(adjusted_item_rate) > 0 ? parseFloat(adjusted_item_rate) : caculated_item_rate );



	}



	else if(calculation_type == 4){



		var sell_rate  = (isNaN($('.sell_rate').val()) || $('.sell_rate').val() == '')  ? 0 : $('.sell_rate').val();



		var adjusted_item_rate  = (isNaN($('.adjusted_item_rate').val()) || $('.adjusted_item_rate').val() == '')  ? 0 : curRow.find('.adjusted_item_rate').val();



	    caculated_item_rate = (parseFloat(sell_rate)*parseFloat(net_wt));



	    $("#caculated_item_rate").val(caculated_item_rate);



	    rate_with_mc = parseFloat(parseFloat(adjusted_item_rate) > 0 ? parseFloat(adjusted_item_rate) : caculated_item_rate );



	}



	/*console.log('Calculation : '+calculation_type);



	console.log('Wastage : '+wast_wgt);



	console.log('Total Wastage : '+tot_wastage);



	console.log('MC : '+mc_type);



	console.log('Rate with MC : '+rate_with_mc);



	console.log(' MC TYPE : '+mc_type);



	console.log(' Rate Per Gram : '+rate_per_grm);*/



	// Tax Calculation



	$.each(tgi_calculation_type,function(ckey,citem)



	{



		if(citem==1) //Base value



		{



			$.each(tax_percentage,function(key,item){



				if(ckey==key)



				{



					base_value_tax+=(parseFloat(item));



				}



			});



			base_rate_tax	 = parseFloat(rate_with_mc*parseFloat(( base_value_tax / 100)));



			base_value_price = parseFloat(rate_with_mc +base_rate_tax).toFixed(2);



		}



		if(citem==2) //arrived value



		{



				$.each(tax_percentage,function(key,item){



				if(ckey==key)



				{



					arrived_value_tax+=(parseFloat(item));



				}



				});



			arrived_rate_tax    = parseFloat(parseFloat(base_value_price)*parseFloat(( arrived_value_tax / 100)));



			arrived_value_price = parseFloat(parseFloat(base_value_price)+arrived_rate_tax).toFixed(2);



		}



	});



	total_tax_rate=parseFloat(parseFloat(base_rate_tax)+parseFloat(arrived_rate_tax)).toFixed(2);



	total_tax_per=parseFloat(parseFloat(base_value_tax)+parseFloat(arrived_value_tax)).toFixed(2);



	total_price=parseFloat(arrived_value_price!=0 ? arrived_value_price:base_value_price).toFixed(2);



	$('#sales_value').val(Math.round(total_price));



	/*console.log('Amount : '+total_price);



	console.log('Tax Rate : '+total_tax_rate);



	console.log('Arrived value : '+arrived_value_price);



	console.log('*************************');*/



}



$('#update_tag').on('click',function(){



	$('#img_source').val(JSON.stringify(pre_img_resource));



	var tagging=$('#tagging').serialize();



	my_Date = new Date();



		$.ajax({



		url:base_url+ "index.php/admin_ret_tagging/updateTag?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),



		data:tagging,



		type:"POST",



        dataType:"JSON",



        success:function(data)



        {



        	$('#tagEditModal').modal('toggle');



        	//alert(data.msg);



        	window.location.reload();



            $('#tagEditModal .modal-body').find('#tagging_stone_details tbody').empty();



            $('#tagEditModal .modal-body').find('#preview').empty();



        	//get_tagging_list();



			$(".overlay").css("display", "none");



        },



        error:function(error)



        {



        }



    	});



});



$('#tagEditModal .modal-body #add_stone_details').on('click', function(){



if(validateStoneItemDetailRow()){



			create_new_empty_est_cat_stone_item();



		}else{



			alert("Please fill required fields");



		}



});



function validateStoneItemDetailRow(){



	var row_validate = true;



	$('#tagEditModal .modal-body #tagging_stone_details> tbody  > tr').each(function(index, tr) {



		if($(this).find('td:first .stone_id').val() == "" || $(this).find('td:eq(1) .stone_pcs').val() == "" || $(this).find('td:eq(2) .stone_wt').val() == "" ){



			row_validate = false;



		}



	});



	return row_validate;



}



function openStoneModal(){



    if(modalStoneDetail.length > 0){



        	$('#cus_stoneModal .modal-body').find('#estimation_stone_cus_item_details tbody').empty();



            $('#cus_stoneModal').modal('show');



        $.each(modalStoneDetail, function (key, item) {



	        console.log(item);



	        if(item){



                create_new_empty_stone_item(item);



	        }



        })



    }else{



		/*if($('#lot_bal_stone_wt').val() == 0 && $('#lot_bal_dia_wt').val() == 0)



		{



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br> There is no stone details "});



		}



        else if(current_po_details[0].stonedetail.length > 0)



		{*/



            	$('#cus_stoneModal .modal-body').find('#estimation_stone_cus_item_details tbody').empty();



                $('#cus_stoneModal').modal('show');



				if($('#cus_stoneModal tbody >tr').length == 0)



				{



					create_new_empty_stone_item();



				}



        //}



		/*else if($('#tag_lot_received_id option:selected').attr('data-lotfrom') == 2){



            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br> There is no stone details for this PO"});



        }*/



		else{



             $('#cus_stoneModal .modal-body').find('#estimation_stone_cus_item_details tbody').empty();



                $('#cus_stoneModal').modal('show');



            if($('#cus_stoneModal tbody >tr').length == 0)



            {



                 create_new_empty_stone_item();



            }



        }



    }



}



function create_new_empty_stone_item(stn_data=[])



{



        var row='';



    	var stones_list = "<option value=''> -Select Stone- </option>";



    	var stones_type = "<option value=''>-Stone Type-</option>";



    	var uom_list = "<option value=''>-UOM-</option>";



		var quality_list = "<option value=''>-Quality-</option>";



		var disable_quality = (stn_data ? (stn_data.stones_type == 1 ? '': 'disabled') : 'disabled');



		var clarity="";



        var color ="";



        var cut ="";



        var shape ="";



    	$.each(stones, function (pkey, pitem) {



    	    /*if($('#tag_lot_received_id option:selected').attr('data-lotfrom') == 2){



        	    $.each(current_po_details[0].stonedetail, function (spkey, spitem) {



        	        if(pitem.stone_id == spitem.po_stone_id){



        		        stones_list += "<option value='"+pitem.stone_id+"' "+(stn_data ? (pitem.stone_id == stn_data.stone_id ? 'selected' : '') : '')+">"+pitem.stone_name+"</option>";



        	        }



        	    });



    	    }else{



    	        stones_list += "<option value='"+pitem.stone_id+"' "+(stn_data ? (pitem.stone_id == stn_data.stone_id ? 'selected' : '') : '')+">"+pitem.stone_name+"</option>";



    	    }*/



			stones_list += "<option value='"+pitem.stone_id+"' "+(stn_data ? (pitem.stone_id == stn_data.stone_id ? 'selected' : '') : '')+">"+pitem.stone_name+"</option>";



    	});



    	$.each(uom_details, function (pkey, pitem) {



    		uom_list += "<option value='"+pitem.uom_id+"' "+(stn_data ? (pitem.uom_id == stn_data.stone_uom_id ? 'selected' : '') : '')+">"+pitem.uom_name+"</option>";



    	});



    	$.each(stone_types, function (pkey, pitem) {



    		stones_type += "<option value='"+pitem.id_stone_type+"' "+(stn_data ? (pitem.id_stone_type == stn_data.stones_type ? 'selected' : '') : '')+">"+pitem.stone_type+"</option>";



    	});



		$.each(quality_code, function (pkey, pitem) {



            quality_list += "<option value='"+pitem.quality_id+"' "+(stn_data ? (pitem.quality_id == stn_data.stone_quality_id ? 'selected' : '') : '')+" >"+pitem.code+"</option>";



            if(pitem.quality_id == stn_data.stone_quality_id)



            {



                clarity=pitem.clarity;



                color=pitem.color;



                cut=pitem.cut;



                shape=pitem.shape;



            }



        });



    	var show_in_lwt = (stn_data ? stn_data.show_in_lwt : '');



    	var stone_pcs = (stn_data ? (stn_data.stone_pcs == undefined ? '':stn_data.stone_pcs) : '');



    	var stone_wt = (stn_data ? (stn_data.stone_wt == undefined ? '':stn_data.stone_wt) : '');


		var pur_rate = (stn_data ? (stn_data.stone_rate == undefined ? 0:stn_data.stn_pur_rate): 0);


    	var rate = (stn_data ? (stn_data.stone_rate == undefined ? 0:stn_data.stone_rate): 0);



    	var price = (stn_data ? (stn_data.stone_price == undefined ? 0:stn_data.stone_price) : 0);



    	var cal_type = (stn_data ? (stn_data.stone_cal_type == undefined ? 1:stn_data.stone_cal_type) : 1);



    	var row_cls = $('#estimation_stone_cus_item_details tbody tr').length;



		row='<tr id="'+$('#estimation_stone_cus_item_details tbody tr').length+'" class="st_'+$('#estimation_stone_cus_item_details tbody tr').length+'">'



			+'<td><select class="show_in_lwt form-control" name="est_stones_item[show_in_lwt][]" style="width:70px;"><option value="">-Select-</option><option value=1 '+(show_in_lwt==1 || show_in_lwt == undefined ? 'selected' :'')+'>Yes</option><option value=0 '+(show_in_lwt==0 ? 'selected' :'')+'>No</option></select></td>'



			+'<td><select class="stones_type form-control" name="est_stones_item[stones_type][]" style="width:80px;">'+stones_type+'</select></td>'



			+'<td><select class="stone_id form-control" name="est_stones_item[stone_id][]" style="width:80px;">'+stones_list+'</select><input type="hidden" class="stone_type" value=""></td>'



			+'<td><select class="quality_id form-control" name="est_stones_item[quality_id][]" '+disable_quality+' style="width:80px;">'+quality_list+'</select></td>'



			+'<td><input type="number" class="stone_pcs form-control" name="est_stones_item[stone_pcs][]" value="'+stone_pcs+'" style="width: 70px;"/></td>'



			+'<td><div class="input-group" style="width:159px;"><input class="stone_wt form-control" type="number" name="est_stones_item[stone_wt][]" value="'+stone_wt+'" style="width: 78px;"/><span class="input-group-btn" style="width: 138px;"><select class="stone_uom_id form-control" name="est_stones_item[uom_id][]">'+uom_list+'</select></span></div></td>'



			+'<td><div class="form-group" style="width: 100px;"><input class="stone_cal_type" type="radio" name="est_stones_item[cal_type]['+parseFloat(row_cls+1)+']" value="1" '+(cal_type == 1 ? 'checked' : '')+'>Wt&nbsp;<input type="radio" name="est_stones_item[cal_type]['+parseFloat(row_cls+1)+']" class="stone_cal_type" value="2" '+(cal_type == 2 ? 'checked' : '')+'>Pcs</div></td>'



			+'<td><span class="stone_cut">'+cut+'</span></td>'



			+'<td><span class="stone_color">'+color+'</span></td>'



			+'<td><span class="stone_clarity">'+clarity+'</span></td>'



			+'<td><span class="stone_shape">'+shape+'</span></td>'


			+'<td><input type="number" class="pur_stone_rate form-control" name="est_stones_item[pur_stone_rate][]" value="'+pur_rate+'" style="width: 100px"/></td>'


			+'<td><input type="number" class="stone_rate form-control" name="est_stones_item[stone_rate][]" value="'+rate+'" style="width: 100px"/></td>'



			+'<td><input type="number" class="stone_price form-control" name="est_stones_item[stone_price][]" value="'+price+'" style="width: 100px"/></td>'



			+'<td style="width: 100px;"><button type="button" class="btn btn-success btn-xs create_stone_item_details"><i class="fa fa-plus"></i></button><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-xs btn-del"><i class="fa fa-trash"></i></a></td></tr>';



		$('#cus_stoneModal .modal-body').find('#estimation_stone_cus_item_details tbody').append(row);



		$("#cus_stoneModal").on('shown.bs.modal', function(){



			$(this).find('.stones_type').focus();



		});



    	$('#custom_active_id').val("st_" + row_cls);



}



function create_new_empty_est_cat_stone_item()



{



			var row='';



			var stones_list = "<option value=''>-Select Stone-</option>";



			$.each(stones, function (pkey, pitem) {



				stones_list += "<option value='"+pitem.stone_id+"'>"+pitem.stone_name+"</option>";



			});



			row += '<tr><td><select class="stone_id" name="est_stones_item[stone_id][]">'+stones_list+'</select></td><td><input type="number" class="stone_pcs" name="est_stones_item[stone_pcs][]" value="" style="width:80px;"/></td><td><input class="stone_wt" type="number" name="est_stones_item[stone_wt][]" value="" style="width:80px;"/></td><td><input type="number" class="stone_price" name="est_stones_item[stone_price][]" value="" /></td><td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td></tr>';



	$('#tagEditModal .modal-body').find('#tagging_stone_details tbody').append(row);



}



$(document).on('keyup','.stone_price,.stone_pcs',function(e){



		calculateTagEditSaleValue();



});



/*$(document).on('change','.stone_wt,.stone_uom_id',function()



{



	var row = $(this).closest('tr');



	var tr_id = $(this).closest('tr').attr('id');



	calculateBalanceStones(row,tr_id);



});*/



$(document).on('change','.stone_rate',function()



{



	var row = $(this).closest('tr');



	if( (ctrl_page[1]=='tagging' && ctrl_page[2]=='add') || ctrl_page[2]=='bulk_edit')



	{



		check_min_max_stone_rate(row);



	}



});



function check_min_max_stone_rate(curRow)



{



	var stone_rate = curRow.find('.stone_rate').val();



	$.each(stone_rate_settings,function(key,items)



	{



		var stone_centwt = 0;



		if(curRow.find('.stones_type').val()==1)



		{



			var stone_pcs  = (isNaN(curRow.find('.stone_pcs').val()) || curRow.find('.stone_pcs').val() == '')  ? 0 : parseInt(curRow.find('.stone_pcs').val());



			var stone_wt  = (isNaN(curRow.find('.stone_wt').val()) || curRow.find('.stone_wt').val() == '')  ? 0 : parseFloat(curRow.find('.stone_wt').val());



			stone_centwt = parseFloat(((stone_wt)/(stone_pcs))*100).toFixed(3);



		}



		if($('#branch_select').val() == items.id_branch && curRow.find('.stones_type').val()==items.stone_type && curRow.find('.stone_id').val()==items.stone_id && curRow.find('.quality_id').val() == items.quality_id && curRow.find('.stone_uom_id').val()==items.uom_id)



		{



			if(stone_centwt > 0)



			{



				if(stone_centwt >= parseFloat(items.from_cent) && stone_centwt <= parseFloat(items.to_cent))



				{



					if(curRow.find('.stone_rate').val()>=parseFloat(items.min_rate) && curRow.find('.stone_rate').val()<=parseFloat(items.max_rate))



					{



						curRow.find('.stone_rate').val(stone_rate);



					}



					else



					{



						curRow.find('.stone_rate').val(items.max_rate);



						$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Entered Stone Rate Must be Within '+items.min_rate+' and '+items.max_rate+' !'});



					}



				}



			}



			else if(curRow.find('.stone_rate').val()>=parseFloat(items.min_rate) && curRow.find('.stone_rate').val()<=parseFloat(items.max_rate))



			{



				curRow.find('.stone_rate').val(stone_rate);



			}



			else



			{



				curRow.find('.stone_rate').val(items.max_rate);



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Entered Stone Rate Must be Within '+items.min_rate+' and '+items.max_rate+' !'});



			}



		}



	});



	calculate_stone_amount();



}



function calculateBalanceStones(row,tr_id)



{



	var uom_id      =  row.find('.stone_uom_id ').val();



	var stone_wt    =  row.find('.stone_wt').val();



	var bal_stn_wt  =  $('#lot_bal_stone_wt').val();



	var bal_dia_wt  =  $('#lot_bal_dia_wt').val();



	var tot_stn_wt = 0;



	var tot_dia_wt = 0;



	console.log('tr_id',tr_id);



	$('#estimation_stone_cus_item_details > tbody  > tr').each(function(index, tr)



	{



		var prev_row = $(this);



		var table_id = $(this).attr('id');



		console.log('table_id',table_id);



		if(prev_row.find('.stone_uom_id').val() == 1)



		{



			if(table_id!=tr_id)



			{



				tot_stn_wt = tot_stn_wt + (parseFloat(prev_row.find('.stone_wt').val()));



			}



		}



		else



		{



			if(table_id!=tr_id)



			{



				tot_dia_wt = tot_dia_wt + (parseFloat(prev_row.find('.stone_wt').val()));



			}



		}



	});



	var tot_bal_stn_wt = parseFloat(parseFloat(bal_stn_wt) - parseFloat(tot_stn_wt)).toFixed(3);



	var tot_bal_dia_wt = parseFloat(parseFloat(bal_dia_wt) - parseFloat(tot_dia_wt)).toFixed(3);



	console.log('tot_bal_stn_wt'+':',tot_bal_stn_wt);



	console.log('tot_bal_dia_wt'+':',tot_bal_dia_wt);



	if(uom_id==1)



	{



		if(parseFloat(stone_wt) > parseFloat(tot_bal_stn_wt))



		{



			row.find('.stone_wt').val('');



			row.find('.stone_wt').focus();



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br> Balance Stone Weight is : "+tot_bal_stn_wt+""});



		}



	}



	else



	{



		if(parseFloat(stone_wt) > parseFloat(tot_bal_dia_wt))



		{



			row.find('.stone_wt').val('');



			row.find('.stone_wt').focus();



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br> Balance Diamond Weight is : "+tot_bal_dia_wt+""});



		}



	}



}



/*function calculateBalanceStones(row)



{



	var uom_id      =  row.find('.stone_uom_id ').val();



	var stone_wt    =  row.find('.stone_wt').val();



	var bal_stn_wt  =  $('#lot_bal_stone_wt').val();



	var bal_dia_wt  =  $('#lot_bal_dia_wt').val();



	if(uom_id==1)



	{



		if(parseFloat(stone_wt) > parseFloat(bal_stn_wt))



		{



			row.find('.stone_wt').val('');



			row.find('.stone_wt').focus();



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br> Balance Stone Weight is : "+bal_stn_wt+""});



		}



	}



	else



	{



		if(parseFloat(stone_wt) > parseFloat(bal_dia_wt))



		{



			row.find('.stone_wt').val('');



			row.find('.stone_wt').focus();



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br> Balance Diamond Weight is : "+bal_dia_wt+""});



		}



	}



}*/



$(document).on('click','.btn-del',function(e){



		//calculateTagEditSaleValue();



});



 //Update Tag list



 //duplicate print



function get_ActiveProduct()



{



	$('#prod_select option').remove();



	$("div.overlay").css("display", "block");



	$.ajax({



	type: 'GET',



	url: base_url+'index.php/admin_ret_reports/get_ActiveProduct',



	dataType:'json',



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



function get_ActiveNontagProduct()

{

	$('#prod_select option').remove();

	$('#des_select option').remove();

	$('#sub_des_select option').remove();

	$("div.overlay").css("display", "block");

	$.ajax({

	type: 'GET',

	url: base_url+'index.php/admin_ret_reports/get_ActiveNontagProduct',

	dataType:'json',

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



function get_ActiveSize()



{



	$('#select_size option').remove();

	$('#tag_size option').remove();



	$("div.overlay").css("display", "block");



	$.ajax({



	type: 'POST',



	data :{'id_product':$('#tag_lt_prod').val()}, //tag_lt_prod From Tagging Form



	url: base_url+'index.php/admin_ret_tagging/get_ActiveSize',



	dataType:'json',



	success:function(data){



		var id =  $("#tag_size").val();



		var tag_add_size =  $("#id_size").val();



		$.each(data, function (key, item) {



		    $("#tag_size").append(



		    $("<option></option>")



		    .attr("value", item.id_size)



		    .text(item.value+'-'+item.name)



		    );



		});



		if($("#select_size").length == 1){



		    var id =  $("#select_size").val();



		    $.each(data, function (key, item) {



		        if(item.id_product == $("#prod_select").val()){



        		    $("#select_size").append(



        		    $("<option></option>")



        		    .attr("value", item.id_size)



        		    .text(item.value+'-'+item.name)



        		    );



		        }



    		});



    		$("#select_size").select2(



    		{



    			placeholder:"Select Size",



    			allowClear: true



    		});



		    $("#select_size").select2("val",(id!='' && id>0?id:''));



		}



		$("#tag_size").select2(



		{



			placeholder:"Select Size",



			allowClear: true



		});



		    $("#tag_size").select2("val",(tag_add_size!='' && tag_add_size>0?tag_add_size:''));



		}



	});



	$("div.overlay").css("display", "none");



}



$('#prod_select').on('change',function(){



	if(this.value!='')



	{



	    if(ctrl_page[1]!='retagging' ||ctrl_page[2]!='tag_mark')



	    {



	        get_Activedesign(this.value);



    		if(ctrl_page[2]=='tag_edit')



    		{



    		    get_ActiveSize();



    		}



    		if(ctrl_page[2] == 'duplicate_print')



    		{



        		if(this.value!=''){



        			$('#id_product').val(this.value);



        		}



        		else{



        			$('#id_product').val('');



        		}



        	}



	    }else{



	        get_active_design_products(this.value);



	    }



	}else{



		get_Activedesign('');



	}



});







function get_active_design_products(id_product)



{



    $('#des_select_filter option').remove();



	$('#des_select option').remove();



	$('#sub_des_select option').remove();



	$.ajax({



	type: 'POST',



	url: base_url+'index.php/admin_ret_reports/get_Activedesign',



	data : { 'id_product' : id_product},



	dataType:'json',



	success:function(data){



	    pro_designs = data;



		var id =  $("#id_design").val()!=undefined?$("#id_design").val():'';



		$.each(data, function (key, item) {



		    $("#des_select,#des_select_filter").append(



		    $("<option></option>")



		    .attr("value", item.design_no)



		    .text(item.design_name)



		    );



		});



		$("#des_select,#des_select_filter").select2(



		{



			placeholder:"Select Design",



			allowClear: true



		});



		 $("#des_select").select2("val",(id!='' && id>0?id:''));



		}



	});



}











function get_Activedesign(id_product)



{



	if($('#des_select_filter').length>0) {



    	$('#des_select_filter option').remove();



	}



	if($('#des_select').length>0) {



		$('#des_select option').remove();



	}



	if($('#bulkedit_des_update').length>0) {



		$('#bulkedit_des_update option').remove();



	}



	if($('#sub_des_select').length>0) {



		$('#sub_des_select option').remove();



		$('#sub_des_select').select2("val",'');



	}



	if($('#bulkedit_sub_des_update').length>0) {



		$('#bulkedit_sub_des_update option').remove();



		$('#bulkedit_sub_des_update').select2("val",'');



	}



	$.ajax({



	type: 'POST',



	url: base_url+'index.php/admin_ret_tagging/get_active_design_products',



	data : { 'id_product' : id_product,'id_lot_inward_detail':$('#tag_lt_prod option:selected').attr('data-id_lot_inward_detail'), 'lot_from' : $('#tag_lot_received_id option:selected').attr('data-lotfrom'),  'id_lot_no' : $('#tag_lot_received_id').val() },



	dataType:'json',



	success:function(data){



	    pro_designs = data;



		var id =  $("#id_design").val();



		$.each(data, function (key, item) {



		    $("#des_select,#des_select_filter,#bulkedit_des_update").append(



		    $("<option></option>")



		    .attr("value", item.design_no)



		    .text(item.design_name)



		    );



		});



		$("#des_select,#des_select_filter,#bulkedit_des_update").select2(



		{



			placeholder:"Select Design",



			allowClear: true



		});



		    if(ctrl_page[2]=='duplicate_print')



		    {



		        $("#des_select").select2("val",'');



		    }else{



		        $("#des_select").select2("val",(id!='' && id>0?id:''));



		    }



		    if($('#des_select_filter').length>0)



		    {



		        $("#des_select_filter").select2("val",'');



		    }



			if($('#bulkedit_des_update').length > 0)



			{



				$('#bulkedit_des_update').select2("val",'');



			}



		}



	});



}



$('#get_duplicate_tag').on('click',function(){



	if($('#id_branch').val()=='' || $('#id_branch').val()==null || $('#id_branch').val()==undefined)


    {



		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Select The Branch..' });



		return false;



	}



	  else if (($('#prod_select').val()=='' || $('#prod_select').val()==null || $('#prod_select').val()==undefined) && ($('#tag_lot_id').val()=='' || $('#tag_lot_id').val()==null || $('#tag_lot_id').val()==undefined)) {

        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Lot ID or Product Is Required..' });

		return false;



	}
	





	else



	{



	    $('#tagging_list tbody').empty();



	    $("div.overlay").css("display", "block");



        var tag_lot_id = $('#tag_lot_id').val();



        var from_weight=$('#from_weight').val();



        var to_weight=$('#to_weight').val();



        my_Date = new Date();
		var FromDt = $('#rpt_from_date').html();
		var ToDt = $('#rpt_to_date').html();



        $.ajax({



            url: base_url+'index.php/admin_ret_tagging/get_duplicate_tag/?nocache=' + my_Date.getUTCSeconds(),



            dataType: "json",



            method: "POST",



            data: { 'FromDt': FromDt, 'ToDt': $('#rpt_to_date').html() ,'tag_lot_id':tag_lot_id,'id_branch':$('#id_branch').val(),'tag_id':$('#tag_id').val(),'id_product':$('#prod_select').val(),'des_select':$('#des_select').val(),'sub_des_select':$('#sub_des_select').val(),'from_weight':from_weight,'to_weight':to_weight,'tag_code':$('#tag_no').val(),'old_tag_code':$('#old_tag_no').val()},



            success: function ( data ) {



                $('#tag_no').val('');



            $.each(data,function(key,item){



            dublicate_tag_details.push({'tag_id':item.tag_id,'old_tag_id':item.old_tag_id,'tag_code':item.tag_code,'tag_lot_id':item.tag_lot_id,'product_name':item.product_name,'design_name':item.design_name,'gross_wt':item.gross_wt,'net_wt':item.net_wt,'sub_design_name':item.sub_design_name});



            });



            set_duplicate_tag_print();



            }



        });





	}



});



$('#add_tag').on('click',function(){



    $("div.overlay").css("display", "block");



		my_Date = new Date();



			$.ajax({



				url: base_url+'index.php/admin_ret_tagging/get_duplicate_tag/?nocache=' + my_Date.getUTCSeconds(),



				dataType: "json",



				method: "POST",



				data: {'id_branch':$('#id_branch').val(),'tag_id':$('#tag_id').val(),'id_product':$('#prod_select').val(),'des_select':$('#des_select').val(),'sub_des_select':$('#sub_des_select').val(),},



				success: function ( data ) {



				   $.each(data,function(key,item){



				        dublicate_tag_details.push({'tag_id':item.tag_id,'tag_code':item.tag_code,'tag_lot_id':item.tag_lot_id,'product_name':item.product_name,'design_name':item.design_name,'gross_wt':item.gross_wt,'net_wt':item.net_wt,'sub_design_name':item.sub_design_name});



				    });



					set_duplicate_tag_print();



				}



			 });



	$("div.overlay").css("display", "none");



});



function set_duplicate_tag_print()



{



	$("div.overlay").css("display", "block");



    var row ='';



    $.each(dublicate_tag_details,function(key,item){



    var rowExist = false;



    $('#tagging_list > tbody tr').each(function(bidx, trow){



        tag_det = $(this);



        if(tag_det.find('.tag_id').val() != '')



        {



            if(item.tag_id==tag_det.find('.tag_id').val())



            {



                rowExist = true;



            }



        }



    });



        if(!rowExist)



        {



             row = '<tr id="'+key+'">'



                +'<td><input type="checkbox" class="tag_id" name="tag_id[]" value="'+item.tag_id+'"/>'+item.tag_id+'</td>'



				+'<td><input type="hidden" class="tag_id" name="tag_id[]" value="'+item.tag_id+'"><input type="hidden" class="tag_code" name="tag_code[]" value="'+item.tag_code+'">'+item.tag_code+'</td>'



				+'<td class="align_left"><input type="hidden" class="old_tag_id" name="old_tag_code[]" value="'+item.old_tag_id+'">'+(item.old_tag_id ? '<input type="hidden" class="old_tag_code" name="old_tag_code[]" value="'+item.old_tag_id+'">'+item.old_tag_id : '') +'</td>'



                +'<td class="align_left">'+item.tag_lot_id+'</td>'



                +'<td class="align_left">'+item.product_name+'</td>'



                +'<td class="align_left">'+item.design_name+'</td>'



                +'<td class="align_left">'+item.sub_design_name+'</td>'



                +'<td class="align_right">'+money_format_india(item.gross_wt)+'</td>'



                +'<td class="align_right">'+money_format_india(item.net_wt)+'</td>'



                $('#tagging_list  tbody').append(row);



        }



    });

	$("div.overlay").css("display", "none");

    dublicate_tag_details=[];



}



$('.duplicate_print').on('click',function(){



	if($("input[name='tag_id[]']:checked").val())



	{



		send_otp();



	}else{



		alert('Please Select Atleast Any One Tag');



	}



});



$('#tagResend').on('click',function(){



	send_otp();



});



function send_otp()



{



	$('#tagResend').css('display','none');



	my_Date = new Date();



	$.ajax({



			 url:base_url+ "index.php/admin_ret_tagging/send_tag_otp?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),



			 type:"POST",



			 dataType: "json",



			 async:false,



			 	  success:function(data){



			 	  	if(data.status)



			 	  	{



			 	  		if(data.status)



			 	  		{



			 	  		if(data.sms_req==1)



			 	  		    {



                                $('#otp_modal').modal('show');



                                setTimeout(function() {



                                $('#tagResend').css('display','block');



                                },60000);



			 	  		    }



			 	  		    else



			 	  		    {



    	 	  		            var tag_id='';

								var branchId = $('#id_branch').val();

		 	  		        	$("#tagging_list tbody tr").each(function(index, value){



                    				if($(value).find("input[name='tag_id[]']:checked").is(":checked"))



                    				{



                        			    tag_id+= $(value).find(".tag_id").val()+',';
										dupPrintLog.push({
											
											'tag_id' : $(this).find(".tag_id").val(),
											
											'printer_selected' : $(this).find(".printer_name").val(),

											'branchId' : branchId,

										});




                    				}



                				});



                				req_data = tag_id;



                				dupPrintLog = dupPrintLog;

                				tagging_print(req_data, dupPrintLog);



			 	  		    }



			 	  		}else{



			 	  			alert(data.msg);



			 	  		}



			 	  	}



				  },



				  error:function(error)



				  {



					 $(".overlay").css('display',"none");



				  }



		  });



}



$('#verify_otp').on('click',function(){



    if(this.value!='')



    {



        my_Date = new Date();



    	$.ajax({



    		 url:base_url+ "index.php/admin_ret_tagging/verify_otp?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),



    		 type:"POST",



    		 data :{'otp':$('#otp').val()},



    		 dataType: "json",



    	 	  success:function(data)



    	 	  {



    	 	  	if(data.status)



    	 	  	{



    	 	  		var selected = [];



    	 	  		var tag_id='';



    				$("#tagging_list tbody tr").each(function(index, value){



    				if($(value).find("input[name='tag_id[]']:checked").is(":checked"))



    				{



    			    tag_id+= $(value).find(".tag_id").val()+',';



    				transData = {



    				'tag_id': $(value).find(".tag_id").val(),



    				'net_wt': $(value).find(".net_wt").val(),



    				'product_name': $(value).find(".product_name").val(),



    				'size': $(value).find(".size").val(),



    				'tag_code': $(value).find(".tag_code").val(),



    				'product_id': $(value).find(".product_id").val(),



    				'short_code': $(value).find(".short_code").val(),



    				'code_karigar': $(value).find(".code_karigar").val(),



    				'tot_print_taken': $(value).find(".tot_print_taken").val(),



    				}



    				selected.push(transData);



    				}



    				});



    				req_data = tag_id;



    				tagging_print(req_data);



    	 	  	}else{



    	 	  		$('#otp').val('');



    	 	  		alert(data.msg);



    	 	  	}



    		  },



    		  });



    }else{



        alert('Please Enter The OTP');



    }



});



//duplicate print



function printBTtagData() {



	if(ctrl_page[2] != ''){



		window.open( base_url+'index.php/admin_ret_brntransfer/branch_transfer/print/'+ctrl_page[2]+'/1'+'/'+'1','_blank');



	}



	//window.location.replace( base_url+'index.php/admin_ret_tagging/tagging/list');



}



   	/*$('#tag_id').blur(function(e){



		var row = $(this).closest('tr');



		var input =this.value;



		var tag_id=input.split('/')[0];



		var print_taken=input.split('/')[1];



		console.log($(this));



		setTimeout(function () {



			get_tag_scan_details(tag_id,print_taken,row);



		}, 100);



	});*/



   $(document).bind('paste',"#tag_id", function(e){



		var row = $(this).closest('tr');



	    console.log($(this));



		var input = $(this).val();



		var tag_id=input.split('/')[0];



		var print_taken=input.split('/')[1];



		setTimeout(function () {



			$('#tag_id').trigger('blur');



			get_tag_scan_details();



		}, 100);



	});



function get_tag_scan_details()



{



    var input=$('#tag_id').val();



    var tag_id=input.split('/')[0];



    var print_taken=input.split('/')[1];



    my_Date = new Date();



    $.ajax({



        url: base_url+'index.php/admin_ret_tagging/get_tag_scan_details/?nocache=' + my_Date.getUTCSeconds(),



        dataType: "json",



        method: "POST",



        data: {'tag_id': tag_id, 'print_taken':print_taken},



        success: function (data) {



            if(data.status)



            {



                set_tag_scanned_list(data.tag_details);



            }else{



                $('#tag_id').val('');



                alert(data.msg);



                $("div.overlay").css("display", "none");



            }



        }



    });



}



function set_tag_scanned_list(tag_details)



{



	$("div.overlay").css("display", "block");



	var row='';



	row='<tr><td>'+tag_details.tag_code+'</td><td>'+tag_details.tag_lot_id+'</td><td>'+tag_details.gross_wt+'</td><td>'+tag_details.net_wt+'</td><td>'+(tag_details.less_wt!=null ? tag_details.less_wt:'-')+'</td></tr>'



	$('#tagging_scan_list tbody').append(row);



	$("div.overlay").css("display", "none");



}







$('#metal').on('change',function(e){



	if(this.value!='')

	{



	get_category(this.value);



	}



});





$('#category').on('change',function(e){



	if(this.value!='')

	{



	get_category_product(this.value);



	}



});



$('#tag_mark_search').on('click',function(){



	set_tag_marking();



});



function set_tag_marking()



{



	$("div.overlay").css("display", "block");



	my_Date = new Date();



	$.ajax({



		url: base_url+"index.php/admin_ret_tagging/get_tag_marking/ajax?nocache=" + my_Date.getUTCSeconds(),



		type:"POST",



		data:{'id_branch':$("#id_branch").val(),'id_product':$("#prod_select").val(),'est_no':$('#est_no').val(),'filter_by':$('#filter_by').val(),'id_category':$('#category').val(),'id_metal':$('#metal').val()},



		dataType: 'json',



		cache:false,



		success:function(data){



			if(data.status)



			{



			    var list=data.data;



    			 var oTable = $('#tagging_list').DataTable();



    			 oTable.clear().draw();



    			 if (list!= null && list.length > 0)



    			 {



    				oTable = $('#tagging_list').dataTable({



    						"bDestroy": true,



    						"bInfo": true,



    						"bFilter": true,



    						"bSort": true,



							"columnDefs": [

								{

									targets: [5,6],

									className: 'dt-body-right'

								},

							],



    						"order": [[ 0, "desc" ]],



    						"aaData"  : list,



    						"lengthMenu": [[-1, 25, 50, 100, 250], ["All" ,25, 50, 100, 10]],



    						"aoColumns": [



    					    { "mDataProp": function ( row, type, val, meta )



                            {



                                 if(row.status==0)



                                 {



                                     return '<input type="checkbox" class="tag_id" name="tag_id[]" value="'+row.tag_id+'"/>'+row.tag_code;



                                 }else{



                                    return row.tag_code;



                                 }



                            }},



							{ "mDataProp": "metal" },



							{ "mDataProp": "category_name" },



    						{ "mDataProp": "product_name" },



    						{ "mDataProp": "tag_date" },



    						{ "mDataProp": "gross_wt" },



    						{ "mDataProp": "net_wt" },



    						{ "mDataProp": function ( row, type, val, meta )



							{



							    if(row.status==1)



							    {



							        return '<span class="badge bg-green">'+row.tag_status+'</span>';



							    }else



							    {



							        return '<span class="badge bg-red">'+row.tag_status+'</span>';



							    }

    		                }},



    						{ "mDataProp": "tag_mark" },

							{"mDataProp": "green_tag_date" },

							{"mDataProp": "emp_name" },



    						]



    					});



    				}



		    }



		    else{



		        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message});



		    }



			$("div.overlay").css("display", "none");



		},



		 error:function(error)



		  {



			 $("div.overlay").css("display", "none");



		  }



	});



}



$('#tag_edit_filter').on('click',function(){



    if($('#branch_select').val()=='' || $('#branch_select').val()==null)



    {



        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Branch..'});



    }else{



        get_tag_edit_det();



    }



});



$("input[name='upd_status_btn']:radio").change(function(){



    if($("input[name='tag_id[]']:checked").val())



        {



            $('#set_green_tag').prop('disabled',true);



            var selected = [];



            var approve=false;



            $("#tagging_list tbody tr").each(function(index, value)



            {



                if($(value).find("input[name='tag_id[]']:checked").is(":checked"))



                {



                    transData = {



                    'tag_id'            : $(value).find(".tag_id").val(),



                    "req_status"        : $("input[name='upd_status_btn']:checked").val(),



                    }



                    selected.push(transData);



                }



            })



            req_data = selected;



            update_green_tag(req_data);



        }else{



            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Any One Tag..'});



        }



});



function update_green_tag(req_data)



{



    my_Date = new Date();



    $("div.overlay").css("display", "block");



    $.ajax({



    url:base_url+ "index.php/admin_ret_reports/update_green_tag?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),



    data:  {'req_data':req_data},



    type:"POST",



    async:false,



    success:function(data){



    $('#set_green_tag').prop('disabled',false);



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



function get_tag_edit_det()



{



    	$("div.overlay").css("display", "block");



    	my_Date = new Date();



    	$.ajax({



		url: base_url+"index.php/admin_ret_tagging/get_tag_edit_det/ajax?nocache=" + my_Date.getUTCSeconds(),



		type:"POST",



		data:{'lot_id':$("#tag_edit_lot").val(),'id_branch':$("#branch_select").val(),'id_product':$('#prod_select').val(),'id_design':$('#des_select_filter').val(),'id_sub_design':$('#sub_des_filter').val(),'tag_code':$('#tag_code').val(),'est_no':$('#est_no').val()},



		dataType: 'json',



		cache:false,



		success:function(data){



			 $("div.overlay").css("display","block");



			 rowExist = false;



			 var trHtml='';



			 if(data.length>0)



			 {



    			 $.each(data,function(key,row){



    			        $('#tagging_list > tbody tr').each(function(bidx, brow){



    			            curRow = $(this);



    			            if(curRow.find('.tag_id').val()!='')



    			            {



    			                if(curRow.find('.tag_id').val()==row.tag_id)



    			                {



    			                    rowExist = true;



    					            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'TAG NO Already Exists..'});



    			                }



    			            }



    			        });



    			        if(!rowExist)



    			        {



    			              trHtml+='<tr>'



    			                +'<td><input type="checkbox" class="tag_id" name="tag_id[]" value="'+row.tag_id+'"/><input type="hidden" class="design_id" name="design_id[]" value="'+row.design_id+'"/><input type="hidden" class="id_sub_design" name="id_sub_design[]" value="'+row.id_sub_design+'"/><input type="hidden" class="size" name="size[]" value="'+row.size+'"/><input type="hidden" class="old_tag_id" name="old_tag_id[]" value="'+row.old_tag_id+'"/>'+row.tag_code+'</td>'



    			                +'<td>'+row.tag_lot_id+'</td>'



    			                +'<td>'+row.product_name+'</td>'



    			                +'<td>'+row.design_name+'</td>'



    			                +'<td>'+row.sub_design_name+'</td>'



    			                +'<td>'+row.gross_wt+'</td>'



    			                +'<td>'+row.net_wt+'</td>'



    			                +'<td>'+row.size_name+'</td>'



    			                +'<td>'+row.old_tag_id+'</td>'



    			             +'</tr>';



    			        }



    			 });



    			 if($('#tagging_list > tbody  > tr').length>0)



            	{



            	    $('#tagging_list > tbody > tr:first').before(trHtml);



            	}else{



            	    $('#tagging_list tbody').append(trHtml);



            	}



            	$('#est_no').val('');



            	$('#tag_code').val('');



		    }else{



		        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Record Found..'});



		    }



				$("div.overlay").css("display", "none");



		},



		 error:function(error)



		  {



			 $("div.overlay").css("display", "none");



		  }



	});



}



function get_tag_edit_lots(){



	$.ajax({



	 	type: 'GET',



	 	url : base_url + 'index.php/admin_ret_tagging/get_lot_ids',



	 	dataType : 'json',



	 	success  : function(data){



		 	var id =  $('#tag_lot_id').val();



		 	console.log(id);



	     	$.each(data.lot_inward, function (key, item) {



    		 		$("#tag_edit_lot").append(



    	    	 	$("<option></option>")



    	    	 	.attr("value", item.lot_no)



    	    	 	.attr("data-lotfrom", item.lot_from)



    	    	 	.text(item.lot_no)



    	    	 	);



	     	});



	     	$("#tag_edit_lot").select2({



        	 	placeholder: "Select Lot No",



        	 	allowClear: true



         	});



	     	$("#tag_edit_lot").select2("val",(id!='' && id>0?id:''));



	     	$(".overlay").css("display", "none");



	 	}



	});



}



     $("#update_tag_edit").on('click',function(){



        if($("input[name='tag_id[]']:checked").val())



        {



            /*if($('#prod_select').val()!='' && $('#prod_select').val()!=null && $('#prod_select').val()!=undefined)



            {*/



                    var selected = [];



                    var approve=false;



                    $("#tagging_list tbody tr").each(function(index, value)



                    {



                    if($(value).find("input[name='tag_id[]']:checked").is(":checked"))



                    {



                    transData = {



                    'tag_id'            : $(value).find(".tag_id").val(),



                    'id_design'         : $(value).find(".design_id").val(),



                    'id_size'           : $(value).find(".size").val(),



                    'id_sub_design'     : $(value).find(".id_sub_design").val(),



                    'old_tag_id'        : $(value).find(".old_tag_id").val()



                    }



                    selected.push(transData);



                    }



                    })



                    req_data = selected;



                    console.log(req_data);



                    update_tag(req_data);



           /* }else{



                alert('Please Select Product');



                return false;



            }*/



        }else{



            alert('Please Select Any One Tag.');



        }



    });



    function update_tag(data="")



    {



        my_Date = new Date();



        $("div.overlay").css("display", "block");



        $.ajax({



        url:base_url+ "index.php/admin_ret_tagging/update_tag?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),



        data:  {'id_design':$('#des_select').val(),'id_size':$('#select_size').val(),'id_sub_design':$('#sub_des_select').val(),'old_tag_id':$('#old_tag_id').val(),'req_data':data},



        type:"POST",



        async:false,



        success:function(data){



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



    function get_branchwise_emp()



	{



		my_Date = new Date();



		$.ajax({



		url:base_url+ "index.php/admin_ret_tagging/get_employee?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),



        type:"GET",



        dataType:"JSON",



        success:function(data)



        {



           var id_employee=$('#id_employee').val();



           emp_details=data;



           $.each(data, function (key, item) {



                	 	$("#emp_select").append(



                	 	$("<option></option>")



                	 	.attr("value", item.id_employee)



                	 	.text(item.firstname)



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



$("#add_multiple_tag").on('click', function () {



    var rows = $('#row_value').val();



    console.log(rows);



    var lastRow;



    for (var i = 0; i < rows; i++) {



        lastRow = $('#lt_item_list tr').last().clone();



        console.log(lastRow.find('.lot_id_design').val());



        set_multiple_rows($('#tag_lot_id').val(),$('#tag_lt_prodId').val(),lastRow.find('.gross_wt').val(),lastRow.find('.lot_id_design').val(),lastRow.find('.cus_design ').val(),lastRow.find('.wastage_percentage').val(),lastRow.find('.making_charge').val(),lastRow.find('.sale_value').val(),lastRow.find('.net_wt').val(),lastRow.find('.less_wt').val(),lastRow.find('.making_charge').val(),lastRow.find('.wastage_percentage').val(),lastRow.find('.calc_type').val(),lastRow.find('.making_type').val(),lastRow.find('.sell_rate').val());



        //$('#lt_item_list tr').last().after(lastRow);



    }



});



function set_multiple_rows(lot_no,lot_product,gross_wt,id_design,des_name,wastage_percentage,making_charge,sale_value,net_wt,less_wt,making_charge,wastage_percentage,calculation_based_on,mc_type,sell_rate)



{



	$(".overlay").css("display", "block");



	console.log(id_design);



	console.log(des_name);



	my_Date = new Date();



		$.ajax({



		url:base_url+ "index.php/admin_ret_tagging/get_lot_inward_details?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),



        data: {'lot_no':lot_no,'lot_product':lot_product,'lot_id_design':''},



        type:"POST",



        dataType:"JSON",



        success:function(data)



        {



        	if(data.lot_inward_detail.length>0)



        	{



        		//	$('.lot_bal_wt').val(data.lot_inward_detail[0].lot_bal_wt);



					//$('.lot_bal_pcs').val(data.lot_inward_detail[0].lot_bal_pcs);



					$('#lt_tax_group').html(data.tax_percentage.tax_name);



					$('#tax_percentage').val(data.tax_percentage.tax_percentage);



					$('#tgi_calculation').val(data.tax_percentage.tgi_calculation);



        		var row = "";



        		var i=1;



        		var allow_row_create=false;



        		$.each(data.lot_inward_detail, function (key, item) {



					var curr_used_gross = 0;



					var curr_used_pcs = 0;



					var lot_bal_wt=0;



					var blc_lot_wt=0;



					var blc_gross_wt=0;



					var act_gross_blc=0;



					var weight_per=$('#weight_per').val();



					var sales_mode=item.sales_mode;



					var size_sel = "<option value=''>- Select Size-</option>";



					$.each(item.size_details, function (mkey, mitem) {



                		size_sel += "<option  value='"+mitem.id_size+"'>"+mitem.value+'-'+mitem.name+"</option>";



                	});



					$('#lot_bal_wt').val(item['lot_blc'].lot_bal_wt);



					$('#lot_bal_pcs').val(item['lot_blc'].lot_bal_pcs);



					var total_row=$('#lt_item_list tbody > tr').length;



					$('#lt_item_list> tbody  > tr').each(function(index, tr) {



						if($(this).find('.id_lot_inward_detail').val() == item.id_lot_inward_detail)



						{



							curr_used_gross+=parseFloat(($(this).find('.gross_wt').val()=='' ?0 :$(this).find('.gross_wt').val()));



							curr_used_pcs+=parseFloat(($(this).find('.no_of_piece').val()=='' ?0 :$(this).find('.no_of_piece').val()));



							act_gross_blc =parseFloat(($(this).find('.act_gross_blc').val()=='' ?0 :$(this).find('.act_gross_blc').val()));



						}



					});



					if(weight_per>0)



					{



						lot_bal_wt=parseFloat(((item['lot_blc'].lot_bal_wt + weight_per)));



						//lot_bal_wt=parseFloat(lot_bal_wt-curr_used_gross).toFixed(3);



						blc_lot_wt=parseFloat(item['lot_blc'].lot_bal_wt-curr_used_gross);



					}else



					{



						lot_bal_wt=item['lot_blc'].lot_bal_wt;



						blc_lot_wt=parseFloat(item['lot_blc'].lot_bal_wt-curr_used_gross);



					}



					var blc_pcs=item['lot_blc'].lot_bal_pcs - curr_used_pcs;



					blc_gross=parseFloat(act_gross_blc-curr_used_gross);



				if(item.sales_mode!=1)



					{



						if(curr_used_gross==0)



						{



							allow_row_create=true;



						}



						else if(blc_gross>0 && blc_pcs>0)



						{



							allow_row_create=true;



						}else{



							allow_row_create=false;



						}



					}



					else if(item.sales_mode=1 && blc_pcs>0)



					{



						allow_row_create=true;



					}else{



						allow_row_create=false;



					}



				console.log('blc_gross:'+blc_gross);



				console.log('blc_pcs:'+blc_pcs);



				console.log('sales_mode:'+item.sales_mode);



				console.log('*******');



				if(allow_row_create)



				{



					 row += '<tr id='+item.id_lot_inward_detail+' class='+total_row+'>'



           				 +'<td width="5%">'+item.lot_no+'<input type="hidden" name="lt_item[lot_no][]" value="'+item.lot_no+'" class="lot_no" /><input type="hidden" name="lt_item[id_lot_inward_detail][]" id="id_lot_inward_detail" value="'+item.id_lot_inward_detail+'" class="id_lot_inward_detail"><input type="hidden" name="lt_item[sales_mode][]" id="sales_mode" value="'+sales_mode+'" class="sales_mode"></td>'



           				 +'<td width="10%">'+item.product_name+'<input type="hidden" name="lt_item[lot_product][]" value="'+item.lot_product+'" class="lot_product" /><input type="hidden" name="lt_item[product_short_code][]" value="'+item.product_short_code+'" class="product_short_code" /><input type="hidden" name="lt_item[id_metal][]" value="'+item.id_metal+'" class="id_metal" /></td>'



           				 +'<td width="10%"><input type="text" class="cus_design"  value="'+des_name+'" required style="width:150;"/><input type="hidden" class="lot_id_design" name="lt_item[lot_id_design][]" value="'+id_design+'"></td>'



           				 +'<td width="10%">'+(item.design_for==1 ? 'Men' :(item.design_for==2 ? 'Female':'Unisex'))+'<input type="hidden" name="lt_item[design_for][]" value="'+item.design_for+'" class="design_for" /></td>'



           				 +'<td width="10%"><input type="hidden" name="" value="'+calculation_based_on+'" class="calc_type" /><select class="calculation_based_on" name="lt_item[calculation_based_on][]"><option value="0" '+(calculation_based_on == 0 ? "selected":"")+' '+(calculation_based_on >= 3 ? "disabled":"")+'>Mc & Wast On Gross</option><option value="1" '+(calculation_based_on == 1 ? "selected":"")+' '+(calculation_based_on >= 3 ? "disabled":"")+'>Mc & Wast On Net</option><option value="2" '+(calculation_based_on == 2 ? "selected":"")+' '+(calculation_based_on >= 3 ? "disabled":"")+'>Mc on Gross,Wast On Net</option><option value="3" '+(calculation_based_on == 3 ? "selected":"")+' '+(calculation_based_on == 4 ? "disabled":"")+'>Fixed Rate</option><option value="4" '+(calculation_based_on == 4 ? "selected":"")+' '+(calculation_based_on == 3 ? "disabled":"")+'>Fixed Rate based on Weight</option></select></td>'



           				 +'<td width="5%"><input type="number" step="any" name="lt_item[no_of_piece][]"   class="no_of_piece" value="1" style="width:80px;"/>Blc :<span class="blc_pcs"> '+(item['lot_blc'].lot_bal_pcs - curr_used_pcs)+'</span><input type="hidden" disabled class="act_blc_pcs" value="'+item['lot_blc'].lot_bal_pcs+'" style="width:80px;"></td>'



           				 +'<td width="10%"><input type="number" step="any" name="lt_item[gross_wt][]"   class="gross_wt" value="'+gross_wt+'" style="width:80px;" '+(item.calculation_based_on == 3 ? "readonly":"")+'/>Blc:<span class="blc_gross"> '+parseFloat(item['lot_blc'].lot_bal_wt - curr_used_gross-gross_wt).toFixed(3)+'</span><input type="hidden" class="act_gross_blc" value="'+lot_bal_wt+'"><input type="hidden" class="gross_wt_blc" value="'+item['lot_blc'].lot_bal_wt+'"></td>'



           				 +'<td width="10%"><input type="number" step="any" name="lt_item[less_wt][]"  class="less_wt" value="'+less_wt+'" style="width:80px;" '+(item.calculation_based_on == 3 ? "readonly":"")+'/></td>'



           				 +'<td width="10%"><input type="number" step="any" name="lt_item[net_wt][]"  class="net_wt" value="'+net_wt+'" style="width:80px;" readonly/></td>'



           				 +'<td width="5%"><input type="text" name="lt_item[wastage_percentage][]" value="'+wastage_percentage+'" class="wastage_percentage" style="width:80px;"/></td>'



           				 +'<td><input type="hidden"  value="'+mc_type+'" class="making_type" /><select class="mc_type"><option value="1" '+(mc_type == 1 ? "selected":"")+'>Per Gram</option><option value="2" '+(mc_type == 2 ? "selected":"")+'>Per Piece</option></select><input type="hidden" value="'+mc_type+'" name="lt_item[id_mc_type][]" class="id_mc_type"></td>'



           				 +'<td width="10%"><input type="number" step="any" name="lt_item[making_charge][]"  class="making_charge" value="'+making_charge+'" style="width:80px;"/></td>'



           				 +'<td width="10%"><input type="number" step="any" name="lt_item[sell_rate][]"  class="sell_rate" value="'+sell_rate+'" style="width:80px;" '+(item.calculation_based_on < 3 ? "readonly":"")+'/>'+(item.calculation_based_on == 3 ? "per piece":(item.calculation_based_on == 4 ? "per gram":""))+'</td>'



           				 +'<td width="10%"><select class="size"  name=lt_item[size][]>'+size_sel+'</select></td>'



           				 //+'<td><a href="#" onClick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));" class="btn btn-default btn-sm"><i class="fa fa-plus"></i></a></td>'



           				 +'<td><a href="#" onClick="update_image_upload($(this).closest(\'tr\'));" class="btn btn-default btn-sm"><i class="fa fa-plus"></i></a></td>'



           				 +'<td width="5%"><input type="number" step="any" name="lt_item[caculated_item_rate][]"  class="caculated_item_rate" value="" style="width:70px;" readonly/></td>'



           				 +'<td width="5%"><input type="number" step="any" name="lt_item[adjusted_item_rate][]"  class="adjusted_item_rate" value="'+item.adjusted_item_rate+'" style="width:80px;" '+(item.calculation_based_on < 3 ? "readonly":"")+'/></td>'



           				 +'<td width="10%"><input type="number" name="lt_item[sale_value][]"  class="sale_value" VALUE="'+sale_value+'" readonly style="width:80px;" /><input type="hidden" class="stone_details" name="lt_item[stone_details][]"><input type="hidden" class="stone_price" name="lt_item[stone_price][]"><input type="hidden" class="price" name="lt_item[price][]"><input type="hidden" class="tag_img" name="lt_item[tag_img][]"><input type="hidden" class="normal_st_certif" value="'+item.normal_st_certif+'"><input type="hidden" class="semiprecious_st_certif" value="'+item.semiprecious_st_certif+'"><input type="hidden" class="precious_st_certif" value="'+item.precious_st_certif+'"></td>'



           				 +'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'



            		+'</tr>';



					$('#lt_item_list tbody').append(row);



				}



        		if(item.calculation_based_on == 3){



					var tr = $('#lt_item_list tbody tr:eq('+total_row+')');



        			calculateTagSaleValue(tr);



				}



        		i++;



        		});



        	}



        },



        error:function(error)



        {



        }



    	});



    	$(".overlay").css("display", "none");



}



//Order Link



$(document).on('keyup',	".est_tag_name", function(e){



		var row = $(this).closest('tr');

        var tag_code = this.value;

		if(tag_code.length>4)

        {

			getSearchTags(tag_code,row);

        }

        if(tag_code==''){

            row.find('.est_tag_name').val('');

        }

});





$(document).on('keyup',	".est_old_tag_name", function(e){



	var row = $(this).closest('tr');



	if(this.value.length>4)



	{





		getSearchTags("",row,this.value);





	}



});



function getSearchTags(searchTxt,curRow,old_tag_id){



	my_Date = new Date();



	$.ajax({



        url: base_url+'index.php/admin_ret_tagging/getTaggingBySearch/?nocache=' + my_Date.getUTCSeconds(),



        dataType: "json",



        method: "POST",



        data: {'searchTxt': searchTxt,'id_branch': $("#branch_select").val(),'old_tag_id':old_tag_id},



        success: function (data) {



			cur_search_tags = data;





			$(".est_tag_name").autocomplete(



			{



				source: data,



				select: function(e, i)



				{



					e.preventDefault();



					var curRowItem = i.item;



					var allow_submit=true;



					if(ctrl_page[2]=='tag_link')



					{



					    if(curRow.find('.id_product').val()!=i.item.lot_product)



    					{



    					    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Invalid Product.'});



        				 	allow_submit=false;



        				 	$(".est_tag_name").val("");



							 $('.tag_id').val("");



							 $('.old_tag_id').val("");



							 $('.est_old_tag_name').val("");







    					}else if(curRow.find('.id_design').val()!=i.item.design_id)



    					{



    					    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Invalid Design.'});



        				 	allow_submit=false;



        				 	$(".est_tag_name").val("");



							 $('.tag_id').val("");



							 $('.old_tag_id').val("");



							 $('.est_old_tag_name').val("");







    					}



    					else if(curRow.find('.id_sub_design').val()!=i.item.id_sub_design)



    					{



    					    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Invalid Sub Design.'});



        				 	allow_submit=false;



        				 	$(".est_tag_name").val("");



							 $('.tag_id').val("");



							 $('.old_tag_id').val("");



							 $('.est_old_tag_name').val("");







    					}

						else if(i.item.id_orderdetails != '' && i.item.res_orderno != ''){

							var orderno = i.item.res_orderno;

							$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Tag is reserved for the order "' + orderno + '".',
								settings: {
									timeout: 5000 
								}
							});
	
							allow_submit = false;

							$(".est_tag_name").val("");
							
						}

    					else



    					{



    					    $('#order_details > tbody > tr').each(function(idx, row){



            				 	if(i.item.value==$(this).find('.tag_id').val())



            				 	{



            				 	    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Tag Already Exists..'});



            				 	    allow_submit=false;



            				 	    $(".est_tag_name").val("");



										$('.tag_id').val("");



										$('.old_tag_id').val("");



										$('.est_old_tag_name').val("");







            				 	}



            			     });



    					}



					}



					else if(ctrl_page[1]=='collection_mapping' && ctrl_page[2]=='add')



					{



					    create_new_empty_collection_tag_map(curRowItem);



					}



				    if(allow_submit)



				    {



				        curRow.find('.est_tag_name').val(i.item.label);



				        curRow.find('.tag_id').val(i.item.value);



						curRow.find('.old_tag_id').val(i.item.old_tag_id);



						curRow.find('.est_old_tag_name').val(i.item.old_tag_id);



						curRow.find('.tag_weight').val(i.item.gross_wt);





				    }



				},



				change: function (event, ui)



				{



			    },



				response: function(e, i) {



		            // ui.content is the array that's about to be sent to the response callback.

		            	if (searchTxt != "") {



								if (i.content.length !== 0) {



									//console.log("content : ", i.content);



								}



							} else {



								curRow.find('.tag_id').val('');



							}



		        },



				 minLength: 3,



			});



			$(".est_old_tag_name").autocomplete(



				{



					source: data,



					select: function(e, i)



					{



						e.preventDefault();



						var curRowItem = i.item;



						var allow_submit=true;



						if(ctrl_page[2]=='tag_link')



						{



							if(curRow.find('.id_product').val()!=i.item.lot_product)



							{



								$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Invalid Product.'});



								 allow_submit=false;





								 $(".est_old_tag_name").val("");





									$('.tag_id').val("");



									$('.old_tag_id').val("");



									$('.est_old_tag_name').val("");





							}

							else if(curRow.find('.id_design').val()!=i.item.design_id)



							{



								$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Invalid Design.'});



								 allow_submit=false;





								 $(".est_old_tag_name").val("");





									$('.tag_id').val("");



									$('.old_tag_id').val("");



									$('.est_old_tag_name').val("");





							}



							else if(curRow.find('.id_sub_design').val()!=i.item.id_sub_design)



							{



								$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Invalid Sub Design.'});



								 allow_submit=false;





								 $(".est_old_tag_name").val("");





									$('.tag_id').val("");



									$('.old_tag_id').val("");



									$('.est_old_tag_name').val("");





							}



							else



							{



								$('#order_details > tbody > tr').each(function(idx, row){



									 if(i.item.value==$(this).find('.tag_id').val())



									 {



										 $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Tag Already Exists..'});



										 allow_submit=false;





										 $(".est_old_tag_name").val("");





											$('.tag_id').val("");



											$('.old_tag_id').val("");



											$('.est_old_tag_name').val("");





									 }



								 });



							}



						}



						else if(ctrl_page[1]=='collection_mapping' && ctrl_page[2]=='add')



						{



							create_new_empty_collection_tag_map(curRowItem);



						}



						if(allow_submit)



						{



							curRow.find('.est_tag_name').val(i.item.tag_code);



							curRow.find('.tag_id').val(i.item.value);



							curRow.find('.old_tag_id').val(i.item.old_tag_id);



							curRow.find('.est_old_tag_name').val(i.item.old_tag_id);





						}



					},



					change: function (event, ui)



					{



					},



					response: function(e, i) {



						// ui.content is the array that's about to be sent to the response callback.



					},



					 minLength: 3,



				});







        }



     });



}



function create_new_empty_tag_row(data)



{



	var row = "";



       row += '<tr>'



			+'<td><input type="checkbox" class="tag_id" name="tag_id[]" value="'+data.tag_id+'">'+data.tag_id+'<input type="hidden" class="tag_id" value="'+data.tag_id+'"></td>'



			+'<td>'+data.tag_code+'</td>'



			+'<td>'+data.tag_lot_id+'</td>'



			+'<td>'+data.product_name+'<input type="hidden" class="id_product" value="'+data.lot_product+'"></td>'



			+'<td>'+data.design_name+'<input type="hidden" class="id_design" value="'+data.design_id+'" ></td>'



			+'<td>'+data.sub_design_name+'</td>'



			+'<td>'+data.gross_wt+'</td>'



			+'<td>'+data.net_wt+'</td>'



			+'<td><input type="text" class="est_tag_name"  required><input type="hidden" class="tag_id" value=""></td>'



			+'<td><select class="form-control order_det" ></select></td>'



			+'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger"><i class="fa fa-trash"></i></a></td>'



			+'</tr>';



	$('#tagging_list tbody').append(row);



	$('#tagging_list > tbody').find('.order_search').focus();



	$('#est_tag_name').val('');



}



$(document).on('keyup',	".order_search", function(e){



		var row = $(this).closest('tr');



		var tagData = this.value;



		var type  = "";



		var searchTxt  = "";



		getSearchOrders(this.value,row);



});



$('#select_order').on('change',function(){



   if(this.value!='')



   {



       get_customer_order_details(this.value);



   }



});



function get_customer_order_details(id_customerorder)



{



        $(".overlay").css("display", "block");



        my_Date = new Date();



		$.ajax({



		url:base_url+ "index.php/admin_ret_tagging/get_customer_order_details?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),



        type:"POST",



        data:{'id_customerorder':id_customerorder},



        dataType:"JSON",



        success:function(data)



        {



                var row = "";



                $('#order_details tbody').empty();



                $.each(data,function(key,val){



                    row += '<tr>'



                    +'<td><input type="checkbox" class="id_orderdetails" name="id_orderdetails[]" value="'+val.id_orderdetails+'">'+val.id_orderdetails+'</td>'



                    +'<td>'+val.order_no+'</td>'



                    +'<td>'+val.product_name+'<input type="hidden" class="id_product" value="'+val.id_product+'"></td>'



                    +'<td>'+val.design_name+'<input type="hidden" class="id_design" value="'+val.design_no+'" ></td>'



                    +'<td>'+val.sub_design_name+'<input type="hidden" class="id_sub_design" value="'+val.id_sub_design+'" ></td>'



                    +'<td class="weight" >'+val.weight+'</td>'



                    +'<td><input type="text" class="est_tag_name" required><input type="hidden" class="tag_id" value=""><input type="hidden" class="order_type" value="'+val.order_type+'"><input type="hidden" class="tag_weight" value=""></td>'



					+'<td><input type="text" class="est_old_tag_name" required><input type="hidden" class="old_tag_id" value=""></td>'



                    +'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger"><i class="fa fa-trash"></i></a></td>'



                    +'</tr>';



                });



                $('#order_details tbody').append(row);



         	    $(".overlay").css("display", "none");



        },



        error:function(error)



        {



            $(".overlay").css("display", "none");



        }



    	});



}



function get_CustomerOrders()



{



    $('#select_order option').remove();



    my_Date = new Date();



		$.ajax({



		url:base_url+ "index.php/admin_ret_tagging/get_CustomerOrders?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),



        type:"POST",



        data:{'id_branch':$('#branch_select').val(),'fin_year_code':$('#order_fin_year_select').val()},



        dataType:"JSON",



        success:function(data)



        {



           var id_employee=$('#id_employee').val();



           emp_details=data;



           $.each(data, function (key, item) {



                	 	$("#select_order").append(



                	 	$("<option></option>")



                	 	.attr("value", item.id_customerorder)



                	 	.text(item.order_no)



                	 	);



                 	});



             	$("#select_order").select2({



            	 	placeholder: "Select Order",



            	 	allowClear: true



             	});



             	$('#select_order').select2("val","");



         	    $(".overlay").css("display", "none");



        },



        error:function(error)



        {



        }



    	});



}



function getSearchOrders(searchTxt,curRow){



	my_Date = new Date();



	$.ajax({



        url: base_url+'index.php/admin_ret_tagging/getOrdersBySearch/?nocache=' + my_Date.getUTCSeconds(),



        dataType: "json",



        method: "POST",



        data: {'searchTxt': searchTxt,'id_branch': $("#branch_select").val(),'id_product':curRow.find('.id_product').val(),'id_design':curRow.find('.id_design').val(),'fin_year_code':$('#order_fin_year_select').val()},



        success: function (data) {



			cur_search_tags = data;



			$.each(data, function(key, item){



				$('#tagging_list > tbody tr').each(function(idx, row){



					if(item != undefined){



						if($(this).find('.id_customerorder').val() == item.value){



							data.splice(key, 1);



						}



					}



				});



			});



			$(".order_search").autocomplete(



			{



				source: data,



				select: function(e, i)



				{



					e.preventDefault();



					var curRowItem = i.item;



					curRow.find('.order_search').val(i.item.label);



					get_order_details(i.item.value,curRow);



				},



				change: function (event, ui) {



			    },



				response: function(e, i) {



		            // ui.content is the array that's about to be sent to the response callback.



		        },



				 minLength: 4,



			});



        }



     });



}



function get_order_details(id_customerorder,curRow)



{



    $(".overlay").css("display", "none");



    my_Date = new Date();



		$.ajax({



		url:base_url+ "index.php/admin_ret_tagging/getOrderDetailBySearch?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),



        type:"POST",



        data:{'id_branch':$('#branch_select').val(),'id_customerorder':id_customerorder,'id_product':curRow.find('.id_product').val(),'id_design':curRow.find('.id_design').val()},



        dataType:"JSON",



        success:function(data)



        {



           $.each(data, function (key, item) {



                	 	curRow.find(".order_det").append(



                	 	$("<option></option>")



                	 	.attr("value", item.id_orderdetails)



                	 	.text(item.weight)



                	 	);



                 	});



         	    $(".overlay").css("display", "none");



        },



        error:function(error)



        {



            $(".overlay").css("display", "none");



        }



    	});



        $(".overlay").css("display", "none");



}



 $("#tag_link_submit").on('click',function(){



        if($("input[name='id_orderdetails[]']:checked").val())



        {



            var allow_submit=true;



            $("#order_details tbody tr").each(function(index, value)



            {



              if($(value).find(".tag_id").val()=='' ||  $(value).find(".tag_id").val()==undefined)



              {



                  allow_submit=false;



                  alert('Please Select The Tag No..');



              }





            });



                if(allow_submit)



                {



                    $('#tag_link_submit').prop('disabled',true);



                    var selected = [];



                    var approve=false;



                    $("#order_details tbody tr").each(function(index, value)



                    {



                    if($(value).find("input[name='id_orderdetails[]']:checked").is(":checked"))



                    {



                        transData = {



                        'tag_id'            : $(value).find(".tag_id").val(),



						'old_tag_id'        : $(value).find(".old_tag_id").val(),



                        'id_orderdetails'   : $(value).find(".id_orderdetails").val(),



						'order_type'   		: $(value).find(".order_type").val(),



						'tag_weight'   		: $(value).find(".tag_weight").val(),



						'weight'   		: $(value).find(".weight").html(),



                    }



                    selected.push(transData);



                    }



                    })



                    req_data = selected;



                    console.log(req_data);



                    update_order_link(req_data);



                }else{



                    $('#tag_link_submit').prop('disabled',false);



                }



        }else{



            alert('Please Select Any One Tag.');



        }



    });



    function update_order_link(data="")



    {



        my_Date = new Date();



        $("div.overlay").css("display", "block");



        $.ajax({



        url:base_url+ "index.php/admin_ret_tagging/update_order_link?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),



        data:  {'req_data':data},



        type:"POST",



        async:false,



        success:function(data){



        $('#tag_link_submit').prop('disabled',false);



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



//Order Link



//New Tag Form changes



$("#reset_tag_form").on('click',function(e){



    var image='[]';



	$("#tag_lot_parent_prod").val("");



	//$("#tag_id_lot_inward_detail").val("");



	$("#tag_lot_no").val($("#tag_lot_received_id").val());



	$("#tag_id_collection").val("");



	//$("#tag_lot_design").val("");



	//$("#tag_size").val("");



	$("#tag_pcs").val(1);



	$("#tag_act_blc_pcs").val("");



	$("#tag_blc_pcs").val("");



	$("#tag_gwt").val("");



	$("#tag_act_gross_blc").val("");



	$("#tag_blc_gross").val("");



	$("#tag_lwt").val("");



	$("#tag_nwt").val("");



	$("#tag_wast_perc").val(0);

	$('#tag_pur_wast_perc').val(0);
	$('#tag_pur_wast_value').val(0);


	//$("#tag_mc_value").val("");



	//$("#tag_id_mc_type").val("");



	//$("#tag_mc_type").val("");



	$("#tag_sell_rate").val("");



	$("#tag_buy_rate").val("");



	$("#tag_buy_rate_type").val("");



	$("#tag_sale_value").val("");



	$("#tags_images").val("");



	$("#tag_tax_type").val("");



	$("#tag_stone_details").val("");



	$("#tag_blc_pcs_disp").html("Blc : 0");



	$("#tag_blc_gross_disp").html("Blc : 0");



	$("#buy_rate_type").html("");



    $("#other_huid_details").val("");



	modalHuidDetail =[];



	$("#tag_img").data('img',image);



	$("#tag_img").attr('data-img',image);



	$("#tag_img_copy").val("");



	$("#tag_img_default").val("");



	$("#tag_charge").val("");



	$("#tag_huid").val("");



	$("#tag_huid2").val("");



	$("#cert_no").val("");



	$("#cert_img").val("");



	$("#cert_img_base64").val("");



	$("#cert_img_preview").attr("src", "");



	$(".cert_img_container").css("display", "none");



	$("#manufacture_code").val("");



	$("#style_code").val("");



	$("#remarks").val("");



	$("#narration").val("");



	$("#other_metal_wt").val("");



	$("#gwt_uom_id").val($("#gwt_uom_id option:first").val());



	$("#tag_cat_type").val("");



	$("#stone_calculation_based_on").val("");



	$("#other_metal_amount").val(0);



	$("#tag_gwt").focus();



	$("#min_mc").val("");



	$("#min_va").val("");



	$('#tag_saved').val();



	$('#tag_purchase_cost').val("");



	$('#tag_images').val("");



	$('#quality_code').val("");



	$("#is_new_arrival").val(0);



});



$(document).on("focusout","#reset_tag_form", function() {



	console.log(document.activeElement.className);



	let classPattern = /(?:^|\s)sidebar-mini(?:\s|$)/



	if (document.activeElement.className.match(classPattern)) {



		$("#tag_gwt").focus();



	}



});



/*$(document).on('keyup change', '#bulk_tag,#tag_pcs,#tag_gwt,#tag_lwt,#tag_wast_perc,#tag_mc_value,#tag_id_mc_type,#tag_calculation_based_on,#tag_sell_rate,#stone_calculation_based_on,#gwt_uom_id', function(e){



	var gross_wt = parseFloat((isNaN($('#tag_gwt').val()) || $('#tag_gwt').val() == '')  ? 0 : $('#tag_gwt').val()).toFixed(3);



	if(gross_wt == 0)



	{



		$('#tag_lwt').val(0);



	}



	var less_wt  = (isNaN($('#tag_lwt').val()) || $('#tag_lwt').val() == '')  ? 0 : $('#tag_lwt').val();



	var oth_mtl_wt = isNaN(parseFloat($('#other_metal_wt').val())) ? 0 : parseFloat($('#other_metal_wt').val());



	var net_wt = parseFloat(parseFloat(gross_wt) - parseFloat(less_wt) - parseFloat(oth_mtl_wt)).toFixed(3);



	$('#tag_nwt').val(net_wt);



	if($("#tag_calculation_based_on").val() == 2){



		if($("#tag_cat_type").val() == 3) {



			$('#tag_sell_rate').attr("disabled", false);



		} else {



			$('#tag_sell_rate').attr("disabled", true);



		}



		//$("#tag_id_mc_type").prop("disabled",false);



		//$("#tag_id_mc_type").val(1);



		//$("#tag_mc_value").prop("disabled",false);



		$("#buy_rate_type").html("Per Gram");



	}else{



		$("#tag_sell_rate").prop("disabled",false);



		//$("#tag_id_mc_type").prop("disabled",true);



		//$("#tag_mc_value").prop("disabled",true);



		//$("#tag_id_mc_type").val("");



		$("#buy_rate_type").html("");



	}



	if(this.id == 'tag_pcs') {



		var tag_blc_pcs = parseFloat($("#tag_blc_pcs").val());



		var tag_input_pcs = $("#tag_pcs").val() == '' ? 0 : $("#tag_pcs").val();



		if(tag_blc_pcs >0){



			if(tag_input_pcs > tag_blc_pcs){



				$("#tag_pcs").val(tag_blc_pcs);



				$("#tag_blc_pcs_disp").html("Blc : 0");



			}else if(tag_input_pcs < 0){



				var blc_pc = tag_blc_pcs-1;



				$("#tag_pcs").val(1);



				$("#tag_blc_pcs_disp").html("Blc : "+blc_pc);



			}



			else{



				var blc_pc = tag_blc_pcs-tag_input_pcs;



				$("#tag_blc_pcs_disp").html("Blc : "+blc_pc);



			}



		}



		else



		{



		    $("#tag_pcs").val(0);



		}



	}



	else if(this.id == 'tag_gwt') {



		var tag_blc_wgt = parseFloat($("#tag_blc_gross").val());



		var tag_act_blc_wgt = parseFloat($("#tag_act_gross_blc").val());



		var tag_input_wgt = $("#tag_gwt").val() == '' ? 0 : parseFloat($("#tag_gwt").val());



		if(tag_blc_wgt >0){



			if(tag_input_wgt > tag_blc_wgt){



				$("#tag_gwt").val(tag_act_blc_wgt);



				$("#tag_blc_gross_disp").html("Blc : 0");



			}else if(tag_input_wgt < 0){



				var blc_wt = tag_blc_wgt-1;



				$("#tag_gwt").val(1);



				$("#tag_blc_gross_disp").html("Blc : "+blc_wt);



			}



			else{



				var blc_wt = tag_blc_wgt-tag_input_wgt;



				$("#tag_blc_gross_disp").html("Blc : "+blc_wt);



			}



		}



		else



		{



		    $("#tag_gwt").val(0);



		}



	}



	calculateTagFormSaleValue();



});	*/



$(document).on('keyup change', '#bulk_tag,#tag_pcs,#tag_gwt,#tag_lwt,#tag_wast_perc,#tag_mc_value,#tag_id_mc_type,#tag_calculation_based_on,#gwt_uom_id', function(e)



{



	if($('#tag_product_stone_type').val()!=0)



	{



		setLoose_product_rateSettings();



	}



	var gross_wt = parseFloat((isNaN($('#tag_gwt').val()) || $('#tag_gwt').val() == '')  ? 0 : $('#tag_gwt').val()).toFixed(3);



	if(gross_wt == 0)



	{



		$('#tag_lwt').val(0);



	}



	var less_wt  = (isNaN($('#tag_lwt').val()) || $('#tag_lwt').val() == '')  ? 0 : $('#tag_lwt').val();



	var oth_mtl_wt = isNaN(parseFloat($('#other_metal_wt').val())) ? 0 : parseFloat($('#other_metal_wt').val());



	var net_wt = parseFloat(parseFloat(gross_wt) - parseFloat(less_wt) - parseFloat(oth_mtl_wt)).toFixed(3);



	$('#tag_nwt').val(net_wt);



	if($("#tag_calculation_based_on").val() == 2){



		if($("#tag_cat_type").val() == 3) {



			$('#tag_sell_rate').attr("disabled", false);



		} else {



			$('#tag_sell_rate').attr("disabled", true);



		}



		//$("#tag_id_mc_type").prop("disabled",false);



		//$("#tag_id_mc_type").val(1);



		//$("#tag_mc_value").prop("disabled",false);



		$("#buy_rate_type").html("Per Gram");



	}else{



		$("#tag_sell_rate").prop("disabled",false);



		//$("#tag_id_mc_type").prop("disabled",true);



		//$("#tag_mc_value").prop("disabled",true);



		//$("#tag_id_mc_type").val("");



		$("#buy_rate_type").html("");



	}



	if(this.id == 'tag_pcs' || this.id == 'bulk_tag' ) {



        var  bulk=$("#bulk_tag").val() !=''?parseFloat($("#bulk_tag").val()):1;



		var tag_blc_pcs = parseFloat($("#tag_blc_pcs").val());

		var product_pieces = parseFloat($("#product_pcs").val());

		var tag_input_pcs = $("#tag_pcs").val() == '' ? 0 : parseFloat($("#tag_pcs").val()) * bulk ;

		var allow_tag_pcs = parseFloat($('#allow_tag_pcs').val());

		var tot_allow_tag = parseFloat(tag_blc_pcs + allow_tag_pcs);

		var tag_product_pcs = parseFloat((tot_allow_tag < product_pieces)? tot_allow_tag : product_pieces) ;


		var validate_tag_pcs = true;

		// var tag_product_pcs = parseFloat((tag_input_pcs > tot_allow_tag )? tot_allow_tag : parseFloat($("#tag_pcs").val())) ;


		// if(tag_input_pcs > tag_blc_pcs){

			if(tag_input_pcs > tot_allow_tag){

			validate_tag_pcs = false;

			}

		// }

		if(tag_blc_pcs >0){

			if(!validate_tag_pcs){

				$("#tag_pcs").val('');



				$("#tag_pcs").focus();
				// $('#tag_pcs').val(tag_product_pcs);

				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Enter Valid Pcs. The balance piece is '+ tot_allow_tag});




				$("#tag_blc_pcs_disp").html("Blc : 0");



			}else if(tag_input_pcs < 0){



				var blc_pc = tag_blc_pcs-1;



				$("#tag_pcs").val(1);



				$("#tag_blc_pcs_disp").html("Blc : "+blc_pc);



			}



			else{



				var blc_pc = tag_blc_pcs-tag_input_pcs;



				$("#tag_blc_pcs_disp").html("Blc : "+blc_pc);



			}



		}



		else



		{
			if(tag_input_pcs > tot_allow_tag){

		    	$("#tag_pcs").val(0);

			}

		}



	}



	 if(  this.id == 'tag_gwt' || this.id == 'bulk_tag' ) {



		var  bulk=$("#bulk_tag").val() !=''?parseFloat($("#bulk_tag").val()):1;


		var lot_grs_wt = parseFloat($('.disp_lot_wt').html());
		var tag_blc_wgt = parseFloat($("#tag_blc_gross").val());
		var tag_act_blc_wgt = parseFloat($("#tag_act_gross_blc").val());

		var tag_input_wgt = $("#tag_gwt").val() == '' ? 0 : parseFloat($("#tag_gwt").val()) * bulk ;

		var weight_per = parseFloat($("#weight_per").val());

		var allow_tag_weight = 	parseFloat(weight_per / 100 * lot_grs_wt).toFixed(3);

		var tot_allow_weight = parseFloat(parseFloat(tag_act_blc_wgt) + parseFloat(allow_tag_weight)).toFixed(3);

		var validate_weight = true;	

		if(tag_input_wgt > tot_allow_weight){

			// if((weight_per > 0 && (tag_input_wgt > tot_allow_weight)) || (weight_per < 0 && (tag_input_wgt > tag_act_blc_wgt))){

			validate_weight = false;

			// }

		}

	if(tag_blc_wgt >0){

			if(!validate_weight){



				$("#tag_gwt").val('');



				$("#tag_gwt").focus();



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Enter Valid Weight.The balance weight is '+tot_allow_weight});



				$("#tag_blc_gross_disp").html("Blc : 0");



			}else if(tag_input_wgt < 0){



				var blc_wt = tag_blc_wgt-1;



				$("#tag_gwt").val(1);



				$("#tag_blc_gross_disp").html("Blc : "+blc_wt);



			}



			else{



				var blc_wt = tag_blc_wgt-tag_input_wgt;



				$("#tag_blc_gross_disp").html("Blc : "+blc_wt);



			}



		}



		else



		{



		    $("#tag_gwt").val(0);



		}



	}



	calculateTagFormSaleValue();



});



function calculateTagFormSaleValue(row)



{



    // stone_price, stone_wt, certification_price



	var total_price 		= 0;



	var base_value_price 	= 0;



	var arrived_value_price = 0;



	var base_value_tax 		= 0;



	var arrived_value_tax 	= 0;



	var base_rate_tax 		= 0;



	var arrived_rate_tax 	= 0;



	var total_tax_per 		= 0;



	var total_tax_rate 		= 0;



	var rate_with_mc 		= 0;



	var material_price  	= 0; // Not worked



	var stone_price  		= 0; // Not worked



	var stone_po_price      = 0;



	var stone_wt  			= 0; // Not worked



	var certification_price = 0; // Not worked



	var tot_stone_wt        =0;



	var purchase_stone_price        =0;



	var stone_details=$('#tag_stone_details').val();



	var purchase_cost       = 0;



	if(stone_details!='')



	{



        var st_details=JSON.parse(stone_details);



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



						 purchase_stone_price+=parseFloat(pitem.stn_pur_cost);





                         if(current_po_details.length){



                         $.each(current_po_details[0].stonedetail, function (spkey, spitem) {



    	                    if(pitem.stone_id == spitem.po_stone_id){



    	                        if(spitem.po_stone_calc_based_on == 1){



    	                            stone_po_price += parseFloat((spitem.po_stone_rate * pitem.stone_wt));



    	                        }else{



    	                            stone_po_price += parseFloat((spitem.po_stone_rate * pitem.stone_pcs));



    	                        }



    	                    }



                         });



						}



                     }



                 });



             });



        }



    }



    $('#tag_lwt').val(parseFloat(tot_stone_wt).toFixed(3));



	var gross_wt 			= (isNaN($('#tag_gwt').val()) || $('#tag_gwt').val() == '')  ? 0 : $('#tag_gwt').val();



	var gross_wt 			= (isNaN($('#tag_gwt').val()) || $('#tag_gwt').val() == '')  ? 0 : $('#tag_gwt').val();



	var less_wt  			= (isNaN($('#tag_lwt').val()) || $('#tag_lwt').val() == '')  ? 0 : $('#tag_lwt').val();



	var oth_mtl_wt 			= isNaN(parseFloat($('#other_metal_wt').val())) ? 0 : parseFloat($('#other_metal_wt').val());



	var net_wt 				= parseFloat(gross_wt)-parseFloat(less_wt)-oth_mtl_wt;



	var calculation_type 	= (isNaN($('#tag_calculation_based_on').val()) || $('#tag_calculation_based_on').val() == '')  ? 0 : $('#tag_calculation_based_on').val();



	var metal_type 			= (isNaN($('#id_metal').val()) || $('#id_metal').val() == '')  ? 1 : $('#id_metal').val();



	var sales_mode 			= (isNaN($('#tag_sales_mode').val()) || $('#tag_sales_mode').val() == '')  ? 1 : $('#tag_sales_mode').val();



	var tax_type 			= (isNaN($('#tag_tax_type').val()) || $('#tag_tax_type').val() == '')  ? 1 : $('#tag_tax_type').val();



	var tax_group 			= (isNaN($('#tax_group_id').val()) || $('#tax_group_id').val() == '')  ? 1 : $('#tax_group_id').val();



	var total_charges 		= (isNaN($('#tag_charge').val()) || $('#tag_charge').val() == '')  ? 1 : $('#tag_charge').val();



	let sell_rate  			= (isNaN($('#tag_sell_rate').val()) || $('#tag_sell_rate').val() == '')  ? 0 : $('#tag_sell_rate').val();



	var no_tag_pcs			= (isNaN($('#tag_pcs').val()) || $('#tag_pcs').val() == '' || !$('#tag_pcs').val() > 0)  ? 0 : $("#tag_pcs").val();



	var stone_calc			= (isNaN($('#tag_cat_type').val()) || $('#tag_cat_type').val() != 3)  ? 0 : 1;



	var stone_calc_type		= (!isNaN($('#stone_calculation_based_on').val()) && $('#stone_calculation_based_on').val() != '' && $('#stone_calculation_based_on').val() > 0)  ? $('#stone_calculation_based_on').val() : 0;



	var product_type =  $('#tag_product_stone_type').val(); // 0 -> ornaments,1->stone,2->Diamond



  	$('#tag_nwt').val(parseFloat(net_wt).toFixed(3));



    if(metal_type==1)



	{



		var rate_per_grm = $('#metal_rate').val();//Gold



	}else if(metal_type==2){



		var rate_per_grm = $('#silverrate_1gm').val();//Silver



	}



	else if(metal_type==3){



		var rate_per_grm = $('#platinum_1g').val();//Platinum



	}



	var rate_field = '';



	$.each(metal_rate_details,function(k,val){



		if(val.id_metal == metal_type && val.id_purity==$('#id_purity').val())



		{



			rate_field = val.rate_field;



		}



	});



	if(rate_field!='')



	{



	    rate_per_grm = metal_rates[rate_field];



	}



	var tgi_calculation_type    = ($('#tgi_calculation').val()).split(",");



	var tax_percentage          = ($('#tax_percentage').val()).split(",");



	var retail_max_mc           = (isNaN($('#tag_mc_value').val()) || $('#tag_mc_value').val() == '')  ? 0 : $('#tag_mc_value').val();



	var tot_wastage             = (isNaN($('#tag_wast_perc').val()) || $('#tag_wast_perc').val() == '')  ? 0 : $('#tag_wast_perc').val();



	var no_of_piece             = (isNaN($('#tag_pcs').val()) || $('#tag_pcs').val() == '')  ? 0 : $('#tag_pcs').val();



	var purchase_mc 			= 0;



	var pur_mc_type = $('#tag_lt_prod option:selected').attr('data-mc_type') == undefined ? 1:$('#tag_lt_prod option:selected').attr('data-mc_type');



	var pur_mc_val = $('#tag_lt_prod option:selected').attr('data-making_charge') == undefined ? 0:$('#tag_lt_prod option:selected').attr('data-making_charge');



	/**



	*	Amount calculation based on settings (without discount and tax )



	*   0 - Wastage on Gross weight And MC on Gross weight



	*   1 - Wastage on Net weight And MC on Net weight



	*   2 - Wastage On Netwt And MC On Grwt



	*   rate_with_mc = Metal Rate + Stone + OM + Wastage + MC



	*/



	let stone_rate = 0;



	if(stone_calc == 1) {



		let st_uom = $("#gwt_uom_id").val() > 0 ? $("#gwt_uom_id").val() : 0;



		let tag_st_wt = 0;



		if(stone_calc_type == 1)  {



			$.each(uom_details,function(key,item){



				if(item.uom_id==st_uom) {



					let divided_by_value = 1;



					if(item.divided_by_value > 0) {



						divided_by_value = item.divided_by_value;



					}



					tag_st_wt = parseFloat(net_wt) / parseFloat(divided_by_value);



					return false;



				}



			});



			stone_rate = parseFloat(sell_rate) * parseFloat(tag_st_wt);



		} else if(stone_calc_type == 2)  {



			stone_rate = parseFloat(sell_rate) * parseFloat(no_tag_pcs);



		}



	}



	if(calculation_type == 0){



		var wast_wgt      = parseFloat(parseFloat(gross_wt) * parseFloat(tot_wastage/100)).toFixed(3);



		if($('#tag_id_mc_type').val() != 3){



    		var mc_type       =  parseFloat($('#tag_id_mc_type').val() == 2 ? parseFloat(retail_max_mc * gross_wt ) : parseFloat(retail_max_mc * $('#tag_pcs').val()));



    		// Metal Rate + Stone + OM + Wastage + MC



			 purchase_mc =parseFloat(pur_mc_type == 1 ? parseFloat(pur_mc_val * gross_wt ) : parseFloat(pur_mc_val * 1));



			if(stone_calc == 1) {



				rate_with_mc = parseFloat(parseFloat(stone_rate) + parseFloat(mc_type)+parseFloat(stone_price)+parseFloat(material_price)+parseFloat(certification_price));



			} else {



    			rate_with_mc = parseFloat(parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(mc_type)+parseFloat(stone_price)+parseFloat(material_price)+parseFloat(certification_price));



			}



		}else{



		    var mc_type       =  parseFloat((parseFloat(gross_wt) * parseFloat(rate_per_grm)) * parseFloat(retail_max_mc/100)).toFixed(3);



    		// Metal Rate + Stone + OM + Wastage + MC



			if(stone_calc == 1) {



				rate_with_mc = parseFloat(parseFloat(stone_rate) + parseFloat(mc_type)+parseFloat(stone_price)+parseFloat(material_price)+parseFloat(certification_price));



			} else {



    			rate_with_mc = parseFloat(parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(mc_type)+parseFloat(stone_price)+parseFloat(material_price)+parseFloat(certification_price));



			}



		}



	}



	else if(calculation_type == 1){



		var wast_wgt      = parseFloat(parseFloat(net_wt) * parseFloat(tot_wastage/100)).toFixed(3);



		purchase_mc =parseFloat(pur_mc_type == 1 ? parseFloat(pur_mc_val * net_wt ) : parseFloat(pur_mc_val * 1));



		if($('#tag_id_mc_type').val() != 3){



    		var mc_type       =  parseFloat($('#tag_id_mc_type').val() == 2 ? parseFloat(retail_max_mc * net_wt ) : parseFloat(retail_max_mc * $('#tag_pcs').val()));



    		// Metal Rate + Stone + OM + Wastage + MC



			if(stone_calc == 1) {



				rate_with_mc = parseFloat(parseFloat(stone_rate) + parseFloat(mc_type)+parseFloat(stone_price)+parseFloat(material_price)+parseFloat(certification_price));



			} else {



    			rate_with_mc = parseFloat(parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(mc_type)+parseFloat(stone_price)+parseFloat(material_price)+parseFloat(certification_price));



			}



		}else{



		    var mc_type       =   parseFloat((parseFloat(net_wt) * parseFloat(rate_per_grm)) * parseFloat(retail_max_mc/100)).toFixed(3);



    		// Metal Rate + Stone + OM + Wastage + MC



			if(stone_calc == 1) {



				rate_with_mc = parseFloat(parseFloat(stone_rate) + parseFloat(mc_type)+parseFloat(stone_price)+parseFloat(material_price)+parseFloat(certification_price));



			} else {



    			rate_with_mc = parseFloat(parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(mc_type)+parseFloat(stone_price)+parseFloat(material_price)+parseFloat(certification_price));



			}



		}



	}



	else if(calculation_type == 2){



		purchase_mc =parseFloat(pur_mc_type == 1 ? parseFloat(pur_mc_val * gross_wt ) : parseFloat(pur_mc_val * 1));



		var wast_wgt      = parseFloat(parseFloat(net_wt) * parseFloat(tot_wastage/100)).toFixed(3);



		if($('#tag_id_mc_type').val() != 3){



    		var mc_type       =  parseFloat($('#tag_id_mc_type').val() == 2 ? parseFloat(retail_max_mc * gross_wt ) : parseFloat(retail_max_mc * $('#tag_pcs').val()));



    		// Metal Rate + Stone + OM + Wastage + MC



			if(stone_calc == 1) {



				rate_with_mc = parseFloat(parseFloat(stone_rate) + parseFloat(mc_type)+parseFloat(stone_price)+parseFloat(material_price)+parseFloat(certification_price));



			} else {



    	    	rate_with_mc = parseFloat((parseFloat(rate_per_grm) * (parseFloat(wast_wgt) + parseFloat(net_wt)))+ parseFloat(mc_type))+parseFloat(stone_price)+parseFloat(material_price)+parseFloat(certification_price);



			}



		}else{



		    var mc_type      = parseFloat((parseFloat(gross_wt) * parseFloat(rate_per_grm)) * parseFloat(retail_max_mc/100)).toFixed(3);



    		// Metal Rate + Stone + OM + Wastage + MC



			if(stone_calc == 1) {



				rate_with_mc = parseFloat(parseFloat(stone_rate) + parseFloat(mc_type)+parseFloat(stone_price)+parseFloat(material_price)+parseFloat(certification_price));



			} else {



    	    	rate_with_mc = parseFloat((parseFloat(rate_per_grm) * (parseFloat(wast_wgt) + parseFloat(net_wt)))+ parseFloat(mc_type))+parseFloat(stone_price)+parseFloat(material_price)+parseFloat(certification_price);



			}



		}



	}



	else if(calculation_type == 3){



		purchase_mc =parseFloat(pur_mc_type == 1 ? parseFloat(pur_mc_val * gross_wt ) : parseFloat(pur_mc_val * 1));



		var adjusted_item_rate  = 0;



	    caculated_item_rate = parseFloat(sell_rate);



	    $('.caculated_item_rate').val(caculated_item_rate);



	    rate_with_mc = parseFloat(parseFloat(adjusted_item_rate) > 0 ? parseFloat(adjusted_item_rate) : caculated_item_rate );



	}



	else if(calculation_type == 4){



		purchase_mc =parseFloat(pur_mc_type == 1 ? parseFloat(pur_mc_val * gross_wt ) : parseFloat(pur_mc_val * 1));



		var adjusted_item_rate  = 0;



	    caculated_item_rate = (product_type==0 ? parseFloat((parseFloat(sell_rate)*parseFloat(net_wt))):parseFloat(stone_rate));



	    $('.caculated_item_rate').val(caculated_item_rate);



	    rate_with_mc = parseFloat(parseFloat(adjusted_item_rate) > 0 ? parseFloat(adjusted_item_rate) : caculated_item_rate );



	}



	/*let total_charges = 0;



	$(modalChargeDetail).each(function(idx, row){



		let rowChargesValue = isNaN(parseFloat(row.rowChargesValue)) ? 0 : parseFloat(row.rowChargesValue);



		total_charges += rowChargesValue;



	});*/



	var rate_with_charges = parseFloat(parseFloat(rate_with_mc) + parseFloat(total_charges)).toFixed(2);



	var other_metal_amount = isNaN(parseFloat($("#other_metal_amount").val())) ? 0 : parseFloat($("#other_metal_amount").val());



	var rate_with_other_metal = parseFloat(rate_with_charges) + other_metal_amount;



	console.log('Calculation : '+calculation_type);



	console.log('Wastage : '+wast_wgt);



	console.log('Total Wastage : '+tot_wastage);



	console.log('MC : '+mc_type);



	console.log('Rate with MC : '+rate_with_mc);



	console.log(' MC TYPE : '+mc_type);



	console.log(' Rate Per Gram : '+rate_per_grm);



	console.log('Gross Wt : '+gross_wt);



	console.log("rate with charges",rate_with_charges);



	console.log("rate with other metal",rate_with_other_metal);



	console.log("total_charge : ",total_charges);



	console.log("rate_with_mc : ",rate_with_mc);



	console.log('other metal amount : ',other_metal_amount);



	/*



	if(current_po_details.length > 0){ // Purchase cost calculation



    	var pur_purity  = (isNaN(current_po_details[0].purchase_touch) || current_po_details[0].purchase_touch == '')  ? 0 : parseFloat(current_po_details[0].purchase_touch);



    	var pur_wastage = (isNaN(current_po_details[0].item_wastage) || current_po_details[0].item_wastage == '')  ? 0 : parseFloat(current_po_details[0].item_wastage);[0];



    	var pure_wt = parseFloat((parseFloat(net_wt) * (parseFloat(pur_purity) + parseFloat(pur_wastage))) / 100).toFixed(3);



    	var pur_mc_type         = current_po_details[0].mc_type;



    	var pur_mc_val          = current_po_details[0].mc_value;



    	var po_rate_per_gram    = current_po_details[0].fix_rate_per_grm;



        var mctax               = 3;



    	purchase_cost = parseFloat(((parseFloat(pure_wt))*parseFloat(po_rate_per_gram))+parseFloat(pur_mc_type == 1 ? ((pur_mc_val * gross_wt) * ((100 + mctax) / 100)) : ((pur_mc_val * no_of_piece) * ((100 + mctax) / 100)))+parseFloat(stone_po_price * (103/100))).toFixed(2);



    	$("#tag_purchase_cost").val(purchase_cost);



	} */



	//purchase_cost --Start



	var tot_pcs = $('#tag_pcs').val();



	var rate = $('#tag_lt_prod option:selected').attr('data-rate') == undefined ? 0:$('#tag_lt_prod option:selected').attr('data-rate');



	var ratecaltype = $('#tag_lt_prod option:selected').attr('data-rate_calc_type') == undefined ? 0:$('#tag_lt_prod option:selected').attr('data-rate_calc_type');



	var purchase_touch = $('#tag_lt_prod option:selected').attr('data-touch') == undefined ? 0:$('#tag_lt_prod option:selected').attr('data-touch');



	var karigar_calc_type = $('#tag_lt_prod option:selected').attr('data-calc_type') == undefined ? 0:$('#tag_lt_prod option:selected').attr('data-calc_type');







	var wastage_per = $('#tag_lt_prod option:selected').attr('data-wastage_percentage') == undefined ? 0:$('#tag_lt_prod option:selected').attr('data-wastage_percentage');







	if(karigar_calc_type==1)



	{



		var purewt = parseFloat((parseFloat(net_wt) * (parseFloat(purchase_touch) + parseFloat(wastage_per))) / 100);



	}else if(karigar_calc_type==2) //Net weight * touch



	{



		var purewt = parseFloat((parseFloat(net_wt) * (parseFloat(purchase_touch)/100)));



	}



	else if(karigar_calc_type==3) // ((net wt * 3%)*92%)



	{



		var touch_weight       = parseFloat((parseFloat(net_wt)*parseFloat(purchase_touch)/100)).toFixed(3);



		var wastage_touch      = parseFloat(parseFloat(touch_weight)*(parseFloat(wastage_per))/100);



		var purewt             = parseFloat(parseFloat(touch_weight)+parseFloat(wastage_touch)).toFixed(3);



	}



	if(ratecaltype==1) // Rate Calc By Grm(Wt)



	{



		purchase_cost   = parseFloat((parseFloat(purewt)*parseFloat(rate))+parseFloat(purchase_mc)+parseFloat(other_metal_amount) + parseFloat(total_charges)+parseFloat(purchase_stone_price)).toFixed(2);



	}else



	{



		purchase_cost   = parseFloat((parseFloat(tot_pcs)*parseFloat(rate))+parseFloat(purchase_mc)+parseFloat(other_metal_amount) + parseFloat(total_charges)+parseFloat(purchase_stone_price)).toFixed(2);



	}



	var purchase_cost_taxable =purchase_cost;

	var pur_total_tax_rate = 0;

	// tax_type = 1;



	 if(tax_type==1) // Inclusive of Tax



	 {



		 tax_details = calculate_inclusiveGST(purchase_cost,tax_group);



		 console.log(tax_details);



		 var total_tax_rate = tax_details;



		 purchase_cost_taxable =purchase_cost -  total_tax_rate;



		// var tax_percentage = tax_details['tax_percentage'];





	 }else



	 {



		 var tax_details     = calculate_base_value_tax(purchase_cost, tax_group);



		 var pur_total_tax_rate  = tax_details;



	//	 var tax_percentage  = tax_details['tax_percentage'];



		// item_cost           = parseFloat(parseFloat(item_cost)+parseFloat(total_tax_rate)).toFixed(2);

		 purchase_cost = parseFloat(parseFloat(purchase_cost)+parseFloat(pur_total_tax_rate)).toFixed(2);



	}



	$("#tag_purchase_cost").val(purchase_cost);



    //Purchase Cost --End



        // Tax Calculation



        if(tax_type==2)



        {



            if(tax_details.length > 0){



            		var base_value_tax	= parseFloat(calculate_base_value_tax(rate_with_other_metal,tax_group)).toFixed(2);



            		var base_value_amt	= parseFloat(parseFloat(rate_with_other_metal)+parseFloat(base_value_tax)).toFixed(2);



            		var arrived_value_tax= parseFloat(calculate_arrived_value_tax(base_value_amt,tax_group)).toFixed(2);



            		var arrived_value_amt= parseFloat(parseFloat(base_value_amt)+parseFloat(arrived_value_tax)).toFixed(2);



            		total_tax_rate	= parseFloat(parseFloat(base_value_tax)+parseFloat(arrived_value_tax)).toFixed(2);



            }



            total_price = parseFloat(parseFloat(rate_with_other_metal)+parseFloat(total_tax_rate)).toFixed(2);



        }



        if((calculation_type==3 || calculation_type==4) && (tax_type==1) ) //tax_type 1-Inclusive ,2-Exclusive



        {



            total_price=rate_with_other_metal;



            var total_tax_rate = parseFloat(calculate_inclusiveGST(rate_with_other_metal,tax_group)).toFixed(2);



        }



	$('#tag_sale_value').val(Math.round(parseFloat(total_price)).toFixed(2));



	console.log('Amount : '+total_price);



	console.log('Tax Rate : '+total_tax_rate);



	console.log('Arrived value : '+arrived_value_price);



	console.log('*************************');



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



function calculate_base_value_tax(taxcallrate, taxgroup){



	var totaltax = 0;



	console.log(tax_details);



	$.each(tax_details, function(idx, taxitem){



		if(taxitem.tgi_tgrpcode == taxgroup){



			if(taxitem.tgi_calculation == 1){



				console.log(1);



				if(taxitem.tgi_type == 1){



					totaltax += parseFloat(taxcallrate)	* (parseFloat(taxitem.tax_percentage)/100);



				}else{



					totaltax -= parseFloat(taxcallrate)	* (parseFloat(taxitem.tax_percentage)/100);



				}



			}



		}



	});



	return totaltax;



}



function calculate_arrived_value_tax(taxcallrate, taxgroup){



	var totaltax = 0;



	$.each(tax_details, function(idx, taxitem){



		if(taxitem.tgi_tgrpcode == taxgroup){



			if(taxitem.tgi_calculation == 2){



				if(taxitem.tgi_type == 1){



					totaltax += parseFloat(taxcallrate)	* (parseFloat(taxitem.tax_percentage)/100);



				}else{



					totaltax -= parseFloat(taxcallrate)	* (parseFloat(taxitem.tax_percentage)/100);



				}



			}



		}



	});



	return totaltax;



}



function validateTagDetail(){



	var valid = true;



	let huid = $.trim($("#tag_huid").val().toUpperCase());



	$("#tag_huid").val(huid);



	let huid2 = $.trim($("#tag_huid2").val().toUpperCase());



	$("#tag_huid2").val(huid2);



	let gwt 				= (isNaN($('#tag_gwt').val()) || $('#tag_gwt').val() == '')  ? 0 : $.trim($('#tag_gwt').val());



	let net_wt 				= (isNaN($('#tag_nwt').val()) || $('#tag_nwt').val() == '')  ? 0 : $.trim($('#tag_nwt').val());



	let calculation_type 	= (isNaN($('#calculation_based_on').val()) || $('#calculation_based_on').val() == '')  ? 0 : $('#calculation_based_on').val();



	let tag_mc_value        = (isNaN($('#tag_mc_value').val()) || $('#tag_mc_value').val() == '')  ? 0 : $.trim($("#tag_mc_value").val());



	let tag_id_mc_type 		= (isNaN($('#tag_id_mc_type').val()) || $('#tag_id_mc_type').val() == '')  ? 0 : $.trim($('#tag_id_mc_type').val());



	let mc_va_limit = get_mc_va_limit();



	console.log("mc_va_limit", mc_va_limit);



	let min_mc = mc_va_limit.mc_min;



	let min_va = mc_va_limit.va_min;



	let min_va_wt = mc_va_limit.va_min_wt;



	let va_method = mc_va_limit.va_method;



	let min_mc_cal_type = mc_va_limit.mc_cal_type;



	let margin_mrp = mc_va_limit.margin_mrp;



	let total_mc = calculate_total_mc(calculation_type, gwt, net_wt, tag_id_mc_type, tag_mc_value);



	let total_min_mc = calculate_total_mc(calculation_type, gwt, net_wt, min_mc_cal_type, min_mc);



	if($('#tag_sales_mode').val()==1) // Fixed



	{



		var MRP = ($("#tag_sell_rate").val()=='' ? 0 :parseFloat($("#tag_sell_rate").val()));



		var purchaseRate = isNaN(parseFloat($("#tag_buy_rate").val())) || $.trim($('#tag_purchase_cost').val()) == '' ? 0 :  $.trim($('#tag_purchase_cost').val());



		var purchaseCost = isNaN($('#tag_purchase_cost').val()) || $.trim($('#tag_purchase_cost').val()) == '' ? 0 : $.trim($('#tag_purchase_cost').val());



		var tag_sale_value = isNaN($('#tag_sale_value').val()) || $.trim($('#tag_sale_value').val()) == '' ? 0 : $.trim($('#tag_sale_value').val());



		var margin_added_cost = parseFloat(purchaseCost) + (parseFloat(purchaseCost) * parseFloat(margin_mrp) / 100);



		margin_added_cost = isNaN(margin_added_cost) ? 0 : margin_added_cost;

		// if(!(parseFloat(purchaseCost) > 0)) {

		// 	$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Purchase cost required.'});

		// 	valid = false;

		// } else 
		if(MRP==0){

			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'MRP value required'});



			valid = false;



		}  else if(parseFloat(tag_sale_value) < parseFloat(margin_added_cost)) {



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'MRP sales value cannot be less than '+margin_added_cost});



			valid = false;



		}



	}



	else if($('#tag_sales_mode').val()==2) // Flexible



	{



		if($('#tag_lot_received_id').val()=='' || $('#tag_lot_received_id').val()==null){



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Select Lot'});



			valid = false;



		}



		else if($('#tag_lt_prod').val()=='' || $('#tag_lt_prod').val()==null){



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Select Product'});



			valid = false;



		}



		else if($('#des_select').val()=='' || $('#des_select').val()==null){



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Select Design'});



			valid = false;



		}



		else if($('#sub_des_select').val()=='' || $('#sub_des_select').val()==null){



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Select Sub Design'});



			valid = false;



		}



		else if(isInValid($('#tag_pcs').val())){



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Enter The Piece'});



			valid = false;



		}



		else if(isInValid(gwt)){



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Enter The Gross Wt'});



			valid = false;



		}



		else if($.trim($("#tag_wast_perc").val()) == ''){



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Enter Wastage'});



			valid = false;



		}



		else if(va_method == 1 ? $.trim($("#tag_wast_perc").val()) < parseFloat(min_va) : (va_method == 2 ? $.trim($("#tag_wast_value").val()) < parseFloat(min_va_wt) : false)){



			let min_va_value = (va_method == 1 ? min_va+"%" : ((va_method == 2 ? min_va_wt+" g" : "")));



			let min_va_type = (va_method == 1 ? "perc" : ((va_method == 2 ? "wt" : "")));



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Wastage '+min_va_type+' cannot be less than '+min_va_value});



			valid = false;



		}



		else if($.trim($("#tag_mc_value").val()) == ''){ // ||  $("#tag_mc_value").val() == 0



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Enter MC'});



			valid = false;



		}



		else if(parseFloat(total_mc) < parseFloat(total_min_mc)){



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Total MC cannot be less than '+total_min_mc});



			valid = false;



		}



		else if(($('#has_size').val()==1 &&  $('#tag_size').val()==null ))



    	{



    		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Select Size'});



    		$('#tag_size').focus();



    		valid = false;



    	}



	}



	if(huid != "" ?  !huid_validation(huid) : false) {



		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Invalid HUID Number!'});



		valid = false;



	}



	else if(huid2 != "" ?  !huid_validation(huid2) : false) {



		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Invalid HUID Number!'});



		valid = false;



	}



	/*else if($.trim($("#remarks").val()) == '') {



		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Old Tag Id required!'});



		valid = false;



	}*/



	if($('#is_section_req').val()==1)



    {



    	if($('#section_select').val()=='' || $('#section_select').val()==null)



    	{



    		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Select Section!'});



    		valid = false;



    	}



    }



	return valid;



}





function get_mc_va_limit() {



	let mc_min = 0;



	let va_min = 0;



	let va_min_wt = 0;



	let va_method = 0;



	let margin_mrp = 0;



	let mc_cal_type = 0;



	$.each(wast_settings_details,function(key,items){



		if((items.id_product == $('#tag_lt_prodId').val()) && (items.id_design == $('#des_select').val()) && (items.id_sub_design == $('#sub_des_select').val())) {



			margin_mrp = isNaN(items.margin_mrp) || items.margin_mrp == '' || items.margin_mrp == null ? 0 : items.margin_mrp;



			mc_cal_type = isNaN(items.mc_cal_type) || items.mc_cal_type == '' || items.mc_cal_type == null ? 0 : items.mc_cal_type;



			if(items.wastage_type==1) { //Fixed



				mc_min = isNaN(items.mc_min) || items.mc_min == '' || items.mc_min == null ? 0 : items.mc_min;



				va_min = isNaN(items.wastag_min) || items.wastag_min == '' || items.wastag_min == null ? 0 : items.wastag_min;



				va_min_wt = isNaN(items.wastag_min_wt) || items.wastag_min_wt == '' || items.wastag_min_wt == null ? 0 : items.wastag_min_wt;



				va_method = isNaN(items.wastag_method) || items.wastag_method == '' || items.wastag_method == null ? 0 : items.wastag_method;

			}

			else if(items.wastage_type==2) { //Flexiable



				$.each(items.weight_range_det,function(i,result) {



					if((parseFloat(result.wc_from_weight) <= parseFloat($('#tag_gwt').val())) && (parseFloat($('#tag_gwt').val()) <= parseFloat(result.wc_to_weight))){



						mc_min = isNaN(result.mcrg_min) || result.mcrg_min == '' || result.mcrg_min == null ? 0 : result.mcrg_min;



						va_min = isNaN(result.wc_min) || result.wc_min == '' || result.wc_min == null ? 0 : result.wc_min;



						va_min_wt = isNaN(result.wc_min_wt) || result.wc_min_wt == '' || result.wc_min_wt == null ? 0 : result.wc_min_wt;



						va_method = isNaN(result.wc_method) || result.wc_method == '' || result.wc_method == null ? 0 : result.wc_method;

						

						mc_cal_type = isNaN(result.mc_type) || result.mc_type == '' || result.mc_type == null ? 1 : result.mc_type;



					}

				});

			}

		}

	});



	if(va_method == 2) {



		let net_wt = (isNaN($('#tag_nwt').val()) || $('#tag_nwt').val() == '')  ? 0 : $('#tag_nwt').val();



		let wast_perc  = parseFloat(net_wt) > 0 ? parseFloat(parseFloat(va_min_wt * 100) / parseFloat(net_wt)).toFixed(2) : 0;



		va_min = wast_perc;



	}



	let po_mc = current_po_details[0].mc_value != "undefined" && current_po_details[0].mc_value != null ? current_po_details[0].mc_value : 0;



	let po_va = current_po_details[0].item_wastage != "undefined" && current_po_details[0].item_wastage != null ? current_po_details[0].item_wastage : 0;



	let _mc = parseFloat(mc_min) > parseFloat(po_mc) ? mc_min : po_mc;



	let _va = parseFloat(va_min) > parseFloat(po_va) ? va_min : po_va;



	let return_data = {



		"mc_min" : _mc,



		"va_min" : _va,



		"va_min_wt" : va_min_wt,



		"va_method" : va_method,



		"margin_mrp" : margin_mrp,



		"mc_cal_type": mc_cal_type



	}



	return return_data;



}



function calculate_total_mc(calculation_type, gross_wt, net_wt, id_mc_type, mc) {



	let total_mc = 0



	if (calculation_type == 0) {



		total_mc = parseFloat(id_mc_type) == 2 ? parseFloat(mc * gross_wt) : parseFloat(mc * 1);



	}



	else if (calculation_type == 1) {



		total_mc = parseFloat(id_mc_type) == 2 ? parseFloat(mc * net_wt) : parseFloat(mc * 1);



	}



	else if (calculation_type == 2) {



		total_mc = parseFloat(id_mc_type) == 2 ? parseFloat(mc * gross_wt) : parseFloat(mc * 1);



	}



	return total_mc;



}



function isInValid(value){



	return (value == "" || value == undefined || value == 0 ? true : false);



}



$("#addTagToPreview").on('click',function(e){



	if(validateTagDetail()){



		set_tag_to_preview();



		modalStoneDetail = [];



		$("#addTagToPreview").css('display',"inline");



		$("#updateTagInPreview").css('display',"none");



		createTag();



	}



});



function createTag(){



    var postData = {};



    var my_Date = new Date();

	var lt_tag_pcs = ((parseFloat($("#tag_blc_pcs").val())) > 0 ? 1 : '') ;

    $('#lt_item_tag_preview > tbody tr').each(function(idx, row){



        if(($(row).find(".tag_id").val()=='')  && ($(row).find(".tag_saved").val() == 0))



        {



            if($(row).find(".tag_saved").val() == 0){



                var images              = $('#tag_img_url').val();



                postData = {



                            'purity'                : { 0 : $(row).find(".purity").val()},



                            'id_branch'             : $('#branch_select').val(),



                            'to_branch'             : $('#current_branch').val(),



                            'purity'                : { 0 : $(row).find(".purity").val()},



                            'lot_no'                : { 0 : $(row).find(".lot_no").val()},



                            'id_lot_inward_detail'  : { 0 : $(row).find(".id_lot_inward_detail").val()},



                            'lot_product'           : { 0 : $(row).find(".lot_product").val()},



                            'lot_id_design'         : { 0 : $(row).find(".lot_id_design").val()},



                            'lot_id_sub_design'     : { 0 : $(row).find(".lot_id_sub_design").val()},



                            'design_for'            : { 0 : $(row).find(".design_for").val()},



                            'size'                  : { 0 : $(row).find(".size").val()},



                            'no_of_piece'           : { 0 : $(row).find(".no_of_piece").val()},



                            'gross_wt'              : { 0 : $(row).find(".gross_wt").val()},



                            'less_wt'               : { 0 : $(row).find(".less_wt").val()},



                            'net_wt'                : { 0 : $(row).find(".net_wt").val()},


                            'purchase_touch'        : { 0 : $(row).find(".purchase_touch").val()},



                            'calculation_based_on'  : { 0 : $(row).find(".calculation_based_on").val()},



                            'wastage_percentage'    : { 0 : $(row).find(".wastage_percentage").val()},



                            'id_mc_type'            : { 0 : $(row).find(".id_mc_type").val()},



                            'making_charge'         : { 0 : $(row).find(".making_charge").val()},



                            'sell_rate'             : { 0 : $(row).find(".sell_rate").val()},



                            'sale_value'            : { 0 : $(row).find(".sale_value").val()},



                            'product_short_code'    : { 0 : $(row).find(".tag_product_short_code").val()},



                            'id_metal'              : { 0 : $(row).find(".id_metal").val()},



                            'tax_group_id'          : { 0 : $(row).find(".tax_group_id").val()},



                            'tag_sales_mode'        : { 0 : $(row).find(".tag_sales_mode").val()},



                            'tag_tax_type'          : { 0 : $(row).find(".tag_tax_type").val()},



                            'charges_value'         : { 0 : $(row).find(".charges_value").val()},



                            'huid'                  : { 0 : $(row).find(".huid").val()},



							'huid2'                 : { 0 : $(row).find(".huid2").val()},



							'cert_no'				: { 0 : $(row).find(".cert_no").val()},



							'cert_image'			: { 0 : $(row).find(".cert_image").val()},



                            'adjusted_item_rate'    : { 0 : $(row).find(".adjusted_item_rate").val()},



                            'charges'               : { 0 : $(row).find(".charges").val()},



							'othermetals'           : { 0 : $(row).find(".othermetals").val()},



                            'tag_img'               : { 0 : []},



                            'stone_details'         : { 0 : $(row).find(".stone_details").val()},



                            'stone_price'           : { 0 : $(row).find(".stone_price").val()},



                            'normal_st_certif'      : { 0 : $(row).find(".normal_st_certif").val()},



                            'semiprecious_st_certif': { 0 : $(row).find(".semiprecious_st_certif").val()},



                            'precious_st_certif'    : { 0 : $(row).find(".precious_st_certif").val()},



							'attributes'            : { 0 : $(row).find(".tag_attributes").val()},



							'manufacture_code'      : { 0 : $(row).find(".manufacture_code").val()},



							'style_code'            : { 0 : $(row).find(".style_code").val()},



							'remarks'            	: { 0 : $(row).find(".remarks").val()},



							'narration'            	: { 0 : $(row).find(".narration").val()},



							'tag_purchase_cost'     : { 0 : $(row).find(".tag_purchase_cost").val()},

							'purchase_touch'        : { 0 : $(row).find(".purchase_touch").val()},



							'tag_product_division'  : { 0 : $(row).find(".tag_product_division").val()},



							'is_suspense_stock'     : $('.issuspensestock').val(),



							'gwt_uom_id'     		: { 0 : $(row).find(".gwt_uom_id").val()},



							'tag_cat_type'     		: { 0 : $(row).find(".tag_cat_type").val()},



							'stone_calculation_based_on' : { 0 : $(row).find(".stone_calculation_based_on").val()},



							'tag_img'     			: { 0 : images},



                        	'tag_img_copy'     		: { 0 : $("#tag_img_copy").val()},



                        	'tag_img_default'     	: { 0 : $("#tag_img_default").val()},



                        	'tag_section'           : { 0 : $(row).find(".tag_id_section").val()},



							'emp_select'            : { 0 : $('#emp_select').val()},



							'huid_details'          : { 0 : $(row).find(".huid_details").val()},



							'quality_id'            : {0 : $(row).find('.quality_id').val()},



							'lot_rate'            : {0 : $(row).find('.lot_rate').val()},



							'lot_calc_type'            : {0 : $(row).find('.lot_calc_type').val()},



							'lot_rate_calc_type'      : {0 : $(row).find('.lot_rate_calc_type').val()},



							'lot_purchase_touch'            : {0 : $(row).find('.lot_purchase_touch').val()},



							'lot_wastage_percentage'            : {0 : $(row).find('.lot_wastage_percentage').val()},



							'lot_mc_type'            : {0 : $(row).find('.lot_mc_type').val()},



							'lot_making_charge'            : {0 : $(row).find('.lot_making_charge').val()},



							'is_new_arrival'  		: { 0 : $(row).find(".is_new_arrival").val()},

							'pur_wastage_percentage': {0: $(row).find(".pur_wastage_percentage").val(),},

							'pur_id_mc_type': { 0: $(row).find(".pur_id_mc_type").val() },

							'pur_making_charge': { 0: $(row).find(".pur_making_charge").val() },



                           }



                $(".overlay").css('display','block');



            	$.ajax({



            		url: base_url+'index.php/admin_ret_tagging/tagging/save/?nocache=' + my_Date.getUTCSeconds(),



            		dataType: "json",



            		method: "POST",



            		data: { 'lt_item': postData },



            		success: function ( data ) {



            			console.log(data);



            			if(data.status) {



							window.open(base_url+'index.php/admin_ret_tagging/generate_tagqrcode/'+data.tag_id+'?nocache=' + my_Date.getUTCSeconds(), '_blank');



            			    $("#tag_id").val("");



            			    $("#tag_saved").val("");



            			    $(row).find('.tag_id').val(data.tag_id);



            			    $(row).find('.tag_code').html(data.tag_code);



            			    $.toaster({ priority : 'success', title : 'Success!', message : data.message});



            			    modalStoneDetail = [];



							modalOtherMetalDetail = [];



							display_othermetals_details();



            			    $(row).find(".tag_saved").val(1);



            			    $(row).find(".btn-del").attr('data-href',base_url+'index.php/admin_ret_tagging/tagging/delete/'+data.tag_id+'/0/add');



            			    $("#tag_lot_received_id").focus();



            			    checking_lot_availability();



            			    get_lot_inwards_detail($('#tag_lot_received_id').val(),$('#tag_lt_prod').val(),'');



            			    $('#stone-det tbody').empty();



							get_productCharges();



							get_attributes_from_subdesign();



							$('#tag_gwt').focus();

						}



            			else if(data.status == false){



            			    $.toaster({ priority : 'danger', title : 'Warning!', message : data.msg});



            			}



            			$(".overlay").css('display','none');



            		}



            	});



            }



        }



        else if(($(row).find(".tag_id").val()!='') && ($(row).find(".tag_saved").val() ==1) && ($(row).find(".tag_id").val() == $('#tag_id').val()))



        {



            var images              = $('#tag_img_url').val();



            postData = {



                            'purity'                : { 0 : $(row).find(".purity").val()},



                            'id_branch'             : $('#branch_select').val(),



                            'to_branch'             : $('#current_branch').val(),



                            'purity'                : { 0 : $(row).find(".purity").val()},



                            'lot_no'                : { 0 : $(row).find(".lot_no").val()},



                            'id_lot_inward_detail'  : { 0 : $(row).find(".id_lot_inward_detail").val()},



                            'lot_product'           : { 0 : $(row).find(".lot_product").val()},



                            'lot_id_design'         : { 0 : $(row).find(".lot_id_design").val()},



                            'lot_id_sub_design'     : { 0 : $(row).find(".lot_id_sub_design").val()},



                            'design_for'            : { 0 : $(row).find(".design_for").val()},



                            'size'                  : { 0 : $(row).find(".size").val()},



                            'no_of_piece'           : { 0 : $(row).find(".no_of_piece").val()},



                            'gross_wt'              : { 0 : $(row).find(".gross_wt").val()},



                            'less_wt'               : { 0 : $(row).find(".less_wt").val()},



                            'net_wt'                : { 0 : $(row).find(".net_wt").val()},


                            'purchase_touch'         : { 0 : $(row).find(".purchase_touch").val()},



                            'calculation_based_on'  : { 0 : $(row).find(".calculation_based_on").val()},



                            'wastage_percentage'    : { 0 : $(row).find(".wastage_percentage").val()},



                            'id_mc_type'            : { 0 : $(row).find(".id_mc_type").val()},



                            'making_charge'         : { 0 : $(row).find(".making_charge").val()},



                            'sell_rate'             : { 0 : $(row).find(".sell_rate").val()},



                            'sale_value'            : { 0 : $(row).find(".sale_value").val()},



                            'product_short_code'    : { 0 : $(row).find(".tag_product_short_code").val()},



                            'id_metal'              : { 0 : $(row).find(".id_metal").val()},



                            'tax_group_id'          : { 0 : $(row).find(".tax_group_id").val()},



                            'tag_sales_mode'        : { 0 : $(row).find(".tag_sales_mode").val()},



                            'tag_tax_type'          : { 0 : $(row).find(".tag_tax_type").val()},



                            'charges_value'         : { 0 : $(row).find(".charges_value").val()},



                            'huid'                  : { 0 : $(row).find(".huid").val()},



							'huid2'                 : { 0 : $(row).find(".huid2").val()},



							'cert_no'				: { 0 : $(row).find(".cert_no").val()},



							'cert_image'			: { 0 : $(row).find(".cert_image").val()},



                            'adjusted_item_rate'    : { 0 : $(row).find(".adjusted_item_rate").val()},



                            'charges'               : { 0 : $(row).find(".charges").val()},



							'othermetals'           : { 0 : $(row).find(".othermetals").val()},



                            'tag_img'               : { 0 : []},



                            'stone_details'         : { 0 : $(row).find(".stone_details").val()},



                            'stone_price'           : { 0 : $(row).find(".stone_price").val()},



                            'normal_st_certif'      : { 0 : $(row).find(".normal_st_certif").val()},



                            'semiprecious_st_certif': { 0 : $(row).find(".semiprecious_st_certif").val()},



                            'precious_st_certif'    : { 0 : $(row).find(".precious_st_certif").val()},



							'attributes'            : { 0 : $(row).find(".tag_attributes").val()},



							'manufacture_code'      : { 0 : $(row).find(".manufacture_code").val()},



							'style_code'            : { 0 : $(row).find(".style_code").val()},



							'remarks'            	: { 0 : $(row).find(".remarks").val()},



							'narration'            	: { 0 : $(row).find(".narration").val()},



							'tag_purchase_cost'     : { 0 : $(row).find(".tag_purchase_cost").val()},



							'gwt_uom_id'			: { 0 : $(row).find(".gwt_uom_id").val()},



							'tag_cat_type'     		: { 0 : $(row).find(".tag_cat_type").val()},



							'stone_calculation_based_on' : { 0 : $(row).find(".stone_calculation_based_on").val()},



							'tag_img'     			: { 0 : images},



                        	'tag_img_copy'     		: { 0 : $("#tag_img_copy").val()},



                        	'tag_img_default'     	: { 0 : $("#tag_img_default").val()},



                        	'tag_product_division'  : { 0 : $(row).find(".tag_product_division").val()},



                        	'tag_section'           : { 0 : $(row).find(".tag_id_section").val()},



                        	'huid_details'         : { 0 : $(row).find(".huid_details").val()},



							'quality_id'            : {0 : $(row).find('.quality_id').val()},



							'is_new_arrival'  		: { 0 : $(row).find(".is_new_arrival").val()},

							'pur_wastage_percentage': {0: $(row).find(".pur_wastage_percentage").val(),},

							'pur_id_mc_type': { 0: $(row).find(".pur_id_mc_type").val() },

							'pur_making_charge': { 0: $(row).find(".pur_making_charge").val() },


                           }



                $(".overlay").css('display','block');



            	$.ajax({



            		url: base_url+'index.php/admin_ret_tagging/tagging/tag_update/?nocache=' + my_Date.getUTCSeconds(),



            		dataType: "json",



            		method: "POST",



            		data: { 'lt_item': postData,'tag_id':$(row).find(".tag_id").val() },



            		success: function ( data ) {



            			console.log(data);



            			if(data.status){



							window.open(base_url+'index.php/admin_ret_tagging/generate_tagqrcode/'+data.tag_id+'?nocache=' + my_Date.getUTCSeconds(), '_blank');



            			    $("#tag_id").val("");



            			    $("#tag_saved").val("");



            			    $(row).find('.tag_id').val(data.tag_id);



							$(row).find('.tag_code').html(data.tag_code);



            			    $.toaster({ priority : 'success', title : 'Success!', message : data.message});



            			    modalStoneDetail = [];



							modalOtherMetalDetail = [];



							display_othermetals_details();



            			    $(row).find(".tag_saved").val(1);



            			    $(row).find(".btn-del").attr('data-href',base_url+'index.php/admin_ret_tagging/tagging/delete/'+data.tag_id+'/0/add');



            			    $("#tag_lot_received_id").focus();



            			    checking_lot_availability();



            			    get_lot_inwards_detail($('#tag_lot_received_id').val(),$('#tag_lt_prod').val(),'');



            			    $('#stone-det tbody').empty();



            			    get_productCharges();



            			    get_attributes_from_subdesign();



							$('#tag_gwt').focus();



						}



            			else if(data.status == false){



            			    $.toaster({ priority : 'danger', title : 'Warning!', message : data.msg});



            			}



            			$(".overlay").css('display','none');



            		}



            	});



        }



    });



}



function set_tag_to_preview()



{



    var row = "";



	var total_row = $('#lt_item_tag_preview tbody > tr').length;



	var charges_value = 0;



	$(modalChargeDetail).each(function(idx, row) {



		charges_value += parseFloat(row.charge_value);



	});



	var chargesPostRow = "<input type='hidden' class='charges' name='lt_item[charges][]' value='"+JSON.stringify(modalChargeDetail)+"' />";



	console.log("chargesPostRow",chargesPostRow);



	var attrsPostRow = "<input type='hidden' class='tag_attributes' name='lt_item[attrs][]' value='"+JSON.stringify(modalAttributeDetail)+"' />";



	console.log("attrsPostRow",attrsPostRow);



	var othermetals = "<input type='hidden' class='othermetals' name='lt_item[othermetals][]' value='"+JSON.stringify(modalOtherMetalDetail)+"' />";



	console.log("othermetals",othermetals);



	var images       = $('#tag_img_url').val();



	var img=decodeURIComponent(images);



	var image_preview =  base_url+'assets/img/no_image.png';



	if(img!='')



	{



	    var image_details = JSON.parse(img);



        console.log(image_details);



    	if(image_details.length>0)



    	{



    	    $.each(image_details,function(k,i){



    	       if($('#tag_img_default').val()==k)



    	       {



    	           image_preview=i.src;



    	       }



    	    });



    	}



	}



	if($('#tag_images').val()!='')



	{



	    var tagged_images = JSON.parse($('#tag_images').val());



	}else



	{



	    tagged_images=[];



	}



	var tag_pre_img_resource=[];



	$.each(tagged_images,function(k,i){



		tag_pre_img_resource.push({'src':i.src,'name':(Math.floor(100000 + Math.random() * 900000))+'jpg','is_default':i.is_default});



	});



	row +='<tr id='+$('#tag_id_lot_inward_detail').val()+' class='+(total_row+1)+'>'



	                +'<td>'



	                    +$('#tag_lot_received_id').val()



	                    +'<input type="hidden" class="tag_saved" name="lt_item[tag_saved][]" value="'+$('#tag_saved').val()+'">'



	                    +'<input type="hidden" class="tag_id_section" name="lt_item[tag_id_section][]" value="'+$('#section_select').val()+'">'



	                    +'<input type="hidden" class="tag_id" name="lt_item[tag_id][]" value="'+$('#tag_id').val()+'">'



	                    +'<input type="hidden" class="lot_no" name="lt_item[lot_no][]" value="'+$('#tag_lot_received_id').val()+'">'



	                    +'<input type="hidden" class="id_lot_inward_detail" name="lt_item[id_lot_inward_detail][]" value="'+$('#tag_id_lot_inward_detail').val()+'">'



	                    +'<input type="hidden" class="lot_product" name="lt_item[lot_product][]" value="'+$('#tag_lt_prod').val()+'">'



	                    +'<input type="hidden" class="lot_id_design" name="lt_item[lot_id_design][]" value="'+$('#des_select').val()+'">'



	                    +'<input type="hidden" class="lot_id_sub_design" name="lt_item[lot_id_sub_design][]" value="'+$('#sub_des_select').val()+'">'



						+'<input type="hidden" class="quality_id" name="lt_item[quality_id][]" value="'+$('#quality_code').val()+'">'



	                    +'<input type="hidden" class="design_for" name="lt_item[design_for][]" value="'+$('#tag_design_for').val()+'">'



	                    +'<input type="hidden" class="purity" name="lt_item[purity][]" value="'+$('#id_purity').val()+'">'



	                    +'<input type="hidden" class="size" name="lt_item[size][]" value="'+$('#tag_size').val()+'">'



	                    +'<input type="hidden" class="no_of_piece" name="lt_item[no_of_piece][]" value="'+$('#tag_pcs').val()+'">'



	                    +'<input type="hidden" class="gross_wt" name="lt_item[gross_wt][]" value="'+$('#tag_gwt').val()+'">'



	                    +'<input type="hidden" class="less_wt" name="lt_item[less_wt][]" value="'+$('#tag_lwt').val()+'">'



	                    +'<input type="hidden" class="net_wt" name="lt_item[net_wt][]" value="'+$('#tag_nwt').val()+'">'



	                    +'<input type="hidden" class="calculation_based_on" name="lt_item[calculation_based_on][]" value="'+$('#tag_calculation_based_on').val()+'">'



	                    +'<input type="hidden" class="wastage_percentage" name="lt_item[wastage_percentage][]" value="'+$('#tag_wast_perc').val()+'">'



	                    +'<input type="hidden" class="id_mc_type" name="lt_item[id_mc_type][]" value="'+$('#tag_id_mc_type').val()+'">'



	                    +'<input type="hidden" class="making_charge" name="lt_item[making_charge][]" value="'+$('#tag_mc_value').val()+'">'



	                    +'<input type="hidden" class="sell_rate" name="lt_item[sell_rate][]" value="'+$('#tag_sell_rate').val()+'">'



	                    +'<input type="hidden" class="sale_value" name="lt_item[sale_value][]" value="'+$('#tag_sale_value').val()+'">'



	                    +'<input type="hidden" class="tag_product_short_code" name="lt_item[product_short_code][]" value="'+$('#tag_product_short_code').val()+'">'



	                    +'<input type="hidden" class="id_metal" name="lt_item[id_metal][]" value="'+$('#id_metal').val()+'">'



	                    +'<input type="hidden" class="tax_group_id" name="lt_item[tax_group_id][]" value="'+$('#tax_group_id').val()+'">'



	                    +'<input type="hidden" class="tag_sales_mode" name="lt_item[tag_sales_mode][]" value="'+$('#tag_sales_mode').val()+'">'



	                    +'<input type="hidden" class="tag_tax_type" name="lt_item[tag_tax_type][]" value="'+$('#tag_tax_type').val()+'">'



						+'<input type="hidden" class="charges_value" name="lt_item[charges_value][]" value="'+charges_value+'">'



						+'<input type="hidden" class="huid" name="lt_item[huid][]" value="'+$('#tag_huid').val()+'">'



						+'<input type="hidden" class="huid2" name="lt_item[huid2][]" value="'+$('#tag_huid2').val()+'">'



						+'<input type="hidden" class="cert_no" name="lt_item[huid2][]" value="'+$('#cert_no').val()+'">'



						+'<input type="hidden" class="cert_image" name="lt_item[cert_image][]" value="'+$("#cert_img_base64").val()+'">'



						+'<input type="hidden" class="stone_details" name="lt_item[stone_details][]" value=\''+$('#tag_stone_details').val()+'\'><input type="hidden" class="stone_price" name="lt_item[stone_price][]"><input type="hidden" class="normal_st_certif" value=""><input type="hidden" class="semiprecious_st_certif" value=""><input type="hidden" class="precious_st_certif" value="">'



						+'<input type="hidden" class="manufacture_code" name="lt_item[manufacture_code][]" value="'+$('#manufacture_code').val()+'">'



						+'<input type="hidden" class="style_code" name="lt_item[style_code][]" value="'+$('#style_code').val()+'">'



						+'<input type="hidden" class="narration" name="lt_item[narration][]" value="'+$('#narration').val()+'">'



						+'<input type="hidden" class="remarks" name="lt_item[remarks][]" value="'+$('#remarks').val()+'"><input type="hidden" class="is_suspense_stock" name="lt_item[is_suspense_stock][]" value="'+$('.issuspensestock').val()+'">'



						+'<input type="hidden" class="tag_purchase_cost" name="lt_item[tag_purchase_cost][]" value="'+$('#tag_purchase_cost').val()+'">'

						+'<input type="hidden" class="purchase_touch" name="lt_item[purchase_touch][]" value="'+$('#purchase_touch').val()+'">'



						+'<input type="hidden" class="tag_product_division" name="lt_item[tag_product_division][]" value="'+$('#tag_product_division').val()+'">'



						+'<input type="hidden" class="is_new_arrival" name="lt_item[is_new_arrival][]" value="'+$('#is_new_arrival').val()+'">'



						+chargesPostRow



						+attrsPostRow



						+othermetals



						+'<input type="hidden" class="gwt_uom_id" name="lt_item[gwt_uom_id][]" value="'+$('#gwt_uom_id').val()+'">'



						+'<input type="hidden" class="tag_cat_type" name="lt_item[tag_cat_type][]" value="'+$('#tag_cat_type').val()+'">'



						+'<input type="hidden" class="stone_calculation_based_on" name="lt_item[stone_calculation_based_on][]" value="'+$('#stone_calculation_based_on').val()+'">'



                        +'<input type="hidden" class="tag_img" value="'+$('#tag_img_url').val()+'" name="lt_item[tag_img][]">'



                    	+'<input type="hidden" class="tag_img_copy" name="lt_item[tag_img_copy][]" value="'+$('#tag_img_copy').val()+'">'



                    	+'<input type="hidden" class="tag_img_default" value="'+$('#tag_img_default').val()+'" name="lt_item[tag_img_default][]"/>'



						+'<input type="hidden" class="min_mc" value="'+$('#min_mc').val()+'">'



						+'<input type="hidden" class="min_va" value="'+$('#min_va').val()+'">'



						+'<input type="hidden" class="lot_purchase_touch" value="'+$('#tag_lt_prod option:selected').attr('data-touch')+'">'



						+'<input type="hidden" class="lot_rate_calc_type" value="'+$('#tag_lt_prod option:selected').attr('data-rate_calc_type')+'">'



						+'<input type="hidden" class="lot_making_charge" value="'+$('#tag_lt_prod option:selected').attr('data-making_charge')+'">'



						+'<input type="hidden" class="lot_mc_type" value="'+$('#tag_lt_prod option:selected').attr('data-mc_type')+'">'



						+'<input type="hidden" class="lot_wastage_percentage" value="'+$('#tag_lt_prod option:selected').attr('data-wastage_percentage')+'">'



						+'<input type="hidden" class="lot_rate" value="'+$('#tag_lt_prod option:selected').attr('data-rate')+'">'



						+'<input type="hidden" class="lot_calc_type" value="'+$('#tag_lt_prod option:selected').attr('data-calc_type')+'">'



						+'<input type="hidden" class="huid_details" name="lt_item[huid_details][]" value=\''+$('#other_huid_details').val()+'\'>'

						+'<input type="hidden" class="pur_wastage_percentage" name="lt_item[pur_wastage_percentage][]" value="' +$("#tag_pur_wast_perc").val() +'">'

						+'<input type="hidden" class="pur_id_mc_type" name="lt_item[pur_id_mc_type][]" value="' + $("#tag_pur_id_mc_type").val()+'">'

						+'<input type="hidden" class="pur_making_charge" name="lt_item[pur_making_charge][]" value="' +$("#tag_pur_mc_value").val() +'">'


	                +'</td>'



	                +'<td><span class="tag_code"></span></td>'



	                +'<td>'+$("#tag_lt_prod option:selected").text()+'</td>'



	                +'<td>'+$("#des_select option:selected").text()+'</td>'



	                +'<td>'+$("#sub_des_select option:selected").text()+'</td>'



	                +'<td>'+$("#tag_calculation_based_on option:selected").text()+'</td>'



	               // +'<td>'+$("#tag_size option:selected").text()+'</td>'



	                +'<td>'+$("#tag_pcs").val()+'</td>'



	                +'<td>'+parseFloat($("#tag_gwt").val()).toFixed(3)+'</td>'



	                +'<td><span class="tag_preview_lwt">'+$("#tag_lwt").val()+'</span></td>'



	                +'<td><span class="tag_preview_nwt">'+$("#tag_nwt").val()+'</span></td>'



	                +'<td>'+parseFloat($("#tag_wast_perc").val()).toFixed(2)+'</td>'



	               // +'<td>'+$("#tag_id_mc_type option:selected").text()+'</td>'



	                +'<td>'+$("#tag_mc_value").val()+'</td>'



	               // +'<td><a href="#" onClick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));" class="btn btn-default btn-sm"><i class="fa fa-plus"></i></a><input type="hidden" class="stone_details" name="lt_item[stone_details][]" value='+$('#tag_stone_details').val()+'><input type="hidden" class="stone_price" name="lt_item[stone_price][]"><input type="hidden" class="normal_st_certif" value=""><input type="hidden" class="semiprecious_st_certif" value=""><input type="hidden" class="precious_st_certif" value=""></td>'



	                +'<td class="td_image"><input type="hidden" class="tagged_images" value='+JSON.stringify(tag_pre_img_resource)+'><img src='+image_preview+' style="width:30px;height:30px;"><a  class="btn btn-secondary order_img"  id="edit" data-toggle="modal" onClick="view_tag_images($(this).closest(\'tr\'));" ><i class="fa fa-eye" ></i></a></td>'



	                +'<td><span class="tag_preview_sell_rate">'+$("#tag_sell_rate").val()+'</span></td>'



	                +'<td><span class="tag_preview_sale_value">'+$("#tag_sale_value").val()+'</span></td>'



	                +'<td><div style="display: flex;"><span id="items_add_'+total_row+'"><a style="" href="#" onClick="edit_tag($(this).closest(\'tr\'));" class="btn-del label label-primary" style="padding:5px;" data-toggle="tooltip" title="Edit"><i class="fa fa-edit"></i></a><a href="#" class="btn btn-danger btn-del btn-xs" style="padding:5px;" data-toggle="tooltip" title="Delete" data-target="#confirm-delete"><i class="fa fa-trash"></i></a></span></div></td>'



	                //onClick="remove_row($(this).closest(\'tr\'));"



	       +'</tr>';



	       if($('#lt_item_tag_preview > tbody  > tr').length>0)



        	{



        	    $('#lt_item_tag_preview > tbody > tr:first').before(row);



        	}else{



        	    $('#lt_item_tag_preview tbody').append(row);



        	}



        	set_tag_preview_class();



        	//calculateTagPreviewSaleValue();



        	$('#reset_tag_form').trigger('click');



        	checking_lot_availability();



        	calculate_tag_summary();



        //	get_lot_inwards_detail($('#tag_lot_received_id').val(),$('#tag_lt_prod').val(),'');



}



function view_tag_images(curRow)



{



	  data = [];



     var tag_codeimage1 =  base_url+'assets/img/tag';



	 $('#imageModal_bulk_edit').modal('show');



     $(".overlay").css('display',"none");



	 $.ajax({



        data: ( {'tag_id':curRow.find('.tag_id').val()}),



			  url:base_url+ "index.php/admin_ret_tagging/get_img_by_id?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),



			  dataType:"JSON",



			  type:"POST",



			  success:function(data){



				  retrive_img = data;



				  for (i = 0; i < data.length; i++)



				  {



					img_src = data[i].image;



					var preview = $('#order_images');



					var img = tag_codeimage1 + '/' + img_src;



					if (img_src) {



                    div = document.createElement("div");



                    div.setAttribute('class', 'col-md-3 images');



                    div.setAttribute('id', 'order_img_edit_' + [i]);



					$('.images').css('margin-right','25px');



                    key = [i];



                    param = img_src;



                    div.innerHTML += "<div class='form-group'><div class='image-input image-input-outline' id='kt_image_'><div class='image-input-wrapper'><img class='thumbnail' src='" + img + "'" + "style='width: 300px;height: 250px;'/></div></div>";



                    preview.append(div);



                }



            }



			  },



			  error:function(error)



				{



					$("div.overlay").css("display", "none");



				}



		  });



}



function set_tag_preview_class()



{



     var total_row = $('#lt_item_tag_preview tbody > tr').length;



     $('#lt_item_tag_preview > tbody tr').each(function(idx, row){



          $(this).removeClass();



          $(this).closest('tr').addClass(idx.toString());



     });



}



function edit_tag(curRow){



	console.log("curRow On Edit",curRow);



    $("div.overlay").css("display", "block");



    var item=curRow;



    $('#tag_saved').val(item.find('.tag_saved').val());



    $('#tag_id').val(item.find('.tag_id').val());



    $('#tag_lot_id').val(item.find('.lot_no').val());



    $('#tag_lt_prodId').val(item.find('.lot_product').val());



    $('#id_design').val(item.find('.lot_id_design').val());



    $('#id_sub_design').val(item.find('.lot_id_sub_design').val());



    $('#tag_id_lot_inward_detail').val(item.find('.id_lot_inward_detail').val());



    $('#calculation_based_on').val(item.find('.calculation_based_on').val());



    $('#id_metal').val(item.find('.id_metal').val());



    $('#tax_group_id').val(item.find('.tax_group_id').val());



    $('#tag_lot_no').val(item.find('.lot_no').val());



    $('#tag_sales_mode').val(item.find('.tag_sales_mode').val());



    $('#id_purity').val(item.find('.purity').val());



    $('#tag_product_short_code').val(item.find('.tag_product_short_code').val());



    $('#des_select').select2("val",item.find('.lot_id_design').val());



    $('#tag_pcs').val(item.find('.no_of_piece').val());



    $('#tag_gwt').val(item.find('.gross_wt').val());



    $('#tag_lwt').val(item.find('.less_wt').val());



    $('#tag_nwt').val(item.find('.net_wt').val());



    $('#tag_calculation_based_on').val(item.find('.calculation_based_on').val());



    $('#tag_sell_rate').val(item.find('.sell_rate').val());



    $('#tag_mc_value').val(item.find('.making_charge').val());



    $('#tag_id_mc_type').val(item.find('.id_mc_type').val());



    $('#tag_wast_perc').val(item.find('.wastage_percentage').val());



    $('#tag_tax_type').val(item.find('.tag_tax_type').val());



    $('#tag_stone_details').val(item.find('.stone_details').val());



    $('#tag_charge').val(item.find('.charges_value').val());



	$("#tag_charge_amt").val(item.find('.charges_value').val());



	$("#tag_huid").val(item.find('.huid').val());



	$("#tag_huid2").val(item.find('.huid2').val());



	$("#cert_no").val(item.find('.cert_no').val());



	$("#cert_img_base64").val(item.find('.cert_image').val());



	$('#manufacture_code').val(item.find('.manufacture_code').val());



	$('#style_code').val(item.find('.style_code').val());



	$('#remarks').val(item.find('.remarks').val());



	$('#narration').val(item.find('.narration').val());



	$('#tag_purchase_cost').val(item.find('.tag_purchase_cost').val());


	$('#purchase_touch').val(item.find('.purchase_touch').val());



	$('#tag_product_division').val(item.find('.tag_product_division').val());



	$('#gwt_uom_id').val(item.find('.gwt_uom_id').val());



	$('#tag_cat_type').val(item.find('.tag_cat_type').val());



	$('#tag_size').select2("val",item.find('.size').val());



	$('#id_size').val(item.find('.size').val());



	$('#stone_calculation_based_on').val(item.find('.stone_calculation_based_on').val());



	$('#min_mc').val(item.find('.min_mc').val());



	$('#min_va').val(item.find('.min_va').val());



	$('#tag_images').val(item.find('.tagged_images').val());



    $('#section_select').select2("val",item.find('.tag_id_section').val());



    $('#id_section').val(item.find('.tag_id_section').val());



	$('#id_quality').val(item.find('.quality_id').val());



	$('#quality_code').select2("val",item.find('.quality_id').val());



	$('#is_new_arrival').val(item.find('.is_new_arrival').val());
	$('#tag_pur_wast_perc').val(item.find('.pur_wastage_percentage').val());
	calc_pur_wastage('WV');
	$('#tag_pur_id_mc_type').val(item.find('.pur_id_mc_type').val());
	$('#tag_pur_mc_value').val(item.find('.pur_making_charge').val());



	preview_image_on_edit(item.find('.cert_image').val(), 'cert_img_preview');



	$('#tag_gwt').focus();



	//Stone Update



	modalStoneDetail=(item.find('.stone_details').val()!='' ?JSON.parse(item.find('.stone_details').val()) :[]);



    $('#stone-det tbody').empty();



	var stnRow = "";



	$.each(modalStoneDetail, function (key, item) {



        stnRow +='<tr id='+item.stone_id+'>'



                +'<td>'+item.stone_name+'</td>'



                +'<td>'+item.stone_pcs+'</td>'



                +'<td>'+item.stone_wt+' '+item.stone_uom_name+'</td>'



                +'<td>'+item.stone_price+'</td>'



           +'</tr>';



    })



    $('#stone-det tbody').append(stnRow);



	//Charges Update



	modalChargeDetail=(item.find('.charges').val()!='' ?JSON.parse(item.find('.charges').val()) :[]);



	display_charges_details();



	//Attribute Update



	modalAttributeDetail=(item.find('.tag_attributes').val()!='' ?JSON.parse(item.find('.tag_attributes').val()) :[]);



	display_attribute_details();



	//Other Metals Update



	modalOtherMetalDetail=(item.find('.othermetals').val()!='' ?JSON.parse(item.find('.othermetals').val()) :[]);



	display_othermetals_details();



	// huid update



        modalHuidDetail=(item.find('.huid_details').val()!='' ?JSON.parse(item.find('.huid_details').val()) :[]);



	    $('.other_huid_details').val(item.find('.huid_details').val()!='' ?JSON.parse(item.find('.huid_details').val()) :'')



	let oth_m_wt = 0;



	let oth_m_amt = 0;



	$.each(modalOtherMetalDetail, function (othkey, othitem) {



		oth_m_wt += isNaN(parseFloat(othitem.nwt)) ? 0 : parseFloat(othitem.nwt);



		oth_m_amt += isNaN(parseFloat(othitem.amount)) ? 0 : parseFloat(othitem.amount);



	});



	$("#other_metal_wt").val(oth_m_wt);



	$("#other_metal_amount").val(oth_m_amt);



	setTimeout(function(){



         $("div.overlay").css("display", "block");



        calculateTagFormSaleValue();



         $("div.overlay").css("display", "none");



   	},1500);



    get_received_lots();



    get_lot_products();



    curRow.remove();



    set_tag_preview_class();



    update_po_details();



}



//New Tag Form changes



//Charges



function get_productCharges() {



	let prod_id = $('#tag_lt_prod').val();



	modalChargeDetail = [];



	$('#table_charges tbody').empty();



	let _href = base_url+'index.php/admin_ret_tagging/get_product_charges';



	let _data = {



		"prod_id" : prod_id



	}



	_ajaxCallPost(_href, _data)



	.then((data) => {



		console.log("product charges", data);



		update_charges(data);



	})



	.catch((error) => {



		console.log("Error On get_productCharges ",error);



		$.toaster({ priority : 'danger', title : 'Warning!', message : "Error occured in retriving charges based on product."});



	});



}



$(".add_tag_charge").on('click',function(){



    openChargeModal();



});



$(document).on('click', '#table_charges tbody tr .create_charge_item_details, .add_charges', function(){



    if(validate_charges_row()){



		create_new_empty_charge_item();



	}else{



		alert("Please fill required charge fields");



	}



});



$('#cus_chargeModal  #close_charge_details').on('click', function(){



	$('#cus_chargeModal .modal-body').find('#table_charges tbody').empty();



});



function openChargeModal() {



	$('#cus_chargeModal .modal-body').find('#table_charges tbody').empty();



    $('#cus_chargeModal').modal('show');



    console.log(modalChargeDetail);



    if(modalChargeDetail.length > 0){



        $.each(modalChargeDetail, function (key, item) {



            create_new_empty_charge_item(item);



        })



    }else{



        create_new_empty_charge_item();



    }



}



function create_new_empty_charge_item(selData = [])



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



function remove_charge(obj)



{



	$(obj).closest('tr').remove();



	addChargeModal_buttons();



	calculateTagFormSaleValue();



}



$('#cus_chargeModal  #update_charge_details').on('click', function(){



	if(validate_charges_row())



    {



		var charge_details=[];



		var charge_value=0;



		modalChargeDetail = []; // Reset Old Value of charge modal



		$('#cus_chargeModal .modal-body #table_charges> tbody  > tr').each(function(index, tr) {



			charge_value+=parseFloat($(this).find('.chargesValue').val());



			charge_details.push({



						'charge_value'       : $(this).find('.chargesValue').val(),



						'charge_id'          : $(this).find('.chargesType').val(),



						'name_charge'       : $(this).find('.chargesType :selected').text(),



						});



		});



		modalChargeDetail = charge_details;



		console.log(modalChargeDetail);



		// Update charge Summary



		display_charges_details();



		$("#tag_charge").val(charge_value);



		$("#tag_charge_amt").val(charge_value);



		calculateTagFormSaleValue();



        $('#cus_chargeModal .modal-body').find('#table_charges tbody').empty();



        $('#cus_chargeModal').modal('hide');



        $('#addTagToPreview').focus();



    }



    else



    {



    	alert('Please Fill The Required charge Details');



    }



});



function update_charges(product_charges)



{



	var charge_details=[];



	var charge_value=0;



	modalChargeDetail = []; // Reset Old Value of charge modal



	$(product_charges).each(function(index, prod_charge) {



		charge_value +=	parseFloat(prod_charge.charge_value);



		charge_details.push({



					'charge_value'       : prod_charge.charge_value,



					'charge_id'          : prod_charge.charge_id,



					'name_charge'        : prod_charge.name_charge,



					});



	});



	if(ctrl_page[1]=='tagging' && ctrl_page[2]=='add'){



		if(charge_details.length>0){



			$(".other_charges").css("display", "block");



		}else{



			$(".other_charges").css("display", "none");



		}



	}



	modalChargeDetail = charge_details;



	console.log(modalChargeDetail);



	// Update charge Summary



	display_charges_details();



	$("#tag_charge").val(charge_value);



	$("#tag_charge_amt").val(charge_value);



	calculateTagFormSaleValue();



}



function add_charges_value(row) {



	console.log("row charges",row);



	let chargesType =  $(row).find(".chargesType").val();



	$(charges_list).each(function(idx, charges){



		if(charges.id_charge == chargesType) {



			$(row).find(".chargesValue").val(charges.value_charge);



		}



	});



	calculateTagFormSaleValue();



}



//End charges



//calculate tag preview sale



function calculateTagPreviewSaleValue()



{



    $('#lt_item_tag_preview > tbody tr').each(function(idx, row){



         curRow = $(this);



            // stone_price, stone_wt, certification_price



        	var total_price 		= 0;



        	var base_value_price 	= 0;



        	var arrived_value_price = 0;



        	var base_value_tax 		= 0;



        	var arrived_value_tax 	= 0;



        	var base_rate_tax 		= 0;



        	var arrived_rate_tax 	= 0;



        	var total_tax_per 		= 0;



        	var total_tax_rate 		= 0;



        	var rate_with_mc 		= 0;



        	var material_price  	= 0; // Not worked



        	var stone_price  		= 0; // Not worked



        	var tot_stone_wt  			= 0; // Not worked



        	var certification_price = 0; // Not worked



            var stone_wt=0;



            var stone_details=curRow.find('.stone_details').val();



            if(stone_details!='')



            {



                var st_details=JSON.parse(stone_details);



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



            curRow.find('.less_wt').val(parseFloat(tot_stone_wt).toFixed(3));



        	var gross_wt 			= (isNaN(curRow.find('.gross_wt').val()) || curRow.find('.gross_wt').val() == '')  ? 0 : curRow.find('.gross_wt').val();



        	var less_wt  			= (isNaN(curRow.find('.less_wt').val()) || curRow.find('.less_wt').val() == '')  ? 0 : curRow.find('.less_wt').val();



        	var net_wt 				= parseFloat(gross_wt)-parseFloat(less_wt);



        	var calculation_type 	= (isNaN(curRow.find('.calculation_based_on').val()) || curRow.find('.calculation_based_on').val() == '')  ? 0 : curRow.find('.calculation_based_on').val();



        	var metal_type 			= (isNaN(curRow.find('.id_metal').val()) || curRow.find('.id_metal').val() == '')  ? 1 : curRow.find('.id_metal').val();



        	var sales_mode 			= (isNaN(curRow.find('.tag_sales_mode').val()) || curRow.find('.tag_sales_mode').val() == '')  ? 1 : curRow.find('.tag_sales_mode').val();



        	var tax_type 			= (isNaN(curRow.find('.tag_tax_type').val()) || curRow.find('.tag_tax_type').val() == '')  ? 1 : curRow.find('.tag_tax_type').val();



        	var tax_group 			= (isNaN(curRow.find('.tax_group_id').val()) || curRow.find('.tax_group_id').val()=='')  ? 1 : curRow.find('.tax_group_id').val();



        	/*var stone_price  		= (isNaN($('.stone_price').val()) || $('.stone_price').val() == '')  ? 0 : $('.stone_price').val();



        	var stone_wt  			= (isNaN($('.stone_wt').val()) || $('.stone_wt').val() == '')  ? 0 : $('.stone_wt').val();



        	var certification_price = (isNaN($('.certification_price').val()) || $('.certification_price').val() == '')  ? 0 : $('.certification_price').val();*/



          	curRow.find('.net_wt').val(parseFloat(net_wt).toFixed(3))



            if(metal_type==1)



        	{



        		var rate_per_grm = $('#metal_rate').val();//Gold



        	}else if(metal_type==2){



        		var rate_per_grm = $('#silverrate_1gm').val();//Silver



        	}



        	else if(metal_type==3){



        		var rate_per_grm = $('#platinum_1g').val();//Platinum



        	}



            var retail_max_mc           = (isNaN(curRow.find('.making_charge').val()) || curRow.find('.making_charge').val() == '')  ? 0 : curRow.find('.making_charge').val();



        	var tot_wastage             = (isNaN(curRow.find('.wastage_percentage').val()) || curRow.find('.wastage_percentage').val() == '')  ? 0 : curRow.find('.wastage_percentage').val();



        	var no_of_piece             = (isNaN(curRow.find('.no_of_piece').val()) || curRow.find('.no_of_piece').val() == '')  ? 0 : curRow.find('.no_of_piece').val();



        	/**



        	*	Amount calculation based on settings (without discount and tax )



        	*   0 - Wastage on Gross weight And MC on Gross weight



        	*   1 - Wastage on Net weight And MC on Net weight



        	*   2 - Wastage On Netwt And MC On Grwt



        	*   rate_with_mc = Metal Rate + Stone + OM + Wastage + MC



        	*/



        	if(calculation_type == 0){



        		var wast_wgt      = parseFloat(parseFloat(gross_wt) * parseFloat(tot_wastage/100)).toFixed(3);



        		if(curRow.find('.id_mc_type').val() != 3){



            		var mc_type       =  parseFloat(curRow.find('.id_mc_type').val() == 2 ? parseFloat(retail_max_mc * gross_wt ) : parseFloat(retail_max_mc * no_of_piece));



            		// Metal Rate + Stone + OM + Wastage + MC



            		rate_with_mc = parseFloat(parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(mc_type)+parseFloat(stone_price)+parseFloat(material_price)+parseFloat(certification_price));



        		}else{



        		    var mc_type       =  parseFloat((parseFloat(gross_wt) * parseFloat(rate_per_grm)) * parseFloat(retail_max_mc/100)).toFixed(3);



            		// Metal Rate + Stone + OM + Wastage + MC



            		rate_with_mc = parseFloat(parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(mc_type)+parseFloat(stone_price)+parseFloat(material_price)+parseFloat(certification_price));



        		}



        	}



        	else if(calculation_type == 1){



        		var wast_wgt      = parseFloat(parseFloat(net_wt) * parseFloat(tot_wastage/100)).toFixed(3);



        		if(curRow.find('.id_mc_type').val() != 3){



            		var mc_type       =  parseFloat(curRow.find('.id_mc_type').val() == 2 ? parseFloat(retail_max_mc * net_wt ) : parseFloat(retail_max_mc * no_of_piece));



            		// Metal Rate + Stone + OM + Wastage + MC



            		rate_with_mc = parseFloat(parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(mc_type)+parseFloat(stone_price)+parseFloat(material_price)+parseFloat(certification_price));



        		}else{



        		    var mc_type       =   parseFloat((parseFloat(net_wt) * parseFloat(rate_per_grm)) * parseFloat(retail_max_mc/100)).toFixed(3);



            		// Metal Rate + Stone + OM + Wastage + MC



            		rate_with_mc = parseFloat(parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(mc_type)+parseFloat(stone_price)+parseFloat(material_price)+parseFloat(certification_price));



        		}



        	}



        	else if(calculation_type == 2){



        		var wast_wgt      = parseFloat(parseFloat(net_wt) * parseFloat(tot_wastage/100)).toFixed(3);



        		if(curRow.find('.id_mc_type').val() != 3){



            		var mc_type       =  parseFloat(curRow.find('.id_mc_type').val() == 2 ? parseFloat(retail_max_mc * gross_wt ) : parseFloat(retail_max_mc * no_of_piece));



            		// Metal Rate + Stone + OM + Wastage + MC



            	    rate_with_mc = parseFloat((parseFloat(rate_per_grm) * (parseFloat(wast_wgt) + parseFloat(net_wt)))+ parseFloat(mc_type))+parseFloat(stone_price)+parseFloat(material_price)+parseFloat(certification_price);



        		}else{



        		    var mc_type      = parseFloat((parseFloat(gross_wt) * parseFloat(rate_per_grm)) * parseFloat(retail_max_mc/100)).toFixed(3);



            		// Metal Rate + Stone + OM + Wastage + MC



            	    rate_with_mc = parseFloat((parseFloat(rate_per_grm) * (parseFloat(wast_wgt) + parseFloat(net_wt)))+ parseFloat(mc_type))+parseFloat(stone_price)+parseFloat(material_price)+parseFloat(certification_price);



        		}



        	}



        	else if(calculation_type == 3){



        		var sell_rate  = (isNaN(curRow.find('.sell_rate').val()) || curRow.find('.sell_rate').val() == '')  ? 0 : curRow.find('.sell_rate').val();



        		var adjusted_item_rate  = 0;



        	    caculated_item_rate = parseFloat(sell_rate);



        	    $('.caculated_item_rate').val(caculated_item_rate);



        	    rate_with_mc = parseFloat(parseFloat(adjusted_item_rate) > 0 ? parseFloat(adjusted_item_rate) : caculated_item_rate );



        	}



        	else if(calculation_type == 4){



        		var sell_rate  = (isNaN(curRow.find('.sell_rate').val()) || curRow.find('.sell_rate').val() == '')  ? 0 : curRow.find('.sell_rate').val();



        		var adjusted_item_rate  = 0;



        	    caculated_item_rate = parseFloat((parseFloat(sell_rate)*parseFloat(net_wt))*parseFloat(no_of_piece));



        	    $('.caculated_item_rate').val(caculated_item_rate);



        	    rate_with_mc = parseFloat(parseFloat(adjusted_item_rate) > 0 ? parseFloat(adjusted_item_rate) : caculated_item_rate );



        	}



			/*let total_charges = 0;



			let charges_row = $("#table_charges tbody tr");



			$(charges_row).each(function(idx, row){



				let rowChargesValue = parseFloat($(row).find('.chargesValue').val());



				total_charges += rowChargesValue;



			});*/



			let total_charges = $(curRow).find('.charges_value').val();



			console.log("total_charges",total_charges);



			console.log("rate_with_mc",rate_with_mc);



			var rate_with_charges = parseFloat(rate_with_mc) + parseFloat(total_charges);



        	console.log('Calculation : '+calculation_type);



        	console.log('Wastage : '+wast_wgt);



        	console.log('Total Wastage : '+tot_wastage);



        	console.log('MC : '+mc_type);



        	console.log('Rate with MC : '+rate_with_mc);



        	console.log(' MC TYPE : '+mc_type);



        	console.log(' Rate Per Gram : '+rate_per_grm);



        	console.log('Gross Wt : '+gross_wt);



        	// Tax Calculation



            if(tax_details.length > 0){



            		var base_value_tax	= parseFloat(calculate_base_value_tax(rate_with_charges,tax_group)).toFixed(2);



            		var base_value_amt	= parseFloat(parseFloat(rate_with_charges)+parseFloat(base_value_tax)).toFixed(2);



            		var arrived_value_tax= parseFloat(calculate_arrived_value_tax(base_value_amt,tax_group)).toFixed(2);



            		var arrived_value_amt= parseFloat(parseFloat(base_value_amt)+parseFloat(arrived_value_tax)).toFixed(2);



            		total_tax_rate	= parseFloat(parseFloat(base_value_tax)+parseFloat(arrived_value_tax)).toFixed(2);



            }



            total_price = parseFloat(parseFloat(rate_with_charges)+parseFloat(total_tax_rate)).toFixed(2);



        	curRow.find('.sale_value').val(Math.round(parseFloat(total_price)).toFixed(2));



        	curRow.find('.tag_preview_nwt').html((parseFloat(net_wt)).toFixed(3));



        	curRow.find('.tag_preview_lwt').html((parseFloat(less_wt)).toFixed(3));



        	curRow.find('.tag_preview_sale_value').html((parseFloat(total_price)).toFixed(2));



        	console.log('Amount : '+total_price);



        	console.log('Tax Rate : '+total_tax_rate);



        	console.log('Arrived value : '+arrived_value_price);



        	console.log('*************************');



    });



}



// Remove Rows



function remove_row(curRow, askConfirm = 1)



{



	if(ctrl_page[2]!='edit')



	{



		if(askConfirm == 1){



			var answer = confirm("Are you sure want to remove this Item Records?")



			if(answer){



		     	 curRow.remove();



				 // Check Lot Items Validations



					var last_item_id  = $('#lt_item_list > tbody tr:last').attr('class');



					if($('#lt_item_list > tbody tr').length >= 1) {



					    $("#items_add_"+last_item_id).css('display','block');



						setTimeout(function() {



						  	$('#lt_item_list > tbody tr:last td:last').find('a:first').focus();



						}, 600);



				    }



			}



		}else{



			curRow.remove();



		 	// Check Lot Items Validations



			var last_item_id  = $('#lt_item_list > tbody tr:last').attr('class');



			if($('#lt_item_list > tbody tr').length >= 1) {



			    $("#items_add_"+last_item_id).css('display','block');



				setTimeout(function(){



				  $('#lt_item_list > tbody tr:last td:last').find('a:first').focus();



				}, 600);



		    }



		    auto_added_tagging_datas();



		}



	}



    checking_lot_availability();



	calculate_tag_summary();



}



//calculate tag preview sale



function isLoaded()



{



	setTimeout(function(){



	  var pdfFrame = window.frames["iFramePdf"];



	  pdfFrame.focus();



	  pdfFrame.print();



	  $("#tag_gwt").focus();



	  pdfFrame.addEventListener('onclose', oncloseIframe, false);



	}, 300);



}



function oncloseIframe() {



	console.log("closed iframe");



}



$('#des_select').on('change',function(){



    $('#sub_des_select option').remove();



   if(this.value!='')



   {



       if(ctrl_page[2]=='add' && ctrl_page[1]=='tagging')



       {



           get_ActiveTagSubDesingns();



       }



   }



   get_ActiveSubDesingns('sub_des_select');



});



$('#bulkedit_des_update').on('change',function(){



    get_ActiveSubDesingns('bulkedit_sub_des_update');



});



$('#des_select_filter').on('change',function()



{



    if(this.value!='')



    {



        if(ctrl_page[2]=='tag_edit')



        {



            get_ActiveSubDesingnsFilter();



        }



    }



});



function get_ActiveTagSubDesingns()



{



	$('#sub_des_select option').remove();



	$.ajax({



	type: 'POST',



	url: base_url+'index.php/admin_ret_tagging/get_ActiveSubDesingns',



	data :{'design_no':$('#des_select').val(), 'id_product':$('#tag_lt_prod').val(), 'lot_from' : $('#tag_lot_received_id option:selected').attr('data-lotfrom'),  'id_lot_no' : $('#tag_lot_received_id').val(),'id_lot_inward_detail':$('#tag_lt_prod option:selected').attr('data-id_lot_inward_detail')},



	dataType:'json',



	success:function(data){



		var id =  $("#id_sub_design").val();



		$.each(data, function (key, item) {



		    $("#sub_des_select").append(



		    $("<option></option>")



		    .attr("value", item.id_sub_design)



		    .text(item.sub_design_name)



		    );



		});



		$("#sub_des_select").select2(



		{



			placeholder:"Select Sub Design",



			allowClear: true



		});



		    $("#sub_des_select").select2("val",(id!='' && id>0?id:''));



		}



	});



}



function get_ActiveSubDesingnsFilter()



{



	$('#sub_des_filter option').remove();



	$("#sub_des_filter").select2("val",'');



	$.ajax({



	type: 'POST',



	url: base_url+'index.php/admin_ret_tagging/get_ActiveSubDesingns',



	data :{'design_no':$('#des_select_filter').val(),'id_product':$('#prod_select').val()},



	dataType:'json',



	success:function(data){



		var id =  $("#id_sub_design").val();



		$.each(data, function (key, item) {



		    $("#sub_des_filter").append(



		    $("<option></option>")



		    .attr("value", item.id_sub_design)



		    .text(item.sub_design_name)



		    );



		});



		$("#sub_des_filter").select2(



		{



			placeholder:"Select Sub Design",



			allowClear: true



		});



		    $("#sub_des_filter").select2("val",(id!='' && id>0?id:''));



		}



	});



}



function get_ActiveSubDesingns(id_select = "")



{



	$('#'+id_select+' option').remove();



	$("#" + id_select).select2("val", '');



	var id_design = "";



	if(id_select == 'sub_des_select') {



		id_design = $('#des_select').val();



	} else if(id_select == 'bulkedit_sub_des_update') {



		id_design = $('#bulkedit_des_update').val();



	}



	var id_product = $('#prod_select').val();



	if(id_product > 0 && id_design > 0) {



		$.ajax({



		type: 'POST',



		url: base_url+'index.php/admin_ret_reports/get_ActiveSubDesign',



		data :{'id_design':id_design,'id_product':id_product},



		dataType:'json',



		success:function(data){



				var id = '';



				if(id_select == 'sub_des_select') {



					id =  $("#id_sub_design").val();



				}



				$.each(data, function (key, item) {



					$("#"+id_select).append(



					$("<option></option>")



					.attr("value", item.id_sub_design)



					.text(item.sub_design_name)



					);



				});



				$("#"+id_select).select2(



				{



					placeholder:"Select Sub Design",



					allowClear: true



				});



				$("#"+id_select).select2("val",(id!='' && id>0?id:''));



			}



		});

	}

}





function get_wastage_settings_details()



{



    $.ajax({



	type: 'POST',



	url: base_url+'index.php/admin_ret_tagging/get_wastage_settings_details',



	dataType:'json',



	success:function(data){



	        wast_settings_details=data;



		}



	});



}



$('#sub_des_select').on('change', function (e) {

	if(this.value!=''){

		set_tagging_wastage_and_mc();

		get_attributes_from_subdesign();

	}

});



$(document).on('change', '#tag_gwt', function(e){



   set_tagging_wastage_and_mc();
   calc_pur_wastage("WV");



});



/*function set_tagging_wastage_and_mc()



{



    $('#tag_wast_perc').val(0);



    $('#tag_mc_value').val(0);



    $('#tag_id_mc_type').val('');



    if($('#tag_lt_prodId').val()=='' || $('#tag_lt_prodId').val()==null)



    {



        //$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Product"});



    }else if($('#des_select').val()=='' || $('#des_select').val()==null)



    {



        //$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Design"});



    }else if($('#sub_des_select').val()=='' || $('#sub_des_select').val()==null)



    {



        //$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Sub Design"});



    }



    else



    {



        $.each(wast_settings_details,function(key,items){



            if((items.id_product==$('#tag_lt_prodId').val()) && (items.id_design==$('#des_select').val()) && (items.id_sub_design==$('#sub_des_select').val()) )



            {



                if(items.wastage_type==1)//Fixed



                {



                    $('#tag_wast_perc').val(items.wastag_value);



                    calc_wastage('WV');



                    $('#tag_mc_value').val(items.mc_cal_value);



                    $('#tag_id_mc_type').val(items.mc_cal_type);



                }



                else if(items.wastage_type==2)//Flexiable



                {



                    $.each(items.weight_range_det,function(i,result){



                        if((parseFloat(result.wc_from_weight) <= parseFloat($('#tag_gwt').val())) && (parseFloat($('#tag_gwt').val()) <= parseFloat(result.wc_to_weight))){



                            $('#tag_wast_perc').val(result.wc_percent);



                            calc_wastage('WV');



                            $('#tag_mc_value').val(result.mc);



                            $('#tag_id_mc_type').val(items.mc_cal_type);



                        }



                    });



                }



            }



        });



    }



}*/



function set_tagging_wastage_and_mc()



{



	$('#tag_wast_perc').val(0);
	
	$('#tag_wast_value').val(0);

	$('#tag_mc_value').val(0);



	$('#tag_id_mc_type').val('');



	if($('#tag_lt_prodId').val()=='' || $('#tag_lt_prodId').val()==null)



	{



		//$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Product"});



	}else if($('#des_select').val()=='' || $('#des_select').val()==null)



	{



		//$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Design"});



	}else if($('#sub_des_select').val()=='' || $('#sub_des_select').val()==null)



	{



		//$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Sub Design"});



	}



	else if($('#is_va_mc_based_on_branch').val() == 1 && $('#branch_select').val()==null)



	{



		//$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Sub Design"});



	}



	else



	{



		$.each(wast_settings_details,function(key,items){



			if((items.id_product==$('#tag_lt_prodId').val()) && (items.id_design==$('#des_select').val()) && (items.id_sub_design==$('#sub_des_select').val()) && ($('#is_va_mc_based_on_branch').val() == 1 ? items.id_branch==$('#tag_branch_va_mc').val(): true) )



			{
				sell_mc_va_edit_access = parseFloat($("#sell_mc_va_edit_access").val());



				if(items.wastage_type==1)//Fixed



				{



					if(items.wastag_method == 1) {



						$('#tag_wast_perc').val(items.wastag_value);

						if (items.wastag_value != null && items.wastag_value !== "" && sell_mc_va_edit_access == 0) {
							$('#tag_wast_perc').prop('readonly', true);
							$('#tag_wast_value').prop('readonly', true);
							$('#tag_id_mc_type').prop('disabled', true);
							$('#tag_id_mc_type').css('pointer-events', 'none');
							$('#tag_mc_value').prop('readonly', true);
						}	
						else{
							$('#tag_wast_perc').prop('readonly', false);
							$('#tag_wast_value').prop('readonly', false);
							$('#tag_id_mc_type').prop('disabled', false);
							$('#tag_mc_value').prop('readonly', false);
						}


						calc_wastage('WV');



					} else if(items.wastag_method == 2) {



						$('#tag_wast_value').val(items.wastag_value_wt);
						if (items.wastag_value_wt != null && items.wastag_value_wt !== "" && sell_mc_va_edit_access == 0) {
							$('#tag_wast_perc').prop('readonly', true);
							$('#tag_wast_value').prop('readonly', true);
							$('#tag_id_mc_type').prop('disabled', true);
							$('#tag_id_mc_type').css('pointer-events', 'none');
							$('#tag_mc_value').prop('readonly', true);
						}
						else{
							$('#tag_wast_perc').prop('readonly', false);
							$('#tag_wast_value').prop('readonly', false);
							$('#tag_id_mc_type').prop('disabled', false);
							$('#tag_mc_value').prop('readonly', false);
						}



						calc_wastage('WP');



					}



					$('#tag_mc_value').val(items.mc_cal_value);



					$('#tag_id_mc_type').val(items.mc_cal_type);



				}



				else if(items.wastage_type==2)//Flexiable



				{



					$.each(items.weight_range_det,function(i,result){



						if((parseFloat(result.wc_from_weight) <= parseFloat($('#tag_gwt').val())) && (parseFloat($('#tag_gwt').val()) <= parseFloat(result.wc_to_weight))){



							if(result.wc_method == 1) {



								$('#tag_wast_perc').val(result.wc_percent);

								if (result.wc_percent != null && result.wc_percent !== "" && sell_mc_va_edit_access == 0) {
									$('#tag_wast_perc').prop('readonly', true);
									$('#tag_wast_value').prop('readonly', true);
									$('#tag_id_mc_type').prop('disabled', true);
									$('#tag_id_mc_type').css('pointer-events', 'none');
									$('#tag_mc_value').prop('readonly', true);
								}
								else{
									$('#tag_wast_perc').prop('readonly', false);
									$('#tag_wast_value').prop('readonly', false);
									$('#tag_id_mc_type').prop('disabled', false);
									$('#tag_mc_value').prop('readonly', false);
								}


								calc_wastage('WV');



							} else if(result.wc_method == 2) {



								$('#tag_wast_value').val(result.wc_wt);

								if (items.wastag_value_wt != null && items.wastag_value_wt !== "" && sell_mc_va_edit_access == 0) {
									$('#tag_wast_perc').prop('readonly', true);
									$('#tag_wast_value').prop('readonly', true);
									$('#tag_id_mc_type').prop('disabled', true);
									$('#tag_id_mc_type').css('pointer-events', 'none');
									$('#tag_mc_value').prop('readonly', true);
								}
								else{
									$('#tag_wast_perc').prop('readonly', false);
									$('#tag_wast_value').prop('readonly', false);
									$('#tag_id_mc_type').prop('disabled', false);
									$('#tag_mc_value').prop('readonly', false);
								}



								calc_wastage('WP');



							}



							$('#tag_mc_value').val(result.mc);



							$('#tag_id_mc_type').val(result.mc_type);



						}



					});



				}



			}



		});



	}



}



$(document).on("change", "#tag_branch_va_mc", function() {



    $("#des_select").select2('val', '')



    $("#sub_des_select").select2('val', '')



});



function calc_wastage($wast_type) {



	console.log("calc_wastage",$wast_type);



	let calculation_type 	= isNaN($('#calculation_based_on').val()) || $('#calculation_based_on').val() == ''  ? "" : $('#calculation_based_on').val();



	let gwt = (isNaN($('#tag_gwt').val()) || $('#tag_gwt').val() == '')  ? 0 : $('#tag_gwt').val();



	let net_wt = (isNaN($('#tag_nwt').val()) || $('#tag_nwt').val() == '')  ? 0 : $('#tag_nwt').val();



	let wt = 0;



	if(calculation_type == 0){



		wt = gwt;



	} else if(calculation_type == 1 || calculation_type == 2){



		wt  = net_wt;



	}



	if($wast_type == 'WV') {



		let tot_wastage = $("#tag_wast_perc").val() > 0 ? $("#tag_wast_perc").val() : 0;



		let wast_wgt      = parseFloat(parseFloat(wt) * parseFloat(tot_wastage/100)).toFixed(3);



		$("#tag_wast_value").val(wast_wgt);



	} else if($wast_type == 'WP') {



		let tot_wastage = $("#tag_wast_value").val() > 0 ? $("#tag_wast_value").val() : 0;



		if(parseFloat(tot_wastage) <= parseFloat(net_wt)) {



			let wast_perc  = parseFloat(parseFloat(tot_wastage / parseFloat(wt)) * 100 ).toFixed(2);



			$("#tag_wast_perc").val(wast_perc);



		} else {



			calc_wastage("WV");



		}



	}



}
function calc_pur_wastage($pur_wast_type){
	let calculation_type = isNaN($('#calculation_based_on').val()) || $('#calculation_based_on').val() == ''  ? "" : $('#calculation_based_on').val();
	let gwt = (isNaN($('#tag_gwt').val()) || $('#tag_gwt').val() == '')  ? 0 : $('#tag_gwt').val();
	let net_wt = (isNaN($('#tag_nwt').val()) || $('#tag_nwt').val() == '')  ? 0 : $('#tag_nwt').val();
	let wt = 0;
	if(calculation_type == 0){
		wt = gwt;
	} else if(calculation_type == 1 || calculation_type == 2){
		wt  = net_wt;
	}else{
		$('#tag_pur_wast_perc').prop('readonly', true);
		$('#tag_pur_wast_value').prop('readonly', true);
		$('#tag_pur_id_mc_type').css('pointer-events', 'none');
		$('#tag_pur_id_mc_type').prop('disabled', true);
		$('#tag_pur_mc_value').prop('readonly', true);
		$('#tag_pur_wast_perc').val(0);
	}
	if($pur_wast_type == 'WV') {
		let tot_wastage = $("#tag_pur_wast_perc").val() > 0 ? $("#tag_pur_wast_perc").val() : 0;
		let wast_wgt      = parseFloat(parseFloat(wt) * parseFloat(tot_wastage/100)).toFixed(3);
		$("#tag_pur_wast_value").val(wast_wgt);
	} else if($pur_wast_type == 'WP') {
		let tot_wastage = $("#tag_pur_wast_value").val() > 0 ? $("#tag_pur_wast_value").val() : 0;
		if(parseFloat(tot_wastage) <= parseFloat(net_wt)) {
			let wast_perc  = parseFloat(parseFloat(tot_wastage / parseFloat(wt)) * 100 ).toFixed(2);
			$("#tag_pur_wast_perc").val(wast_perc);
		}
	}
}



function checking_lot_availability()



{



    var total_used_wt=0;



    var total_used_pcs=0;



    var lot_bal_wt=0;



    var lot_bal_pcs=0;



    var weight_per=$('#weight_per').val();



         $.each(lot_inward_detail,function(key,items){



             console.log(items.lot_blc['lot_bal_pcs']);



             lot_bal_pcs=parseFloat(items.lot_blc['lot_bal_pcs']);



             lot_bal_wt=parseFloat(items.lot_blc['lot_bal_wt']);



             $('#lt_item_tag_preview > tbody tr').each(function(idx, row){



                 curRow = $(this);



                  if(items.id_lot_inward_detail==curRow.closest('tr').attr('id'))



                    {



                        if(items.id_lot_inward_detail==curRow.closest('tr').attr('id'))



                        {



                            if(items.lot_product==curRow.find('.lot_product').val())



                            {



                                total_used_wt+=parseFloat(curRow.find('.gross_wt').val());



                                total_used_pcs+=parseFloat(curRow.find('.no_of_piece').val());



                            }



                        }



                    }



             });



        });



        if(weight_per>0)



        {



            $('#tag_blc_pcs').val(parseFloat(lot_bal_pcs));

            $('#tag_blc_gross').val(parseFloat(parseFloat(lot_bal_wt )+parseFloat(weight_per)));

        }else{



            $('#tag_blc_pcs').val(parseFloat(lot_bal_pcs));



            $('#tag_blc_gross').val(parseFloat(lot_bal_wt));



        }



        $('#tag_act_gross_blc').val(lot_bal_wt);



        $('#tag_act_blc_pcs').val(lot_bal_pcs);



        $('#tag_blc_gross_disp').html(parseFloat(lot_bal_wt).toFixed(3));



        $("#tag_blc_pcs_disp").html("Blc : "+parseFloat(lot_bal_pcs-1));



    console.log('tag_blc_pcs:'+total_used_pcs);



    console.log('tag_blc_gross:'+total_used_wt);



}



//calculate tag preview sale



//AJAX Call



function _ajaxCallPost(href, data) {



	$("div.overlay").css("display", "block");



	return new Promise((resolve, reject) => {



	  $.ajax({



		url: href,



		type: 'POST',



		dataType: "json",



		data:data,



		success: function (data) {



			$("div.overlay").css("display", "none");



		  	resolve(data);



		},



		error: function (error) {



		  $("div.overlay").css("display", "none");



		  console.log("Error on "+href+" : ", error);



		  reject(error);



		},



	  })



	})



}



//Attributes



$(".display_attribute_modal").on('click',function(){



    openAttributeModal();



});



$("#update_attribute_details").on("click", function() {



	modalAttributeDetail = [];



	let attr_details	 = [];



	let tableRows = $("#table_attribute_detail tbody tr");



	$(tableRows).each(function(index, attritem) {



		attr_details.push({



					'attr_id'       : $(attritem).find(".tag_upd_attr_name").val(),



					'attr_val_id'   : $(attritem).find(".tag_upd_attr_value").val(),



					'attr_name'     : $(attritem).find('.tag_upd_attr_name option:selected').text(),



					'attr_val'      : $(attritem).find('.tag_upd_attr_value option:selected').text()



				});



	});



	modalAttributeDetail = attr_details;



	display_attribute_details();



	$('#attribute_modal .modal-body').find('#table_attribute_detail tbody').empty();



	$('#attribute_modal').modal('hide');



});



function openAttributeModal() {



	$('#attribute_modal .modal-body').find('#table_attribute_detail tbody').empty();



	if(modalAttributeDetail.length > 0) {



		$.each(modalAttributeDetail, function (key, item) {



			add_tag_attribute(item.attr_id, item.attr_val_id);



		});



	} else {



		add_tag_attribute();



	}



	$('#attribute_modal').modal('show');



}



function get_activeAttributes()



{



	let _href = base_url+'index.php/admin_ret_catalog/attribute/get_attribute_with_values';



	let _data = {}



	_ajaxCallPost(_href, _data)



	.then((data) => {



		attributes_list = data;



		console.log("attributes_list", attributes_list);



	})



	.catch((error) => {



		attributes_list = [];



		console.log("Error On get_activeAttributes ",error);



		$.toaster({ priority : 'danger', title : 'Warning!', message : "Error occured in retriving data."});



	});



}



function get_attributes_from_subdesign() {



	$("#table_attribute_detail tbody").empty();



	$("#attributes-det tbody").empty();



	let product_id = $("#tag_lt_prod").val();



	let design_id = $("#des_select").val();



	let subdesign_id = $("#sub_des_select").val();



	console.log("product_id",product_id);



	console.log("design_id",design_id);



	console.log("subdesign_id",subdesign_id);



	if(product_id > 0 && design_id > 0 && subdesign_id > 0)



	{



		_href = base_url+'index.php/admin_ret_tagging/get_attributes_from_subdesign/?nocache=' + my_Date.getUTCSeconds();



		_data = { 'product_id': product_id, 'design_id': design_id, 'subdesign_id': subdesign_id }



		_ajaxCallPost(_href, _data)



		.then((data) => {



			console.log("attributes_from_subdesign", data);



			update_in_attribute_modal(data);



		})



		.catch((error) => {



			console.log("Error On get_attributes_from_subdesign ",error);



			$.toaster({ priority : 'danger', title : 'Warning!', message : "Error occured in retriving data."});



		});



    	update_po_details();



	}



}



function update_po_details(){



    let product_id = $("#tag_lt_prod").val();



	let design_id = $("#des_select").val();



	let subdesign_id = $("#sub_des_select").val();



    $.ajax({



    		url: base_url+'index.php/admin_ret_tagging/get_po_details/?nocache=' + my_Date.getUTCSeconds(),



    		type: 'POST',



    		dataType: "json",



    		data: { 'product_id': product_id, 'design_id': design_id, 'subdesign_id': subdesign_id, 'lot_no' : $('#tag_lot_received_id').val(), 'lot_from' : $('#tag_lot_received_id option:selected').attr('data-lotfrom') },



    		success: function (data) {



    		  	current_po_details = data;



    		},



    		error: function (error) {



    		  $("div.overlay").css("display", "none");



    		  console.log("Error on : ", error);



    		},



	    });



}



function update_in_attribute_modal(data) {



	modalAttributeDetail = [];



	let attr_details	 = [];



	$(data).each(function(index, attr) {



		attr_details.push({



					'attr_id'       : attr.attr_id,



					'attr_val_id'   : attr.attr_val_id,



					'attr_name'     : attr.attr_name,



					'attr_val'      : attr.attr_val



				});



	});



	modalAttributeDetail = attr_details;



	console.log("modalAttributeDetail",modalAttributeDetail);



	display_attribute_details();



}



function add_tag_attribute(selected_attr_id = 0, selected_attr_val_id = 0) {



	if(validate_tagAttr_row()) {



		let tableName = "table_attribute_detail";



		let fieldName = "tag_upd_attr_name";



		if(check_has_attributes(tableName, fieldName)) {



			let attr_row_last = $("#table_attribute_detail tbody tr:last-child");



			let sno = (attr_row_last.length > 0 ? parseInt(attr_row_last.find('.sno').text()) : 0)+1;



			let _html_add = '<tr class="attr_row">'+



								'<td width="10%" class="sno">'+



									sno+



								'</td>'+



								'<td width="35%">'+



									'<select class="form-control tag_upd_attr_name" name="desAttr[attr_name][]" placeholder="Attribute Name"></select>'+



								'</td>'+



								'<td width="35%">'+



									'<select class="form-control tag_upd_attr_value" name="desAttr[attr_value][]" placeholder="Attribute Value"></select>'+



								'</td>'+



								'<td width="20%" class="attribute_row_buttons">'+



								'</td>'+



							'</tr>';



			$("#table_attribute_detail tbody").append(_html_add);



			let attr_lastObj 	= $('#table_attribute_detail .attr_row:last-child').find('.tag_upd_attr_name');



			let attrVal_lastObj = $('#table_attribute_detail .attr_row:last-child').find('.tag_upd_attr_value');



			attr_lastObj.select2();



			attrVal_lastObj.select2();



			load_attributes(attr_lastObj, attrVal_lastObj, selected_attr_id, selected_attr_val_id);



			add_attribute_buttons();



		} else {



			$.toaster({ priority : 'warning', title : 'Warning!', message : ''+"</br>"+'All Attributes are Filled.'});



		}



	} else {



		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill All The Required Fields Of Attributes'});



	}



}



function get_attribute_values_from_attribute(attribute_id) {



	let selected_attr_values = [];



	$.each(attributes_list, function (key, item) {



		if(attribute_id == item.attr_id)



		{



			selected_attr_values = item.attr_values;



		}



	});



	return selected_attr_values;



}



function load_attributes(attrObj, attrValObj = '', curr_attr = 0, curr_attr_val = 0) {



	let tableName = "table_attribute_detail";



	let fieldName = "tag_upd_attr_name";



	let all_selected_attribute = getall_selected_attribute(tableName, fieldName);



	$.each(attributes_list, function (key, item) {



		if($.inArray(item.attr_id, all_selected_attribute) == -1) {



			$(attrObj).append(



				$("<option></option>").attr("value", item.attr_id).text(item.attr_name)



			);



		}



	});



	$(attrObj).select2({



		placeholder:"Select Attribute",



		allowClear: true



	});



	if(curr_attr > 0)  {



		$(attrObj).select2("val",curr_attr);



		if(!attrValObj == '') {



			let attribute_values = get_attribute_values_from_attribute(curr_attr);



			load_attribute_values(attribute_values, attrValObj, curr_attr_val);



		}



	}  else  {



		$(attrObj).select2("val","");



	}



}



function load_attribute_values(attr_values_list, attrValObj, curr_attr_val = 0) {



	$(attrValObj).empty();



	$.each(attr_values_list, function (key, item) {



		$(attrValObj).append(



			$("<option></option>").attr("value", item.attr_val_id).text(item.attr_val)



		);



	});



	$(attrValObj).select2({



		placeholder: "Select Attribute Value",



		allowClear: true



	});



	if(curr_attr_val > 0) {



		$(attrValObj).select2("val",curr_attr_val);



	} else {



		$(attrValObj).select2("val","");



	}



}



function remove_tag_attribute(itemObj)



{



	$(itemObj).closest('tr').remove();



	add_attribute_buttons();



}



function validate_tagAttr_row()



{



	let row_validated = true;



	let tag_attr_rows = $("#table_attribute_detail tbody tr");



	$.each(tag_attr_rows, function (attrkey, attritem) {



		let attr_name = $(attritem).find('.tag_upd_attr_name').val();



		let attr_value = $(attritem).find('.tag_upd_attr_value').val();



		if(!attr_name > 0 || !attr_value > 0) {



			row_validated = false;



			return false;



		}



	});



	return row_validated;



}



function check_has_attributes(tableName, fieldName) {



	let all_selected_attribute = getall_selected_attribute(tableName, fieldName);



	if(all_selected_attribute.length > 0) {



		let attr_available = false;



		$.each(attributes_list, function (attrkey, attritem) {



			if($.inArray(attritem.attr_id, all_selected_attribute) == -1) {



				attr_available = true;



				return false;



			}



		});



		return attr_available;



	} else {



		return true;



	}



}



function add_attribute_buttons() {



	let attr_rows = $("#table_attribute_detail tbody");



	$(attr_rows).find('.add_tag_attribute').remove();



	$(attr_rows).find('.remove_tag_attribute').remove();



	$(attr_rows).find(".attribute_row_buttons:last").prepend('<button type="button" class="btn btn-success add_tag_attribute"><i class="fa fa-plus"></i></button>');



	$(attr_rows).find(".attribute_row_buttons").append('<button type="button" class="btn btn-danger remove_tag_attribute"><i class="fa fa-trash"></i></button>');



}



function display_charges_details() {



	$('#charges-det tbody').empty();



	$.each(modalChargeDetail, function (key, item) {



		chrgRow = '<tr id='+item.charge_id+'>'



					+'<td>'+item.name_charge+'</td>'



					+'<td>'+item.charge_value+'</td>'



			+'</tr>';



		$('#charges-det tbody').append(chrgRow);



	})



}



function display_attribute_details() {



	$("#attributes-det tbody").empty();



	$.each(modalAttributeDetail, function (attrkey, attritem) {



		let attr_name 	= attritem.attr_name;



		let attr_value 	= attritem.attr_val;



		let trRow = "<tr><td>"+attr_name+"</td><td>"+attr_value+"</td></tr>";



		$("#attributes-det tbody").append(trRow);



	});



}



function huid_validation(huid) {



	let letters_and_numbers = /^[a-zA-Z0-9]+$/



	let result = letters_and_numbers.test(huid);



	if(huid.length != 6) {



		result = false;



	}



	return result;



}



function bluk_edit_charges_view(id) {



	$('#bulk_edit_charges_detail tbody').empty();



    my_Date = new Date();



    $.ajax({



        type:"GET",



        url: base_url+"index.php/admin_ret_tagging/get_tag_charges/"+id+"?nocache=" + my_Date.getUTCSeconds(),



        cache:false,



        dataType:"JSON",



        success:function(data)



        {



            var charges = data;



            var oTable = $('#bulk_edit_charges_detail').DataTable();



            oTable.clear().draw();



            oTable = $('#bulk_edit_charges_detail').dataTable({



            "bDestroy": true,



            "bInfo": true,



            "order": [[ 0, "desc" ]],



            "bFilter": true,



            "bSort": true,



            "aaData": charges,



            "aoColumns": [



            { "mDataProp" : "name_charge" },



            { "mDataProp" : "charge_value" },



			/*{ "mDataProp": function ( row, type, val, meta ) {



				let des_wc_id = row.id_wc;



				let delete_url = base_url+'index.php/admin_ret_catalog/delete_design_weight_range/'+des_wc_id;



				action_content='<a href="#" class="btn btn-danger btn-del delete_design_weight_range" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" data-parent="'+id+'" ><i class="fa fa-trash"></i></a>'



				return action_content;



				}



		   	}*/



            ]



            });



        }



    });



}



function bulk_edit_attribute_view(id) {



	$('#bulk_edit_table_attr_detail tbody').empty();



    my_Date = new Date();



    $.ajax({



        type:"GET",



        url: base_url+"index.php/admin_ret_tagging/get_tag_attributes/"+id+"?nocache=" + my_Date.getUTCSeconds(),



        cache:false,



        dataType:"JSON",



        success:function(data)



        {



            var attribute = data;



            var oTable = $('#bulk_edit_table_attr_detail').DataTable();



            oTable.clear().draw();



            oTable = $('#bulk_edit_table_attr_detail').dataTable({



            "bDestroy": true,



            "bInfo": true,



            "order": [[ 0, "desc" ]],



            "bFilter": true,



            "bSort": true,



            "aaData": attribute,



            "aoColumns": [



            { "mDataProp" : "attr_name" },



            { "mDataProp" : "attr_val" },



			{ "mDataProp": function ( row, type, val, meta ) {



				let _attr_id = row.attr_tag_id;



				let delete_url = base_url+'index.php/admin_ret_tagging/delete_tag_attribute/'+_attr_id;



				action_content='<a href="#" class="btn btn-danger btn-del delete_tag_attribute" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" data-parent="'+id+'" ><i class="fa fa-trash"></i></a>'



				return action_content;



				}



		   	}



            ]



            });



        }



    });



}



/** Bulk edit update for attribute */



function bulk_tag_upd_add_attribute() {



	if(validate_bulk_edit_attr_row()) {



		let tableName = "bulk_edit_attribute_detail";



		let fieldName = "bulk_tag_upd_attr_name";



		if(check_has_attributes(tableName, fieldName)) {



			let attr_row_last = $("#bulk_edit_attribute_detail tbody tr:last-child");



			let sno = (attr_row_last.length > 0 ? parseInt(attr_row_last.find('.sno').text()) : 0)+1;



			let _html_add = '<tr class="bulk_edit_attr_row">'+



								'<td class="sno">'+



									sno+



								'</td>'+



								'<td>'+



									'<select class="form-control bulk_tag_upd_attr_name" placeholder="Attribute Name"></select>'+



								'</td>'+



								'<td>'+



									'<select class="form-control bulk_tag_upd_attr_value" placeholder="Attribute Value"></select>'+



								'</td>'+



								'<td class="bulk_edit_attr_buttons">'+



								'</td>'+



							'</tr>';



			$("#bulk_edit_attribute_detail tbody").append(_html_add);



			$('#bulk_edit_attribute_detail .bulk_edit_attr_row:last-child').find('.bulk_tag_upd_attr_name').select2();



			$('#bulk_edit_attribute_detail .bulk_edit_attr_row:last-child').find('.bulk_tag_upd_attr_value').select2();



			bulk_edit_attributes($('#bulk_edit_attribute_detail .bulk_edit_attr_row:last-child').find('.bulk_tag_upd_attr_name'));



			bulk_edit_attribute_buttons();



		} else {



			$.toaster({ priority : 'warning', title : 'Warning!', message : ''+"</br>"+'All Attributes are Filled.'});



		}



	} else {



		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill The Required Fields..'});



	}



}



function bulk_tag_upd_remove_attribute(itemObj) {



	$(itemObj).closest('tr').remove();



	bulk_edit_attribute_buttons();



}



function validate_bulk_edit_attr_row() {



	let row_validated = true;



	let _attr_rows = $("#bulk_edit_attribute_detail tbody tr");



	$.each(_attr_rows, function (attrkey, attritem) {



		let attr_name = $(attritem).find('.bulk_tag_upd_attr_name').val();



		let attr_value = $(attritem).find('.bulk_tag_upd_attr_value').val();



		if(!attr_name > 0 || !attr_value > 0) {



			row_validated = false;



			return false;



		}



	});



	return row_validated;



}



function delete_bulkedit_settings(href) {



	let del_design_args = {}



	let result = _ajaxCallPost(href, del_design_args)



			.then((data) => {



				if(data.status) {



					$.toaster({ priority : 'success', title : 'Success!', message : ''+"</br>"+data.msg});



					return true;



				} else {



					$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.msg});



					return false;



				}



				return true;



			})



			.catch((error) => {



				console.log("Error On delete_design_settings ",error);



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Error occured. Please try again..'});



				return false;



			});



	return result;



}



function bulk_edit_attributes(attrObj) {



	let tableName = "bulk_edit_attribute_detail";



	let fieldName = "bulk_tag_upd_attr_name";



	let all_selected_attribute = getall_selected_attribute(tableName, fieldName);



	$.each(attributes_list, function (key, item) {



		if($.inArray(item.attr_id, all_selected_attribute) == -1) {



			$(attrObj).append(



				$("<option></option>").attr("value", item.attr_id).text(item.attr_name)



			);



		}



	});



	$(attrObj).select2({



		placeholder:"Select Attribute",



		allowClear: true



	});



	$(attrObj).select2("val","");



}



function bulk_edit_attribute_values(attr_values_list, attrValObj) {



	$(attrValObj).empty();



	$.each(attr_values_list, function (key, item) {



		$(attrValObj).append(



			$("<option></option>").attr("value", item.attr_val_id).text(item.attr_val)



		);



	});



	$(attrValObj).select2({



		placeholder: "Select Attribute Value",



		allowClear: true



	});



	$(attrValObj).select2("val","");



}



function getall_selected_attribute(tableName, FieldName) {



	let attr_selected_ids = [];



	let tableRows = $("#"+tableName+" tr");



	$.each(tableRows, function (attrkey, attritem) {



		let attr_id = $(attritem).find("."+FieldName).val();



		if(attr_id > 0) {



			attr_selected_ids.push(attr_id);



		}



	});



	return attr_selected_ids;



}



function bulk_edit_attribute_buttons() {



	let attr_rows = $("#bulk_edit_attribute_detail tbody");



	$(attr_rows).find('.bulk_tag_upd_add_attribute').remove();



	$(attr_rows).find('.bulk_tag_upd_remove_attribute').remove();



	$(attr_rows).find(".bulk_edit_attr_buttons:last").prepend('<button type="button" class="btn btn-success bulk_tag_upd_add_attribute"><i class="fa fa-plus"></i></button>');



	$(attr_rows).find(".bulk_edit_attr_buttons").append('<button type="button" class="btn btn-danger bulk_tag_upd_remove_attribute"><i class="fa fa-trash"></i></button>');



}



function delete_tag_attribute(href)



{



	let del_tag_args = {}



	let result = _ajaxCallPost(href, del_tag_args)



			.then((data) => {



				if(data.status) {



					$.toaster({ priority : 'success', title : 'Success!', message : ''+"</br>"+data.msg});



					return true;



				} else {



					$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.msg});



					return false;



				}



				return true;



			})



			.catch((error) => {



				console.log("Error On delete_tag_attribute ",error);



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Error occured. Please try again..'});



				return false;



			});



	return result;



}



function validate_image(event, img, outputId)



{



	if(img.files[0].size > 1048576)



	{



		console.log('File size cannot be greater than 1 MB');



		img.value = "";



		$(".cert_img_container").css("display", "none");



	}



	else



	{



		var fileName =	img.value;



		var ext = fileName.substring(fileName.lastIndexOf('.') + 1);



		ext = ext.toLowerCase();



		if(ext != "jpg" && ext != "png" && ext != "jpeg")



		{



			console.log("Upload JPG or PNG Images only");



			img.value = "";



			$(".cert_img_container").css("display", "none");



		}



		else



		{



			document.getElementById(outputId).src = "";



			preview_image(event, outputId);



		}



	}



}



function preview_image(event, outputId)



{



  $(".cert_img_container").css("display", "block");



  let reader = new FileReader();



  reader.onload = function()



  {



    let output = document.getElementById(outputId);



    output.src = reader.result;



	$("#cert_img_base64").val(reader.result);



  }



  reader.readAsDataURL(event.target.files[0]);



}



function preview_image_on_edit(imageVal, outputId)



{



	if(imageVal != "")



	{



		let output = document.getElementById(outputId);



    	output.src = imageVal;



		$(".cert_img_container").css("display", "block");



	}



	else



	{



		let output = document.getElementById(outputId);



    	output.src = "";



		$(".cert_img_container").css("display", "none");



	}



}



function remove_cert_image()



{



	let output = document.getElementById('cert_img_preview');



    output.src = "";



	$("#cert_img").val("");



	$("#cert_img_base64").val("");



	$(".cert_img_container").css("display", "none");



}



//Other Metals



$(".add_other_metals").on('click',function(){



    open_other_metal_modal();



});



function open_other_metal_modal() {



	$('#other_metalmodal .modal-body').find('#other_metal_table tbody').empty();



	if(modalOtherMetalDetail.length > 0) {



		$.each(modalOtherMetalDetail, function (key, item) {



			create_new_empty_other_metal_item(item);



			calculate_other_metal_amount();



		});



	} else {



		create_new_empty_other_metal_item();



	}



	$('#other_metalmodal').modal('show');



}



function create_new_empty_other_metal_item(itemData = []) {



	console.log(itemData);



    var trHtml='';



    var metal='<option value="">Select Metal</option>';



    var purity='<option value="">Select Purity</option>';



    $.each(metalDetails, function (mkey, mitem) {



		metal += "<option "+(itemData.id_metal == undefined ? '' : mitem.id_metal == itemData.id_metal ? 'selected' : '')+" value='"+mitem.id_metal+"'>"+mitem.metal+"</option>";



	});



	$.each(purityDetails, function (k, p) {



		purity += "<option "+(itemData.id_purity == undefined ? '' : p.id_purity == itemData.id_purity ? 'selected' : '')+" value='"+p.id_purity+"'>"+p.purity+"</option>";



	});



	trHtml+='<tr>'



          +'<td><select class="form-control select_metal">'+metal+'</td>'



          +'<td><select class="form-control select_purity">'+purity+'</td>'



		  +'<td><input type="number" class="form-control nwt" value="'+(itemData.nwt == undefined ? '' : itemData.nwt)+'"></td>'



		  +'<td><input type="number" class="form-control oth_m_wast" value="'+(itemData.wastage_perc == undefined ? '' : itemData.wastage_perc)+'"></td>'



     	  +'<td><select class="form-control oth_m_calc_type"><option value="">Mc Type</option><option '+(itemData.calc_type == undefined ? '' : 1 == itemData.calc_type ? 'selected' : '')+' value="1">Per Gram</option><option '+(itemData.calc_type == undefined ? '' : 2 == itemData.calc_type ? 'selected' : '')+' value="2">Per Piece</option></select></td>'



      	  +'<td><input type="number" class="form-control oth_m_mc" value="'+(itemData.making_charge == undefined ? '' : itemData.making_charge)+'"></td>'



          +'<td><input type="number" class="form-control rate_per_gram" value="'+(itemData.rate_per_gram == undefined ? '' : itemData.rate_per_gram)+'"></td>'



          +'<td><input type="number" class="form-control oth_m_amount" readonly value="'+(itemData.amount == undefined ? '' : itemData.amount)+'"></td>'



          +'<td class="other_metal_row_buttons"></td>'



        +'</tr>';



     $('#other_metal_table tbody').append(trHtml);



	 add_othermetal_buttons();



	 setTimeout(function() {



		$('#other_metal_table > tbody tr:last').find('.select_metal').focus();



 	 }, 600);



}



$(document).on('click', '#create_other_metal_item_details, .add_other_metal',function() {



    if(validate_other_metal_row()) {



        create_new_empty_other_metal_item();



    } else {



        $.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>Please Fill The Required Other Metal Details"});



    }



});



$('#other_metalmodal #update_other_metal_details').on('click', function() {



	if(validate_other_metal_row()) {



		calculate_other_metal_amount();



    	var metal_details=[];



    	var tot_amount = 0;



		var tot_nwt = 0;



    	$('#other_metalmodal .modal-body #other_metal_table > tbody  > tr').each(function(index, tr) {



    		tot_amount += isNaN(parseFloat($(this).find('.oth_m_amount').val())) ? 0 : parseFloat($(this).find('.oth_m_amount').val());



			tot_nwt += isNaN(parseFloat($(this).find('.nwt').val())) ? 0 : parseFloat($(this).find('.nwt').val());



    		metal_details.push({



    		            'id_metal'      : $(this).find('.select_metal').val(),



						'metal_name'    : $(this).find('.select_metal option:selected').text(),



    		            'id_purity'     : $(this).find('.select_purity').val(),



						'purity_name'   : $(this).find('.select_purity option:selected').text(),



    		            'nwt'           : $(this).find('.nwt').val(),



    		            'wastage_perc'  : $(this).find('.oth_m_wast').val(),



    		            'calc_type'     : $(this).find('.oth_m_calc_type').val(),



    		            'making_charge' : $(this).find('.oth_m_mc').val(),



    		            'rate_per_gram' : $(this).find('.rate_per_gram').val(),



    		            'amount'        : $(this).find('.oth_m_amount').val(),



    		            });



    	});



		modalOtherMetalDetail = metal_details;



    	$('#other_metal_details').val(JSON.stringify(modalOtherMetalDetail));



		console.log("others_tot_amount",tot_amount);



		console.log("others_tot_nwt",tot_nwt);



		$('#other_metal_wt').val(tot_nwt);



    	$('#other_metal_amount').val(tot_amount);



		calculateTagFormSaleValue();



        $('#other_metalmodal').modal('hide');



		display_othermetals_details();



    } else {



        $.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>Please Fill The Required Other Metal Details"});



    }



});



function validate_other_metal_row() {



    var row_validate = true;



	$('#other_metal_table > tbody  > tr').each(function(index, tr) {



		if(!$(this).find('.select_metal').val() > 0 || !$(this).find('.select_purity').val() > 0 || !$(this).find('.nwt').val() > 0 || !$(this).find('.oth_m_wast').val() > 0 || !$(this).find('.oth_m_calc_type').val() > 0 || !$(this).find('.oth_m_mc').val() > 0 || !$(this).find('.rate_per_gram').val() > 0 || !$(this).find('.oth_m_amount').val() > 0) {



			row_validate = false;



		}



	});



	return row_validate;



}



$(document).on('change','.oth_m_calc_type',function(){



    calculate_other_metal_amount();



});



$(document).on('keyup','.rate_per_gram,.nwt,.oth_m_wast,.making_charge',function(){



    calculate_other_metal_amount();



});



function calculate_other_metal_amount() {



    var tot_amount=0;



    $('#other_metal_table > tbody  > tr').each(function(index, tr) {



        row = $(this);



        var net_wt        	= (isNaN(row.find('.nwt').val()) || row.find('.nwt').val()=='' ? 0:row.find('.nwt').val());



        var wastage_perc    = (isNaN(row.find('.oth_m_wast').val()) || row.find('.oth_m_wast').val()=='' ? 0:row.find('.oth_m_wast').val());



        var rate_per_gram   = (isNaN(row.find('.rate_per_gram').val()) || row.find('.rate_per_gram').val()=='' ? 0:row.find('.rate_per_gram').val());



        var wast_wt         = parseFloat((net_wt*wastage_perc)/100);



        var mc_type         = (row.find('.oth_m_calc_type').val()=='' ? 0:row.find('.oth_m_calc_type').val());



        var making_charge   = (row.find('.oth_m_mc').val()=='' ? 0 : row.find('.oth_m_mc').val());



        var mc_value        = (mc_type==1 ? parseFloat(net_wt*making_charge) : (mc_type==2 ? parseFloat(making_charge) :0));



        var total_amount    = parseFloat(parseFloat(rate_per_gram)*parseFloat(parseFloat(net_wt)+parseFloat(wast_wt))+parseFloat(mc_value));



        row.find('.oth_m_amount').val(parseFloat(total_amount).toFixed(2));



        tot_amount+=parseFloat(total_amount);



		console.log('wast_wt:'+wast_wt);



        console.log('mc_value:'+mc_value);



		console.log('total_amount:'+total_amount);



    });



    $('.total_amount').html(parseFloat(tot_amount).toFixed(2));



}



function add_othermetal_buttons() {



	let attr_rows = $("#other_metal_table tbody");



	$(attr_rows).find('.add_other_metal').remove();



	$(attr_rows).find('.remove_other_metal').remove();



	$(attr_rows).find(".other_metal_row_buttons:last").prepend('<button type="button" class="btn btn-success add_other_metal"><i class="fa fa-plus"></i></button>');



	$(attr_rows).find(".other_metal_row_buttons").append('<button type="button" class="btn btn-danger remove_other_metal"><i class="fa fa-trash"></i></button>');



}



function remove_other_metal(itemObj) {



	$(itemObj).closest('tr').remove();



	calculate_other_metal_amount();



	add_othermetal_buttons();



}



function get_rate_from_metal_and_purity(metal_id, purity_id) {



	let _href = base_url+'index.php/admin_ret_tagging/get_rate_from_metal_and_purity/'+metal_id+'/'+purity_id;



	let _data = {}



	return _ajaxCallPost(_href, _data)



	.then((data) => {



		if(!data.status) {



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.msg});



		}



		return data.metal_rate;



	})



	.catch((error) => {



		console.log("get_rate_from_metal_and_purity ",error);



		$.toaster({ priority : 'danger', title : 'Warning!', message : "Error occured in retriving rate based on metal and purity."});



	});



}



function display_othermetals_details() {



	$("#othermetals-det tbody").empty();



	$.each(modalOtherMetalDetail, function (othkey, othitem) {



		let trRow = "<tr><td>"+othitem.metal_name+"</td><td>"+othitem.purity_name+"</td><td>"+othitem.nwt+"</td><td>"+othitem.wastage_perc+"</td><td>"+othitem.making_charge+"</td><td>"+othitem.amount+"</td></tr>";



		$("#othermetals-det tbody").append(trRow);



	});



}



function get_ActiveMetals() {



    $("div.overlay").css("display", "block");



    $.ajax({



	type: 'GET',



	url: base_url+'index.php/admin_ret_catalog/active_metals',



	dataType:'json',



	success:function(data){



	        metalDetails=data;



	        var id=$('#select_metal').val();



	        $.each(data, function (key, item) {



                $('#select_metal,#metal').append(



                $("<option></option>")



                .attr("value", item.id_metal)



                .text(item.metal)



                );



        	});



        	$('#select_metal,#metal').select2({



        	    placeholder: "Metal",



        	    allowClear: true



        	});



	        if($('#select_metal,#metal').length)



	        {



	            $('#select_metal,#metal').select2("val",(id!='' ? id:''));



	        }



	    	$("div.overlay").css("display", "none");



		}



	});



}



function get_ActivePurity() {



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



function get_metal_rate_purities() {



    $("div.overlay").css("display", "block");



    $.ajax({



	type: 'GET',



	url: base_url+'index.php/admin_ret_catalog/ret_metalpurity',



	dataType:'json',



	success:function(data){



		metal_rate_details=data.Purity;



	    	$("div.overlay").css("display", "none");



		}



	});



}



function calculate_tag_summary()



{



    var total_pcs=0;



    var total_gross_wt=0;



    var total_nwt=0;



    $('#lt_item_tag_preview > tbody tr').each(function(idx, row){



        curRow = $(this);



        total_pcs+=parseFloat(curRow.find('.no_of_piece').val());



        total_gross_wt+=parseFloat(curRow.find('.gross_wt').val());



        total_nwt+=parseFloat(curRow.find('.net_wt').val());



    });



    $('#total_pcs').html(total_pcs);



    $('#total_wt').html(parseFloat(total_gross_wt).toFixed(3));



}



$('#add_to_transfer').on('click',function(){



    if($('#branch_select').val()=='' || $('#branch_select').val()==null)



    {



        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Branch..'});



    }



    else if($('#branch_select').val()==$('.to_branch').val())



    {



        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Invalid To Branch..'});



    }



    else



    {



        $('#add_to_transfer').prop('disabled',true);



        if($('#lt_item_tag_preview > tbody > tr').length>0)



        {



             var selected=[];



             $('#lt_item_tag_preview > tbody tr').each(function(idx, row){



                    curRow = $(this);



                    if(curRow.find('.tag_id').val()!='' && curRow.find('.tag_saved').val()==1)



                    {



                         transData = {



                            'tag_id'                 : curRow.find('.tag_id').val(),



                            'id_lot_inward_detail'   : curRow.find('.id_lot_inward_detail').val(),



                        }



                        selected.push(transData);



                    }



             });



            req_data = selected;



            add_to_transfer_tag(req_data);



        }



        else



        {



            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Records Found..'});



        }



    }



});



function add_to_transfer_tag(req_data)



{



    my_Date = new Date();



    $("div.overlay").css("display", "block");



    $.ajax({



        url:base_url+ "index.php/admin_ret_tagging/add_to_transfer_tag?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),



        data:  {'req_data':req_data,'tot_pcs':$('#total_pcs').html(),'total_gwt':$('#total_wt').html(),'total_nwt':$('#total_wt').html(),'id_branch':$('#current_branch').val(),'to_branch':$('.to_branch').val()},



        type:"POST",



        async:false,



        dataType: "json",



        success:function(data){



            if(data.status)



            {



                $.toaster({ priority : 'success', title : 'Warning!', message : data.msg});



                $("div.overlay").css("display", "none");



                $('#add_to_transfer').prop('disabled',false);



                $('#lt_item_tag_preview > tbody').empty();



                window.open( base_url+'index.php/admin_ret_brntransfer/branch_transfer/print/'+data.trans_code+'/1'+'/'+'1','_blank');



            }



            else



            {



                $("div.overlay").css("display", "none");



                $('#add_to_transfer').prop('disabled',false);



                $.toaster({ priority : 'danger', title : 'Warning!', message : data.msg});



            }



        },



        error:function(error)



        {



            console.log(error);



            $('#add_to_transfer').prop('disabled',true);



            $("div.overlay").css("display", "none");



        }



    });



}



//ReTag



$('#retag_search').on('click',function(){



   if($('#report_type').val()==1 || $('#report_type').val()==6)
    {
         get_retagging_details();
    }
    else if($('#report_type').val()==3)
    {
        get_partlySaleDetails();
    }
    else if($('#report_type').val()==4)
    {
        get_OldMetalDetails();
    }
    else if($('#report_type').val()==5)
    {
        get_non_tag_return_details();
    }
	else if($('#report_type').val()==7)
	{
		get_NonTagOtherIssue_details();
	}



});

// Transfer Source Toggle — BT/ST filter switching
$('#transfer_source').on('change', function(){
    if($(this).is(':checked')) {
        // ST mode
        $('.bt_filter_group').hide();
        $('.st_filter_group').show();
        $('#bt_number').val('');
    } else {
        // BT mode
        $('.st_filter_group').hide();
        $('.bt_filter_group').show();
        $('#st_fin_year').val('');
        $('#st_bill_no').val('');

    }
});



$('#report_type').on('change',function(){



    $('.retag_details').css("display","none");



    $('.partly_sale_details').css("display","none");



    $('.old_metal_details').css("display","none");



    $('.non_tag').css('display','none');



	$('.non_tag_otr_issue').css('display','none');



    $('#tag_process').select2("val","");



    $('#tag_process').prop("disabled",false);



    //$('#tag_process').prop("disabled",false);



    if(this.value==1 || this.value==6)



    {



        //get_retagging_details();



        $('.retag_details').css("display","block");



        $('.partly_sale_details').css("display","none");



    }



    else if(this.value==3)



    {



        $('#tag_process').val(3);



        $('.retag_details').css("display","none");



        $('.partly_sale_details').css("display","block");



        //get_partlySaleDetails();



    }



    else if(this.value==4)



    {



        //$('.non_tag').css('display','block');



         $('.old_metal_details').css("display","block");



    }



    else if(this.value==5)



    {



         $('#tag_process').select2("val",1);



        // $('#tag_process').prop("disabled",true);



         $('.non_tag').css('display','block');



    }

	else if(this.value==7)

	{

		$('#tag_process').select2("val",1);



		$('.non_tag_otr_issue').css('display','block');

	}



});



//Change event



$('#tag_process').on('change',function(){



    $('.product').css("display","none");



    $('.design').css("display","none");



    $('.sub_design').css("display","none");



    $('.category').css("display","none");



    $('.purity').css("display","none");



	$('.remark').css("display","none");





    if(this.value==4)



    {



        get_ActiveNontagProduct();



        $('.product').css("display","block");



        $('.design').css("display","block");



        $('.sub_design').css("display","block");



		$('.category').css("display","block");



		$('.purity').css("display","block");



		$('.remark').css("display","block");





    }



    else if(this.value==5)



    {



        get_ActiveNontagProduct();



        $('.product').css("display","block");



		$('.remark').css("display","block");





    }



    else if(this.value==1)



    {



        if($('#report_type').val()==4 || $('#report_type').val()==3 || $('#report_type').val()==1 || $('#report_type').val()==6 || $('#report_type').val()==5 || $('#report_type').val()==7)



        {



            $('.product').css("display","block");



            $('.category').css("display","block");



            $('.purity').css("display","block");



			$('.remark').css("display","block");





        }



    }



	$('#retag_search').trigger('click');



});



$('#non_tag_select_all').click(function(event) {



	  $("tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));



      event.stopPropagation();



});



$('#select_all_tag').click(function(event) {



	  $("tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));



      event.stopPropagation();



	  calculate_average_purity_and_rate();





});



$('#select_all_old_metal').click(function(event) {



	  $("#old_metal_sale_list tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));



      event.stopPropagation();



	  calculate_average_purity_and_rate();





});



function get_partlySaleDetails()



{



	var tag_process = $('#tag_process').val();



    $("div.overlay").css("display", "block");



	my_Date = new Date();



	var is_st = $('#transfer_source').is(':checked');
	var ajax_endpoint = is_st ? 'st_partly_sale' : 'partly_sale';
	var ajax_data = is_st
		? {'id_branch':$("#id_branch").val(),'fin_year':$("#st_fin_year").val(),'bill_no':$("#st_bill_no").val()}
		: {'id_branch':$("#id_branch").val(),'bt_number':$("#bt_number").val()};

	$.ajax({
		url: base_url+"index.php/admin_ret_tagging/retagging/"+ajax_endpoint+"?nocache=" + my_Date.getUTCSeconds(),
		type:"POST",
		data: ajax_data,



		dataType: 'json',



		cache:false,



		success:function(data){



			 $("div.overlay").css("display","add_newstone");



			 var oTable = $('#partly_sale_list').DataTable();



			 oTable.clear().draw();



			 if (data!= null && data.length > 0)



			 {



				oTable = $('#partly_sale_list').dataTable({



						"bDestroy": true,



						"bInfo": true,



						"bFilter": true,



						"bSort": false,



						"order": [[ 0, "desc" ]],



						"lengthMenu": [[-1, 25, 50, 100, 250], ["All", 25, 50, 100, 250]],



						"aaData"  : data,



						"aoColumns": [



                            { "mDataProp": function ( row, type, val, meta ){



                                        	chekbox='<input type="checkbox" class="tag_id" name="tag_id[]" value="'+row.tag_id+'"/><input type="hidden" class="blc_gwt" name="blc_gwt[]" value="'+row.blc_gwt+'"/><input type="hidden" class="cat_id" value="'+row.cat_id+'"><input type="hidden" class="amount" value="'+row.amount+'"><input type="hidden" class="rate_per_grm" value="'+row.rate_per_grm+'">'



                                           	return chekbox+" "+row.tag_code;



                            }},



                            { "mDataProp": "branch_name"},



                            { "mDataProp": function (row,type,val,meta){



                                    var url = base_url+'index.php/admin_ret_billing/billing_invoice/'+row.bill_id;



                            	return '<a href='+url+' target="_blank">'+row.metal_code+'-'+row.sales_ref_no+'</a>';



                            }},



                            { "mDataProp": "bill_date" },



							{ "mDataProp": function(row){ return $('#transfer_source').is(':checked') ? '-' : row.branch_trans_code; } },



							{

								"mDataProp":function(row,type,val,meta)

								{

									return '<input type="hidden" class="purity" value="'+row.purity+'">'+row.purity+'';



								}

							},



                            { "mDataProp": "product_name" },



                            { "mDataProp": "design_name" },



                            { "mDataProp": "sub_design_name" },



                            { "mDataProp": "gross_wt" },



                            { "mDataProp": "sold_gwt" },



                            { "mDataProp": function (row,type,val,meta){



                            	return '<input type="number" class="form-control partial_sale_pcs" value="'+row.blc_pcs+'" style="width:100px;"><input type="hidden" class="form-control available_blc_pcs" value="'+row.blc_pcs+'">';



                            }},



                            { "mDataProp": function (row,type,val,meta){



                            	return '<input type="number" class="form-control partial_sale_gwt" value="'+row.blc_gwt+'" style="width:100px;"><input type="hidden" class="form-control available_blc_wt" value="'+row.blc_gwt+'">';



                            }},



                            { "mDataProp": function (row,type,val,meta){



                            	return '<div class="form-group"><div class="input-group "><input class="form-control lwt" value=' + row.blc_lwt + ' onClick="create_new_empty_partial_sale_retag_stone_item($(this).closest(\'tr\'),'+row.tag_id+');"  type="number" step="any" readonly style="width:100px;"/><span class="input-group-addon input-sm add_tag_lwt" onClick="create_new_empty_retag_stone_item($(this).closest(\'tr\'));">+</span></div></div><input type="hidden" class="stone_details" value=\''+(JSON.stringify(row.stone_details))+'\' >';



                            }},



                            { "mDataProp": function (row,type,val,meta){



                            	return '<input type="number" class="form-control partial_sale_nwt" value="'+row.blc_nwt+'" style="width:100px;" readonly ><input type="hidden" class="form-control blc_nwt" value="'+row.blc_nwt+'">';



                            }},



							{

								"mDataProp" : function(row,type,val,meta)

								{

									return '<input type="hidden" class="partial_dia_wt" value="'+row.blc_diawt+'">'+row.blc_diawt;

								}

							},



							{ "mDataProp": function ( row, type, val, meta )



							{



								if(tag_process==2)



								{



									return '<textarea class="form-control" id="othr_issue_remarks"  rows="5" cols="25"> </textarea>';



								}



								else



								{



									return '-';



								}



							}}



						],



					});



					calculate_PartlySale_RowTotal();



				}



				$("div.overlay").css("display", "none");



		},



		 error:function(error)



		  {



			 $("div.overlay").css("display", "none");



		  }



	});



}





function calculate_PartlySale_RowTotal()

{

	var pieces = 0;

	var grs_wt = 0;

	var less_wt = 0;

	var net_wt = 0;

	$("#partly_sale_list > tbody > tr").each(function () {

		var row = $(this).closest('tr');

		pieces = pieces + (parseFloat(row.find('.partial_sale_pcs').val()) || 0);

		grs_wt = grs_wt + (parseFloat(row.find('.partial_sale_gwt').val()) || 0);

		less_wt = less_wt + (parseFloat(row.find('.lwt').val()) || 0);

		net_wt = net_wt + (parseFloat(row.find('.partial_sale_nwt').val()) || 0);

	});

	$(".bal_pcs").html(pieces);

	$(".bal_gwt").html(parseFloat(grs_wt).toFixed(3));

	$(".bal_lwt").html(parseFloat(less_wt).toFixed(3));

	$('.bal_nwt').html(parseFloat(net_wt).toFixed(3));

}



function get_OldMetalDetails()



{



    $("div.overlay").css("display", "block");



	my_Date = new Date();



	var is_st = $('#transfer_source').is(':checked');
	var ajax_endpoint = is_st ? 'st_old_metal' : 'old_metal';
	var ajax_data = is_st
		? {'id_branch':$("#id_branch").val(),'fin_year':$("#st_fin_year").val(),'bill_no':$("#st_bill_no").val()}
		: {'id_branch':$("#id_branch").val(),'bt_number':$("#bt_number").val()};

	$.ajax({
		url: base_url+"index.php/admin_ret_tagging/retagging/"+ajax_endpoint+"?nocache=" + my_Date.getUTCSeconds(),
		type:"POST",
		data: ajax_data,



		dataType: 'json',



		cache:false,



		success:function(data){



			 $("div.overlay").css("display","add_newstone");



			 var oTable = $('#old_metal_sale_list').DataTable();



			 oTable.clear().draw();



			 if (data!= null && data.length > 0)



			 {



				oTable = $('#old_metal_sale_list').dataTable({



						"bDestroy": true,



						"bInfo": true,



						"bFilter": true,



						"bSort": false,



						"aaData"  : data,



						"aoColumns": [



						    { "mDataProp": function ( row, type, val, meta ){



                            chekbox='<input type="checkbox" class="old_metal_sale_id" name="old_metal_sale_id[]" value="'+row.old_metal_sale_id+'"/><input type="hidden" class="purity" value="'+row.purity+'"><input type="hidden" class="net_wt" value="'+row.net_wt+'"><input type="hidden" class="gross_wt" value="'+row.gross_wt+'"><input type="hidden" class="id_old_metal_type" value="'+row.id_old_metal_type+'">'



                            return chekbox+" "+row.old_metal_sale_id;



                            }},



                            { "mDataProp": "branch_name" },



                            { "mDataProp": function ( row, type, val, meta ){



                            var url = base_url+'index.php/admin_ret_billing/billing_invoice/'+row.bill_id;



                            return '<a href='+url+' target="_blank">'+row.metal_code+'-'+row.pur_ref_no+'</a>';



                            }



                            },



                            { "mDataProp": "bill_date" },



							{ "mDataProp": function(row){ return $('#transfer_source').is(':checked') ? '-' : row.branch_trans_code; } },



                            { "mDataProp": "old_metal_cat" },



                            { "mDataProp": function (row,type,val,meta){



								return '<input type="number" class="form-control retag_pcs" value="'+row.piece+'"><input type="hidden" class="form-control actual_piece" value="'+row.piece+'">';



							}},



                            { "mDataProp": function (row,type,val,meta){



                            	return '<input type="number" class="form-control retaggross_wt" value="'+row.gross_wt+'"><input type="hidden" class="form-control actual_gross_wt" value="'+row.gross_wt+'">';



                            }},



							{ "mDataProp": function (row,type,val,meta)



							{



								var stn_less_wt = 0;



								var stone_details = row.stone_details;



								$.each(stone_details,function(pkey,pitem)



								{



									if(pitem.uom_id==6)



									{



										stn_less_wt+=parseFloat(pitem.stone_wt/5);



									}



									else



									{



										stn_less_wt+=parseFloat(pitem.stone_wt);



									}



								});



                            	return '<div class="form-group"><div class="input-group "><input class="form-control lwt" value=' + parseFloat(stn_less_wt).toFixed(3) + ' onClick="create_new_empty_oldmet_stone_item($(this).closest(\'tr\'),'+row.old_metal_sale_id+');"  type="number" step="any" readonly style="width:100px;"/><span class="input-group-addon input-sm add_tag_lwt" onClick="create_new_empty_oldmet_stone_item($(this).closest(\'tr\'));">+</span></div></div><input type="hidden" class="stone_details" value=\'' + JSON.stringify(row.stone_details) +'\'>';



                            }},



                            { "mDataProp": function (row,type,val,meta){



                                return '<input type="number" class="form-control retag_net_wt" value="'+row.net_wt+'" readonly style="width:100px;"><input type="hidden" class="form-control retag_dust_wt" value="'+row.dust_wt+'"><input type="hidden" class="form-control retag_wast_wt" value="'+row.wast_wt+'"><input type="hidden" class="form-control retag_gwt" value="'+row.retag_gwt+'">';



                            }},



							{ "mDataProp" : function (row,type,val,meta){



								return '<input type="hidden" class="retag_dia_wt" value="'+row.diawt+'">'+row.diawt;



							}

							},





							{

								"mDataProp": function(row,type,val,meta)

								{

									return '<input type="hidden" class="purity" value="'+row.purity+'">'+row.purity;

								}

							},



							{

								"mDataProp": function(row,type,val,meta)

								{

									return '<input type="hidden" class="amount" value="'+row.amount+'"><input type="hidden" class="rate_per_grm" value="'+row.rate_per_grm+'">'+row.amount;

								}

							},



						],



					});



					calculate_OldMetal_RowTotal();



				}



				$("div.overlay").css("display", "none");



		},



		 error:function(error)



		  {



			 $("div.overlay").css("display", "none");



		  }



	});



}



function calculate_OldMetal_RowTotal()

{

	var pieces = 0;

	var grs_wt = 0;

	var less_wt = 0;

	var net_wt = 0;

	$("#old_metal_sale_list > tbody > tr").each(function () {

		var row = $(this).closest('tr');

		pieces = pieces + (parseFloat(row.find('.retag_pcs').val()) || 0);

		grs_wt = grs_wt + (parseFloat(row.find('.retaggross_wt').val()) || 0);

		less_wt = less_wt + (parseFloat(row.find('.lwt').val()) || 0);

		net_wt = net_wt + (parseFloat(row.find('.retag_net_wt').val()) || 0);

	});

	$(".om_pcs").html(pieces);

	$(".om_gwt").html(parseFloat(grs_wt).toFixed(3));

	$(".om_lwt").html(parseFloat(less_wt).toFixed(3));

	$('.om_nwt').html(parseFloat(net_wt).toFixed(3));

}



function get_retagging_details()



{



	var tag_process = $('#tag_process').val();



    $("div.overlay").css("display", "block");



	my_Date = new Date();



	var is_st = $('#transfer_source').is(':checked');
	var ajax_endpoint = is_st ? 'st_ajax' : 'ajax';
	var ajax_data = is_st
		? {'id_branch':$("#id_branch").val(),'fin_year':$("#st_fin_year").val(),'bill_no':$("#st_bill_no").val(),'report_type':$('#report_type').val()}
		: {'id_branch':$("#id_branch").val(),'bt_number':$("#bt_number").val(),'report_type':$('#report_type').val()};

	$.ajax({
		url: base_url+"index.php/admin_ret_tagging/retagging/"+ajax_endpoint+"?nocache=" + my_Date.getUTCSeconds(),
		type:"POST",
		data: ajax_data,



		dataType: 'json',



		cache:false,



		success:function(data){



			 $("div.overlay").css("display","add_newstone");



			 var oTable = $('#retagging_list').DataTable();



			 oTable.clear().draw();



			 if (data!= null && data.length > 0)



			 {



				oTable = $('#retagging_list').dataTable({



						"bDestroy": true,



						"bInfo": true,



						"bFilter": true,



						"bSort": true,



						"order": [[ 0, "desc" ]],



						"aaData"  : data,



						"aoColumns": [



					        { "mDataProp": function ( row, type, val, meta ){



                            chekbox='<input type="checkbox" class="tag_id" name="tag_id[]" value="'+row.tag_id+'"/><input type="hidden" class="net_wt" value="'+row.net_wt+'"><input type="hidden" class="piece" value="'+row.piece+'"><input type="hidden" class="bill_det_id" value="'+row.bill_det_id+'"><input type="hidden" class="gross_wt" value="'+row.gross_wt+'"><input type="hidden" class="calculation_based_on" value="'+row.calculation_based_on+'"><input type="hidden" class="stock_type" value="'+row.stock_type+'"><input type="hidden" class="product_short_code" value="'+row.product_short_code+'"><input type="hidden" class="cat_id" value="'+row.cat_id+'">'



                            return chekbox+" "+row.tag_id;



                            }},



                            { "mDataProp": "branch_name"},



                            { "mDataProp": function ( row, type, val, meta ){



                                if( $('#report_type').val()==1){



                                    var url = base_url+'index.php/admin_ret_billing/billing_invoice/'+row.bill_id;



                                    return '<a href='+url+' target="_blank">'+row.metal_code+'-'+row.sales_ref_no+'</a>';



                                }else{



                                    return '-';



                                }







                            }},



                            { "mDataProp": function ( row, type, val, meta ){



                                if( $('#report_type').val()==1){



                                    return row.bill_date



                                }else{



                                    return '-';



                                }







                            }},



							{ "mDataProp": function(row){ return $('#transfer_source').is(':checked') ? '-' : row.branch_trans_code; } },



                            { "mDataProp": "tag_code" },



							{

								"mDataProp":function(row,type,val,meta)

								{

									return '<input type="hidden" class="form-control purity" value="'+row.purity+'">'+row.purity+'';

								}

							},



                            { "mDataProp": "product_name" },



                            { "mDataProp": "design_name" },



							{ "mDataProp": function (row,type,val,meta){



                            	return '<input type="number" class="form-control retag_pcs" value="'+row.piece+'" style="width:100px;"><input type="hidden" class="form-control actual_piece" value="'+row.piece+'" >';



                            }},



                            { "mDataProp": function (row,type,val,meta){



                            	return '<input type="number" class="form-control retaggross_wt" value="'+row.gross_wt+'" style="width:100px;"><input type="hidden" class="form-control actual_gross_wt" value="'+row.gross_wt+'" ><input type="hidden" class="form-control id_metal_type" value="'+row.id_metal+'"">';



                            }},



							{ "mDataProp": function (row,type,val,meta)



							{



								var stn_less_wt = 0;



								var stone_details = row.stone_details;



								$.each(stone_details,function(pkey,pitem)



								{



									if(pitem.uom_id==6)



									{



										stn_less_wt+=parseFloat(pitem.stone_wt/5);



									}



									else



									{



										stn_less_wt+=parseFloat(pitem.stone_wt);



									}



								});



                            	return '<div class="form-group"><div class="input-group "><input class="form-control lwt" value=' + parseFloat(stn_less_wt).toFixed(3) + ' onClick="create_new_empty_retag_stone_item($(this).closest(\'tr\'),'+row.bill_det_id+');"  type="number" step="any" readonly style="width:100px;"/><span class="input-group-addon input-sm add_tag_lwt" onClick="create_new_empty_retag_stone_item($(this).closest(\'tr\'));">+</span></div></div><input type="hidden" class="stone_details" value=\'' + JSON.stringify(row.stone_details) +'\'>';



                            }},



                            { "mDataProp": function (row,type,val,meta){



                            	return '<input type="number" class="form-control retag_net_wt" value="'+row.net_wt+'" readonly style="width:100px;">';



                            }},



                            { "mDataProp" : function(row,type,val,meta)

							{

								return '<input type="number" class="form-control retag_dia_wt" value="'+row.dia_wt+'" readonly style="width:100px;">';

							}

							},



							{

								"mDataProp":function(row,type,val,meta)

								{

									return '<input type="hidden" class="form-control retag_amount" value="'+row.sales_value+'"><input type="hidden" class="rate_per_grm" value="'+row.rate_per_grm+'">'+row.sales_value+'';

								}

							},



							{ "mDataProp": function ( row, type, val, meta )



							{



								if(tag_process==2)



								{



									return '<textarea class="form-control othr_issue_remarks" id=""  rows="5" cols="25"> </textarea>';



								}



								else



								{



									return '-';



								}



							}},



						],



					});



					calculate_SalesReturn_RowTotal();





				}



				$("div.overlay").css("display", "none");



		},



		 error:function(error)



		  {



			 $("div.overlay").css("display", "none");



		  }



	});



}







function calculate_SalesReturn_RowTotal()

{

	var pieces = 0;

	var grs_wt = 0;

	var less_wt = 0;

	var net_wt = 0;

	var dia_wt = 0;

	$("#retagging_list > tbody > tr").each(function () {

		var row = $(this).closest('tr');

		pieces = pieces + (parseFloat(row.find('.retag_pcs').val()) || 0);

		grs_wt = grs_wt + (parseFloat(row.find('.retaggross_wt').val()) || 0);

		less_wt = less_wt + (parseFloat(row.find('.lwt').val()) || 0);

		net_wt = net_wt + (parseFloat(row.find('.retag_net_wt').val()) || 0);

		dia_wt = dia_wt + (parseFloat(row.find('.retag_dia_wt').val()) || 0);

	});

	$(".sr_pcs").html(pieces);

	$(".sr_gwt").html(parseFloat(grs_wt).toFixed(3));

	$(".sr_lwt").html(parseFloat(less_wt).toFixed(3));

	$('.sr_nwt').html(parseFloat(net_wt).toFixed(3));

	$('.sr_diawt').html(parseFloat(dia_wt).toFixed(3));

}



$(document).on('keyup','.retag_pcs',function()



{



	var row = $(this).closest('tr');



	var retag_pcs = row.find('.retag_pcs').val();



	var actual_pcs = row.find('.actual_piece').val();



	if(parseInt(retag_pcs)>parseInt(actual_pcs))



	{



		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Valid Pcs!'});



        row.find('.retag_pcs').val(actual_pcs);



        row.find('.retag_pcs').val(actual_pcs);



	}



	else



	{



		row.find('.retag_pcs').val(retag_pcs);



	}



	calculate_old_metal_retag_row();



	calculate_average_purity_and_rate();





});



$(document).on('keyup','.retaggross_wt',function(){



    var row = $(this).closest('tr');



    var retaggross_wt = row.find('.retaggross_wt').val();



	var retaglwt = row.find('.lwt').val();



    var actual_weight = row.find('.actual_gross_wt').val();



    var retag_net_wt = 0;



    if(retaggross_wt!='' && retaggross_wt!=0){



        if(parseFloat(retaggross_wt)>parseFloat(actual_weight))



        {



            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Valid Weight!'});



            row.find('.retaggross_wt').val(actual_weight);



            row.find('.retag_net_wt').val(actual_weight);



        }else{



            retag_net_wt = parseFloat(parseFloat(retaggross_wt)-parseFloat(retaglwt)).toFixed(3);



            row.find('.retag_net_wt').val(retag_net_wt);



        }



    }else{



        row.find('.retaggross_wt').val(actual_weight);



        row.find('.retag_net_wt').val(actual_weight);



    }





	calculate_old_metal_retag_row();



	calculate_average_purity_and_rate();





});



function calculate_old_metal_retag_row()



{



    $('#old_metal_sale_list > tbody  > tr').each(function(index, tr)



    {



        curRow = $(this);



        var gross_wt = (curRow.find('.retaggross_wt').val()!='' ? curRow.find('.retaggross_wt').val() :0);



        var lesst_wt = (curRow.find('.lwt').val()!='' ? curRow.find('.lwt').val() : 0);



        var retag_gwt = (curRow.find('.retag_gwt').val()!='' ? curRow.find('.retag_gwt').val() : 0);



        var retag_dust_wt = (curRow.find('.retag_dust_wt').val()!='' ? curRow.find('.retag_dust_wt').val() : 0);



        var retag_wast_wt = (curRow.find('.retag_wast_wt').val()!='' ? curRow.find('.retag_wast_wt').val() : 0);



        if(retag_gwt==0)



        {



            old_metal_nwt = parseFloat(parseFloat(gross_wt)-parseFloat(lesst_wt)-parseFloat(retag_dust_wt)-parseFloat(retag_wast_wt)).toFixed(3);



        }else{



            old_metal_nwt = parseFloat(parseFloat(gross_wt)-parseFloat(lesst_wt)).toFixed(3);



        }



        var old_metal_nwt = parseFloat(old_metal_nwt).toFixed(3);



        curRow.find('.retag_net_wt').val(old_metal_nwt);



    });



}



$(document).on('keyup','.partial_sale_pcs',function(){



    var row = $(this).closest('tr');



    var partial_sale_pcs = row.find('.partial_sale_pcs').val();



    var available_blc_pcs = row.find('.available_blc_pcs').val();



if(parseFloat(partial_sale_pcs)>parseFloat(available_blc_pcs) || partial_sale_pcs == 0)



    {



        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Valid Pcs!'});



        row.find('.partial_sale_pcs').val(available_blc_pcs);



    }



    calculate_partly_sale_retag_row();



	calculate_average_purity_and_rate();





});



$(document).on('keyup','.partial_sale_gwt',function(){



    var row = $(this).closest('tr');



    var partial_sale_gwt = row.find('.partial_sale_gwt').val();



    var available_blc_wt = row.find('.available_blc_wt').val();



    if(parseFloat(partial_sale_gwt)>parseFloat(available_blc_wt))



    {



        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Valid Weight!'});



        row.find('.partial_sale_gwt').val(available_blc_wt);



    }



    calculate_partly_sale_retag_row();



	calculate_average_purity_and_rate();





});



/* Stone modal function*/



function create_new_empty_retag_stone_item(curRow,bill_det_id)



{



	retageditRow = curRow;



    var rowDetails = $('#retagging_list').DataTable().rows().data();



    rowDetails.each(function (value, index) {



		if(value.bill_det_id==bill_det_id)



		{



			var row = '';



			var row_st_details=value.stone_details;



			$('#estimation_stone_cus_item_details tbody').empty();



			var stone_details=(row_st_details);



			$.each(stone_details,function(pkey,pitem)



			{



				console.log('Stn',pitem);



				var stones_list='';



				var stones_type_list='';



				var uom_list='';



				var html='';



				var cal_type = pitem.stone_cal_type;



				$.each(uom_details, function (pkey, item)



				{



					var uom_selected = "";



				   if(item.uom_id == pitem.uom_id)



				   {



					   uom_selected = "selected='selected'";



				   }



				   uom_list += "<option value='"+item.uom_id+"' "+uom_selected+">"+item.uom_name+"</option>";



			   });



				row += '<tr>'



					+'<td>'+pitem['stone_types']+'<input type="hidden" class="stone_type" value="'+pitem['stone_type']+'"><input type="hidden" class="bill_det_id" value="'+bill_det_id+'"></td>'



					+'<td>'+pitem['stone_name']+'<input type="hidden" class="stone_id" value="'+pitem['stone_id']+'"></td>'



					+'<td><input type="number" class="ret_stone_pcs form-control" name="est_stones_item[ret_stone_pcs][]"  value="'+pitem['stone_pcs']+'" style="width: 70%;"/><input type="hidden" class="act_retstn_pcs" value="'+pitem['stone_pcs']+'"></td>'



					+'<td><div class="input-group" style="width:159px;"><input class="ret_stone_wt form-control" type="number" name="est_stones_item[ret_stone_wt][]" value="'+pitem['stone_wt']+'" style="width: 100px;"/><input type="hidden" class="act_retstn_wt" value="'+pitem['stone_wt']+'"><span class="input-group-btn" style="width: 138px;"><select class="stone_uom_id form-control" name="est_stones_item[uom_id][]" style="width: 100px;" disabled>'+uom_list+'</select></span></div></td>'



				+'</tr>'



			});



			$('#cus_return_stoneModal .modal-body').find('#estimation_stone_cus_item_details tbody').append(row);



			$('#cus_return_stoneModal').modal('show');



		}



    });



}



$(document).on('change','.ret_stone_pcs',function()



{



	var row = $(this).closest('tr');



	var act_pcs = row.find('.act_retstn_pcs').val();



	if(this.value < 0)



	{



		row.find('.ret_stone_pcs').val(act_pcs);



		row.find('.ret_stone_pcs').focus();



		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Entered Pcs greater than Actual Pcs!'});



	}



	else if(parseInt(this.value) > act_pcs)



	{



		row.find('.ret_stone_pcs').val(act_pcs);



		row.find('.ret_stone_pcs').focus();



		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Entered Pcs greater than Actual Pcs!'});



	}



	else



	{



		row.find('.ret_stone_pcs').val(this.value);



	}



});



$(document).on('change','.ret_stone_wt',function()



{



	var row = $(this).closest('tr');



	var act_wt = row.find('.act_retstn_wt').val();







	if(this.value < 0)



	{



		row.find('.ret_stone_wt').val(act_wt);



		row.find('.ret_stone_wt').focus();



		$.toaster({priority:'danger',title:'Warning!',message:''+"</br>"+'Entered Wt greater than Actual Wt!'});



	}



	else if(parseFloat(this.value) > parseFloat(act_wt))



	{



		row.find('.ret_stone_wt').val(act_wt);



		row.find('.ret_stone_wt').focus();



		$.toaster({priority:'danger',title:'Warning!',message:''+"</br>"+'Entered Wt greater than Actual Wt!'});



	}



	else



	{



		row.find('.ret_stone_wt').val(this.value);



	}



})



$('#cus_return_stoneModal #update_retstone_details').on('click',function()



{



	var stone_details=[];



	var ret_less_wgt = 0;



	$('#cus_return_stoneModal .modal-body #estimation_stone_cus_item_details> tbody  > tr').each(function(index, tr)



	{



		if($(this).find('.stone_uom_id').val()==6)



		{



			ret_less_wgt+=parseFloat($(this).find('.ret_stone_wt').val()/5);



		}



		else



		{



			ret_less_wgt+=parseFloat($(this).find('.ret_stone_wt').val());



		}



		stone_details.push({



			'stone_id'          : $(this).find('.stone_id').val(),



			'stones_type'       : $(this).find('.stone_type').val(),



			'stone_pcs'         : $(this).find('.ret_stone_pcs').val(),



			'stone_wt'          : $(this).find('.ret_stone_wt').val(),



			'uom_id'            : $(this).find('.stone_uom_id').val(),



		});



	});



	var retaggross_wt = retageditRow.find('.retaggross_wt').val();



	var retag_gwt = retageditRow.find('.retag_gwt').val();



	var dust_wt = retageditRow.find('.retag_dust_wt').val();



	var wast_wt = retageditRow.find('.retag_wast_wt').val();



	var retag_net_wt = 0;



	if(retag_gwt==0)



	{



	    retag_net_wt = parseFloat(parseFloat(retaggross_wt)-parseFloat(dust_wt)-parseFloat(ret_less_wgt)-parseFloat(wast_wt)).toFixed(3);



	}else{



	    retag_net_wt = parseFloat(parseFloat(retaggross_wt)-parseFloat(ret_less_wgt)).toFixed(3);



	}



	if(parseFloat(retaggross_wt)<parseFloat(ret_less_wgt))



	{



	    $.toaster({priority:'danger',title:'Warning!',message:''+"</br>"+'Entered Wt is greater than Gross weight!'});



	}



	else{



	    retageditRow.find('.stone_details').val(JSON.stringify(stone_details));



    	retageditRow.find('.lwt').val(parseFloat(ret_less_wgt).toFixed(3));



    	retageditRow.find('.retag_net_wt').val(parseFloat(retag_net_wt).toFixed(3));



    	$('#cus_return_stoneModal .modal-body').find('#estimation_stone_cus_item_details tbody').empty();



    	retageditRow = '';



    	$('#cus_return_stoneModal').modal('hide');



    	calculate_old_metal_retag_row();



		calculate_average_purity_and_rate();





	}







})



/*Old Metal Stone Function*/



function create_new_empty_oldmet_stone_item(curRow,old_metal_sale_id)



{



	retageditRow = curRow;



    var rowDetails = $('#old_metal_sale_list').DataTable().rows().data();



    rowDetails.each(function (value, index) {



		if(value.old_metal_sale_id==old_metal_sale_id)



		{



			var row = '';



			var row_st_details=value.stone_details;



			$('#estimation_stone_cus_item_details tbody').empty();



			var stone_details=(row_st_details);



			$.each(stone_details,function(pkey,pitem)



			{



				console.log('Stn',pitem);



				var stones_list='';



				var stones_type_list='';



				var uom_list='';



				var html='';



				var cal_type = pitem.stone_cal_type;



				$.each(uom_details, function (pkey, item)



				{



					var uom_selected = "";



				   if(item.uom_id == pitem.uom_id)



				   {



					   uom_selected = "selected='selected'";



				   }



				   uom_list += "<option value='"+item.uom_id+"' "+uom_selected+">"+item.uom_name+"</option>";



			   });



				row += '<tr>'



					+'<td>'+pitem['stone_types']+'</td>'



					+'<td>'+pitem['stone_name']+'<input type="hidden" class="stone_id" value="'+pitem['stone_id']+'"></td>'



					+'<td><input type="number" class="ret_stone_pcs form-control" name="est_stones_item[ret_stone_pcs][]"  value="'+pitem['stone_pcs']+'" style="width: 70%;"/><input type="hidden" class="act_retstn_pcs" value="'+pitem['stone_pcs']+'"></td>'



					+'<td><div class="input-group" style="width:159px;"><input class="ret_stone_wt form-control" type="number" name="est_stones_item[ret_stone_wt][]" value="'+pitem['stone_wt']+'" style="width: 100px;"/><input type="hidden" class="act_retstn_wt" value="'+pitem['stone_wt']+'"><span class="input-group-btn" style="width: 138px;"><select class="stone_uom_id form-control" name="est_stones_item[uom_id][]" style="width: 100px;" disabled>'+uom_list+'</select></span></div></td>'



				+'</tr>'



			});



			$('#cus_return_stoneModal .modal-body').find('#estimation_stone_cus_item_details tbody').append(row);



			$('#cus_return_stoneModal').modal('show');



		}



    });



}



function create_new_empty_partial_sale_retag_stone_item(curRow,tag_id)



{



    retageditRow = curRow;



    var rowDetails = $('#partly_sale_list').DataTable().rows().data();



    rowDetails.each(function (value, index) {



         if(value.tag_id==tag_id){



             var row = '';



            var row_st_details=value.stone_details;



            	$('#estimation_stone_cus_item_details tbody').empty();



        		var stone_details=(row_st_details);



        		$.each(stone_details,function(pkey,pitem)



				{



                    row += '<tr>'



                    +'<td>'+pitem['stone_type_name']+'<input type="hidden" class="stone_type_name" value="'+pitem['stone_type_name']+'"><input type="hidden" class="stone_type" value="'+pitem['stone_type']+'"></td>'



                    +'<td>'+pitem['stone_name']+'<input type="hidden" class="stone_name" value="'+pitem['stone_name']+'"><input type="hidden" class="stone_id" value="'+pitem['stone_id']+'"></td>'



                    +'<td>'+pitem['uom_name']+'<input type="hidden" class="uom_name" value="'+pitem['uom_name']+'" ><input type="hidden" class="stone_uom_id form-control" value="'+pitem['uom_id']+'" ></td>'



                    +'<td><input type="number" class="partly_sold_stone_pcs form-control" name="est_stones_item[ret_stone_pcs][]" value="'+pitem['stone_pcs']+'" style="width: 70%;"/><input type="hidden" class="act_partly_sold_stone_pcs" value="'+pitem['blc_pcs']+'"></td>'



                    +'<td><input class="partly_sold_stone_wt form-control" type="number" name="est_stones_item[stone_wt][]" value="'+pitem['stone_wt']+'" style="width: 100px;"/><input type="hidden" class="act_partly_sold_stone_wt" value="'+pitem['blc_wt']+'"><span class="input-group-btn" style="width: 138px;"></td>'



                    +'</tr>'



        		 });



        		$('#cus_stoneModal .modal-body').find('#estimation_stone_cus_item_details tbody').append(row);



	            $('#cus_stoneModal').modal('show');



         }



     });



}



$(document).on('keyup','.partly_sold_stone_wt',function(){



    var row = $(this).closest('tr');



    var partly_sold_stone_wt = row.find('.partly_sold_stone_wt').val();



    var act_partly_sold_stone_wt = row.find('.act_partly_sold_stone_wt').val();



	if(partly_sold_stone_wt<0)



	{



        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Valid Weight!'});



        row.find('.partly_sold_stone_wt').val(act_partly_sold_stone_wt);



	}



   else if(parseFloat(partly_sold_stone_wt)>parseFloat(act_partly_sold_stone_wt))



    {



        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Valid Weight!'});



        row.find('.partly_sold_stone_wt').val(act_partly_sold_stone_wt);



    }



    calculate_partly_sale_retag_row();



});



$(document).on('keyup','.partly_sold_stone_pcs',function(){



    var row = $(this).closest('tr');



    var partly_sold_stone_pcs = row.find('.partly_sold_stone_pcs').val();



    var act_partly_sold_stone_pcs = row.find('.act_partly_sold_stone_pcs').val();



    if(partly_sold_stone_pcs<0)



    {



        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Valid Pcs!'});



        row.find('.partly_sold_stone_pcs').val(act_partly_sold_stone_pcs);



    }







    else if(parseFloat(partly_sold_stone_pcs)>parseFloat(act_partly_sold_stone_pcs))



    {



        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Valid Pcs!'});



        row.find('.partly_sold_stone_pcs').val(act_partly_sold_stone_pcs);



    }



    calculate_partly_sale_retag_row();



});



$('#cus_stoneModal  #update_partial_sold_stone_details').on('click', function(){



    	var stone_details=[];



    	var less_wgt = 0;



    	$('#cus_stoneModal .modal-body #estimation_stone_cus_item_details> tbody  > tr').each(function(index, tr) {



    		if($(this).find('.stone_uom_id').val() == 6){



    		    less_wgt+=parseFloat(parseFloat($(this).find('.partly_sold_stone_wt').val())/5);



    		}else{



    		    less_wgt+=parseFloat($(this).find('.partly_sold_stone_wt').val());



    		}



    		stone_details.push({



    		            'stone_id'          : $(this).find('.stone_id').val(),



    		            'stone_type'        : $(this).find('.stone_type').val(),



    		            'stone_type_name'   : $(this).find('.stone_type_name').val(),



    		            'uom_name'          : $(this).find('.uom_name').val(),



    		            'stone_name'        : $(this).find('.stone_name').val(),



    		            'blc_pcs'           : $(this).find('.act_partly_sold_stone_pcs').val(),



    		            'blc_wt'            : $(this).find('.act_partly_sold_stone_wt').val(),



    		            'stone_pcs'         : $(this).find('.partly_sold_stone_pcs').val(),



    		            'stone_wt'          : $(this).find('.partly_sold_stone_wt').val(),



    		            'uom_id'            : $(this).find('.stone_uom_id').val(),



    		});



    	});



    	retageditRow.find('.lwt').val(parseFloat(less_wgt).toFixed(3));



    	retageditRow.find('.stone_details').val(JSON.stringify(stone_details));



        $('#cus_stoneModal .modal-body').find('#estimation_stone_cus_item_details tbody').empty();



        retageditRow = '';



        $('#cus_stoneModal').modal('hide');



        calculate_partly_sale_retag_row();



		calculate_average_purity_and_rate();





});



function calculate_partly_sale_retag_row()



{



    $('#partly_sale_list > tbody  > tr').each(function(index, tr)



    {



        curRow = $(this);



        var gross_wt = (curRow.find('.partial_sale_gwt').val()!='' ? curRow.find('.partial_sale_gwt').val() :0);



        var lesst_wt = (curRow.find('.lwt').val()!='' ? curRow.find('.lwt').val() : 0);



        var partial_sale_nwt = parseFloat(parseFloat(gross_wt)-parseFloat(lesst_wt)).toFixed(3);



        curRow.find('.partial_sale_nwt').val(partial_sale_nwt);



    });



}



function get_non_tag_return_details()



{



    $("div.overlay").css("display", "block");



	my_Date = new Date();



	$.ajax({



		url: base_url+"index.php/admin_ret_tagging/retagging/non_tag_details?nocache=" + my_Date.getUTCSeconds(),



		type:"POST",



		data:{'id_branch':$("#id_branch").val(),'bt_number':$("#bt_number").val()},



		dataType: 'json',



		cache:false,



		success:function(data){



			 $("div.overlay").css("display","add_newstone");



			 var oTable = $('#non_tag_list').DataTable();



			 oTable.clear().draw();



			 if (data!= null && data.length > 0)



			 {



				oTable = $('#non_tag_list').dataTable({



						"bDestroy": true,



						"bInfo": true,



						"bFilter": true,



						"bSort": true,



						"order": [[ 0, "desc" ]],



						"aaData"  : data,



						"aoColumns": [



					        { "mDataProp": function ( row, type, val, meta ){



                            chekbox='<input type="checkbox" class="bill_det_id" name="bill_det_id[]" value="'+row.bill_det_id+'"/><input type="hidden" class="net_wt" value="'+row.net_wt+'"><input type="hidden" class="piece" value="'+row.piece+'"><input type="hidden" class="gross_wt" value="'+row.gross_wt+'"><input type="hidden" class="stock_type" value="'+row.stock_type+'"><input type="hidden" class="product_short_code" value="'+row.product_short_code+'">'



                            return chekbox+" "+row.bill_det_id;



                            }},



                            { "mDataProp": "branch_name"},



                            { "mDataProp": function ( row, type, val, meta ){



                            var url = base_url+'index.php/admin_ret_billing/billing_invoice/'+row.bill_id;



                            return '<a href='+url+' target="_blank">'+row.metal_code+'-'+row.sales_ref_no+'</a>';



                            }},



                            { "mDataProp": "bill_date" },



							{ "mDataProp": "branch_trans_code" },



                            { "mDataProp": "product_name" },



                            { "mDataProp": "design_name" },



							{ "mDataProp":function(row,type,val,meta){

								return '<input type="number" class="form-control nt_ret_pieces" value="'+row.piece+'" style="width:100px;"><input type="hidden" class="form-control actual_nt_pcs" value="'+row.piece+'">'

							}},



                            { "mDataProp": function (row,type,val,meta){



                            	return '<input type="number" class="form-control nt_ret_grswt" value="'+row.gross_wt+'" style="width:100px;"><input type="hidden" class="form-control actual_nt_grswt" value="'+row.gross_wt+'" >';



                            }},



                            { "mDataProp": function (row,type,val,meta){



                            	return '<input type="number" class="form-control nt_ret_netwt" value="'+row.net_wt+'" style="width:100px;" readonly>';



                            }},



                            { "mDataProp": "item_cost" },



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



$(document).on('keyup','.nt_ret_grswt',function()

	{

	var row = $(this).closest('tr');



	var nt_grswt  = row.find('.nt_ret_grswt').val();



	var act_nt_wt = row.find('.actual_nt_grswt').val();



	if(nt_grswt!='' && nt_grswt!=0){



        if(parseFloat(nt_grswt)>parseFloat(act_nt_wt))



        {



            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Valid Weight!'});



            row.find('.nt_ret_grswt').val(act_nt_wt);



            row.find('.nt_ret_netwt').val(act_nt_wt);



        }else{



            nt_net_wt = parseFloat(parseFloat(nt_grswt)).toFixed(3);



            row.find('.nt_ret_netwt').val(nt_net_wt);



        }



    }else{



        row.find('.nt_ret_grswt').val(act_nt_wt);



        row.find('.nt_ret_netwt').val(act_nt_wt);



    }

})



$(document).on('keyup','.nt_ret_pieces',function()

{

	var row = $(this).closest('tr');

	var nt_ret_pcs = row.find('.nt_ret_pieces').val();

	var actual_nt_pcs = row.find('.actual_nt_pcs').val();



	if(parseFloat(nt_ret_pcs) > parseFloat(actual_nt_pcs))

	{

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Valid Pcs!'});

		row.find('.nt_ret_pieces').val(actual_nt_pcs);

	}

	else

	{

		row.find('.nt_ret_pieces').val(nt_ret_pcs);

	}

})







function validateSalesReturnRow()



{



    var row_validate = true;



	$("#retagging_list tbody tr").each(function(index, value){



	    if($(value).find("input[name='tag_id[]']:checked").is(":checked"))



	    {



	       if(($(value).find(".retaggross_wt").val() == "" || $(value).find('.retaggross_wt').val() == 0 || (parseFloat($(value).find('.retaggross_wt').val())<parseFloat($(value).find('.retag_net_wt').val()))) && $(value).find('.calculation_based_on').val()!=3){



    			row_validate = false;



		    }



	    }







	});



	return row_validate;



}







$('#create_retag').on('click',function(){



    var allow_submit=true;



    if($("#id_branch").val()==null && $("#id_branch").val()=='')



    {



        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Branch!'});



        allow_submit=false;



    }



    else if(($('#retagging_list > tbody tr').length==0) && ($('#report_type').val()==1))



    {



        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Records to Update!'});



        allow_submit=false;



    }



	else if(($('#non_tag_list > tbody tr').length==0) && ($('#report_type').val()==5))

	{

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Records to Update!'});



		allow_submit=false;

	}



    else if($('#report_type').val()==3)



    {



        if(($('#partly_sale_list > tbody tr').length==0))



        {



            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Records to Update!'});



            allow_submit=false;



        }



        else if($('#tag_process').val()==4)



        {



			 if($('#select_category').val()=='' || $('#select_category').val()==null)



			{



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Category'});



			}



			else if($('#select_purity').val()=='' || $('#select_purity').val()==null)



			{



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Purity'});



			}



			else if($('#section_select').val()=='' || $('#section_select').val()==null)



            {



                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Section!'});



                allow_submit=false;



            }





            else if($('#prod_select').val()=='' || $('#prod_select').val()==null)



            {



                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Product!'});



                allow_submit=false;



            }



            else if($('#des_select').val()=='' || $('#des_select').val()==null)



            {



                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Design!'});



                allow_submit=false;



            }



            else if($('#sub_des_select').val()=='' || $('#sub_des_select').val()==null)



            {



                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Sub Design!'});



                allow_submit=false;



            }



			else if($('#tag_karigar').val()=='' || $('#tag_karigar').val()==null)



			{



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Karigar'});



			}



        }



		else if($('#tag_process').val()==2)



		{



			$("#partly_sale_list tbody tr").each(function(index, value)



			{



				if($(value).find("input[name='tag_id[]']:checked").is(":checked") && !$.trim($(value).find('#othr_issue_remarks').val()))



				{



					$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter Reamrks'});



					allow_submit=false;



				}



			});



		}



    }



    else if($('#report_type').val()==4)



    {



        if(($('#old_metal_sale_list > tbody tr').length==0))



        {



            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Records to Update!'});



            allow_submit=false;



        }



    }



    else if($('#report_type').val()==5)



    {



        if(($('#non_tag_list > tbody tr').length==0))



        {



            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Records to Update!'});



            allow_submit=false;



        }

		else if($('#tag_process').val()==1)

		{

			if($('#select_category').val()=='' || $('#select_category').val()==null)

			{

				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Category'});

			}

			else if($('#select_purity').val()=='' || $('#select_purity').val()==null)

			{

				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Purity'});

			}

            else if($('#prod_select').val()=='' || $('#prod_select').val()==null)

            {

                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Product!'});

                allow_submit=false;

            }

		}



    }





	else if($('#report_type').val()==7)



    {



        if(($('#non_tag_otherissue_list > tbody tr').length==0))



        {



            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Records to Update!'});



            allow_submit=false;



        }

		else if($('#tag_process').val()==1)

		{

			if($('#select_category').val()=='' || $('#select_category').val()==null)

			{

				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Category'});

			}

			else if($('#select_purity').val()=='' || $('#select_purity').val()==null)

			{

				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Purity'});

			}

            else if($('#prod_select').val()=='' || $('#prod_select').val()==null)

            {

                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Product!'});

                allow_submit=false;

            }

		}



    }





    else if($('#report_type').val()==1 || $('#report_type').val()==6)



    {



        if(($('#retagging_list > tbody tr').length==0))



        {



            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Records to Update!'});



            allow_submit=false;



        }



        else if(!validateSalesReturnRow())



        {



            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Net weight is Grater than the gross weight!'});



            allow_submit=false;



        }



        else if($('#tag_process').val()==4)



        {



			 if($('#select_category').val()=='' || $('#select_category').val()==null)



			{



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Category'});



			}



			else if($('#select_purity').val()=='' || $('#select_purity').val()==null)



			{



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Purity'});



			}

			else if($('#section_select').val()=='' || $('#section_select').val()==null)



            {



                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Section!'});



                allow_submit=false;



            }



            else if($('#prod_select').val()=='' || $('#prod_select').val()==null)



            {



                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Product!'});



                allow_submit=false;



            }



            else if($('#des_select').val()=='' || $('#des_select').val()==null)



            {



                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Design!'});



                allow_submit=false;



            }



            else if($('#sub_des_select').val()=='' || $('#sub_des_select').val()==null)



            {



                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Sub Design!'});



                allow_submit=false;



            }



			else if($('#tag_karigar').val()=='' || $('#tag_karigar').val()==null)



			{



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Karigar'});



			}



        }



    }



    if(allow_submit)



    {



        var selected = [];



        if($('#report_type').val()==1 || $('#report_type').val()==6)



        {



		  if($('#tag_process').val()==1)



		   {



			if($('#select_category').val()=='' || $('#select_category').val()==null)



			{



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Category'});



			}



			else if($('#select_purity').val()=='' || $('#select_purity').val()==null)



			{



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Purity'});



			}



			else if($('#prod_select').val()=='' || $('#prod_select').val()==null)



			{



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Product'});



			}







			else if($('#tag_karigar').val()=='' || $('#tag_karigar').val()==null)



			{



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Karigar'});



			}



			else if ($("input[name='tag_id[]']:checked").val())



            {



                $("#retagging_list tbody tr").each(function(index, value)



                {



                    if($(value).find("input[name='tag_id[]']:checked").is(":checked"))



                    {



                        transData = {



                        'tag_id'               : $(value).find(".tag_id").val(),



                        'product_short_code'   : $(value).find(".product_short_code").val(),



                        'stock_type'           : $(value).find(".stock_type").val(),



                        'net_wt'               : $(value).find(".retag_net_wt").val(),



						'less_wt'              : $(value).find(".lwt").val(),



                        'gross_wt'             : $(value).find(".retaggross_wt").val(),



                        'piece'                : $(value).find(".retag_pcs").val(),



						'narration'            : $(value).find('#othr_issue_remarks').val(),



						'stone_details'        : $(value).find('.stone_details').val(),



						'id_old_metal_type'        : $(value).find('.id_metal_type').val(),

                        }



                        selected.push(transData);



                    }



                });



                req_data = selected;



                create_retag(req_data);



            } else



            {



                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Any One Tag'});







			}







		  }



		  else if($('#tag_process').val()==4)



          {



			if($('#select_category').val()=='' || $('#select_category').val()==null)



			{



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Category'});



			}



			else if($('#select_purity').val()=='' || $('#select_purity').val()==null)



			{



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Purity'});



			}





			else if($('#section_select').val()=='' || $('#section_select').val()==null)



            {



                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Section!'});



                allow_submit=false;



            }



			else if($('#prod_select').val()=='' || $('#prod_select').val()==null)



			{



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Product'});



			}



			else if($('#des_select').val()=='' || $('#des_select').val()==null)



			{



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Design!'});



				allow_submit=false;



			}



			else if($('#sub_des_select').val()=='' || $('#sub_des_select').val()==null)



			{



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Sub Design!'});



				allow_submit=false;



			}







			else if($('#tag_karigar').val()=='' || $('#tag_karigar').val()==null)



			{



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Karigar'});



			}



			else if ($("input[name='tag_id[]']:checked").val())



            {



                $("#retagging_list tbody tr").each(function(index, value)



                {



                    if($(value).find("input[name='tag_id[]']:checked").is(":checked"))



                    {



                        transData = {



                        'tag_id'               : $(value).find(".tag_id").val(),



                        'product_short_code'   : $(value).find(".product_short_code").val(),



                        'stock_type'           : $(value).find(".stock_type").val(),



                        'net_wt'               : $(value).find(".retag_net_wt").val(),



						'less_wt'              : $(value).find(".lwt").val(),



                        'gross_wt'             : $(value).find(".retaggross_wt").val(),



                        'piece'                : $(value).find(".retag_pcs").val(),



						'narration'            : $(value).find('#othr_issue_remarks').val(),



						'stone_details'        : $(value).find('.stone_details').val(),



                        }



                        selected.push(transData);



                    }



                });



                req_data = selected;



                create_retag(req_data);



            } else



            {



                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Any One Tag'});







			}







		   }



		   else if($('#tag_process').val()==2)



		   {



		    if ($("input[name='tag_id[]']:checked").val())



            {



                $("#retagging_list tbody tr").each(function(index, value)



                {



                    if($(value).find("input[name='tag_id[]']:checked").is(":checked"))



                    {



                        transData = {



                        'tag_id'               : $(value).find(".tag_id").val(),



                        'product_short_code'   : $(value).find(".product_short_code").val(),



                        'stock_type'           : $(value).find(".stock_type").val(),



                        'net_wt'               : $(value).find(".retag_net_wt").val(),



						'less_wt'              : $(value).find(".lwt").val(),



                        'gross_wt'             : $(value).find(".retaggross_wt").val(),



                        'piece'                : $(value).find(".retag_pcs").val(),



						'narration'            : $(value).find('.othr_issue_remarks').val(),



						'stone_details'        : $(value).find('.stone_details').val(),


						'id_old_metal_type'        : $(value).find('.id_metal_type').val(),

                        }



                        selected.push(transData);



                    }



                });



                req_data = selected;



                create_retag(req_data);



            } else



            {



                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Any One Tag'});







			}



		   }



		   else if($('#tag_process').val()==6)  // Add to pocket

			{

				if ($("input[name='tag_id[]']:checked").val())

				{



					$("#retagging_list tbody tr").each(function(index, value)



					{



						if($(value).find("input[name='tag_id[]']:checked").is(":checked"))



						{



							transData = {

							'cat_id'               : $(value).find(".cat_id").val(),



							'tag_id'               : $(value).find(".tag_id").val(),



							'purity'               : $(value).find(".purity").val(),



							'stock_type'           : $(value).find(".stock_type").val(),



							'net_wt'               : $(value).find(".retag_net_wt").val(),



							'less_wt'              : $(value).find(".lwt").val(),



							'gross_wt'             : $(value).find(".retaggross_wt").val(),



							'diawt'                : $(value).find(".retag_dia_wt").val(),



							'piece'                : $(value).find(".retag_pcs").val(),



							'narration'            : $(value).find('#othr_issue_remarks').val(),



							'stone_details'        : $(value).find('.stone_details').val(),



							'item_cost'            : $(value).find('.retag_amount').val(),



							'rate_per_gram'        : $(value).find('.rate_per_grm').val(),



							'pocket_type'          : 2,


							'id_old_metal_type'        : $(value).find('.id_metal_type').val(),

							}



							selected.push(transData);



						}



					});



					req_data = selected;



					create_retag(req_data);



				} else

				{

					$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Any One Tag'});

				}

			}











        }







        else if($('#report_type').val()==3)



        {



			if($('#tag_process').val()==1)



			{







				if($('#select_category').val()=='' || $('#select_category').val()==null)







				{







					$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Category'});







				}







				else if($('#select_purity').val()=='' || $('#select_purity').val()==null)







				{







					$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Purity'});







				}







				else if($('#prod_select').val()=='' || $('#prod_select').val()==null)







				{







					$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Product'});







				}







				else if($('#tag_karigar').val()=='' || $('#tag_karigar').val()==null)







				{







					$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Karigar'});







				}







				else if($("input[name='tag_id[]']:checked").val())



				{



				$("#partly_sale_list tbody tr").each(function(index, value)



				{



					if($(value).find("input[name='tag_id[]']:checked").is(":checked"))



					{



						transData = {



						'tag_id'    : $(value).find(".tag_id").val(),



						'piece'     : $(value).find(".partial_sale_pcs").val(),



						'gross_wt'  : $(value).find(".partial_sale_gwt").val(),



						'less_wt'   : $(value).find(".lwt").val(),



						'net_wt'    : $(value).find(".partial_sale_nwt").val(),



						'stone_details'    : $(value).find(".stone_details").val(),



						'narration' : $(value).find('#othr_issue_remarks').val(),


						'id_old_metal_type'        : $(value).find('.id_metal_type').val(),

						}



						selected.push(transData);



					}



				});



				console.log(selected);



				req_data = selected;



				create_retag(req_data);



				}



				else



				{



					$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Any One Tag'});



				}



			}



			else if($('#tag_process').val()==4)







			{



			if($('#select_category').val()=='' || $('#select_category').val()==null)



			{



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Category'});



			}



			else if($('#select_purity').val()=='' || $('#select_purity').val()==null)



			{



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Purity'});



			}



			else if($('#section_select').val()=='' || $('#section_select').val()==null)



			{



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Section!'});



				allow_submit=false;



			}



			else if($('#prod_select').val()=='' || $('#prod_select').val()==null)



			{



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Product'});



			}



			else if($('#des_select').val()=='' || $('#des_select').val()==null)



			{



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Design!'});



				allow_submit=false;



			}



			else if($('#sub_des_select').val()=='' || $('#sub_des_select').val()==null)



			{



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Sub Design!'});



				allow_submit=false;



			}







			else if($('#tag_karigar').val()=='' || $('#tag_karigar').val()==null)



			{



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Karigar'});



			}







			else if($("input[name='tag_id[]']:checked").val())



			{



			$("#partly_sale_list tbody tr").each(function(index, value)



			{



				if($(value).find("input[name='tag_id[]']:checked").is(":checked"))



				{



					transData = {



					'tag_id'    : $(value).find(".tag_id").val(),



					'piece'     : $(value).find(".partial_sale_pcs").val(),



					'gross_wt'  : $(value).find(".partial_sale_gwt").val(),



					'less_wt'   : $(value).find(".lwt").val(),



					'net_wt'    : $(value).find(".partial_sale_nwt").val(),



					'stone_details'    : $(value).find(".stone_details").val(),



					'narration' : $(value).find('#othr_issue_remarks').val(),



					}



					selected.push(transData);



				}



			});



			console.log(selected);



			req_data = selected;



			create_retag(req_data);



			}



			else



			{



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Any One Tag'});



			}



			}



			else if($('#tag_process').val()==2)



			{



			if($("input[name='tag_id[]']:checked").val())



			{







				$("#partly_sale_list tbody tr").each(function(index, value)







				{







					if($(value).find("input[name='tag_id[]']:checked").is(":checked"))







					{







						transData = {







						'tag_id'    : $(value).find(".tag_id").val(),







						'piece'     : $(value).find(".partial_sale_pcs").val(),







						'gross_wt'  : $(value).find(".partial_sale_gwt").val(),







						'less_wt'   : $(value).find(".lwt").val(),







						'net_wt'    : $(value).find(".partial_sale_nwt").val(),







						'stone_details'    : $(value).find(".stone_details").val(),







						'narration' : $(value).find('#othr_issue_remarks').val(),


						'id_old_metal_type'        : $(value).find('.id_metal_type').val(),




						}







						selected.push(transData);







					}







				});







				console.log(selected);







				req_data = selected;







				create_retag(req_data);







				}







				else







				{







					$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Any One Tag'});







				}







			}



			else if($('#tag_process').val()==6)  // Add to pocket

			{

					if ($("input[name='tag_id[]']:checked").val())

					{



						$("#partly_sale_list tbody tr").each(function(index, value)



						{



							if($(value).find("input[name='tag_id[]']:checked").is(":checked"))



							{



							transData = {



								'cat_id'               : $(value).find(".cat_id").val(),



								'tag_id'               : $(value).find(".tag_id").val(),



								'purity'               : $(value).find(".purity").val(),



								'net_wt'               : $(value).find(".partial_sale_nwt").val(),



								'less_wt'              : $(value).find(".lwt").val(),



								'gross_wt'             : $(value).find(".partial_sale_gwt").val(),



								'diawt'                : 0,



								'piece'                : $(value).find(".partial_sale_pcs").val(),



								'narration'            : $(value).find('#othr_issue_remarks').val(),



								'stone_details'        : $(value).find('.stone_details').val(),



								'item_cost'            : $(value).find('.amount').val(),



								'rate_per_gram'        : $(value).find('.rate_per_grm').val(),



								'pocket_type'          : 3,

								'id_old_metal_type'        : $(value).find('.id_metal_type').val(),



							}



							selected.push(transData);



							}



						});



						req_data = selected;



						create_retag(req_data);



					} else

					{

						$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Any One Tag'});

					}

			}



        }



        else if($('#report_type').val()==4)



        {



            if($('#tag_process').val()==1)



            {



                if($('#select_category').val()=='' || $('#select_category').val()==null)



                {



                    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Category'});



                }



                else if($('#select_purity').val()=='' || $('#select_purity').val()==null)



                {



                    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Purity'});



                }



                else if($('#prod_select').val()=='' || $('#prod_select').val()==null)



                {



                    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Product'});



                }







				else if($('#tag_karigar').val()=='' || $('#tag_karigar').val()==null)



				{



					$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Karigar'});



				}



                else



                {



                        if($("input[name='old_metal_sale_id[]']:checked").val())



                        {



                            $("#old_metal_sale_list tbody tr").each(function(index, value)



                            {



                                if($(value).find("input[name='old_metal_sale_id[]']:checked").is(":checked"))



                                {



                                    transData = {



                                    'old_metal_sale_id'    : $(value).find(".old_metal_sale_id").val(),



                                    'purity'               : $(value).find(".purity").val(),



                                    'net_wt'               : $(value).find(".retag_net_wt").val(),



                                    'gross_wt'             : $(value).find(".retaggross_wt").val(),



									'piece'                : $(value).find(".retag_pcs").val(),



									'less_wt'              : $(value).find(".lwt").val(),



									'stone_details'        : $(value).find('.stone_details').val(),


									'id_old_metal_type'        : $(value).find('.id_metal_type').val(),

                                    }



                                    selected.push(transData);



                                }



                            });



                            console.log(selected);



                            req_data = selected;



                            create_retag(req_data);



                        }else{



                            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Any One Item'});



                        }



                }



            }



			else if($('#tag_process').val()==4)



            {







				if($('#select_category').val()=='' || $('#select_category').val()==null)







				{







					$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Category'});







				}







				 else if($('#select_purity').val()=='' || $('#select_purity').val()==null)







				{







					$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Purity'});







				}



				else if($('#section_select').val()=='' || $('#section_select').val()==null)



				{



					$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Section!'});



					allow_submit=false;



				}



               else if($('#prod_select').val()=='' || $('#prod_select').val()==null)



                {



                    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Product'});



                }



				else if($('#des_select').val()=='' || $('#des_select').val()==null)



				{







					$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Design!'});







					allow_submit=false;







				}







				else if($('#sub_des_select').val()=='' || $('#sub_des_select').val()==null)







				{







					$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Sub Design!'});







					allow_submit=false;







				}











				else if($('#tag_karigar').val()=='' || $('#tag_karigar').val()==null)







				{







					$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Karigar'});







				}



                else



                {



                    if($("input[name='old_metal_sale_id[]']:checked").val())



                        {



                            $("#old_metal_sale_list tbody tr").each(function(index, value)



                            {



                                if($(value).find("input[name='old_metal_sale_id[]']:checked").is(":checked"))



                                {



                                    transData = {



                                    'old_metal_sale_id'    : $(value).find(".old_metal_sale_id").val(),



                                    'purity'               : $(value).find(".purity").val(),



                                    'net_wt'               : $(value).find(".retag_net_wt").val(),



                                    'gross_wt'             : $(value).find(".retaggross_wt").val(),



									'piece'                : $(value).find(".retag_pcs").val(),



									'less_wt'              : $(value).find(".lwt").val(),



									'stone_details'        : $(value).find('.stone_details').val(),


									'id_old_metal_type'        : $(value).find('.id_metal_type').val(),

                                    }



                                    selected.push(transData);



                                }



                            });



                            console.log(selected);



                            req_data = selected;



                            create_retag(req_data);



                        }else{



                            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Any One Item'});



                        }



                }



            }



			else if($('#tag_process').val()==2)



			{







				if ($("input[name='old_metal_sale_id[]']:checked").val())



					{



						$("#old_metal_sale_list tbody tr").each(function(index, value)



						{



							if($(value).find("input[name='old_metal_sale_id[]']:checked").is(":checked"))



							{



								transData = {



								'old_metal_sale_id'    : $(value).find(".old_metal_sale_id").val(),



                                'purity'               : $(value).find(".purity").val(),



                                'net_wt'               : $(value).find(".retag_net_wt").val(),



                                'gross_wt'             : $(value).find(".retaggross_wt").val(),



								'piece'                : $(value).find(".retag_pcs").val(),



								'less_wt'              : $(value).find(".lwt").val(),



								'stone_details'        : $(value).find('.stone_details').val(),

								'id_old_metal_type'        : $(value).find('.id_metal_type').val(),

								}



								selected.push(transData);



							}



						});



						console.log(selected);



						req_data = selected;



						create_retag(req_data);



					}else{



						$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Any One Item'});



					}



			}



			else if($('#tag_process').val()==6)  // Add to pocket

			{

				 if ($("input[name='old_metal_sale_id[]']:checked").val())

				 {



					 $("#old_metal_sale_list tbody tr").each(function(index, value)



					 {



						 if($(value).find("input[name='old_metal_sale_id[]']:checked").is(":checked"))



						 {



							transData = {



								'cat_id'               : '',



								'purity'               : $(value).find(".purity").val(),



								'net_wt'               : $(value).find(".retag_net_wt").val(),



								'less_wt'              : $(value).find(".lwt").val(),



								'gross_wt'             : $(value).find(".retaggross_wt").val(),



								'diawt'                : 0,



								'piece'                : $(value).find(".retag_pcs").val(),



								'stone_details'        : $(value).find('.stone_details').val(),



								'item_cost'            : $(value).find('.amount').val(),



								'rate_per_gram'        : $(value).find('.rate_per_grm').val(),



								'pocket_type'          : 1,



								'old_metal_sale_id'    : $(value).find(".old_metal_sale_id").val(),



							 	'id_old_metal_type'    : $(value).find(".id_old_metal_type").val(),



							}



							selected.push(transData);



						 }



					 });



					 req_data = selected;



					 create_retag(req_data);



				 } else

				 {

					 $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Any One Tag'});

				 }

			}



        }



        else if($('#report_type').val()==5)

        {

			if($('#select_category').val()=='' || $('#select_category').val()==null)

			{

				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Category'});

			}

			else if($('#select_purity').val()=='' || $('#select_purity').val()==null)

			{

				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Purity'});

			}

			else if($('#prod_select').val()=='' || $('#prod_select').val()==null)

			{

				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Product'});

			}

			else if($('#tag_karigar').val()=='' || $('#tag_karigar').val()==null)

			{

				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Karigar'});

			}



			else if($("input[name='bill_det_id[]']:checked").val())

			{

				$("#non_tag_list tbody tr").each(function(index, value)

				{

					if($(value).find("input[name='bill_det_id[]']:checked").is(":checked"))



					{



						transData = {



						'bill_det_id'          : $(value).find(".bill_det_id").val(),



						'piece'                : $(value).find(".nt_ret_pieces").val(),



						'gross_wt'             : $(value).find(".nt_ret_grswt").val(),



						'net_wt'                : $(value).find(".nt_ret_netwt").val(),

						'id_old_metal_type'        : $(value).find('.id_metal_type').val(),

						}



						selected.push(transData);



					}



				});



				req_data = selected;



				create_retag(req_data);



            }else



            {



                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Any One Tag'});



            }



        }

		else if($('#report_type').val()==7)

		{

			if($('#select_category').val()=='' || $('#select_category').val()==null)

			{

				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Category'});

			}

			else if($('#select_purity').val()=='' || $('#select_purity').val()==null)

			{

				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Purity'});

			}

			else if($('#prod_select').val()=='' || $('#prod_select').val()==null)

			{

				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Product'});

			}

			else if($('#tag_karigar').val()=='' || $('#tag_karigar').val()==null)

			{

				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Karigar'});

			}

			else if($("input[name='bt_trans_id[]']:checked").val())

			{

				$("#non_tag_otherissue_list tbody tr").each(function(index, value)

				{

					if($(value).find("input[name='bt_trans_id[]']:checked").is(":checked"))



					{



						transData = {



						'bt_trans_id'          : $(value).find(".bt_trans_id").val(),



						'piece'                : $(value).find(".nt_otriss_pieces").val(),



						'gross_wt'             : $(value).find(".nt_otriss_grswt").val(),



						'net_wt'                : $(value).find(".nt_otriss_netwt").val(),

						'id_old_metal_type'        : $(value).find('.id_metal_type').val(),


						}



						selected.push(transData);



					}



				});



				req_data = selected;



				create_retag(req_data);



            }else



            {



                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Any BT Code'});



            }



		}



    }



});



function create_retag(data="")



{



    $('#create_retag').prop('disabled',true);



    my_Date = new Date();



    $("div.overlay").css("display", "block");



    $.ajax({



    url:base_url+ "index.php/admin_ret_tagging/create_retag?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),



    data:  {'req_data':data,'id_branch':$("#id_branch").val(),'remark':$("#remark").val(),'tag_process':$('#tag_process').val(),'report_type':$('#report_type').val(),'id_product':$('#prod_select').val(),'id_section':$('#section_select').val(),'id_karigar':$('#tag_karigar').val(),'id_design':$('#des_select').val(),'id_sub_design':$('#sub_des_select').val(),'id_category':$('#select_category').val(),'id_purity':$('#select_purity').val(),'total_pcs':$('.total_pcs').val(),'total_gross_wt':$('.total_gross_wt').val(),'total_net_wt':$('.total_net_wt').val(),'total_dia_wt':$('.total_dia_wt').val(),'avg_purity_per':$('.avg_purity_per').val(),'total_item_purity':$('.total_item_purity').val(),'total_amount':$('.total_amount').val()},



    type:"POST",



    async:false,



    dataType: "json",



    success:function(data)



    {



        if(data.status)



        {



            /*if($('#report_type').val()==1 && $('#tag_process').val()==1)



            {



                window.open(base_url+'index.php/admin_ret_tagging/generate_retagqrcode/'+data.id_process,'_blank');



            }*/



            $.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>"+data.message});



			if($('#tag_process').val()==6) // Pocket

			{

				window.location.reload();

			}

			else

			{

				window.open(base_url+'index.php/admin_ret_lot/lot_inward/list');



			}



            // window.location.reload();



        }else



        {



            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message});



            $('#create_retag').prop('disabled',false);



        }



        $("div.overlay").css("display", "none");



    },



    error:function(error)



    {



        console.log(error);



        $("div.overlay").css("display", "none");



    }



    });



}



function get_category(id_metal="")



{



	$(".overlay").css('display','block');



	$('#select_category option').remove();



	$.ajax({



		type: 'POST',



		url: base_url + 'index.php/admin_ret_reports/active_category',



		dataType:'json',



		data: { 'id_metal': $('#metal').val()|| id_metal},



		success:function(data){



		  var id_category =  $('#select_category').val();



		   $.each(data, function (key, item) {



			   		$('#select_category,#category').append(



						$("<option></option>")



						  .attr("value", item.id_ret_category)



						  .text(item.name)



					);



			});



			$("#select_category,#category").select2({



			    placeholder: "Select Category",



			    allowClear: true



			});



			$("#select_category,#category").select2("val",(id_category!='' && id_category>0?id_category:''));



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



function get_category_product(cat_id="")



{



	$('#prod_select option').remove();



	$("div.overlay").css("display", "block");



	$.ajax({



	type: 'POST',



	url: base_url+'index.php/admin_ret_reports/get_ActiveProduct',



	dataType:'json',



	data: {



		'id_category' :$('#select_category').val() || cat_id



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



function get_stock_process_list()



{



    $("div.overlay").css("display", "block");



	my_Date = new Date();



	$.ajax({



		url: base_url+"index.php/admin_ret_tagging/retagging/process_list?nocache=" + my_Date.getUTCSeconds(),



		type:"POST",



		data:{'id_branch':$("#id_branch").val()},



		dataType: 'json',



		cache:false,



		success:function(data){



			 $("div.overlay").css("display","add_newstone");



			 var oTable = $('#process_list').DataTable();



			 oTable.clear().draw();



			 if (data!= null && data.length > 0)



			 {



				oTable = $('#process_list').dataTable({



						"bDestroy": true,



						"bInfo": true,



						"bFilter": true,



						"bSort": true,



						"order": [[ 0, "desc" ]],



						"aaData"  : data,



						"aoColumns": [



						{ "mDataProp": "id_process" },



						{ "mDataProp": "lot_no" },



						{ "mDataProp": "date_add" },



						{ "mDataProp": "branch_name" },



						{ "mDataProp": "type" },



						{ "mDataProp": "process_for" },



						{ "mDataProp": "karigar_name" },



						{ "mDataProp": "cat_name" },



						{ "mDataProp": "purity" },



						{ "mDataProp": "product_name" },



						{ "mDataProp": "design_name" },



						{ "mDataProp": "sub_design_name" },



						{ "mDataProp": "gross_wt" },



						{ "mDataProp": "less_wt" },



						{ "mDataProp": "net_wt" },



						{ "mDataProp": "emp_name" },





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



//ReTag



function load_uom(id) {



	console.log("uom_details",uom_details);



	var uom_list = "";



	$.each(uom_details, function (pkey, pitem) {



		uom_list += "<option value='"+pitem.uom_id+"'>"+pitem.uom_name+"</option>";



	});



	$('#'+id).append(uom_list);



}



//collection map



function get_ActiveCollection()



{



	$('#select_collection option').remove();



	$("div.overlay").css("display", "block");



	$.ajax({



	type: 'GET',



	url: base_url+'index.php/admin_ret_tagging/get_ActiveCollection',



	dataType:'json',



	success:function(data){



		var id =  $("#select_collection").val();



		$.each(data, function (key, item) {



		    $("#select_collection").append(



		    $("<option></option>")



		    .attr("value", item.id_collection)



		    .text(item.collection_name)



		    );



		});



		if($("#select_collection").length)



		{



		    $("#select_collection").select2("val",(id!='' && id>0?id:''));



		}



		}



	});



	$("div.overlay").css("display", "none");



}



function create_new_empty_collection_tag_map(data)



{



	var row = "";



       row += '<tr>'



			+'<td><input type="checkbox" class="tag_id" name="tag_id[]" value="'+data.tag_id+'">'+data.tag_id+'<input type="hidden" class="tag_id" value="'+data.tag_id+'"></td>'



			+'<td>'+data.tag_code+'</td>'



			+'<td>'+data.tag_lot_id+'</td>'



			+'<td>'+data.product_name+'<input type="hidden" class="id_product" value="'+data.lot_product+'"></td>'



			+'<td>'+data.design_name+'<input type="hidden" class="id_design" value="'+data.design_id+'" ></td>'



			+'<td>'+data.sub_design_name+'</td>'



			+'<td><input type="hidden" class="piece" value="'+data.piece+'">'+data.piece+'</td>'



			+'<td><input type="hidden" class="gross_wt" value="'+data.gross_wt+'">'+data.gross_wt+'</td>'



			+'<td><input type="hidden" class="net_wt" value="'+data.net_wt+'">'+data.net_wt+'</td>'



			+'<td><a href="#" onClick="remove_collection_mapping_row($(this).closest(\'tr\'));" class="btn btn-danger"><i class="fa fa-trash"></i></a></td>'



			+'</tr>';



	$('#tagging_list tbody').append(row);



	$('.est_tag_name').val('');



	calculate_collection_tag();



}



function remove_collection_mapping_row(curRow)



{



    curRow.remove();



    calculate_collection_tag();



}



function calculate_collection_tag()



{



    var total_pcs=0;



    var total_gwt=0;



    var total_nwt=0;



    $("#tagging_list tbody tr").each(function(index, value)



    {



        curRow = $(this);



        total_pcs+=parseFloat(curRow.find('.piece').val());



        total_gwt+=parseFloat(curRow.find('.gross_wt').val());



        total_nwt+=parseFloat(curRow.find('.net_wt').val());



    });



    $('.tot_pcs').html(total_pcs);



    $('.tot_gwt').html(parseFloat(total_gwt).toFixed(3));



    $('.tot_nwt').html(parseFloat(total_nwt).toFixed(3));



}



$("#tag_collection_link").on('click',function(){



    if($("input[name='tag_id[]']:checked").val())



    {



           var allow_submit=true;



           if($('#select_collection').val()=='' || $('#select_collection').val()==null)



           {



               $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Collection!'});



               allow_submit=false;



           }



            if(allow_submit)



            {



                $('#tag_collection_link').prop('disabled',true);



                var selected = [];



                var approve=false;



                $("#tagging_list tbody tr").each(function(index, value)



                {



                if($(value).find("input[name='tag_id[]']:checked").is(":checked"))



                {



                    transData = {



                    'tag_id'            : $(value).find(".tag_id").val(),



                }



                selected.push(transData);



                }



                })



                req_data = selected;



                console.log(req_data);



                create_tag_collection(req_data);



            }else{



                $('#tag_collection_link').prop('disabled',false);



            }



    }



    else



    {



        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Any One Tag!'});



    }



});



function create_tag_collection(data="")



{



    my_Date = new Date();



    $("div.overlay").css("display", "block");



    $.ajax({



    url:base_url+ "index.php/admin_ret_tagging/create_tag_collection?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),



    data:  {'req_data':data,'total_pcs':$('.tot_pcs').html(),'tot_gwt':$('.tot_gwt').html(),'tot_nwt':$('.tot_nwt').html(),'id_collection':$('#select_collection').val()},



    type:"POST",



    async:false,



    dataType: "json",



    success:function(data){



        $('#tag_collection_link').prop('disabled',false);



        if(data.status)



        {



            $.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>"+data.message});



        }



        else



        {



            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message});



        }



        window.location.href= base_url+'index.php/admin_ret_tagging/collection_mapping/list';



    $("div.overlay").css("display", "none");



    },



    error:function(error)



    {



    console.log(error);



    $("div.overlay").css("display", "none");



    }



    });



}



function set_collection_mapping_list()



{



    $("div.overlay").css("display", "block");



	my_Date = new Date();



	$.ajax({



		url: base_url+"index.php/admin_ret_tagging/collection_mapping/ajax?nocache=" + my_Date.getUTCSeconds(),



		type:"POST",



		data:{'id_branch':$("#id_branch").val()},



		dataType: 'json',



		cache:false,



		success:function(data){



			var list=data.list;



			var access=data.access;



			 $("div.overlay").css("display","add_newstone");



			 var oTable = $('#mapping_list').DataTable();



			 oTable.clear().draw();



			 if (list!= null && list.length > 0)



			 {



				oTable = $('#mapping_list').dataTable({



						"bDestroy": true,



						"bInfo": true,



						"bFilter": true,



						"bSort": true,



						"order": [[ 0, "desc" ]],



						"aaData"  : list,



						"aoColumns": [



    						{ "mDataProp": "id_tag_mapping" },



    						{ "mDataProp": "date_add" },



    						{ "mDataProp": "ref_no" },



    						{ "mDataProp": "total_pcs" },



    						{ "mDataProp": "total_gwt" },



    						{ "mDataProp": "total_nwt" },



    						{



                            "mDataProp": null,



                            "sClass": "control center",



                            "sDefaultContent":  '<span class="drill-val"><i class="fa fa-chevron-circle-down text-teal"></i></span>'



                            },



    						{ "mDataProp": "coll_status" },



    						{ "mDataProp": "bill_no" },



        					{ "mDataProp": function ( row, type, val, meta ) {



                            id= row.id_tag_mapping;



                            delete_url=(access.delete=='1'  ? base_url+'index.php/admin_ret_tagging/collection_mapping/cancel/'+id : '#' );



                            action_content=(access.edit==1 && row.status==0 ? '<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-close"></i></a>' :'-');



                            return action_content;



                            }



                            }



						]



					});



					var anOpen =[];



            		$(document).on('click',"#mapping_list .control", function(){



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



				}



				$("div.overlay").css("display", "none");



		},



		 error:function(error)



		  {



			 $("div.overlay").css("display", "none");



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



        '<th>TAG CODE</th>'+



        '<th>OLD TAG CODE</th>'+



        '<th>PRODUCT </th>'+



        '<th>DESIGN </th>'+



        '<th>SUB DESIGN </th>'+



        '<th>PCS</th>'+



        '<th>GWT</th>'+



        '<th>NWT</th>'+



        '</tr>';



    var tag_details = oData.tag_details;



    var total_amount=0;



  $.each(tag_details, function (idx, val) {



  	prodTable +=



        '<tr class="prod_det_btn">'+



        '<td>'+parseFloat(idx+1)+'</td>'+



        '<td>'+val.tag_code+'</td>'+



        '<td>'+val.old_tag+'</td>'+



        '<td>'+val.product_name+'</td>'+



        '<td>'+val.design_name+'</td>'+



        '<td>'+val.sub_design_name+'</td>'+



        '<td>'+val.piece+'</td>'+



		'<td>'+val.gross_wt+'</td>'+



        '<td>'+val.net_wt+'</td>'+



        '</tr>';



    total_amount +=parseFloat(val.item_cost);



  });



  rowDetail = prodTable+'</table></div>';



  return rowDetail;



}



function confirm_cancel(id_tag_mapping)



{



	$('#receipt_bill_id').val(bill_id);



	$('#confirm-delete').modal('show');



}



//collection map



$('#tag_unlink').on('keyup', function(e){



    if(this.value.length>4)



    {



        set_tagging_unlink_list(this.value);



    }



});



$('#old_tag_unlink').on('keyup', function(e){



    if(this.value.length>4)



    {



        set_tagging_unlink_list('',this.value);



    }



});



function set_tagging_unlink_list(searchTxt,old_tag_id)



{



    my_Date = new Date();



	$("div.overlay").css("display", "block");



	$.ajax({



		 url:base_url+"index.php/admin_ret_tagging/get_order_linked_tags?nocache=" + my_Date.getUTCSeconds(),



		 data: {'searchTxt': searchTxt,'old_tag_id': old_tag_id,'id_branch': $("#branch_select").val(),},



		 dataType:"JSON",



		 type:"POST",



		 success:function(data){



			$("div.overlay").css("display","add_newstone");



			var oTable = $('#tagging_unlink_list').DataTable();



			oTable.clear().draw();



			if (data!= null && data.length > 0)



			{



			   oTable = $('#tagging_unlink_list').dataTable({



					   "bDestroy": true,



					   "bInfo": true,



					   "bFilter": true,



					   "bSort": true,



					   "order": [[ 0, "desc" ]],



					   "aaData"  : data,



					   "aoColumns": [



					  { "mDataProp": function ( row, type, val, meta ){



						   chekbox='<input type="checkbox" class="order_no_unlink" name="tag_id[]" value="'+row.order_no+'"/><input type="hidden" class="id_cus_order_unlink" value="'+row.id_orderdetails+'"><input type="hidden" class="tag_id_unlink" value="'+row.tag_id+'">'



						   return chekbox;



					   }},



					   { "mDataProp": "tag_code" },



					   { "mDataProp": "old_tag_id" },



					   { "mDataProp": "tag_lot_id" },



					   { "mDataProp": "product_name" },



					   { "mDataProp": "design_name" },



					   { "mDataProp": "sub_design_name" },



					   { "mDataProp": "gross_wt" },



					   { "mDataProp": "net_wt" },



					   { "mDataProp": "order_no" },



					   { "mDataProp": "weight" },



					   { "mDataProp": "order_status" }



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



$('#select_all_unlink').click(function(e) {



	var table = $(e.target).closest('table');



    $('td:first-child input:checkbox', table).prop('checked', this.checked);



});



$(document).on('change',".order_no_unlink",function(){



    if($(this).not(":checked"))



    {



      $('#select_all_unlink').prop('checked', false);



    }



});



$("#tag_unlink_submit").on('click',function(){

	if($('#order_unlink_otp').val()==1){



		$('#confirm-orderUnlink').modal('show');

	    $('.cancel_otp').css("display","none");

	    $('.order_remarks').css("display","none");

	    $('.cancel_otp_confirmation').css("display","block");

	    $('.verify_otp').css("display","none");



	}else{

		tag_unlink();

	}

 });



 //TAG IMAGE



    function take_snapshot(type)



    {



    	//Snap Shots Disables



    	  $('#snap_shots').prop('disabled',true);



    		if(type == 'pre_images'){



    			preview = 'uploadArea_p_stn';



    		}



            Webcam.snap( function(data_uri) {



               $(".image-tag").val(data_uri);



    			pre_img_resource.push({'src':data_uri,'name':(Math.floor(100000 + Math.random() * 900000))+'jpg','is_default':"0"});



    			pre_img_files.push(data_uri);



    			alert("Your Webcam Images Take Snap Shot Successfullys.");



            } );



    		if(pre_img_resource.length > 0)



    		{



    			$("#image_lot_list").css('display','block');



    			$("#lot_images_count").text(pre_img_resource.length);



    		}



    		else



    		{



    			$("#image_lot_list").css('display','none');



    			$("#lot_images_count").text('0');



    		}



    	setTimeout(function(){



    		var resource = [];



    		$('#'+preview+' div').remove();



    		if(type == 'pre_images'){



    			resource = pre_img_resource;



    		}



    		$.each(resource,function(key,item){



    			if(item){



    			var div = document.createElement("div");



    			div.setAttribute('class','images');



    			div.setAttribute('id',+key);



    			param = {"key":key,"preview":preview,"stone_type":type};



    			div.innerHTML+= "<span style='float:left;'><a onclick='remove_stn_img("+JSON.stringify(param)+")' style='cursor:pointer;color:inherit;' data-toggle='tooltip' data-placement='bottom' title='Delete This Images'>&nbsp;<i class='fa fa-trash'></i></a></span>&nbsp;&nbsp;<label style='display:none;'>Is Default</label><span><input type='checkbox' class='tag_default_"+key+"'  value='0' onchange='default_stn_img("+JSON.stringify(param)+",event)' data-toggle='tooltip' data-placement='bottom' title='Click Here To Set Default Image' style='float:right;margin-right:20px;'></span><img class='thumbnail' src='" + item.src + "'" +				"style='width: 100px;height: 100px;'/>";



    			$('#'+preview).append(div);		   	}



    			$('#lot_img_upload').css('display','');



    		});



    		$('#snap_shots').prop('disabled',false);



    		var default_keyimage  = typeof



    		localStorage.getItem("key") !=='undefined' ? localStorage.getItem("key"):'0';



    		if(default_keyimage){



    		$(".tag_default_"+default_keyimage).prop('checked', true);



    		 $(".tag_default_"+default_keyimage).val('1');



    		 localStorage.setItem("key",default_keyimage);



    		}



    		else



    		{



    		$(".tag_default_0").prop('checked', true);



    		$(".tag_default_0").val('1');



    		localStorage.setItem("key",'0');



    	}



    	},1000);



    }



     function image_preview_validaion(type)



     {



    	 if(type == 'pre_images'){



    		preview = 'uploadArea_p_stn';



    	}



    	if(pre_img_resource.length > 0)



    		{



    			$("#image_lot_list").css('display','block');



    		}



    		else



    		{



    			$("#image_lot_list").css('display','none');



    		}



    	 setTimeout(function(){



    		var resource = [];



    		$('#'+preview+' div').remove();



    		if(type == 'pre_images'){



    			resource = pre_img_resource;



    		}



    		$.each(resource,function(key,item){



    		   if(item)



    		   {



    		   		var div = document.createElement("div");



    				div.setAttribute('class','images');



    				div.setAttribute('id',+key);



    				param = {"key":key,"preview":preview,"stone_type":type};



    				div.innerHTML+= "<span style='float:left;'><a onclick='remove_stn_img("+JSON.stringify(param)+")' style='cursor:pointer;color:inherit;' data-toggle='tooltip' data-placement='bottom' title='Delete This Images'>&nbsp;<i class='fa fa-trash'></i></a></span>&nbsp;&nbsp;<label style='display:none;'>Is Default</label><span><input type='checkbox' class='tag_default_"+key+"'  value='0' onchange='default_stn_img("+JSON.stringify(param)+",event)' data-toggle='tooltip' data-placement='bottom' title='Click Here To Set Default Image' style='float:right;margin-right:20px;'></span><img class='thumbnail' src='" + item.src + "'" +				"style='width: 100px;height: 100px;'/>";



    				$('#'+preview).append(div);



    		   }



    		});



    		  var catRow              = $('#custom_active_id').val();



    	      var default_keyimage    = $('#tag_img_default').val();



    		   if(default_keyimage)



    		   {



    			 $(".tag_default_"+default_keyimage).prop('checked', true);



    			 $(".tag_default_"+default_keyimage).val('1');



    			 localStorage.setItem("key",default_keyimage);



    		   }



    		   else



    		   {



    			 $(".tag_default_0").prop('checked', true);



    			 $(".tag_default_0").val('1');



    			 localStorage.setItem("key",'0');



    		   }



    	},100);



     }



    function update_image_upload(curRow,id)



    {



    	// Check Validations



    	pre_img_resource   = [];



    	pre_img_files      = [];



    	if($('#tag_images').val() != '')



    	{



    		var pre_images    = JSON.parse($('#tag_images').val());



    		pre_img_resource  = pre_images;



    		image_preview_validaion('pre_images');



    	}



    	else



    	{



    		image_preview_validaion('pre_images');



    	}



    	$('#imageModal').modal('show');



    	// Image Key Storage Validations Remove Local Storage



    	localStorage.removeItem("key");



		$('#bulktag_images').trigger('click');



    }



    $("#pre_images").on('change',function(){



    	validateCertifImg(this.id);



    });



    function validateCertifImg(type)



    {



             if(type == 'pre_images'){



            	preview = 'uploadArea_p_stn';



            }



            var files = event.target.files;



            var html_1="";



             for (var i = 0; i < files.length; i++)



             {



            	var file = files[i];



            	if(file.size> 1048576)



            	 {



            		  alert('File size cannot be greater than 1 MB');



            		  files[i] = "";



            		  return false;



            	 }



            	 else



            	 {



            		 var fileName =file.name;



            		var ext = fileName.substring(fileName.lastIndexOf('.') + 1);



            		ext = ext.toLowerCase();



            		if(ext != "jpg" && ext != "png" && ext != "jpeg")



            		{



            			alert("Upload JPG or PNG Images only");



            			files[i] = "";



            		}



            		else



            		{



            			var reader = new FileReader();



            			reader.onload = function (event) {



            				if(type == 'pre_images'){



            					pre_img_resource.push({'src':event.target.result,'name':fileName});



            					pre_img_files.push(file);



            				}



            			}



            			if (file)



            			{



            				reader.readAsDataURL(file);



            			}



            			/*else



            			{



            				preview.prop('src','');



            			}*/



            		}



            	 }



            }



            setTimeout(function(){



            	var resource = [];



            	$('#'+preview+' div').remove();



            	if(type == 'pre_images'){



            		resource = pre_img_resource;



            	}



            	$.each(resource,function(key,item){



            	   if(item)



            	   {



            			   var div = document.createElement("div");



            			div.setAttribute('class','col-md-4');



            			div.setAttribute('id',+key);



            			param = {"key":key,"preview":preview,"stone_type":type};



            			//div.innerHTML+= "<a onclick='remove_stn_img('"+key+"','"+preview+"','"+type+"')'><i class='fa fa-trash'></i>Delete</a><img class='thumbnail' src='" + item.src + "'" +



            			div.innerHTML+= "<a onclick='remove_stn_img("+JSON.stringify(param)+")'><i class='fa fa-trash'></i>Delete</a><img class='thumbnail' src='" + item.src + "'" +



            			"style='width: 100px;height: 100px;'/>";



            			$('#'+preview).append(div);



            	   }



            	   $('#lot_img_upload').css('display','');



            	});



            },1000);



    }



 function default_stn_img(param,event)



 {



	 var current_status       = event.target.checked;



	 var parameter            = param.key;



	 $('#uploadArea_p_stn div').each(function (index,value) {



		var image_class      = $(this).find('span input').attr('class');



		 var image_class_key  = 'tag_default_'+parameter;



		 if(image_class == image_class_key)



		 {



			 $("."+image_class).prop('checked', true);



			 $("."+image_class).val('1');



			  localStorage.setItem("key", parameter);



		 }



		 else{



			$("."+image_class).prop('checked', false);



			$("."+image_class).val('0');



		}



	});



 }



 function remove_stn_img(param)



 {



     var current_status   = $(".tag_default_"+param.key).is(':checked');



 	 $('#'+param.preview+' #'+param.key).remove();



 	 if(pre_img_resource.length == 1){



	 	  		pre_img_resource    =  [];



	 	  		$("#lot_images_count").text(pre_img_resource.length);



	 	  		if(ctrl_page[2] == 'edit'){



	 	  			remove_img(file,'certificates','precious_st_certif',id,imgs);



	 	  		}



 	  		}



	else{



		if(param.stone_type == 'pre_images'){



			pre_img_resource.splice(param.key,1);



			$("#lot_images_count").text(pre_img_resource.length);



			if(ctrl_page[2] == 'edit'){



				remove_img(file,'certificates','precious_st_certif',id,imgs);



			}



		}



	}



	if(current_status  ==  true) {



		var image_class_first = $('#uploadArea_p_stn div').find('span input').attr('class');



		$("."+image_class_first).prop('checked', true);



		$("."+image_class_first).val('1');



		var image_class_key  = image_class_first.split('_');



		localStorage.setItem("key", image_class_key[2]);



	}



 }



    $('#imageModal  #update_img').on('click', function(){



        	var set_inddefault_keyimage   = typeof	 localStorage.getItem("key") !=='undefined' ? localStorage.getItem("key"):'0';



        	$('#imageModal').modal('toggle');



        	var copyrow_validation       = $('#tag_img_copy').val();



        	if(copyrow_validation == '1')	{



        		$('#tag_img_copy').val('2');



        	}



        	$('#tag_img').attr("data-img",encodeURIComponent(JSON.stringify(pre_img_resource)));



        	$('#tag_images').val((JSON.stringify(pre_img_resource)));



        	$('#tag_img_url').val(encodeURIComponent(JSON.stringify(pre_img_resource)));



        	$('#tag_img_default').val(set_inddefault_keyimage);



        	var get_default_image  = pre_img_resource[set_inddefault_keyimage];



        	if(Object.keys(get_default_image).length>0)	{



        		if(get_default_image.src!=""){



        			$('#tagging_set_images').attr("src",get_default_image.src);



        		}



        	}



        	else{



        		var type      =  base_url+'assets/img/no_image.png';



        		$('#tagging_set_images').attr("src",type);



        	}



    });



 function remove_tag_img(param)



 {



 		$('#'+param.key).remove();



		pre_img_resource.splice(param.key,1);



 }



//TAG IMAGE



$(document).on('change','#remarks',function(){



    var old_tag_row=$("#remarks").val();



    old_tag_id(old_tag_row);



});



function old_tag_id(old_tag_id)



{



    my_Date = new Date();



    $.ajax({



        url: base_url+"index.php/admin_ret_tagging/get_old_tag?nocache=" + my_Date.getUTCSeconds(),



        type:"POST",



        data:{'old_tag_id':old_tag_id},



        dataType:"JSON",



        cache:false,



        success:function(data)



        {



             let old_tag_id_values=data;



             if(old_tag_id_values.length>0){



                $.toaster({priority : 'danger',title:'warning!',message:''+"</br>"+'Old Tag Id exists...'});



                $("#remarks").focus();



                $("#remarks").val('');



            }



        }



    })



}



function get_ActiveSections(id_branch)



{



	$("#section_select option").remove();



    my_Date = new Date();



    $.ajax({



        type: 'POST',



        url: base_url+"index.php/admin_ret_catalog/get_sectionBranchwise?nocache=" + my_Date.getUTCSeconds(),



		data:{'id_branch':id_branch, 'status' : 1},



        dataType:'json',



        success:function(data){


			$("#section_select option").remove(); // Added here because previous sections are not clearing because of simultaneous requests


            var id=$("#id_section").val();



            $.each(data,function(key, item){



                $("#section_select").append(



                    $("<option></option>")



                    .attr("value",item.id_section)



                    .text(item.section_name+'-'+item.short_code)



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





function get_tag_Sections()



{



	$("#section_select option").remove();



    my_Date = new Date();



    $.ajax({



        type: 'POST',



        url: base_url+"index.php/admin_ret_tagging/get_section_details?nocache=" + my_Date.getUTCSeconds(),



        dataType:'json',



        success:function(data){



            var id=$("#id_section").val();



            $.each(data,function(key, item){



                $("#section_select").append(



                    $("<option></option>")



                    .attr("value",item.id_section)



                    .text(item.section_name+'-'+item.short_code)



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

//Bulk Tag



$("#addTagToPreviewAndCopy").on('click',function(e){



	if(validateTagDetail()){



		var no_of_rows = $('#bulk_tag').val();



		if(no_of_rows!='' && no_of_rows!=1 )



		{



		$("#addTagToPreviewAndCopy").css('display',"inline");



		$("#updateTagInPreview").css('display',"none");



		createTag_bulk();



		modalStoneDetail = [];



	    }else{



			$.toaster({ priority : 'danger', title : 'Add Tag!', message : ''+"</br>"+'Please Enter The Number of Rows..'});



		}



	}



});



function createTag_bulk(){



    var postData = {};



    var my_Date = new Date();



	charges_value=0;



				$(modalChargeDetail).each(function(idx, row) {



					charges_value += parseFloat(row.charge_value);



				});



                var images = $('#tag_img_url').val();



                postData = {



					        'bulk_tag'              : $('#bulk_tag').val(),



                            'purity'                : { 0 : $('#id_purity').val() },



                            'id_branch'             : $('#branch_select').val(),



                            'to_branch'             : $('#current_branch').val(),



                            'purity'                : { 0 : $('#id_purity').val() },



                            'lot_no'                : { 0 : $('#tag_lot_received_id').val()},



                            'id_lot_inward_detail'  : { 0 : $('#tag_id_lot_inward_detail').val()},



                            'lot_product'           : { 0 : $('#tag_lt_prod').val()},



                            'lot_id_design'         : { 0 : $('#des_select').val()},



                            'lot_id_sub_design'     : { 0 : $('#sub_des_select').val()},



                            'design_for'            : { 0 : $('#tag_design_for').val()},



                            'size'                  : { 0 : $('#tag_size').val()},



                            'no_of_piece'           : { 0 : $('#tag_pcs').val()},



                            'gross_wt'              : { 0 : $('#tag_gwt').val()},



                            'less_wt'               : { 0 : $('#tag_lwt').val()},



                            'net_wt'                : { 0 : $('#tag_nwt').val()},



                            'calculation_based_on'  : { 0 : $('#calculation_based_on').val()},



                            'wastage_percentage'    : { 0 : $('#tag_wast_perc').val()},



                            'id_mc_type'            : { 0 : $('#tag_id_mc_type').val()},



                            'making_charge'         : { 0 : $('#tag_mc_value').val()},



                            'sell_rate'             : { 0 : $('#tag_sell_rate').val()},



                            'sale_value'            : { 0 : $('#tag_sale_value').val()},



                            'product_short_code'    : { 0 : $('#tag_product_short_code').val()},



                            'id_metal'              : { 0 : $('#id_metal').val()},



                            'tax_group_id'          : { 0 : $('#tax_group_id').val()},



                            'tag_sales_mode'        : { 0 : $('#tag_sales_mode').val()},



                            'tag_tax_type'          : { 0 : $('#tag_tax_type').val()},



                            'charges_value'         : { 0 : charges_value},



                            'huid'                  : { 0 : $('#tag_huid').val()},



							'huid2'                 : { 0 : $('#tag_huid2').val()},



							'cert_no'				: { 0 : $('#cert_no').val()},



							'cert_image'			: { 0 :$('#cert_img_base64').val()},



                            'adjusted_item_rate'    : { 0 : 0},



                            'charges'               : { 0 : JSON.stringify(modalChargeDetail)},



							'othermetals'           : { 0 : JSON.stringify(modalOtherMetalDetail)},



                            'tag_img'               : { 0 : []},



                            'stone_details'         : { 0 : $('#tag_stone_details').val()},



                            'stone_price'           : { 0 : 0},



                            'normal_st_certif'      : { 0 : 0},



                            'semiprecious_st_certif': { 0 : 0},



                            'precious_st_certif'    : { 0 : 0},



							'attributes'            : { 0 : JSON.stringify(modalAttributeDetail)},



							'manufacture_code'      : { 0 : $('#manufacture_code').val()},



							'style_code'            : { 0 : $('#style_code').val()},



							'remarks'            	: { 0 : $('#remarks').val()},



							'narration'            	: { 0 : $('#narration').val()},



							'tag_purchase_cost'     : { 0 : $('#tag_purchase_cost').val()},


							'purchase_touch'     : { 0 : $('#purchase_touch').val()},



							'tag_product_division'  : { 0 : $('#tag_product_division').val()},



							'is_suspense_stock'     : $('.issuspensestock').val(),



							'gwt_uom_id'     		: { 0 : $('#gwt_uom_id').val()},



							'tag_cat_type'     		: { 0 : $('#tag_cat_type').val()},



							'stone_calculation_based_on' : { 0 : $('#stone_calculation_based_on').val()},



							'tag_img'     			: { 0 : images},



                        	'tag_img_copy'     		: { 0 : $("#tag_img_copy").val()},



                        	'tag_img_default'     	: { 0 : $("#tag_img_default").val()},



                        	'tag_section'           : { 0 : $("#section_select").val()},



							'tag_purchase_cost'     : { 0 : $("#tag_purchase_cost").val()},

							'purchase_touch'     : { 0 : $("#purchase_touch").val()},



							'lot_rate'            : {0 : $('#tag_lt_prod option:selected').attr('data-rate')},



							'lot_calc_type'            : {0 : $('#tag_lt_prod option:selected').attr('data-calc_type')},



							'lot_rate_calc_type'      : {0 : $('#tag_lt_prod option:selected').attr('data-rate_calc_type')},



							'lot_purchase_touch'            : {0 : $('#tag_lt_prod option:selected').attr('data-touch')},



							'lot_wastage_percentage'            : {0 : $('#tag_lt_prod option:selected').attr('data-wastage_percentage')},



							'lot_mc_type'            : {0 : $('#tag_lt_prod option:selected').attr('data-mc_type')},



							'lot_making_charge'            : {0 : $('#tag_lt_prod option:selected').attr('data-making_charge')},



                           }



                $(".overlay").css('display','block');



            	$.ajax({



            		url: base_url+'index.php/admin_ret_tagging/tagging/save_bulk_tag/?nocache=' + my_Date.getUTCSeconds(),



            		dataType: "json",



            		method: "POST",



            		data: { 'lt_item': postData },



            		success: function ( data ) {



            		    	//console.log(data);



							if(data.status){



							let tag_ids = []



							$.each(data.tagging,function(key,items){



								let tag_id = items.tag_id



								if(tag_id > 0) {



									tag_ids.push(tag_id);



								}



							});



							if(tag_ids.length > 0) {



								let tag_ids_string = tag_ids.join(',');



								window.open(base_url+'index.php/admin_ret_tagging/generate_tagqrcode_bulk/'+encodeURIComponent(tag_ids_string)+'?nocache=' + my_Date.getUTCSeconds(), '_blank');



							}



							set_bulk_tag_preview(data.tagging);



            			    $("#tag_id").val("");



            			    $("#tag_saved").val("");



            			    $.toaster({ priority : 'success', title : 'Success!', message : data.message});



            			    modalStoneDetail = [];



							modalOtherMetalDetail = [];



							display_othermetals_details();



            			    $("#tag_lot_received_id").focus();



            			    checking_lot_availability();



            			    get_lot_inwards_detail($('#tag_lot_received_id').val(),$('#tag_lt_prod').val(),'');



            			    $('#stone-det tbody').empty();



							get_productCharges();



							get_attributes_from_subdesign();



							$('#tag_gwt').focus();



						}



            		}



            	});



            }



function set_bulk_tag_preview(tag_details)



{



	var row = "";



	var total_row = $('#lt_item_tag_preview tbody > tr').length;



	var charges_value = 0;



	$(modalChargeDetail).each(function(idx, row) {



		charges_value += parseFloat(row.charge_value);



	});



	var chargesPostRow = "<input type='hidden' class='charges' name='lt_item[charges][]' value='"+JSON.stringify(modalChargeDetail)+"' />";



	console.log("chargesPostRow",chargesPostRow);



	var attrsPostRow = "<input type='hidden' class='tag_attributes' name='lt_item[attrs][]' value='"+JSON.stringify(modalAttributeDetail)+"' />";



	console.log("attrsPostRow",attrsPostRow);



	var othermetals = "<input type='hidden' class='othermetals' name='lt_item[othermetals][]' value='"+JSON.stringify(modalOtherMetalDetail)+"' />";



	console.log("othermetals",othermetals);



	var images       = $('#tag_img_url').val();



	var img=decodeURIComponent(images);



	var image_preview =  base_url+'assets/img/no_image.png';



	if(img!='')



	{



	    var image_details = JSON.parse(img);



        console.log(image_details);



    	if(image_details.length>0)



    	{



    	    $.each(image_details,function(k,i){



    	       if($('#tag_img_default').val()==k)



    	       {



    	           image_preview=i.src;



    	       }



    	    });



    	}



	}



	if($('#tag_images').val()!='')



	{



	    var tagged_images = JSON.parse($('#tag_images').val());



	}else



	{



	    tagged_images=[];



	}



	var tag_pre_img_resource=[];



	$.each(tagged_images,function(k,i){



		tag_pre_img_resource.push({'src':i.src,'name':(Math.floor(100000 + Math.random() * 900000))+'jpg','is_default':i.is_default});



	});



    $.each(tag_details,function(key,items){



                     row = "";



                	 total_row = $('#lt_item_tag_preview tbody > tr').length;



					 row +='<tr id='+$('#tag_id_lot_inward_detail').val()+' class='+(total_row+1)+'>'



					 +'<td>'



						 +$('#tag_lot_received_id').val()



						 +'<input type="hidden" class="tag_saved" name="lt_item[tag_saved][]" value="1">'



						 +'<input type="hidden" class="tag_id" name="lt_item[tag_id][]" value="'+items.tag_id+'">'



						 +'<input type="hidden" class="lot_no" name="lt_item[lot_no][]" value="'+$('#tag_lot_received_id').val()+'">'



						 +'<input type="hidden" class="id_lot_inward_detail" name="lt_item[id_lot_inward_detail][]" value="'+$('#tag_id_lot_inward_detail').val()+'">'



						 +'<input type="hidden" class="lot_product" name="lt_item[lot_product][]" value="'+$('#tag_lt_prod').val()+'">'



						 +'<input type="hidden" class="lot_id_design" name="lt_item[lot_id_design][]" value="'+$('#des_select').val()+'">'



						 +'<input type="hidden" class="lot_id_sub_design" name="lt_item[lot_id_sub_design][]" value="'+$('#sub_des_select').val()+'">'



						 +'<input type="hidden" class="design_for" name="lt_item[design_for][]" value="'+$('#tag_design_for').val()+'">'



						 +'<input type="hidden" class="purity" name="lt_item[purity][]" value="'+$('#id_purity').val()+'">'



						 +'<input type="hidden" class="size" name="lt_item[size][]" value="'+$('#tag_size').val()+'">'



						 +'<input type="hidden" class="no_of_piece" name="lt_item[no_of_piece][]" value="'+$('#tag_pcs').val()+'">'



						 +'<input type="hidden" class="gross_wt" name="lt_item[gross_wt][]" value="'+$('#tag_gwt').val()+'">'



						 +'<input type="hidden" class="less_wt" name="lt_item[less_wt][]" value="'+$('#tag_lwt').val()+'">'



						 +'<input type="hidden" class="net_wt" name="lt_item[net_wt][]" value="'+$('#tag_nwt').val()+'">'



						 +'<input type="hidden" class="purchase_touch" name="lt_item[purchase_touch][]" value="'+$('#purchase_touch').val()+'">'



						 +'<input type="hidden" class="calculation_based_on" name="lt_item[calculation_based_on][]" value="'+$('#calculation_based_on').val()+'">'



						 +'<input type="hidden" class="wastage_percentage" name="lt_item[wastage_percentage][]" value="'+$('#tag_wast_perc').val()+'">'



						 +'<input type="hidden" class="id_mc_type" name="lt_item[id_mc_type][]" value="'+$('#tag_id_mc_type').val()+'">'



						 +'<input type="hidden" class="making_charge" name="lt_item[making_charge][]" value="'+$('#tag_mc_value').val()+'">'



						 +'<input type="hidden" class="sell_rate" name="lt_item[sell_rate][]" value="'+$('#tag_sell_rate').val()+'">'



						 +'<input type="hidden" class="sale_value" name="lt_item[sale_value][]" value="'+$('#tag_sale_value').val()+'">'



						 +'<input type="hidden" class="tag_product_short_code" name="lt_item[product_short_code][]" value="'+$('#tag_product_short_code').val()+'">'



						 +'<input type="hidden" class="id_metal" name="lt_item[id_metal][]" value="'+$('#id_metal').val()+'">'



						 +'<input type="hidden" class="tax_group_id" name="lt_item[tax_group_id][]" value="'+$('#tax_group_id').val()+'">'



						 +'<input type="hidden" class="tag_sales_mode" name="lt_item[tag_sales_mode][]" value="'+$('#tag_sales_mode').val()+'">'



						 +'<input type="hidden" class="tag_tax_type" name="lt_item[tag_tax_type][]" value="'+$('#tag_tax_type').val()+'">'



						 +'<input type="hidden" class="charges_value" name="lt_item[charges_value][]" value="'+charges_value+'">'



						 +'<input type="hidden" class="huid" name="lt_item[huid][]" value="'+$('#tag_huid').val()+'">'



						 +'<input type="hidden" class="huid2" name="lt_item[huid2][]" value="'+$('#tag_huid2').val()+'">'



						 +'<input type="hidden" class="cert_no" name="lt_item[huid2][]" value="'+$('#cert_no').val()+'">'



						 +'<input type="hidden" class="cert_image" name="lt_item[cert_image][]" value="'+$("#cert_img_base64").val()+'">'



						 +'<input type="hidden" class="stone_details" name="lt_item[stone_details][]" value=\''+$('#tag_stone_details').val()+'\'><input type="hidden" class="stone_price" name="lt_item[stone_price][]"><input type="hidden" class="normal_st_certif" value=""><input type="hidden" class="semiprecious_st_certif" value=""><input type="hidden" class="precious_st_certif" value="">'



						 +'<input type="hidden" class="manufacture_code" name="lt_item[manufacture_code][]" value="'+$('#manufacture_code').val()+'">'



						 +'<input type="hidden" class="style_code" name="lt_item[style_code][]" value="'+$('#style_code').val()+'">'



						 +'<input type="hidden" class="narration" name="lt_item[narration][]" value="'+$('#narration').val()+'">'



						 +'<input type="hidden" class="remarks" name="lt_item[remarks][]" value="'+$('#remarks').val()+'"><input type="hidden" class="is_suspense_stock" name="lt_item[is_suspense_stock][]" value="'+$('.issuspensestock').val()+'">'



						 +'<input type="hidden" class="tag_purchase_cost" name="lt_item[tag_purchase_cost][]" value="'+$('#tag_purchase_cost').val()+'">'



						 +'<input type="hidden" class="tag_product_division" name="lt_item[tag_product_division][]" value="'+$('#tag_product_division').val()+'">'



						 +chargesPostRow



						 +attrsPostRow



						 +othermetals



						 +'<input type="hidden" class="gwt_uom_id" name="lt_item[gwt_uom_id][]" value="'+$('#gwt_uom_id').val()+'">'



						 +'<input type="hidden" class="tag_cat_type" name="lt_item[tag_cat_type][]" value="'+$('#tag_cat_type').val()+'">'



						 +'<input type="hidden" class="stone_calculation_based_on" name="lt_item[stone_calculation_based_on][]" value="'+$('#stone_calculation_based_on').val()+'">'



						 +'<input type="hidden" class="tag_img" value="'+$('#tag_img_url').val()+'" name="lt_item[tag_img][]">'



						 +'<input type="hidden" class="tag_img_copy" name="lt_item[tag_img_copy][]" value="'+$('#tag_img_copy').val()+'">'



						 +'<input type="hidden" class="tag_img_default" value="'+$('#tag_img_default').val()+'" name="lt_item[tag_img_default][]"/>'



						 +'<input type="hidden" class="min_mc" value="'+$('#min_mc').val()+'">'



						 +'<input type="hidden" class="min_va" value="'+$('#min_va').val()+'">'



					 +'</td>'



					 +'<td><span class="tag_code">'+items.tag_code+'</span></td>'



					 +'<td>'+$("#tag_lt_prod option:selected").text()+'</td>'



					 +'<td>'+$("#des_select option:selected").text()+'</td>'



					 +'<td>'+$("#sub_des_select option:selected").text()+'</td>'



					 +'<td>'+$("#tag_calculation_based_on option:selected").text()+'</td>'



					// +'<td>'+$("#tag_size option:selected").text()+'</td>'



					 +'<td>'+$("#tag_pcs").val()+'</td>'



					 +'<td>'+$("#tag_gwt").val()+'</td>'



					 +'<td><span class="tag_preview_lwt">'+$("#tag_lwt").val()+'</span></td>'



					 +'<td><span class="tag_preview_nwt">'+$("#tag_nwt").val()+'</span></td>'



					 +'<td>'+$("#tag_wast_perc").val()+'</td>'



					// +'<td>'+$("#tag_id_mc_type option:selected").text()+'</td>'



					 +'<td>'+$("#tag_mc_value").val()+'</td>'



					// +'<td><a href="#" onClick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));" class="btn btn-default btn-sm"><i class="fa fa-plus"></i></a><input type="hidden" class="stone_details" name="lt_item[stone_details][]" value='+$('#tag_stone_details').val()+'><input type="hidden" class="stone_price" name="lt_item[stone_price][]"><input type="hidden" class="normal_st_certif" value=""><input type="hidden" class="semiprecious_st_certif" value=""><input type="hidden" class="precious_st_certif" value=""></td>'



					 +'<td class="td_image"><input type="hidden" class="tagged_images" value='+JSON.stringify(tag_pre_img_resource)+'><img src='+image_preview+' style="width:30px;height:30px;"><a  class="btn btn-secondary order_img"  id="edit" data-toggle="modal" onClick="view_tag_images($(this).closest(\'tr\'));" ><i class="fa fa-eye" ></i></a></td>'



					 +'<td><span class="tag_preview_sell_rate">'+$("#tag_sell_rate").val()+'</span></td>'



					 +'<td><span class="tag_preview_sale_value">'+$("#tag_sale_value").val()+'</span></td>'



					 +'<td><div style="display: flex;"><span id="items_add_'+total_row+'"><a style="" href="#" onClick="edit_tag($(this).closest(\'tr\'));" data-href="'+base_url+'index.php/admin_ret_tagging/tagging/delete/'+items.tag_id+'/0/add" class="btn-del label label-primary" style="padding:5px;" data-toggle="tooltip" title="Edit"><i class="fa fa-edit"></i></a><a href="#" class="btn btn-danger btn-del btn-xs" style="padding:5px;" data-toggle="tooltip" title="Delete" data-target="#confirm-delete" data-href="'+base_url+'index.php/admin_ret_tagging/tagging/delete/'+items.tag_id+'/0/add"><i class="fa fa-trash"></i></a></span></div></td>'



					 //onClick="remove_row($(this).closest(\'tr\'));"



			+'</tr>';



	       if($('#lt_item_tag_preview > tbody  > tr').length>0)



        	{



        	    $('#lt_item_tag_preview > tbody > tr:first').before(row);



        	}else{



        	    $('#lt_item_tag_preview tbody').append(row);



        	}



    });



	set_tag_preview_class();



	//calculateTagPreviewSaleValue();



	$('#reset_tag_form').trigger('click');



	checking_lot_availability();



	calculate_tag_summary();



	$('#bulk_tag').val('');



}



//Bulk Tag



function bulk_tag_upd_add_charges() {



	let charges_options = "";



	$.each(charges_list, function (key, item) {



		charges_options = charges_options+"<option value="+item.id_charge+">"+item.name_charge+"</option>";



	});



	if(charges_list.length > 0) {



		let _row_last = $("#bulk_edit_charge_detail tbody tr:last-child");



		let sno = (_row_last.length > 0 ? parseInt(_row_last.find('.sno').text()) : 0)+1;



		let _html_add = '<tr class="bulk_edit_charge_row">'+



							'<td class="sno">'+



								sno+



							'</td>'+



							'<td>'+



								'<select class="form-control bulk_tag_upd_charge_name" placeholder="Charge Name">'+charges_options+'</select>'+



							'</td>'+



							'<td>'+



								'<input class="form-control bulk_tag_upd_charge_value" placeholder="Charge Value" />'+



							'</td>'+



							'<td class="bulk_edit_charge_buttons">'+



							'</td>'+



						'</tr>';



						$("#bulk_edit_charge_detail tbody").append(_html_add);



						$('#bulk_edit_charge_detail .bulk_edit_charge_row:last-child').find('.bulk_tag_upd_attr_name').select2();



						$('#bulk_edit_charge_detail .bulk_edit_charge_row:last-child').find('.bulk_tag_upd_attr_value').select2();



						bulk_edit_charges_buttons();



						load_charge_value($('#bulk_edit_charge_detail .bulk_edit_charge_row:last-child'));



	} else {



		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No charges found. Before adding charges, it must be created in the master.'});



	}



}



function bulk_edit_charges_buttons() {



	let attr_rows = $("#bulk_edit_charge_detail tbody");



	$(attr_rows).find('.bulk_tag_upd_add_charges').remove();



	$(attr_rows).find('.bulk_tag_upd_remove_charges').remove();



	$(attr_rows).find(".bulk_edit_charge_buttons:last").prepend('<button type="button" class="btn btn-success bulk_tag_upd_add_charges"><i class="fa fa-plus"></i></button>');



	$(attr_rows).find(".bulk_edit_charge_buttons").append('<button type="button" class="btn btn-danger bulk_tag_upd_remove_charges"><i class="fa fa-trash"></i></button>');



}



function load_charge_value(rowObj) {



	let charge_id = rowObj.find('.bulk_tag_upd_charge_name').val();



	let charge_value = 0;



	$.each(charges_list, function (key, item) {



		if(charge_id == item.id_charge) {



			charge_value = item.value_charge;



		}



	});



	rowObj.find(".bulk_tag_upd_charge_value").val(charge_value);



}



$(document).on("change", ".bulk_tag_upd_charge_name", function() {



	let chargeObj = $(this).closest(".bulk_edit_charge_row");



	load_charge_value(chargeObj);



});



$(document).on("click", ".bulk_tag_upd_remove_charges", function() {



	$(this).closest(".bulk_edit_charge_row").remove();



});



$(document).on("change", "#charge_type", function() {



	let charges_type = $(this).val();



	if(charges_type == 1) {



		$("#update_charges_block").css("display", "block");



	} else {



		$("#update_charges_block").css("display", "none");



	}



});



function getLotsforEmp()



{



	my_Date = new Date();



    $.ajax({



        type: 'POST',



        url: base_url+"index.php/admin_ret_tagging/getLotsforEmp",



		data:{'id_employee':$('#id_employee').val()},



        dataType:'json',



        success:function(data)



		{



			var id =  $('#tag_lot_id').val();



			$.each(data,function (key, item) {



			$("#tag_lot_received_id").append(



			$("<option></option>")



			.attr("value", item.lot_no)



			.text(item.lot_no)



			);



			});



			$("#tag_lot_received_id").select2("val",(id!='' && id>0?id:''));



		}



	});



}



function get_lot_split_products(id_emp)



{



	prod_details = [];



	var tag_lot_id=$('#tag_lot_id').val();



	$('#tag_lt_prod').html('');



	$('#tag_lt_design').html('');



	$("#tag_lt_prod option").remove();



	my_Date = new Date();



		$.ajax({



		url:base_url+ "index.php/admin_ret_tagging/get_lot_split_products?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),



        data: {'lot_no':tag_lot_id,'id_employee':id_emp},



        type:"POST",



        dataType:"JSON",



        success:function(data)



        {



       		if(data[0].order_no=='')



       		{



       				$(".overlay").css("display", "block");



       				$('#tag_lt_prod').prop('disabled',false);



					$('#tag_lt_design').prop('disabled',false);



					var tag_lt_prodId=$('#tag_lt_prodId').val();



					prod_details = data;



					var gross_wt=0;



					var gorss_pcs=0;



					var precious_st_wt=0;



					var precious_st_pcs=0;



					var semi_precious_st_wt=0;



					var semi_precious_st_pcs=0;



					var normal_st_wt=0;



					var normal_st_pcs=0;



					$.each(data, function (key, item) {



					    gross_wt +=parseFloat(item.gross_wt);



					    gorss_pcs += parseFloat(item.no_of_piece);



					    precious_st_wt +=parseFloat(item.precious_st_wt);



					    precious_st_pcs +=parseFloat(item.precious_st_pcs);



					    semi_precious_st_wt +=parseFloat(item.semi_precious_st_wt);



					    semi_precious_st_pcs +=parseFloat(item.semi_precious_st_pcs);



					    normal_st_wt +=parseFloat(item.normal_st_wt);



					    normal_st_pcs +=parseFloat(item.normal_st_pcs);



					    product_division = item.product_division;



					$("#tag_lt_prod").append(



					$("<option></option>")



					.attr("value", item.lot_product)



					.attr("data-rate", item.rate)



					.attr("data-touch", item.purchase_touch)



					.attr("data-rate_calc_type", item.rate_calc_type)



					.attr("data-calc_type", item.calc_type)



					.attr("data-wastage_percentage", item.wastage_percentage)



					.attr("data-making_charge", item.making_charge)



					.attr("data-mc_type", item.mc_type)



					.attr("data-id_lot_inward_detail", item.id_lot_inward_detail)



					.text(item.product_name)



					);



					});



					$("#tag_lt_prod").select2({



					placeholder: "Select Product",



					allowClear: true



					});



					$("#tag_lt_prod").select2("val",(tag_lt_prodId!='' && tag_lt_prodId>0?tag_lt_prodId:''));



					$("#tag_product_division").val((product_division!='' && product_division>0?product_division:''));



					$(".overlay").css("display", "none");



					$('#lot_bal_wt').val(gross_wt);



					$('#lot_bal_pcs').val(gorss_pcs);



					$('#lot_bal_prec_wt').val(precious_st_wt);



					$('#lot_bal_prec_pcs').val(precious_st_pcs);



					$('#lot_bal_semi_pre_wt').val(semi_precious_st_wt);



					$('#lot_bal_semi_pre_pcs').val(semi_precious_st_pcs);



					$('#lot_bal_normal_wt').val(normal_st_wt);



					$('#lot_bal_normal_pcs').val(normal_st_pcs);



		}



			else



			{



				$(".overlay").css("display", "block");



				my_Date = new Date();



				$.ajax({



				url: base_url+'index.php/admin_ret_tagging/get_order_details?nocache=' + my_Date.getUTCSeconds(),



				dataType: "json",



				method: "POST",



				data: {'lot_no': tag_lot_id},



				success: function (data) {



				if(data)



				{



				$('#tag_lt_prod').prop('disabled',true);



				$('#tag_lt_design').prop('disabled',true);



				//$('#add_more_tag').prop('disabled',true);



				var row='';



				var gross_wt=0;



				var lot_pcs=0;



				var id_orderdetails='';



				row_exist=false;



				var total_row=$('#lt_item_list tbody > tr').length;



					$.each(data,function(key,item){



						$('#lt_item_list> tbody  > tr').each(function(index, tr) {



								if($(this).find('.id_orderdetails').val() == item.id_orderdetails){



									row_exist = true;



									alert('Tag Already Exists');



									return false;



								}



								});



						});



						if(!row_exist)



						{



						$.each(data,function(key,item){



							gross_wt+=parseFloat(item.gross_wt);



							lot_pcs+=parseFloat(item.no_of_piece);



								row += '<tr id='+item.id_lot_inward_detail+' class='+total_row+'>'



								+'<td width="5%">'+item.lot_no+'<input type="hidden" name="lt_item[lot_no][]" value="'+item.lot_no+'" class="lot_no" /><input type="hidden" name="lt_item[id_lot_inward_detail][]" id="id_lot_inward_detail" value="'+item.id_lot_inward_detail+'" class="id_lot_inward_detail"><input type="hidden" name="lt_item[sales_mode][]" id="sales_mode" value="'+item.sales_mode+'" class="sales_mode"><input type="hidden" name="lt_item[id_orderdetails][]" class="id_orderdetails" value="'+item.id_orderdetails+'"><input type="hidden" class="stn_amt" value="'+item.stn_amt+'"></td>'



								+'<td width="10%">'+item.product_name+'<input type="hidden" name="lt_item[lot_product][]" value="'+item.id_product+'" class="lot_product" /><input type="hidden" name="lt_item[product_short_code][]" value="'+item.product_short_code+'" class="product_short_code" /></td>'



								+'<td width="10%">'+item.design_name+'<input type="hidden" name="lt_item[lot_id_design][]" value="'+item.design_no+'" class="lot_id_design" /></td>'



								+'<td width="10%">'+(item.design_for==1 ? 'Men' :(item.design_for==2 ? 'Female':'Unisex'))+'<input type="hidden" name="lt_item[design_for][]" value="'+item.design_for+'" class="design_for" /></td>'



								+'<td width="10%"><select class="calculation_based_on" name="lt_item[calculation_based_on][]"><option value="0" '+(item.calculation_based_on == 0 ? "selected":"")+' '+(item.calculation_based_on >= 3 ? "disabled":"")+'>Mc & Wast On Gross</option><option value="1" '+(item.calculation_based_on == 1 ? "selected":"")+' '+(item.calculation_based_on >= 3 ? "disabled":"")+'>Mc & Wast On Net</option><option value="2" '+(item.calculation_based_on == 2 ? "selected":"")+' '+(item.calculation_based_on >= 3 ? "disabled":"")+'>Mc on Gross,Wast On Net</option><option value="3" '+(item.calculation_based_on == 3 ? "selected":"")+' '+(item.calculation_based_on == 4 ? "disabled":"")+'>Fixed Rate</option><option value="4" '+(item.calculation_based_on == 4 ? "selected":"")+' '+(item.calculation_based_on == 3 ? "disabled":"")+'>Fixed Rate based on Weight</option></select></td>'



								+'<td width="5%"><input type="number" step="any" name="lt_item[no_of_piece][]"   class="no_of_piece" value="1" style="width:80px;" read/><span class="blc_pcs"></span><input type="hidden" disabled class="act_blc_pcs" value="'+item.no_of_piece+'" style="width:80px;" readonly></td>'



								+'<td width="10%"><input type="number" step="any" name="lt_item[gross_wt][]"   class="gross_wt" style="width:80px;" value="'+item.gross_wt+'"/></span><input type="hidden" class="act_gross_blc" value="'+item.gross_wt+'"><input type="hidden" class="gross_wt_blc" value="'+item.gross_wt+'"></td>'



								+'<td width="10%"><input type="number" step="any" name="lt_item[less_wt][]"  class="less_wt" style="width:80px;" readonly/></td>'



								+'<td width="10%"><input type="number" step="any" name="lt_item[net_wt][]"  class="net_wt" value="'+item.net_wt+'" style="width:80px;" readonly/></td>'



								+'<td width="5%"><input type="text" name="lt_item[wastage_percentage][]" value="'+item.wastage_percentage+'" class="order_wastage_percentage" style="width:80px;"/></td>'



								+'<td><select class="id_mc_type" value='+item.mc_type+'><option value="1">Per Gram</option><option value="2" selected>Per Piece</option></select><input type="hidden" value="'+item.mc_type+'" name="lt_item[id_mc_type][]" class="id_mc_type"></td>'



								+'<td width="10%"><input type="number" step="any" name="lt_item[making_charge][]"  class="order_making_charge" value="'+item.making_charge+'" style="width:80px;"/></td>'



								+'<td width="10%"><input type="number" step="any" name="lt_item[sell_rate][]"  class="order_sell_rate" value="'+item.sell_rate+'" style="width:80px;" '+(item.calculation_based_on < 3 ? "readonly":"")+'/>'+(item.calculation_based_on == 3 ? "per piece":(item.calculation_based_on == 4 ? "per gram":""))+'</td>'



								+'<td width="10%"><input type="number" step="any" name="lt_item[size][]"  class="order_size" style="width:70px;"/></td>'



								// +'<td><a href="#" onClick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));" class="btn btn-default btn-sm"><i class="fa fa-plus"></i></a></td>'



								+'<td><a href="#" onClick="update_image_upload($(this).closest(\'tr\'));" class="btn btn-default btn-sm"><i class="fa fa-plus"></i></a></td>'



								+'<td width="5%"><input type="number" step="any" name="lt_item[caculated_item_rate][]"  class="order_caculated_item_rate" value="'+item.caculated_item_rate+'" style="width:70px;" readonly/></td>'



								+'<td width="5%"><input type="number" step="any" name="lt_item[adjusted_item_rate][]"  class="order_adjusted_item_rate" value="'+item.adjusted_item_rate+'" style="width:80px;" '+(item.calculation_based_on < 3 ? "readonly":"")+'/></td>'



								+'<td width="10%"><input type="number" name="lt_item[sale_value][]"  class="sale_value" readonly style="width:80px;" /><input type="hidden" class="tax_group_id" value="'+item.tax_group_id+'"><input type="hidden" class="stone_details" name="lt_item[stone_details][]"><input type="hidden" class="stone_price" name="lt_item[stone_price][]"><input type="hidden" class="price" name="lt_item[price][]"><input type="hidden" class="tag_img" name="lt_item[tag_img][]"><input type="hidden" class="tag_img_copy" name="lt_item[tag_img_copy][]" value="0"><input type="hidden" class="tag_img_default" name="lt_item[tag_img_default][]" value=""><input type="hidden" class="normal_st_certif" value="'+item.normal_st_certif+'"><input type="hidden" class="semiprecious_st_certif" value="'+item.semiprecious_st_certif+'"><input type="hidden" class="precious_st_certif" value="'+item.precious_st_certif+'"></td>'



								+'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'



								+'</tr>';



						});



						}



						console.log(lot_pcs);



						console.log(gross_wt);



					$('#lot_bal_wt').val(gross_wt);



					$('#lot_bal_pcs').val(lot_pcs);



					$('#lt_item_list tbody').append(row);



					calculateOrderTagSaleValue();



					$(".overlay").css("display", "none");



				}



				else



				{



				$('#errorMsg').css("display","block");



				$('#errorMsg').html(data.message);



				}



				}



				});



			}



        },



        error:function(error)



        {



        }



    	});



}



function getTaggedLot()



{



	$.ajax({



		type: 'GET',



		url: base_url+'index.php/admin_ret_tagging/getTaggedLot',



		dataType:'json',



		success:function(data)



		{



			var id =  $("#tag_lot_no").val();



			$.each(data, function (key, item) {



				$("#tag_lot_no").append(



				$("<option></option>")



				.attr("value", item.lot_no)



				.text(item.lot_no)



				);



			});



			$("#tag_lot_no").select2(



			{



				placeholder:"Select Lot No",



				allowClear:true,



				//closeOnSelect: false



			});



			if(id!='')



			{



				$("#tag_lot_no").select2("val",id);



			}



		}



	});



}



function getTaggedRefNo()



{



	$.ajax({



		type: 'GET',



		url: base_url+'index.php/admin_ret_tagging/getTaggedRefNo',



		dataType:'json',



		success:function(data)



		{



			var id =  $("#tag_po_ref_no").val();



			$.each(data, function (key, item) {



				$("#tag_po_ref_no").append(



				$("<option></option>")



				.attr("value", item.po_id)



				.text(item.po_ref_no)



				);



			});



			$("#tag_po_ref_no").select2(



			{



				placeholder:"Select Po RefNo",



				allowClear:true,



				//closeOnSelect: false



			});



			$("#tag_po_ref_no").select2("val",(id!='' && id>0?id:''));



		}



	});



}



function get_ActiveKarigar()



{



	$.ajax({



	type: 'GET',



	url: base_url+'index.php/admin_ret_catalog/karigar/active_list',



	dataType:'json',



	success:function(data){



		var id =  $("#tag_karigar").val();



		$.each(data, function (key, item) {



		    $("#tag_karigar").append(



		    $("<option></option>")



		    .attr("value", item.id_karigar)



		    .text(item.karigar+' - '+item.code)



		    );



		});



		$("#tag_karigar").select2(



		{



			placeholder:"Select Karigar",



			allowClear:true,



			//closeOnSelect: false



		});



		if($('#tag_karigar').data('karigar'))



		{



			   var ar = $('#tag_karigar').data('karigar');



               $("#tag_karigar").select2('val',ar);



		}



		else



		{



			  $("#tag_karigar").select2('val','');



		}



		}



	});



}



$('#bulktag_images').on('change',function()



{



	validateBulkTagImages();



});



function validateBulkTagImages()



{



	//$('#wast_img_preview').html('');



	var preview = $('#pre_images');



	var files   = event.target.files;



	for(var i=0;i<files.length;i++)



	{



		const compress = new Compress();



		const product_images = [files[i]];



		compress.compress(product_images, {



			size: 4, // the max size in MB, defaults to 2MB



			quality: 0.75, // the quality of the image, max is 1,



			maxWidth: 1920, // the max width of the output image, defaults to 1920px



			maxHeight: 1920, // the max height of the output image, defaults to 1920px



			resize: true // defaults to true, set false if you do not want to resize the image width and height



		}).then((results) => {



			const output = results[0];



			total_files.push(output);



			const file   = Compress.convertBase64ToFile(output.data, output.ext);



			if(output.endSizeInMb < 2)



			{



				pre_img_resource.push({"src":output.prefix +output.data,'name':output.alt,'is_default':"0"});



			}



			else



			{



					alert('File size cannot be greater than 1 MB');



					files[i] = "";



					return false;



			}



		});



	}



	setTimeout(function(){



		var resource = [];



		resource     = pre_img_resource;



		image_preview_validaion('pre_images');



	},500);



}



function getActive_quality_code()



{



    $('#uom option').remove();



    $('#ed_uom option').remove();



    $.ajax({



    type: 'GET',



    url: base_url+'index.php/admin_ret_catalog/get_quality_code',



    dataType:'json',



    success:function(data)



        {



			quality_code =data;



			var id_quality = $('#id_quality').val();



            $("#quality_code option").remove();



			$.each(data, function (key, item) {



					$("#quality_code").append(



						$("<option></option>")



						.attr("value", item.quality_id)



						.text(item.code)



					);



				});



				$("#quality_code").select2({



					placeholder: "Quality Code",



					allowClear: true



				});



				$('#quality_code').select2("val",(id_quality!='' ? id_quality :""));



            $(".overlay").css("display", "none");



        }



    });



}



function getQualityDiamondRates()



{



	$.ajax({



		type: 'GET',



		url: base_url+'index.php/admin_ret_catalog/getQualityDiamondRates',



		dataType:'json',



		success:function(data)



			{



				qulaity_diamond_rates =data;



				$(".overlay").css("display", "none");



			}



		});



}



function set_diamond_rates(curRow)



{



	curRow.find('.stone_rate').val(0);



	curRow.find('.stone_rate').prop('disabled',false);



	$.each(qulaity_diamond_rates,function(key,items)



	{



		if(curRow.find('.stones_type').val() == 1 && (curRow.find('.quality_id').val()==items.quality_code_id))



		{



			var stone_wt =parseFloat(curRow.find('.stone_wt').val()) ;



			if(items.from_cent <= stone_wt && items.to_cent  >= stone_wt)



            {



				console.log(items.rate);



				curRow.find('.stone_rate').val(items.rate);



				curRow.find('.stone_rate').prop('disabled',true);



			}



		}



	});



	calculate_stone_amount();



}



function  set_stones_details(stone_details){



    row ='';



	$.each(stone_details, function (pkey, pitem) {



		 var stones_list='';



		 var stones_type_list='';



		 var uom_list='';



		 var html='';



		 var clarity="";



		 var color ="";



		 var cut ="";



		 var shape ="";



		 var quality_list = '<option value="">-Quality-</option>';



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



			if(item.id_stone_type == pitem.stone_type)



			{



				st_type_selected = "selected='selected'";



			}



			stones_type_list += "<option value='"+item.id_stone_type+"' "+st_type_selected+">"+item.stone_type+"</option>";



		});



		$.each(uom_details, function (pkey, item) {



			 var uom_selected = "";



			if(item.uom_id == pitem.uom_id)



			{



				uom_selected = "selected='selected'";



			}



			uom_list += "<option value='"+item.uom_id+"' "+uom_selected+">"+item.uom_name+"</option>";



		});



       lw_yes_selected ="";



       lw_no_selected ="";



		if(pitem.is_apply_in_lwt == 1){



			lw_yes_selected = "selected='selected'";



		}else{



			lw_no_selected = "selected='selected'";



		}



		quality_selected = '';



		$.each(quality_code, function (key, item) {



			quality_selected = '';



			if(pitem.stone_quality_id == item.quality_id)



			{



				quality_selected = "selected='selected'";



				clarity=item.clarity;



                color=item.color;



                cut=item.cut;



                shape=item.shape;



			}



			quality_list += "<option value='"+item.quality_id+"' "+quality_selected+">"+item.code+"</option>";



		});



	   let st_price_readonly = cal_type == 2 ? 'reaonly' : '';



	   let quality_type = pitem.stone_type == 1 ? '' : 'disabled';



		row += '<tr>'



			+'<td><select class="show_in_lwt form-control" name="est_stones_item[show_in_lwt][]" style="width:100px;"><option value=1 '+lw_yes_selected+' >Yes</option><option value=0 '+lw_no_selected+'>No</option></select></td>'



			+'<td><select class="stones_type form-control" name="est_stones_item[stones_type][]"  >'+stones_type_list+'</select></td>'



			+'<td><select class="stone_id form-control" name="est_stones_item[stone_id][]"  >'+stones_list+'</select></td>'



			+'<td><select class="quality_id form-control" name="est_stones_item[quality_id][]" '+quality_type+' >'+quality_list+'</select></td>'



			+'<td><input type="number" class="stone_pcs form-control" name="est_stones_item[stone_pcs][]"  value="'+pitem.pieces+'" style="width: 100%;"/></td>'



			+'<td><div class="input-group"><input class="stone_wt form-control" type="number"  name="est_stones_item[stone_wt][]" value="'+pitem.wt+'" style="width:100%;"/><span class="input-group-btn" style="width: 70px;"><select class="stone_uom_id form-control" name="est_stones_item[uom_id][]"  >'+uom_list+'</select></span></div></td>'



			+'<td><div class="form-group"><input class="stone_cal_type" type="radio"  name="est_stones_item[cal_type]['+pkey+']" value="1" '+(cal_type == 1 ? 'checked' : '')+'> By Wt&nbsp;<input type="radio"  name="est_stones_item[cal_type]['+pkey+']" '+(cal_type == 2 ? 'checked' : '')+' class="stone_cal_type" value="2">By Pcs</div></td>'



			+'<td><span class="stone_cut">'+cut+'</span></td>'



			+'<td><span class="stone_color">'+color+'</span></td>'



			+'<td><span class="stone_clarity">'+clarity+'</span></td>'



			+'<td><span class="stone_shape">'+shape+'</span></td>'



			+'<td><input type="number" class="stone_rate form-control" name="est_stones_item[stone_rate][]"  value="'+pitem.rate_per_gram+'"  style="width:100%;"/></td>'



			+'<td><input type="number" class="stone_price form-control" name="est_stones_item[stone_price][]" value="'+pitem.amount+'"  style="width:100%;" '+st_price_readonly+' /></td>'



			+'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td></tr>';



	});



	$('#estimation_stone_cus_item_details tbody').append(row);



}



function get_stone_details_bulk_edit(){



	var stone_details=[];



	var stone_price=0;



	var certification_price=0;



	var tag_less_wgt = 0;



	var stone_wt = 0;



	var return_data = [];



	$('#estimation_stone_cus_item_details> tbody  > tr').each(function(index, tr) {



		stone_price+=parseFloat($(this).find('.stone_price').val());



		if($(this).find('.show_in_lwt :selected').val() == 1){



			tag_less_wgt+=parseFloat($(this).find('.stone_wt').val());



		}



		stone_wt+=parseFloat($(this).find('.stone_wt').val());



		stone_details.push({



					'is_apply_in_lwt'       : $(this).find('.show_in_lwt').val(),



					'stone_id'          : $(this).find('.stone_id').val(),



					'pieces'         : $(this).find('.stone_pcs').val(),



					'wt'          : $(this).find('.stone_wt').val(),



					'stone_cal_type'    : $(this).find('input[type=radio]:checked').val(),



					'amount'       : $(this).find('.stone_price').val(),



					'rate_per_gram'        : $(this).find('.stone_rate').val(),



					'uom_id'      : $(this).find('.stone_uom_id').val(),



					'stone_quality_id'      : $(this).find('.quality_id').val(),



		});



	});



	return_data['stone_price'] = stone_price;



	return_data['total_stone_wt'] = stone_wt;



	return_data['less_wt'] = tag_less_wgt;



	return_data['stone_details'] = stone_details;



	return return_data;



 // console.log(stone_details);



}



function validateStoneBulkItemDetailRow(){



	var row_validate = true;



	tag_less_wgt = 0;



	$('#estimation_stone_cus_item_details> tbody  > tr').each(function(index, tr) {



		if($(this).find('.stone_id').val() == "" || $(this).find('.stone_pcs').val() == "" || $(this).find('.stone_wt').val() == "" || $(this).find('.stone_rate').val() == "" || $(this).find('.stone_price').val() == "" || $(this).find('.stone_uom_id').val() == "" ){



			row_validate = false;



		}



		tag_less_wgt+=parseFloat($(this).find('.stone_wt').val());



	});



	if(row_validate){



		$('#tagging_list > tbody  > tr').each(function(index, tr) {



			if($(this).find('.gross_wt').val() < tag_less_wgt ){



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Stone Weight cannot be greater than Gross Wt'});



				row_validate = false;



			}



		});



	}



	return row_validate;



}



// stones



$(".add_tag_edit_lwt").on('click',function(){



	$('#cus_stoneModal').modal('show');



	 var tag_edit_stone_details = $('#tag_edit_stone_details').val();



	 $('#cus_stoneModal tbody >tr').remove();



	 if($('#cus_stoneModal tbody >tr').length == 0)



	 {



		set_stones_details(JSON.parse(tag_edit_stone_details));



	 }



 });



 $('#update_tag_edit_stone_details').on('click',function(){



	if(validateStoneCusItemDetailRow())



	 {



		 var stone_details=[];



		 var stone_price=0;



		 var certification_price=0;



		 var tag_less_wgt = 0;



		 var gross_wt = $('#be_gross_wt').val();



		 modalStoneDetail = []; // Reset Old Value of stone modal



		 $('#cus_stoneModal .modal-body #estimation_stone_cus_item_details> tbody  > tr').each(function(index, tr) {



			 stone_price+=parseFloat($(this).find('.stone_price').val());



			//  if($(this).find('.show_in_lwt :selected').val() == 1){



				 /*



				 tag_less_wgt+=parseFloat($(this).find('.stone_wt').val());



				 if((item.uom_short_code=='CT') && (item.divided_by_value!=null && item.divided_by_value!='')) //For Carat Need to convert into gram



				 {



					 stone_wt=parseFloat(parseFloat(pitem.stone_wt)/parseFloat(item.divided_by_value));



				 }else{



					 stone_wt=pitem.stone_wt;



				 }



				 */



				 model_this=$(this)



				 $.each(uom_details,function(key,item){



					if(item.uom_id== model_this.find('.stone_uom_id').val())



					{



						if(model_this.find('.show_in_lwt :selected').val() == 1)



						{



							if((item.uom_short_code=='CT') && (item.divided_by_value!=null && item.divided_by_value!='')) //For Carat Need to convert into gram



							{



								tag_less_wgt+=parseFloat(parseFloat(model_this.find('.stone_wt').val())/parseFloat(item.divided_by_value));



							}else{



								tag_less_wgt+=parseFloat(model_this.find('.stone_wt').val());



							}



							//tot_stone_wt+=parseFloat(stone_wt);



						}



					//	stone_price+=parseFloat(pitem.stone_price);



					}



				});



			//  }



			 stone_details.push({



				'is_apply_in_lwt'       : $(this).find('.show_in_lwt').val(),



				'stone_type'        : $(this).find('.stones_type').val(),



				'stone_id'          : $(this).find('.stone_id').val(),



				'pieces'         : $(this).find('.stone_pcs').val(),



				'wt'          : $(this).find('.stone_wt').val(),



				'stone_cal_type'    : $(this).find('input[type=radio]:checked').val(),



				'amount'       : $(this).find('.stone_price').val(),



				'rate_per_gram'        : $(this).find('.stone_rate').val(),



				'uom_id'      : $(this).find('.stone_uom_id').val(),



				'stone_quality_id'      : $(this).find('.quality_id').val(),



	           });



		 });



		 if(gross_wt  < tag_less_wgt)



		 {



			 $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Entered Weight is More Than The Gross Wt.."});



		 }else



		 {



			 $('#be_net_wt').val(parseFloat(parseFloat(gross_wt)-parseFloat(tag_less_wgt)).toFixed(3));



			 modalStoneDetail = stone_details;



			 console.log(modalStoneDetail);



			 $("#be_less_wt").val(parseFloat(tag_less_wgt).toFixed(3));



			 $("#tag_edit_stone_details").val(JSON.stringify(stone_details));



			 $('#cus_stoneModal').modal('hide');



		 }



	 }



	 else



	 {



		 alert('Please Fill The Required Details');



	 }



 });



 $(document).on('click', '#create_stone_item_details', function (e) {



	if(validateStoneCusItemDetailRow()){



		   create_new_stone_row();



	   }else{



		   alert("Please fill required stone fields");



	   }



});



function getStoneRateSettings()



{



	$.ajax({



		type: 'GET',



		url: base_url+'index.php/admin_ret_catalog/getStoneRateSettings',



		dataType:'json',



		success:function(data)



			{



				stone_rate_settings =data;



				$(".overlay").css("display", "none");



			}



		});



}







// tag bulk huid add



$(".display_huid_modal").on('click',function(){



    openHuidModal();



});



function openHuidModal() {



	$('#huid_modal .modal-body').find('#table_huid_detail tbody').empty();



	if(modalHuidDetail.length > 0) {



		$.each(modalHuidDetail, function (key, item) {



			add_tag_huid(item);



		});



	} else {



		add_tag_huid();



	}



	$('#huid_modal').modal('show');



}



function add_tag_huid(data=[]){



	console.log(data);



	if(validate_taghuid_row()){



		let attr_row_last = $("#table_huid_detail tbody tr:last-child");



		let sno = (attr_row_last.length > 0 ? parseInt(attr_row_last.find('.sno').text()) : 0)+1;



		var huid = (data ? (data.huid == undefined ? '':data.huid): '');



		let _html_add = '<tr class="huid_row" id="'+$('#table_huid_detail tbody tr').length+'">'+



							'<td width="10%" class="sno">'+



								sno+



							'</td>'+



							'<td width="35%">'+



								'<input class="form-control tag_upd_huid" name="deshuid[huid][]" placeholder="Enter Huid" value="'+huid+'" />'+



							'</td>'+



							'<td width="20%" class="huid_row_buttons">'+



							'</td>'+



						'</tr>';



		$("#table_huid_detail tbody").append(_html_add);



		add_huid_buttons();



	}else {



		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill HUID'});



	}



}



function add_huid_buttons() {



	let attr_rows = $("#table_huid_detail tbody");



	$(attr_rows).find('.add_tag_huid').remove();



	$(attr_rows).find('.remove_tag_huid').remove();



	$(attr_rows).find(".huid_row_buttons:last").prepend('<button type="button" class="btn btn-success add_tag_huid"><i class="fa fa-plus"></i></button>');



	$(attr_rows).find(".huid_row_buttons").append('<button type="button" class="btn btn-danger remove_tag_huid"><i class="fa fa-trash"></i></button>');



}



function remove_tag_huid(itemObj)



{



	$(itemObj).closest('tr').remove();



	add_huid_buttons();



}



$(document).on("change", ".tag_upd_huid", function() {



	var row = $(this).closest('tr');



	var huid = $(this).val();



	var rowId =row.attr('id')



	if(huid_validation(huid))



	{



		duplicate_tag_id(huid,row,rowId);



		// row.find('.tag_upd_huid').val(huid);



	}else{



		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Invalid HUID Number!'});



		row.find('.tag_upd_huid').val('');



		$(this).focus();



	}



});



function validate_taghuid_row()



{



	let row_validated = true;



	let tag_attr_rows = $("#table_huid_detail tbody tr");



	$.each(tag_attr_rows, function (attrkey, attritem) {



		let attr_name = $(attritem).find('.tag_upd_huid').val();



		if(!attr_name > 0 ) {



			row_validated = false;



			return false;



		}



	});



	return row_validated;



}



$("#update_huid_details").on("click", function() {



	modalHuidDetail = [];



	let huid_details	 = [];



	let tableRows = $("#table_huid_detail tbody tr");



	$(tableRows).each(function(index, huiditem) {



		huid_details.push({



					'huid'       : $(huiditem).find(".tag_upd_huid").val(),



				});



	});



	modalHuidDetail = huid_details;



	$('#other_huid_details').val(JSON.stringify(modalHuidDetail));



	$('#huid_modal .modal-body').find('#table_huid_detail tbody').empty();



	$('#huid_modal').modal('hide');



});



function duplicate_tag_id(huid,row,rowId)



{



    my_Date = new Date();



    $.ajax({



        url: base_url+"index.php/admin_ret_tagging/get_prev_huid?nocache=" + my_Date.getUTCSeconds(),



        type:"POST",



        data:{'huid':huid},



        dataType:"JSON",



        cache:false,



        success:function(data)



        {



             let old_tag_id_values=data;



             if(old_tag_id_values.length>0){



                $.toaster({priority : 'danger',title:'warning!',message:''+"</br>"+'HUID Already Exists...'});



                row.find('.tag_upd_huid').focus();



                row.find('.tag_upd_huid').val('');



            }



			else



			{



				// row.find('.tag_upd_huid').val(huid);



				if(duplicate_huid_row(huid,rowId))



				{



					row.find('.tag_upd_huid').val(huid);



				}



				else



				{



					$.toaster({priority : 'danger',title:'warning!',message:''+"</br>"+'HUID Already Exists in row...'});



					row.find('.tag_upd_huid').focus();



					row.find('.tag_upd_huid').val('');



				}



			}



        }



    })



}



function duplicate_huid_row(huid,rowId)



{



	var rowvalidate = true;



	$('#table_huid_detail > tbody  > tr').each(function(index, tr){



		var row = $(this);



        var table_id = $(this).attr('id');



		if(table_id!=rowId){



			if(huid==row.find('.tag_upd_huid').val())



			{



				rowvalidate=false;



			}



		}



	});



	return rowvalidate;



}



/*Minimum Maximum Rate Settings*/



function set_minmaxStone_rates(curRow)



{



	curRow.find('.stone_rate').val(0);



	$.each(stone_rate_settings,function(key,items)



	{



		console.log('item',items);



		var stone_centwt = 0;



		if(curRow.find('.stones_type').val()==1)



		{



			var stone_pcs  = (isNaN(curRow.find('.stone_pcs').val()) || curRow.find('.stone_pcs').val() == '')  ? 0 : parseInt(curRow.find('.stone_pcs').val());



			var stone_wt  = (isNaN(curRow.find('.stone_wt').val()) || curRow.find('.stone_wt').val() == '')  ? 0 : parseFloat(curRow.find('.stone_wt').val());



			stone_centwt = parseFloat(((stone_wt)/(stone_pcs))*100).toFixed(3);



		}



		if($('#branch_select').val() == items.id_branch && curRow.find('.stones_type').val()==items.stone_type && curRow.find('.stone_id').val()==items.stone_id && curRow.find('.quality_id').val() == items.quality_id && curRow.find('.stone_uom_id').val()==items.uom_id)



		{



			if(curRow.find('.stones_type').val()==1)



			{



				if(stone_centwt > 0)



				{



					if(stone_centwt >= parseFloat(items.from_cent) && stone_centwt <= parseFloat(items.to_cent))



					{



						curRow.find('.stone_rate').val(items.max_rate);



					}



				}



			}



			else



			{



				curRow.find('.stone_rate').val(items.max_rate);



			}



		}



	});



	calculate_stone_amount();



}



function getLooseStoneProductRateSettings()



{



	$.ajax({



		type: 'GET',



		url: base_url+'index.php/admin_ret_catalog/getLooseStoneProductRateSettings',



		dataType:'json',



		success:function(data)



			{



				loose_product_rate =data;



				$(".overlay").css("display", "none");



			}



		});



}



function setLoose_product_rateSettings()



{



	$('#tag_sell_rate').val(0);



	$.each(loose_product_rate,function(key,items)



	{



		console.log('looseproduct',items);



		var product_centwt = 0;



		if($('#tag_product_stone_type').val()==2) // for diamond products



		{



			var pcs  = (isNaN($('#tag_pcs').val()) || $('#tag_pcs').val() == '')  ? 0 : parseInt($('#tag_pcs').val());



			var grs_wt  = (isNaN($('#tag_gwt').val()) || $('#tag_gwt').val() == '')  ? 0 : parseFloat($('#tag_gwt').val());



			product_centwt = parseFloat(((grs_wt)/(pcs))*100).toFixed(3);



		}



		if($('#branch_select').val() == items.id_branch && $('#tag_lt_prod').val() == items.id_product && $('#des_select').val() == items.id_design && $('#sub_des_select').val()==items.id_sub_design && $('#quality_code').val()==items.quality_id && $('#gwt_uom_id').val()==items.uom_id)



		{



			if(product_centwt > 0)



			{



				if(product_centwt >= parseFloat(items.from_cent) && product_centwt <= parseFloat(items.to_cent))



				{



					$('#tag_sell_rate').val(items.max_rate);



					$('#stone_calculation_based_on').val(items.stone_calc_type);



				}



			}



			else



			{



				$('#tag_sell_rate').val(items.max_rate);



				$('#stone_calculation_based_on').val(items.stone_calc_type);



			}



		}



	})



}



$('#stone_calculation_based_on').on('change',function()



{



	if(this.value!='')



	{



		calculateTagFormSaleValue();



	}



})



$('#tag_sell_rate').on('change',function()



{



	check_min_max_stone_product_rate();



});



function check_min_max_stone_product_rate()



{



	if($('#tag_product_stone_type').val()!=0)



	{



		var tag_sell_rate =  $('#tag_sell_rate').val();



		$.each(loose_product_rate,function(key,items)



		{



			var product_centwt = 0;



			if($('#tag_product_stone_type').val()==2) // for diamond products



			{



				var pcs  = (isNaN($('#tag_pcs').val()) || $('#tag_pcs').val() == '')  ? 0 : parseInt($('#tag_pcs').val());



				var grs_wt  = (isNaN($('#tag_gwt').val()) || $('#tag_gwt').val() == '')  ? 0 : parseFloat($('#tag_gwt').val());



				product_centwt = parseFloat(((grs_wt)/(pcs))*100).toFixed(3);



			}



			if($('#branch_select').val() == items.id_branch && $('#tag_lt_prod').val() == items.id_product && $('#des_select').val() == items.id_design && $('#sub_des_select').val()==items.id_sub_design && $('#quality_code').val()==items.quality_id && $('#gwt_uom_id').val()==items.uom_id)



			{



				if(product_centwt > 0)



				{



					if(product_centwt >= parseFloat(items.from_cent) && product_centwt <= parseFloat(items.to_cent))



					{



						if(tag_sell_rate>=parseFloat(items.min_rate) && tag_sell_rate<=parseFloat(items.max_rate))



						{



							$('#tag_sell_rate').val(tag_sell_rate);



						}



						else



						{



							$('#tag_sell_rate').val(items.max_rate);



							$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Entered Rate Must be Within '+items.min_rate+' and '+items.max_rate+' !'});



						}



					}



				}



				else if(tag_sell_rate>=parseFloat(items.min_rate) && tag_sell_rate<=parseFloat(items.max_rate))



				{



					$('#tag_sell_rate').val(tag_sell_rate);



				}



				else



				{



					$('#tag_sell_rate').val(items.max_rate);



					$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Entered Stone Rate Must be Within '+items.min_rate+' and '+items.max_rate+' !'});



				}



			}



		});



	}



	calculateTagFormSaleValue();



}



$(document).on('click', '#create_salesret_stone_details', function (e)



{



	create_new_salesret_stone_row();



});



function create_new_salesret_stone_row()



{



	var stones_list = "<option value=''>-Select Stone-</option>";



	var stones_type = "<option value=''>-Stone Type-</option>";



    var length=(($('#estimation_stone_cus_item_details tbody tr').length)+1);



    console.log('length:'+length);



	$.each(stones, function (pkey, pitem) {



		stones_list += "<option value='"+pitem.stone_id+"'>"+pitem.stone_name+"</option>";



	});



	$.each(stone_types, function (pkey, pitem) {



		stones_type += "<option value='"+pitem.id_stone_type+"'>"+pitem.stone_type+"</option>";



	});



	var row='';



        row += '<tr id="'+length+'">'



        	+'<td><select class="stones_type form-control" name="est_stones_item[stones_type][]" >'+stones_type+'</select></td>'



			+'<td><select class="stone_id form-control" name="est_stones_item[stone_id][]" >'+stones_list+'</select><input type="hidden" class="stone_type" value=""></td>'



			+'<td><input type="number" class="stone_pcs form-control" name="est_stones_item[stone_pcs][]" value="1" /></td>'



			+'<td><input class="stone_wt form-control" type="number" name="est_stones_item[stone_wt][]" value="" style="width: 100%;"/></td>'



	$('#cus_stoneModal .modal-body').find('#estimation_stone_cus_item_details tbody').append(row);



}



function caculate_bulk_edit_purchase_sale(){



	var tag_mc=$("#bulk_mc").val();



	var mc_type = $("#bulk_mc_type").val();



	var tot_pcs = $("#bulk_pcs").val();



	var wastage_per = $("#bulk_wastage").val();



	var net_wt = $("#bulk_net").val();



	var gross_wt = $("#bulk_gross").val();



	var total_charges = $("#bulk_charges").val();



	var other_metal_amount = $("#bulk_othermetal").val();



	var stone_price = $("#bulk_stones").val();



	var rate = $("#rate_per_gram").val() ==''?0:$("#rate_per_gram").val();



	var ratecaltype = $("#rate_calc_type").val();



	var purchase_touch = $("#purchase_touch").val() ==''?0:$("#purchase_touch").val();



	var karigar_calc_type = $("#karigar_calc_type").val() ;



	var purchase_mc_type= $("#pur_mc_type").val() ;



	var purchase_mc = $("#pur_mc_value").val() ==''?0:$("#pur_mc_value").val();



	var mc_value =  parseFloat(purchase_mc_type == 2 ? parseFloat(purchase_mc * gross_wt ) : parseFloat(purchase_mc * tot_pcs));



	 var pur_wastage =  $("#purchase_wastage").val() ==''?0:$("#purchase_wastage").val();



	// var tot_pcs = $('#tag_pcs').val();



	if(karigar_calc_type==1)



	{



		var purewt = parseFloat((parseFloat(net_wt) * (parseFloat(purchase_touch) + parseFloat(pur_wastage))) / 100);



	}else if(karigar_calc_type==2) //Net weight * touch



	{



		var purewt = parseFloat((parseFloat(net_wt) * (parseFloat(purchase_touch)/100)));



	}



	else if(karigar_calc_type==3) // ((net wt * 3%)*92%)



	{



		var touch_weight       = parseFloat((parseFloat(net_wt)*parseFloat(purchase_touch)/100)).toFixed(3);



		var wastage_touch      = parseFloat(parseFloat(touch_weight)*(parseFloat(pur_wastage))/100);



		var purewt             = parseFloat(parseFloat(touch_weight)+parseFloat(wastage_touch)).toFixed(3);



	}



	if(ratecaltype==1) // Rate Calc By Grm(Wt)



	{



		purchase_cost   = parseFloat((parseFloat(purewt)*parseFloat(rate))+parseFloat(mc_value)+parseFloat(other_metal_amount) + parseFloat(total_charges)+parseFloat(stone_price)).toFixed(2);



	}else



	{



		purchase_cost   = parseFloat((parseFloat(tot_pcs)*parseFloat(rate))+parseFloat(mc_value)+parseFloat(other_metal_amount) + parseFloat(total_charges)+parseFloat(stone_price)).toFixed(2);



	}



	$("#purchase_cost").val(purchase_cost);



}



$(document).on('change', '#purchase_wastage', function (e) {



	var value = this.value;



	if(value > 100){



		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+' You Have Entered Invalid Value'});



		this.value=0;



	}



});



$(document).on('change', '#pur_mc_value, #pur_mc_type, #rate_per_gram, #karigar_calc_type, #purchase_wastage, #purchase_touch, #rate_calc_type', function (e) {



	caculate_bulk_edit_purchase_sale();



});





function get_NonTagOtherIssue_details()

{

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		url: base_url+"index.php/admin_ret_tagging/retagging/non_tag_other_issue?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		data:{'id_branch':$("#id_branch").val(),'bt_number':$("#bt_number").val()},

		dataType: 'json',

		cache:false,

		success:function(data)

		{

			$("div.overlay").css("display","add_newstone");

			console.log(data);



			var oTable = $('#non_tag_otherissue_list').DataTable();

			oTable.clear().draw();

			if (data!= null && data.length > 0)

			{

			   oTable = $('#non_tag_otherissue_list').dataTable({

					   "bDestroy": true,

					   "bInfo": true,

					   "bFilter": true,

					   "bSort": true,

					   "order": [[ 0, "desc" ]],

					   "aaData"  : data,

					   "aoColumns": [



						   { "mDataProp": function ( row, type, val, meta ){



						   chekbox='<input type="checkbox" class="bt_trans_id" name="bt_trans_id[]" value="'+row.nontag_transfer_id+'"/><input type="hidden" class="net_wt" value="'+row.net_wt+'"><input type="hidden" class="piece" value="'+row.pieces+'"><input type="hidden" class="gross_wt" value="'+row.gross_wt+'">'



						   return chekbox+" "+row.branch_transfer_id;



						   }},



						   { "mDataProp": "branch_name"},



						   { "mDataProp": "bt_date" },



						   { "mDataProp": "branch_trans_code" },



						   { "mDataProp": "product_name" },



						   { "mDataProp": "design_name" },



						   { "mDataProp":function(row,type,val,meta){

							   return '<input type="number" class="form-control nt_otriss_pieces" value="'+row.pieces+'" style="width:100px;"><input type="hidden" class="form-control actual_nt_otriss_pcs" value="'+row.pieces+'">'

						   }},



						   { "mDataProp": function (row,type,val,meta){



							   return '<input type="number" class="form-control nt_otriss_grswt" value="'+parseFloat(row.gross_wt).toFixed(3)+'" style="width:100px;"><input type="hidden" class="form-control actual_nt_otriss_grswt" value="'+row.gross_wt+'" >';



						   }},



						   { "mDataProp": function (row,type,val,meta){



							   return '<input type="number" class="form-control nt_otriss_netwt" value="'+parseFloat(row.net_wt).toFixed(3)+'" style="width:100px;" readonly>';



						   }},



					   ],

				   });



				   calculateNT_OtherIssue_total();





			   }



			   $("div.overlay").css("display", "none");



	   },



		error:function(error)



		 {



			$("div.overlay").css("display", "none");



		}

	});

}



function calculateNT_OtherIssue_total()

{

	var pieces = 0;

	var grs_wt = 0;

	var net_wt = 0;

	$("#non_tag_otherissue_list > tbody > tr").each(function () {

		var row = $(this).closest('tr');

		pieces = pieces + (parseFloat(row.find('.nt_otriss_pieces').val()) || 0);

		grs_wt = grs_wt + (parseFloat(row.find('.nt_otriss_grswt').val()) || 0);

		net_wt = net_wt + (parseFloat(row.find('.nt_otriss_netwt').val()) || 0);

	});

	$(".nt_otr_pieces").html(pieces);

	$(".nt_otr_gwt").html(parseFloat(grs_wt).toFixed(3));

	$(".nt_otr_nwt").html(parseFloat(net_wt).toFixed(3));

}





$(document).on('keyup','.nt_otriss_grswt',function()

	{

	var row = $(this).closest('tr');



	var nt_grswt  = row.find('.nt_otriss_grswt').val();



	var act_nt_wt = row.find('.actual_nt_otriss_grswt').val();



	if(nt_grswt!='' && nt_grswt!=0){



        if(parseFloat(nt_grswt)>parseFloat(act_nt_wt))



        {



            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Valid Weight!'});



            row.find('.nt_otriss_grswt').val(act_nt_wt);



            row.find('.nt_otriss_netwt').val(act_nt_wt);



        }else{



            nt_net_wt = parseFloat(parseFloat(nt_grswt)).toFixed(3);



            row.find('.nt_otriss_netwt').val(nt_net_wt);



        }



    }else{



        row.find('.nt_otriss_grswt').val(act_nt_wt);



        row.find('.nt_otriss_netwt').val(act_nt_wt);



    }

})



$(document).on('keyup','.nt_otriss_pieces',function()

{

	var row = $(this).closest('tr');

	var nt_ret_pcs = row.find('.nt_otriss_pieces').val();

	var actual_nt_pcs = row.find('.actual_nt_otriss_pcs').val();



	if(parseFloat(nt_ret_pcs) > parseFloat(actual_nt_pcs))

	{

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Valid Pcs!'});

		row.find('.nt_otriss_pieces').val(actual_nt_pcs);

	}

	else

	{

		row.find('.nt_otriss_pieces').val(nt_ret_pcs);

	}

})









$('#send_order_unlink_otp_yes').on('click',function()

{

		  allow_discount_otp = true;

		  $('.cancel_otp').css('display','block');

		  $('.cancel_otp_confirmation').css('display','none');

		  $('.verify_otp').css('display','block');



		// $('#tagging_unlink_list > tbody  > tr').each(function(index, tr) {



		// 	if($(this).find('.show_in_lwt').is(":checked")) {



		// 	}







		// });

		 orderunlink_otp()

});





//Order Unlink OTP

function orderunlink_otp()

{

	$("div.overlay").css("display","block");

	my_Date=new Date();

	$.ajax({

		url: base_url+'index.php/admin_ret_tagging/send_order_unlink_otp/?nocache='+my_Date.getUTCSeconds(),

        dataType: "json",

        method: "POST",

		data:{'tag_id_unlink':$('.tag_id_unlink').val(),'id_branch': ($('#branch_filter').val() != '' && $('#branch_filter').val() != undefined ? $('#branch_filter').val() : $("#branch_select").val()),'id_cus_order_unlink' :  $('.id_cus_order_unlink').val()},

		success: function (data) {

			console.log(data.status);

                if(data.status == true)

				{

				    $("div.overlay").css("display", "none");

                    $('#otp_by_emp').val('');

                	$.toaster({ priority : 'success', title : 'Success!', message : ''+"</br>"+data.msg});

                	var fewSeconds = 60;

    		   		$("#resend_order_unlink_otp").prop('disabled', true);

					show_countdown();

    		   		timer = setTimeout(function(){

    			        $("#resend_order_unlink_otp").prop('disabled', false);

    		    	}, fewSeconds*1000);



				}

				else{

				    $("div.overlay").css("display", "none");

					$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br> Unable to Send The OTP..."});

				}

                },

                error:function(error)

                {

                    $("div.overlay").css("display", "none");

                }

	});

}



$('#verfiy_ord_unlink_otp').on('click',function(){

    my_Date = new Date();

	$.ajax({

        url: base_url+'index.php/admin_ret_tagging/verify_order_unlink_otp/?nocache='+my_Date.getUTCSeconds(),

        data:{'otp':$('#orderunlink_otp').val()},

        dataType: "json",

        method: "POST",

        success: function (data) {

                if(data.status == true)

				{

				    $('#confirm-orderUnlink').modal('toggle');

				    $('.cancel_otp_confirmation').css('display','none');

				    $('.cancel_otp').css('display','none');

					$('.verify_otp').css('display','none');

                   $.toaster({ priority : 'success', title : 'Success!', message : ''+"</br>"+data.msg});

				   tag_unlink();

				}else{

				    $('#cc_disc_otp').val('');

				    $.toaster({ priority : 'danger', title : 'Success!', message : ''+"</br>"+data.msg});

				}

                },

                error:function(error)

                {

                }

    });

});



function tag_unlink()

{



	var multiple_unlink_tag = [];

	$.each($("input[name='tag_id[]']:checked"), function(){

				multiple_unlink_tag.push({

				'order_no':$(this).closest('tr').find('.order_no_unlink').val(),

				'tag_id':$(this).closest('tr').find('.tag_id_unlink').val(),

				'id_orderdetails':$(this).closest('tr').find('.id_cus_order_unlink').val(),

			});

		   });



	if(multiple_unlink_tag.length > 0)

	{

		$('#tag_unlink_submit').prop('disabled',true);

		my_Date = new Date();

			$.ajax({

				 url:base_url+"index.php/admin_ret_tagging/unlink_order_tags?nocache=" + my_Date.getUTCSeconds(),

				 data: {'unlink_data':multiple_unlink_tag},

				 dataType:"JSON",

				 type:"POST",

				 success:function(data){



					 if(data == 1)

					 {

						window.location.reload();

					 }else{



						 $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Unable to proceed your request'});

					 }

				  },



			});

	}else

	{

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Select any one tag to unlink'});

	}

}



$('#order_unlink_close_modal').on('click', function () {

	$('#confirm-orderUnlink').modal('toggle');

});



$(document).on('click', '#send_order_unlink_otp_no',function()

{

    $('#confirm-orderUnlink').modal('toggle');

});



function show_countdown(){

const now = new Date().getTime();



const targetTime = now + 60 * 1000;



const x = setInterval(function() {



    const currentTime = new Date().getTime();



    const distance = targetTime - currentTime;



    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));

    const seconds = Math.floor((distance % (1000 * 60)) / 1000);



    document.getElementById("timer").innerHTML = ' ( '+minutes + "m " + seconds + "s " + ' ) ';



    if (distance < 0) {

        clearInterval(x);

        document.getElementById("timer").innerHTML = "";

    }

}, 1000);



}





$(document).on('click','#resend_order_unlink_otp',function(e){

	orderunlink_otp();

});



function bulk_tag_edit_log_list() {



	let id_branch =  $('#branch_filter').val() != '' && $('#branch_filter').val() != undefined ? $('#branch_filter').val() : ($("#branch_select").val() > 0 ? $("#branch_select").val() : 0);



	let from_date = $('#bulkedit_log_date1').html() != "" ? $('#bulkedit_log_date1').html() : "-";



	let to_date = $('#bulkedit_log_date2').html() != "" ? $('#bulkedit_log_date2').html() : "-";



	let emp_id = $('#emp_select').val() > 0 ? $('#emp_select').val() : 0;



	let tag_code = $.trim($("#tag_code").val()) != "" ? $.trim($("#tag_code").val()) : "-";



	$("div.overlay").css("display", "block");



	my_Date = new Date();



	$.ajax({



		url: base_url+"index.php/admin_ret_tagging/bulk_tag_edit_log/ajax/"+id_branch+"/"+emp_id+"/"+tag_code+"/"+from_date+"/"+to_date+"?nocache=" + my_Date.getUTCSeconds(),



		type:"GET",



		dataType: 'json',



		cache:false,



		success:function(data)

		{



			data = data.list;



			$("div.overlay").css("display","add_newstone");



			var oTable = $('#bulk_edit_log_list').DataTable();



			oTable.clear().draw();



			if (data!= null && data.length > 0) {



				oTable = $('#bulk_edit_log_list').dataTable({



						"bDestroy": true,



						"bInfo": true,



						"bFilter": true,



						"bSort": true,



						"order": [[ 0, "desc" ]],



						"aaData"  : data,



						"aoColumns": [



							{ "mDataProp": "edit_log_id"},



							{ "mDataProp": "edit_datetime" },



							{ "mDataProp": "edit_tag" },



							{ "mDataProp": "tag_code" },



							{ "mDataProp": "branch_name" },



							{ "mDataProp": "edit_field" },



							{ "mDataProp": "emp_name" },



							{ "mDataProp":function(row,type,val,meta){



								let previous_values =  "";



								if($.trim(row.previous_values) != "") {



									previous_values = formatJsonString(JSON.parse(row.previous_values));



								} else {



									previous_values = "";



								}



								return previous_values;



							}},



							{ "mDataProp":function(row,type,val,meta){



								let updated_values =  "";



								if($.trim(row.updated_values) != "") {



									updated_values = formatJsonString(JSON.parse(row.updated_values));



								} else {



									updated_values = "";



								}



								return updated_values;



							}},



					   	],

				   });



			   }



			   $("div.overlay").css("display", "none");



	   },



		error:function(error) {



			$("div.overlay").css("display", "none");



		}

	});



}



function formatJsonString(data) {



	var valueString = "";



	$.each(data, function(index, item) {



		for (var key in item) {



			if (Array.isArray(item[key])) {



				let itemValue = formatJsonString(item[key]);



				valueString += key + ": " + itemValue + " , ";



			} else {



				let key_upper_case = key.replace(/(^\w|\s\w)/g, function(match) { return match.toUpperCase(); });



				valueString += '<span class="style-key">'+key_upper_case+'</span>' + ": " + item[key] + " , ";



			}



		}



		valueString = valueString.slice(0, -2); // Remove the trailing comma and space



		valueString += "<br>";



	});



	return valueString;

}

$(document).on("click", ".clear_date", function() {



	$('#bulkedit_log_date1').html("");



	$('#bulkedit_log_date2').html("")



});



$("#tag_sale_value").on("keyup",function () {

	var value = $(this).val();

	var net_wt =$('#tag_nwt').val();

	var no_of_piece =$('#tag_pcs').val();

	// caculated_item_rate = parseFloat((parseFloat(sell_rate)*parseFloat(net_wt))*parseFloat(no_of_piece));



	sell_rate = value / (parseFloat(net_wt) * parseFloat(no_of_piece));



	$('#tag_sell_rate').val(parseFloat(sell_rate).toFixed(2))



});







/*Metal Pocket*/



$(document).on('change',".tag_id,.old_metal_sale_id", function()

{

	calculate_average_purity_and_rate();

});



function calculate_average_purity_and_rate()

{

	var report_type = $('#report_type').val();

	var total_pcs=0;

	var total_gwt=0;

	var total_nwt=0;

	var total_lwt=0;

	var total_diawt=0;

	var total_amt=0;

	var total_purity=0;

	var total_length=0;

	var total_item_purity=0;

	if(report_type==1)  // Sales return

	{

		$("#retagging_list > tbody > tr").each(function ()

		{

			var row = $(this).closest('tr');

			if(row.find("input[name='tag_id[]']:checked").is(":checked"))

			{

				total_pcs = total_pcs + (parseFloat(row.find('.retag_pcs').val()) || 0);

				total_gwt = total_gwt + (parseFloat(row.find('.retaggross_wt').val()) || 0);

				total_lwt = total_lwt + (parseFloat(row.find('.lwt').val()) || 0);

				total_nwt = total_nwt + (parseFloat(row.find('.retag_net_wt').val()) || 0);

				total_diawt = total_diawt + (parseFloat(row.find('.retag_dia_wt').val()) || 0);

				total_purity = total_purity + (parseFloat((parseFloat(row.find('.purity').val()) || 0) * (parseFloat(row.find('.retag_pcs').val()) || 0)).toFixed(3) || 0);

				total_amt = total_amt + (parseFloat(row.find('.retag_amount').val()) || 0);

			}



		});



		$('.total_pcs').val(parseInt(total_pcs));

		$('.total_gross_wt').val(parseFloat(total_gwt).toFixed(3));

		$('.total_net_wt').val(parseFloat(total_nwt).toFixed(3));

		$('.total_dia_wt').val(parseFloat(total_diawt).toFixed(3));

		$('.total_item_purity').val(parseFloat(total_purity).toFixed(3));

		$('.total_amount').val(parseFloat(total_amt).toFixed(3));

		$(".avg_purity_per").val(total_pcs > 0 ? (total_purity / total_pcs).toFixed(2) : "0.00");

	}



	else if(report_type==3) // Partly Sales

	{

		$("#partly_sale_list > tbody > tr").each(function ()

		{

			var row = $(this).closest('tr');

			if(row.find("input[name='tag_id[]']:checked").is(":checked"))

			{

				total_pcs = total_pcs + (parseFloat(row.find('.partial_sale_pcs').val()) || 0);

				total_gwt = total_gwt + (parseFloat(row.find('.partial_sale_gwt').val()) || 0);

				total_lwt = total_lwt + (parseFloat(row.find('.lwt').val()) || 0);

				total_nwt = total_nwt + (parseFloat(row.find('.partial_sale_nwt').val()) || 0);

				total_diawt = total_diawt + (parseFloat(row.find('.partial_dia_wt').val()) || 0);

				total_purity = total_purity + (parseFloat((parseFloat(row.find('.purity').val()) || 0) * (parseFloat(row.find('.partial_sale_pcs').val()) || 0)).toFixed(3) || 0);

				total_amt = total_amt + (parseFloat(row.find('.amount').val()) || 0);

			}



		});



		$('.total_pcs').val(parseInt(total_pcs));

		$('.total_gross_wt').val(parseFloat(total_gwt).toFixed(3));

		$('.total_net_wt').val(parseFloat(total_nwt).toFixed(3));

		$('.total_dia_wt').val(parseFloat(total_diawt).toFixed(3));

		$('.total_item_purity').val(parseFloat(total_purity).toFixed(3));

		$('.total_amount').val(parseFloat(total_amt).toFixed(3));

		$(".avg_purity_per").val(total_pcs > 0 ? (total_purity / total_pcs).toFixed(2) : "0.00");

	}



	else if(report_type==4)  // Old Metal

	{

		$('#old_metal_sale_list > tbody > tr').each(function()

		{

			var row = $(this).closest('tr');

			if(row.find("input[name='old_metal_sale_id[]']:checked").is(":checked"))

			{

				total_pcs = total_pcs + (parseFloat(row.find('.retag_pcs').val()) || 0);

				total_gwt = total_gwt + (parseFloat(row.find('.retaggross_wt').val()) || 0);

				total_lwt = total_lwt + (parseFloat(row.find('.lwt').val()) || 0);

				total_nwt = total_nwt + (parseFloat(row.find('.retag_net_wt').val()) || 0);

				total_diawt = total_diawt + (parseFloat(row.find('.retag_dia_wt').val()) || 0);

				total_purity = total_purity + (parseFloat((parseFloat(row.find('.purity').val()) || 0) * (parseFloat(row.find('.retag_pcs').val()) || 0)).toFixed(3) || 0);

				total_amt = total_amt + (parseFloat(row.find('.amount').val()) || 0);

			}



		});



		$('.total_pcs').val(parseInt(total_pcs));

		$('.total_gross_wt').val(parseFloat(total_gwt).toFixed(3));

		$('.total_net_wt').val(parseFloat(total_nwt).toFixed(3));

		$('.total_dia_wt').val(parseFloat(total_diawt).toFixed(3));

		$('.total_item_purity').val(parseFloat(total_purity).toFixed(3));

		$('.total_amount').val(parseFloat(total_amt).toFixed(3));

		$(".avg_purity_per").val(total_pcs > 0 ? (total_purity / total_pcs).toFixed(2) : "0.00");

	}



}





// BULK EDIT







function caculate_bulk_edit_purchase_sale_row(row){



	var tot_pcs = row.find(".tag_pcs").val();



	var net_wt = row.find(".tag_net_wt").val();



	var gross_wt = row.find(".tag_gross_wt").val();



	var total_charges = row.find(".charge_value").val();



	var other_metal_amount =row.find(".other_metal").val();



	var stone_price = row.find(".stone_price").val();



	var rate = row.find(".lot_rate").val();



	var ratecaltype = row.find(".lot_rate_calc_type").val();



	var purchase_touch = row.find(".lot_purchase_touch").val() ==''?0:row.find(".lot_purchase_touch").val();



	var karigar_calc_type = row.find(".lot_calc_type").val() ;



	var purchase_mc_type= row.find(".lot_mc_type").val() ;



	var purchase_mc = row.find(".lot_making_charge").val() == '' ? 0 : row.find(".lot_making_charge").val();



	var mc_value =  parseFloat(purchase_mc_type == 2 ? parseFloat(purchase_mc * gross_wt ) : parseFloat(purchase_mc * tot_pcs));



	var pur_wastage =  row.find(".lot_wastage_percentage").val() ==''?0:row.find(".lot_wastage_percentage").val();



	var calculation_type =  row.find(".calculation_based_on").val();



	var pur_tax_group =   row.find(".purchase_tgrp").val();



	var stone_details=row.find(".stone_details").val();



	var karigar_type=row.find(".karigar_type").val();



	var tax_type=row.find(".tax_type").val();





	var tax_group=row.find(".tgi_calculation").val();





	var purchase_cost = 0;



	var purchase_stone_price= 0;



	var purchase_stn_rate = 0;



	var tot_stone_wt = 0;



	//var mc_value =0;



	var tot_weight =0;



//	var purchase_stn = row.find(".pur_stones_details").val() != '' ? JSON.parse(row.find(".pur_stones_details").val()) : [];



	wastage_type = 1;



	if(stone_details!='')

	{

        var st_details=JSON.parse(stone_details);

        if(st_details.length>0)

        {

			stone_price = 0;

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



						 purchase_stone_price+=parseFloat(pitem.pur_cost);





                     }

                 });

             });

        }

    }



	stone_price = purchase_stone_price == 0 ? stone_price : purchase_stone_price;





	console.log('Stone Price',stone_price)

	// var tot_pcs = $('#tag_pcs').val();



	if(calculation_type==0)



    {



        var total_mc_value  = (purchase_mc_type==1 ? parseFloat(parseFloat(purchase_mc)*parseFloat(gross_wt)).toFixed(2) :parseFloat(parseFloat(purchase_mc)*parseFloat(tot_pcs)).toFixed(2));







    }



    else if(calculation_type==1)



    {







        var total_mc_value          = (purchase_mc_type==1 ? parseFloat(parseFloat(purchase_mc)*parseFloat(net_wt)).toFixed(2) :parseFloat(parseFloat(purchase_mc)*parseFloat(tot_pcs)).toFixed(2));







    }



    else if(calculation_type==2)



    {



        var total_mc_value          = (purchase_mc_type==1 ? parseFloat(parseFloat(purchase_mc)*parseFloat(gross_wt)).toFixed(2) :parseFloat(parseFloat(purchase_mc)*parseFloat(tot_pcs)).toFixed(2));





    }



	if(karigar_calc_type==1)



	{



		var purewt = parseFloat((parseFloat(net_wt) * (parseFloat(purchase_touch) + parseFloat(pur_wastage))) / 100);



	}else if(karigar_calc_type==2) //Net weight * touch



	{



		var purewt = parseFloat((parseFloat(net_wt) * (parseFloat(purchase_touch)/100)));



	}



	else if(karigar_calc_type==3) // ((net wt * 3%)*92%)



	{



		var touch_weight       = parseFloat((parseFloat(net_wt)*parseFloat(purchase_touch)/100)).toFixed(3);



		var wastage_touch      = parseFloat(parseFloat(touch_weight)*(parseFloat(pur_wastage))/100);



		var purewt             = parseFloat(parseFloat(touch_weight)+parseFloat(wastage_touch)).toFixed(3);



		// var touch_weight = parseFloat((parseFloat(pur_wastage) + parseFloat(purchase_touch)) / 100);



		// var purewt =  parseFloat(parseFloat(touch_weight)*parseFloat(net_wt)).toFixed(3);



	}







	if(ratecaltype==1) // Rate Calc By Grm(Wt)



	{



		purchase_cost   = parseFloat((parseFloat(purewt)*parseFloat(rate))+parseFloat(total_mc_value)+parseFloat(other_metal_amount) + parseFloat(total_charges)+parseFloat(stone_price)).toFixed(2);



	}else



	{



		purchase_cost   = parseFloat((parseFloat(tot_pcs)*parseFloat(rate))+parseFloat(total_mc_value)+parseFloat(other_metal_amount) + parseFloat(total_charges)+parseFloat(stone_price)).toFixed(2);



	}





	var purchase_cost_taxable =purchase_cost;

	var pur_total_tax_rate = 0;

	// tax_type = 1;



	 if(tax_type==1) // Inclusive of Tax



	 {



		 tax_details = calculate_inclusiveGST(purchase_cost,tax_group);



		 console.log(tax_details);



		 var total_tax_rate = tax_details;



		// var tax_percentage = tax_details['tax_percentage'];





	 }else



	 {



		 var tax_details     = calculate_base_value_tax(purchase_cost, tax_group);



		 var pur_total_tax_rate  = tax_details;



	//	 var tax_percentage  = tax_details['tax_percentage'];



		// item_cost           = parseFloat(parseFloat(item_cost)+parseFloat(total_tax_rate)).toFixed(2);

		 purchase_cost = parseFloat(parseFloat(purchase_cost)+parseFloat(pur_total_tax_rate)).toFixed(2);



	}









	// $("#tag_purchase_cost").val(purchase_cost);

	row.find(".tag_purchase_tax").val(pur_total_tax_rate);

	row.find(".tag_purchase_taxable").val(purchase_cost_taxable);





	row.find(".tag_purchase_cost").val(parseFloat(purchase_cost).toFixed(2));





	//$('.tax_type').html((tax_type!='' ? (tax_type==1 ? ' - Inclusive' :' - Exclusive') :''));



}







$(document).on('change', '.lot_purchase_touch,.lot_wastage_percentage,.lot_mc_type,.lot_making_charge,.lot_calc_type,.lot_rate_calc_type,.lot_rate', function (e) {

	var row = $(this).closest('tr');

	caculate_bulk_edit_purchase_sale_row(row);



});





$(document).on('click','.add_tag_lwt_pur',function(){

	var row = $(this).closest('tr');

	openStoneModal_bulk(row);

});



function openStoneModal_bulk(row){

	bulk_edit_stn_active = row;

	modalStoneDetail=JSON.parse(row.find('.stone_details').val());

	if(modalStoneDetail.length > 0){

		$('#cus_stoneModal_edit .modal-body').find('#estimation_stone_cus_item_details tbody').empty();

		$('#cus_stoneModal_edit').modal('show');

	$.each(modalStoneDetail, function (key, item) {

		console.log(item);

		if(item){

			create_new_empty_stone_item_bulk(item);

		}

	})

}else{

	$('#cus_stoneModal_edit .modal-body').find('#estimation_stone_cus_item_details tbody').empty();

	   $('#cus_stoneModal_edit').modal('show');

   if($('#cus_stoneModal_edit tbody >tr').length == 0)

   {

		create_new_empty_stone_item_bulk();

   }

}

}





function create_new_empty_stone_item_bulk(stn_data=[])

{

        var row='';



    	var stones_list = "<option value=''> -Select Stone- </option>";

    	var stones_type = "<option value=''>-Stone Type-</option>";

    	var uom_list = "<option value=''>-UOM-</option>";

    	var quality_list = "<option value=''>-Quality-</option>";

    	$.each(stones, function (pkey, pitem) {

    	    if($('#tag_lot_received_id option:selected').attr('data-lotfrom') == 2){

        	    $.each(current_po_details[0].stonedetail, function (spkey, spitem) {

        	        if(pitem.stone_id == spitem.po_stone_id){

        		        stones_list += "<option value='"+pitem.stone_id+"' "+(stn_data ? (pitem.stone_id == stn_data.stone_id ? 'selected' : '') : '')+">"+pitem.stone_name+"</option>";

        	        }

        	    });

    	    }else{

    	        stones_list += "<option value='"+pitem.stone_id+"' "+(stn_data ? (pitem.stone_id == stn_data.stone_id ? 'selected' : '') : '')+">"+pitem.stone_name+"</option>";



    	    }

    	});

    	$.each(uom_details, function (pkey, pitem) {

    		uom_list += "<option value='"+pitem.uom_id+"' "+(stn_data ? (pitem.uom_id == stn_data.stone_uom_id ? 'selected' : '') : '')+">"+pitem.uom_name+"</option>";

    	});

    	$.each(stone_types, function (pkey, pitem) {

    		stones_type += "<option value='"+pitem.id_stone_type+"' "+(stn_data ? (pitem.id_stone_type == stn_data.stones_type ? 'selected' : '') : '')+">"+pitem.stone_type+"</option>";

    	});

		$.each(quality_code, function (pkey, pitem) {

			quality_list += "<option value='"+pitem.quality_id+"' "+(stn_data ? (pitem.quality_id == stn_data.quality_id ? 'selected' : '') : '')+">"+pitem.code+"</option>";

    	});

    	var show_in_lwt = (stn_data ? stn_data.show_in_lwt : '');

    	var stone_pcs = (stn_data ? (stn_data.stone_pcs == undefined ? '':stn_data.stone_pcs) : '');

    	var stone_wt = (stn_data ? (stn_data.stone_wt == undefined ? '':stn_data.stone_wt) : '');

    	var rate = (stn_data ? (stn_data.stone_rate == undefined ? 0:stn_data.stone_rate): 0);

    	var price = (stn_data ? (stn_data.stone_price == undefined ? 0:stn_data.stone_price) : 0);

		var pur_stone_rate = (stn_data ? (stn_data.pur_rate == undefined ? 0:stn_data.pur_rate): 0);

    	var pur_stone_price = (stn_data ? (stn_data.pur_cost == undefined ? 0:stn_data.pur_cost) : 0);



    	var cal_type = (stn_data ? (stn_data.stone_cal_type == undefined ? 1:stn_data.stone_cal_type) : 1);

    	var row_cls = $('#estimation_stone_cus_item_details tbody tr').length;



            row='<tr id="'+$('#estimation_stone_cus_item_details tbody tr').length+'" class="st_'+$('#estimation_stone_cus_item_details tbody tr').length+'">'

                +'<td><select class="show_in_lwt form-control" name="est_stones_item[show_in_lwt][]" style="width:100px;"><option value="">-Select-</option><option value=1 '+(show_in_lwt==1 ? 'selected' :'')+'>Yes</option><option value=0 '+(show_in_lwt==0 ? 'selected' :'')+'>No</option></select></td>'

            	+'<td><select class="stones_type form-control" name="est_stones_item[stones_type][]" style="width:100px;">'+stones_type+'</select></td>'

    			+'<td><select class="stone_id form-control" name="est_stones_item[stone_id][]" style="width:100px;">'+stones_list+'</select><input type="hidden" class="stone_type" value=""></td>'

    			+'<td><select class="quality_id form-control" name="est_stones_item[quality_id][]" style="width:100px;">'+quality_list+'</select></td>'

    			+'<td><input type="number" class="stone_pcs form-control" name="est_stones_item[stone_pcs][]" value="'+stone_pcs+'" style="width: 60px;"/></td>'

    			+'<td><div class="input-group" style="width:159px;"><input class="stone_wt form-control" type="number" name="est_stones_item[stone_wt][]" value="'+stone_wt+'" style="width: 78px;"/><span class="input-group-btn" style="width: 138px;"><select class="stone_uom_id form-control" name="est_stones_item[uom_id][]">'+uom_list+'</select></span></div></td>'

    		    +'<td><div class="form-group" style="width: 100px;"><input class="stone_cal_type" type="radio" name="est_stones_item[cal_type]['+parseFloat(row_cls+1)+']" value="1" '+(cal_type == 1 ? 'checked' : '')+'>Wt&nbsp;<input type="radio" name="est_stones_item[cal_type]['+parseFloat(row_cls+1)+']" class="stone_cal_type" value="2" '+(cal_type == 2 ? 'checked' : '')+'>Pcs</div></td>'



    			+'<td><input type="number" class="stone_rate form-control" name="est_stones_item[stone_rate][]" value="'+rate+'" /></td>'

    			+'<td><input type="number" class="stone_price form-control" name="est_stones_item[stone_price][]" value="'+price+'" "/></td>'

				+'<td><input type="number" class="pur_stone_rate form-control" name="est_stones_item[pur_stone_rate][]" value="'+pur_stone_rate+'" /></td>'

    			+'<td><input type="number" class="pur_stone_price form-control" name="est_stones_item[pur_stone_price][]" value="'+pur_stone_price+'" "/></td>'

    			+'<td style="width: 100px;"><button type="button" class="btn btn-success btn-xs create_stone_item_details"><i class="fa fa-plus"></i></button><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-xs btn-del"><i class="fa fa-trash"></i></a></td></tr>';



    	$('#cus_stoneModal_edit .modal-body').find('#estimation_stone_cus_item_details tbody').append(row);



    	$("#cus_stoneModal_edit").on('shown.bs.modal', function(){

            $(this).find('.show_in_lwt').focus();

        });



    	$('#custom_active_id').val("st_" + row_cls);

}





$('#cus_stoneModal_edit  #update_tag_pur_edit_stone_details').on('click', function(){





	if(validateStoneCusItemDetailRow_edit())

    {

    	var stone_details=[];

    	var stone_price=0;

    	var certification_price=0;

    	var tag_less_wgt = 0;

    	modalStoneDetail = []; // Reset Old Value of stone modal





    	$('#cus_stoneModal_edit .modal-body #estimation_stone_cus_item_details> tbody  > tr').each(function(index, tr) {



			curRow = $(this);



    		stone_price+=parseFloat($(this).find('.stone_price').val());

    		if($(this).find('.show_in_lwt :selected').val() == 1){



				if($(this).find('.stone_uom_id').val()==6){

                    tag_less_wgt+=parseFloat($(this).find('.stone_wt').val()/5);

                }else{

                    tag_less_wgt+=parseFloat($(this).find('.stone_wt').val());

                }

    		}

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

    		            'stone_uom_name'      : $(this).find('.stone_uom_id :selected').text(),

    		            'stone_name'        : $(this).find('.stone_id :selected').text(),

						'pur_rate'        :$(this).find('.pur_stone_rate').val() == "" || $(this).find('.pur_stone_rate').val() == 0 ?$(this).find('.stone_rate').val(): $(this).find('.pur_stone_rate').val(),

						'pur_cost'        : $(this).find('.pur_stone_price').val() == "" || $(this).find('.pur_stone_price').val() == 0 ?$(this).find('.stone_price').val(): $(this).find('.pur_stone_price').val(),
						
    		            'quality_id'      : $(this).find('.quality_id').val(),

    		});

    	});

    	modalStoneDetail = stone_details;

        console.log(modalStoneDetail);



		curRow=bulk_edit_stn_active;



    	// $('#stone-det tbody').empty();

		var tag_gross_wt = curRow.find(".tag_gross_wt").val();



		var net_wt = parseFloat(tag_gross_wt) - parseFloat(tag_less_wgt);



		if(net_wt > 0){

			curRow.find(".tag_less_wt").val(parseFloat(tag_less_wgt).toFixed(3));

			curRow.find(".less_wt").html(parseFloat(tag_less_wgt).toFixed(3));

			curRow.find(".tag_net_wt").val(parseFloat(net_wt).toFixed(3));

			curRow.find(".net_wt").html(parseFloat(net_wt).toFixed(3));

			curRow.find(".stone_details").val(JSON.stringify(stone_details));

			curRow.find(".stone_price").val(stone_price);

			//calculateTagFormSaleValue();

			$('#cus_stoneModal_edit .modal-body').find('#estimation_stone_cus_item_details tbody').empty();

			$('#cus_stoneModal_edit').modal('hide');



			caculate_bulk_edit_purchase_sale_row(curRow);

		}else{

			alert('Invalid Net Weight');

		}





        // $('#tag_wast_perc').focus();

    }

    else

    {

    	alert('Please Fill The Required Details');

    }

});



$(document).on('input',".pur_stone_rate",function(){

    calculate_stone_amount();

});



$(document).on('input',".pur_stone_price",function(){

    var curRow = $(this).closest('tr');

    var stone_amt=0;

    var stone_pcs    = (isNaN(curRow.find('.stone_pcs').val()) || curRow.find('.stone_pcs').val() == '')  ? 0 : curRow.find('.stone_pcs').val();

    var stone_wt     = (isNaN(curRow.find('.stone_wt').val()) || curRow.find('.stone_wt').val() == '')  ? 0 : curRow.find('.stone_wt').val();

    var stone_price  = (isNaN(curRow.find('.pur_stone_price').val()) || curRow.find('.pur_stone_price').val() == '')  ? 0 : curRow.find('.pur_stone_price').val();

     if(curRow.find('input[type=radio]:checked').val() == 1)

     {

        stone_amt = parseFloat(parseFloat(stone_price) / parseFloat(stone_wt)).toFixed(2);

     }

     else

     {

       stone_amt = parseFloat(parseInt(stone_price) / parseFloat(stone_pcs )).toFixed(2);

     }

     curRow.find('.pur_stone_rate').val(stone_amt);

});



$(document).on('click', '.save_edit_tag', function (e) {

	var curRow = $(this).closest('tr');
	var bulk_edit_options = $('#bulk_edit_options').val();

	// Validate that a field to update is selected
	if(!bulk_edit_options || bulk_edit_options == '' || bulk_edit_options == '--Select--') {
		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please select a field to update"});
		return;
	}

	if(bulk_edit_options == 11) {
		var blk_huid_val = $.trim($('#blk_huid').val());
		var blk_huid2_val = $.trim($('#blk_huid2').val());
		if(blk_huid_val == '' && blk_huid2_val == '') {
			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please enter at least one HUID value"});
			return;
		}

		if(blk_huid_val != '' && blk_huid2_val != '' && blk_huid_val == blk_huid2_val) {
			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>HUID 1 and HUID 2 cannot be the same"});
			return;
		}

		var allHuids = [];
		if(blk_huid_val != '') allHuids.push(blk_huid_val);
		if(blk_huid2_val != '') allHuids.push(blk_huid2_val);

		try {
			var modalHuids = JSON.parse($('#other_huid_details').val());
			if(modalHuids && modalHuids.length > 0) {
				$.each(modalHuids, function(i, item) {
					var h = $.trim(typeof item === 'object' ? (item.huid || '') : item);
					if(h != '') allHuids.push(h);
				});
			}
		} catch(e) {}

		var seen = {};
		var localDuplicate = false;
		$.each(allHuids, function(i, h) {
			if(seen[h]) {
				localDuplicate = true;
				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Duplicate HUID '" + h + "' found in your entries"});
				return false; // break
			}
			seen[h] = true;
		});
		if(localDuplicate) return;

		if(!curRow.find('input[name="tag_id[]"]').is(':checked')) {
			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please select the row first"});
			return;
		}

		var duplicateFound = false;

		$.each(allHuids, function(idx, huidVal) {
			$.ajax({
				url: base_url + 'index.php/admin_ret_tagging/get_prev_huid',
				type: 'POST',
				data: { 'huid': huidVal },
				dataType: 'JSON',
				async: false,
				success: function(data) {
					if(data && data.length > 0) {
						duplicateFound = true;
						$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>HUID '" + huidVal + "' already exists!"});
					}
				}
			});
		});

		if(duplicateFound) {
			return;
		}

		let bulk_edit_validate = validate_bulk_edit();
		if(!bulk_edit_validate) {
			return;
		}

		$("#bulk_edit").trigger('click');
		$('#blk_huid').val('');
		$('#blk_huid2').val('');
		$('#other_huid_details').val('[]');
		return;
	}

	// For Purchase Cost (option 14), use the dedicated purchase cost save path
	if(bulk_edit_options == 14) {
		caculate_bulk_edit_purchase_sale_row(curRow);
		if(validate_purchase_cost(curRow)){
			var data = {
				'gross_wt' 				 : curRow.find('.tag_gross_wt').val(),
				'net_wt'				 : curRow.find('.tag_net_wt').val(),
				'less_wt' 				 : curRow.find('.tag_less_wt').val(),
				'lot_calc_type' 		 : curRow.find('.lot_calc_type').val(),
				'lot_wastage_percentage' : curRow.find('.lot_wastage_percentage').val(),
				'lot_mc_type'       	 : curRow.find('.lot_mc_type').val(),
				'lot_making_charge' 	 : curRow.find('.lot_making_charge').val(),
				'lot_purchase_touch'	 : curRow.find('.lot_purchase_touch').val(),
				'lot_rate' 				 : curRow.find('.lot_rate').val(),
				'lot_rate_calc_type' 	 : curRow.find('.lot_rate_calc_type').val(),
				'tag_purchase_cost'		 : curRow.find('.tag_purchase_cost').val(),
				'tag_purchase_tax'		 : curRow.find('.tag_purchase_tax').val(),
				'tag_purchase_taxable'	 : curRow.find('.tag_purchase_taxable').val(),
				'stone_details'		     : JSON.parse(curRow.find('.stone_details').val()),
				'tag_id'		     	 : curRow.find('.tagid').val(),
				'sales_value'		     : curRow.find('.tag_purchase_cost').val(),
			};
			my_Date = new Date();
			$.ajax({
				url: base_url+'index.php/admin_ret_tagging/update_purchase_cost/?nocache=' + my_Date.getUTCSeconds(),
				dataType: "json",
				method: "POST",
				data: data,
				success: function ( data ) {
					if(data.status){
						$.toaster({ priority : 'success', title : 'Success!', message : ''+"</br>"+data.message});
						$('#get_tag_details').trigger('click');
					}else{
						$.toaster({ priority : 'danger', title : 'Error!', message : ''+"</br>"+data.message});
					}
				}
			});
		}
		return;
	}

	var allCheckboxes = $("input[name='tag_id[]']");
	var previousStates = [];
	allCheckboxes.each(function() {
		previousStates.push($(this).is(':checked'));
	});

	// Uncheck all, check only this row
	allCheckboxes.prop('checked', false);
	curRow.find("input[name='tag_id[]']").prop('checked', true);

	// Validate the edit fields
	let bulk_edit_validate = validate_bulk_edit();
	if(!bulk_edit_validate) {
		// Restore checkbox states
		allCheckboxes.each(function(idx) {
			$(this).prop('checked', previousStates[idx]);
		});
		return;
	}

	$("#bulk_edit").trigger('click');

});

$(document).on('change', 'input[name="tag_id[]"]', function() {
	var bulk_edit_options = $('#bulk_edit_options').val();
	if(bulk_edit_options != 11) return;

	var allCheckboxes = $('input[name="tag_id[]"]');
	var allSaveButtons = $('.save_edit_tag');

	if($(this).is(':checked')) {
		allCheckboxes.not(this).prop('disabled', true);
		allSaveButtons.prop('disabled', true);
		$(this).closest('tr').find('.save_edit_tag').prop('disabled', false);
	} else {
		allCheckboxes.prop('disabled', false);
		allSaveButtons.prop('disabled', false);
	}
});



function validate_purchase_cost(curRow){



	if(curRow.find('.lot_calc_type').val()== ""){



		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br> Select the Purchase Type"});

		curRow.find('.lot_calc_type').focus();

		return false;



	}else if(curRow.find('.lot_wastage_percentage').val()== ""){



		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br> Enter Purchase Wastage"});

		curRow.find('.lot_wastage_percentage').focus();

		return false;



	}else if(curRow.find('.lot_making_charge').val()== ""){



		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br> Enter Purchase MC "});

		curRow.find('.lot_making_charge').focus();

		return false;



	}

	else if(curRow.find('.lot_mc_type').val()== ""){



		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br> Enter Purchase MC Type"});

		curRow.find('.lot_mc_type').focus();

		return false;



	}

	else if(curRow.find('.lot_rate').val()== ""){



		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br> Enter Purchase Rate"});

		curRow.find('.lot_rate').focus();

		return false;



	}

	else if(curRow.find('.lot_rate_calc_type').val()== ""){



		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br> Enter Purchase Rate Type"});

		curRow.find('.lot_rate_calc_type').focus();

		return false;



	}else if(curRow.find('.tag_purchase_cost').val()== ""){



		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br> Enter Tag Purchase Cost"});

		curRow.find('.tag_purchase_cost').focus();

		return false;



	}

	return true;

}



$(document).on('click', '.create_stone_item_details', function (e) {

	if(validateStoneCusItemDetailRow_edit()){

			create_new_empty_stone_item_bulk();

	   }else{

		   alert("Please fill required stone fields");

	   }

});



function validateStoneCusItemDetailRow_edit(){

	var row_validate = true;

	$('#cus_stoneModal_edit .modal-body #estimation_stone_cus_item_details> tbody  > tr').each(function(index, tr) {

		if($(this).find('.stone_id').val() == "" || $(this).find('.stone_pcs').val() == "" || $(this).find('.stone_wt').val() == "" || $(this).find('.stone_rate').val() == "" || $(this).find('.stone_price').val() == "" || $(this).find('.stone_uom_id').val() == "" ){

			row_validate = false;

		}

	});

	return row_validate;

}





$(document).on('input',".pur_stone_rate",function(){

	var curRow = $(this).closest('tr');

    var stone_amt=0;

    var stone_pcs    = (isNaN(curRow.find('.stone_pcs').val()) || curRow.find('.stone_pcs').val() == '')  ? 0 : curRow.find('.stone_pcs').val();

    var stone_wt     = (isNaN(curRow.find('.stone_wt').val()) || curRow.find('.stone_wt').val() == '')  ? 0 : curRow.find('.stone_wt').val();

    var stone_rate  = (isNaN(curRow.find('.pur_stone_rate').val()) || curRow.find('.pur_stone_rate').val() == '')  ? 0 : curRow.find('.pur_stone_rate').val();

	if(curRow.find('input[type=radio]:checked').val() == 1){

		stone_amt = parseFloat(parseFloat(stone_wt)*parseFloat(stone_rate)).toFixed(2);

	 }else{

	   stone_amt = parseFloat(parseInt(stone_pcs)*parseFloat(stone_rate)).toFixed(2);

	 }

     curRow.find('.pur_stone_price').val(stone_amt);

});



$(document).on('input',".pur_stone_price",function(){

    var curRow = $(this).closest('tr');

    var stone_amt=0;

    var stone_pcs    = (isNaN(curRow.find('.stone_pcs').val()) || curRow.find('.stone_pcs').val() == '')  ? 0 : curRow.find('.stone_pcs').val();

    var stone_wt     = (isNaN(curRow.find('.stone_wt').val()) || curRow.find('.stone_wt').val() == '')  ? 0 : curRow.find('.stone_wt').val();

    var stone_price  = (isNaN(curRow.find('.pur_stone_price').val()) || curRow.find('.pur_stone_price').val() == '')  ? 0 : curRow.find('.pur_stone_price').val();

     if(curRow.find('input[type=radio]:checked').val() == 1)

     {

        stone_amt = parseFloat(parseFloat(stone_price) / parseFloat(stone_wt)).toFixed(2);

     }

     else

     {

       stone_amt = parseFloat(parseInt(stone_price) / parseFloat(stone_pcs )).toFixed(2);

     }

     curRow.find('.pur_stone_rate').val(stone_amt);

});





function fntaggedItemsExcelReport(export_type)

{

	function s2ab(s) {

		var buf = new ArrayBuffer(s.length);

		var view = new Uint8Array(buf);

		for (var i = 0; i < s.length; i++) view[i] = s.charCodeAt(i) & 0xff;

		return buf;

	}





	    htmls = ` <table id="itemwise-sales_detail" class="table table-bordered table-striped text-center" style="text-transform:uppercase;">



		<thead>

		<tr>

			<th>Tag ID</th>

			<th>Tag Code</th>

			<th>Old Tag Code</th>

			<th>Product</th>

			<th>Design</th>

			<th>Sub Design</th>

			<th>Tag Date</th>

			<th>Pcs</th>

			<th>GWT(g)</th>

			<th>LWT(g)</th>

			<th>NWT(g)</th>

			<th>Wastage(%)</th>

			<th>MC Type</th>

			<th>MC value</th>

			<th>Purchase Type</th>

			<th>Purchase Wastage(%)</th>

			<th>Purchase MC Type</th>

			<th>Purchase MC value</th>

			<th>Purchase Touch</th>

			<th>Purchase Rate</th>

			<th>Tag Purchase Cost</th>

			<th>Amount</th>

		</tr>

	</thead><tbody>`





	   $.each(export_data,function(key,items) {





		htmls += `<tr>

			<td>`+items.tag_id+`</td>

			<td>`+items.tag_code+`</td>

			<td>`+items.old_tag_id+`</td>

			<td>`+items.product_name+`</td>

			<td>`+items.design_name+`</td>

			<td>`+items.sub_design_name+`</td>

			<td>`+items.tag_datetime+`</td>

			<td>`+items.piece+`</td>

			<td>`+items.gross_wt+`</td>

			<td>`+items.less_wt+`</td>

			<td>`+items.net_wt+`</td>

			<td>`+items.retail_max_wastage_percent+`</td>

			<td>`+items.mc_type+`</td>

			<td>`+items.tag_mc_value+`</td>

			<td>`+items.lot_calc_type_name+`</td>

			<td>`+items.lot_wastage_percentage+`</td>

			<td>`+items.lot_mc_type_name+`</td>

			<td>`+items.lot_making_charge+`</td>

			<td>`+items.lot_purchase_touch+`</td>

			<td>`+items.lot_rate_name+`</td>

			<td>`+items.tag_purchase_cost+`</td>

			<td>`+items.sales_value+`</td>

		</tr>`



	   });







	   htmls+='</tbody><tfoot></tfoot></table>';



	   var table = document.createElement('table');

	   table.innerHTML = htmls;



	   var wb = XLSX.utils.table_to_book(table);



	   var wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'binary' });



	   var blob = new Blob([s2ab(wbout)], { type: 'application/octet-stream' });

	   var link = document.createElement('a');

	   link.href = window.URL.createObjectURL(blob);

	   link.download = 'Bulk_tag_edit.xlsx';

	   link.click();



}
function get_daterange(){
	var date = new Date();
			var firstDay = new Date(date.getFullYear(), date.getMonth(), date.getDate() - 0, 1);
			var from_date = (firstDay.getDate() + "-" + (firstDay.getMonth() + 1) + "-" + firstDay.getFullYear());
			var to_date = (date.getDate() + "-" + (date.getMonth() + 1) + "-" + date.getFullYear());
			$('#rpt_from_date').html(from_date);
			$('#rpt_to_date').html(to_date);
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
					startDate: moment().subtract(0, 'days'),
					endDate: moment()
				},
				function (start, end) {
					// $('#reportrange').html(start.format('D/M/YYYY') + ' - ' + end.format('D/M/YYYY'));
					$('#rpt_date_picker').html(start.format('D/M/YYYY') + ' - ' + end.format('D/M/YYYY'));
					$('#rpt_from_date').html(start.format('DD-MM-YYYY'));
					$('#rpt_to_date').html(end.format('DD-MM-YYYY'));
				}
			);
}
function ret_duplicate_print_log_save(dupPrintLog){

	console.log(dupPrintLog);
	$.ajax({
		url			: base_url + 'index.php/admin_ret_tagging/ret_duplicate_print_log_save?nocache=' + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),
		type		: 'POST',
		data		: { arrayData : JSON.stringify(dupPrintLog)},
		dataType 	: 'JSON',
		success 	: function (){
			console.log('Printer log saved')
		}, 
		error: function () {
			console.log('Error while saving printer log');
		}
	})
}
$('#tagging_list').on('click', '.less_wt', function (e) {
  e.preventDefault();


  let stoneData = $(this).closest("tr").find('.stone_details').val();
  let stoneDetails = JSON.parse(stoneData);

  if (!stoneDetails.length) {
		$('#tag_stone_details tbody').empty();
		$('#tag_stone_details tfoot').empty();
		$('#tag_stone_details tbody').append(
		'<tr><td colspan="13" class="text-center">No stone details available.</td></tr>'
		);
	}
  else {
  
    $('#tag_stone_details tbody').empty();
    $('#tag_stone_details tfoot').empty();

    let trHtml = '';

	let total_pcs = 0;
	let total_wt = 0;  
	let total_amt = 0;

    stoneDetails.forEach(function (stone) {

        let cut = '';
        let color = '';
        let clarity = '';
        let shape = '';


        let selected_stone_name = '';
        if (stone && stone.stone_id) {
            let sItem = stones.find(s => s.stone_id == stone.stone_id);
            if (sItem) selected_stone_name = sItem.stone_name;
        }

    
        let selected_uom = '';
        if (stone && stone.uom_id) {
            let uomItem = uom_details.find(u => u.uom_id == stone.uom_id);
            if (uomItem) selected_uom = uomItem.uom_name;
        }

    
        let selected_stone_type = '';
        if (stone && stone.stone_type) {
            let typeItem = stone_types.find(t => t.id_stone_type == stone.stone_type);
            if (typeItem) selected_stone_type = typeItem.stone_type;
        }

     
        let selected_quality = '';
        if (stone && stone.stone_quality_id) {
            let qItem = quality_code.find(q => q.quality_id == stone.stone_quality_id);
            if (qItem) {
                selected_quality = qItem.code;
                clarity = qItem.clarity;
                color = qItem.color;
                cut = qItem.cut;
                shape = qItem.shape;
            }
        }

		total_pcs += parseFloat(stone.pieces || 0);
    	total_amt += parseFloat(stone.amount || 0);
		let wtInGrams = 0;
		if (stone.uom_id == 6) {
			wtInGrams = parseFloat(stone.wt || 0) / 5;
		} else  {
			wtInGrams = parseFloat(stone.wt || 0);
		} 
		total_wt += wtInGrams;

      trHtml += '<tr>'
            + '<td><input type="text" class="form-control"  style="width:70px;" value="'+ (stone.is_apply_in_lwt==1 ? 'Yes' : stone.is_apply_in_lwt==0 ? 'No' : '-Select-') +'" readonly></td>'
            + '<td><input type="text" class="form-control stn_type" style="width:100px;" value="' + (selected_stone_type || '') + '" readonly></td>'
            + '<td><input type="text" class="form-control stn_name" style="width:100px;"value="' + (selected_stone_name || '') + '" readonly></td>'
            + '<td><input type="text" class="form-control stn_quality"  style="width:80px;" value="' + (selected_quality || '') + '" readonly></td>'
            + '<td><input type="number" class="form-control stn_pcs"  style="width:70px;" value="' + (stone.pieces || '') + '" readonly></td>'
			+ '<td>'
				+ '<input type="text" class="form-control" style="width:100px; display:inline-block; " value="'+(stone.wt || '')+'" readonly>'
				+ '<input type="text" class="form-control" style="width:55px; display:inline-block; " value="'+(selected_uom || '')+'" readonly>'
			+ '</td>'
            + '<td><div class="form-group" style="width: 100px;">'
                + '<input type="radio" disabled ' + (stone.stone_cal_type == 1 ? 'checked' : '') + '>Wt&nbsp;'
                + '<input type="radio" disabled ' + (stone.stone_cal_type == 2 ? 'checked' : '') + '>Pcs'
            + '</div></td>'
            + '<td><span class="stone_cut">'+(cut || '')+'</span></td>'
            + '<td><span class="stone_color">'+(color || '')+'</span></td>'
            + '<td><span class="stone_clarity">'+(clarity || '')+'</span></td>'
            + '<td><span class="stone_shape">'+(shape || '')+'</span></td>'
            + '<td><input type="number" class="form-control stone_rate" style="width: 120px" value="' + (stone.rate_per_gram || '') + '" readonly></td>'
            + '<td><input type="number" class="form-control stone_amt" style="width: 120px" value="' + (stone.amount || '') + '" readonly></td>'
        + '</tr>';
    });

    $('#tag_stone_details tbody').append(trHtml);

	let tfootHtml = '<tr style="font-weight:bold; font-size:15px;">'
    + '<td>Total:</td>'
    + '<td></td>'
    + '<td></td>'
    + '<td></td>'
    + '<td class="stn_tot_pcs">'+ total_pcs +'</td>'
    + '<td class="stn_tot_weight">'+ total_wt.toFixed(3) +'</td>'
    + '<td></td>'
    + '<td></td>'
    + '<td></td>'
    + '<td></td>'
    + '<td></td>'
    + '<td></td>'
    + '<td class="stn_tot_amount">'+ total_amt.toFixed(2) +'</td>'
    + '</tr>';

$('#tag_stone_details tfoot').append(tfootHtml);
  }

  $('#lessWtModal').modal('show');
});

$(document).ready(function() {
   
    // $('#tag_lt_prod').on('change', function() {
    //     var touch = $('option:selected', this).attr('data-touch');
    //     if(touch) {
    //         $('#purchase_touch').val(touch);
    //     } else {
    //          $('#purchase_touch').val('');
    //     }
    // });

    $('#sub_des_select').select2();
    $('#sub_des_select').on('select2:open', function() {
        $('.select2-search__field').on('input', function() {
            this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
        });
    });
});

