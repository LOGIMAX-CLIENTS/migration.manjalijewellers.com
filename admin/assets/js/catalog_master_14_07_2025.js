// const { accessSync } = require("fs");

var path =  url_params();

var ctrl_page = path.route.split('/');

var tax_det=[];

var mat_det=[];

var wt_data=[];

var ret_master_data = [];

var ret_selected_category = [];

var ret_selected_product = [];

var ret_pro_sellecgted = [];

var ret_selected_design = [];

var img_resource=[];

var pre_img_files=[];

var pre_img_resource=[];

var total_files=[];

var kar_charges_list=[];

var prod_details=[];

var charges_list = [];

var kyc_details=[];

var stone_types=[];

var uom_details=[];

var document_resource=[];

var document_files=[];

var cat_details = [];

var loosestone_product = [];

var reorder_product =[];

var reorderitems_weight =[];

var reorder_branch=[];

var reorderitems_sub_design=[];

var section_details=[];

var reorder_size =[];

$(document).ready(function() {

    //console.log(ctrl_page[1]);

    $(document).on('keypress',"input[type='number']",function (event){

         if ((event.which != 46 || $(this).val().indexOf('.') != -1) &&

    	  ((event.which < 48 || event.which > 57) &&

    		(event.which != 0 && event.which != 8))) {

    	  event.preventDefault();

    	}

     });

	 $('#wc_detail').on('change', 'input.from_weight, input.to_weight, input.wc_min, input.wc_min_wt, input.wastage, input.wc_wt, input.mcrg_min, input.making_charge', function() {

        validate_weight_range_row(); // Call the validation function

    });

     $(document).on('keydown',"input[type='number']",function (event){

         if(event.which == 40 || event.which == 38 || event.which == 37 || event.which == 39 )

         {

             event.preventDefault();

         }

     });

    if(ctrl_page['0']=='admin_ret_catalog' && ctrl_page['1']=='shape' && ctrl_page['2']=='list')

    {

         set_shape_table();

    }

     if(ctrl_page['0']=='admin_ret_catalog' && ctrl_page['1']=='diamond' && ctrl_page['2']=='list')

    {

         set_quality_table();

    }

    if(ctrl_page['0']=='admin_ret_catalog' && ctrl_page['1']=='diamond' && ctrl_page['2']=='add')

    {

		 checkcode();

		 get_Activeclarity();

		 get_Activecolor();

		 get_Activecut();

		 get_Activeshape();

    }

    if(ctrl_page['0']=='admin_ret_catalog' && ctrl_page['1']=='diamond' && ctrl_page['2']=='edit')

    {

		 get_Activeclarity();

		 get_Activecolor();

		 get_Activecut();

		 get_Activeshape();

    }

	if(ctrl_page[1]=='karigar' && (ctrl_page[2]=='add'|| ctrl_page[2]=='edit'))

	{

		   Webcam.set({

			width: 290,

			height: 190,

			image_format: 'jpg',

			jpeg_quality: 90

		});

        Webcam.attach( '#my_camera' );

		Webcam.on('error', function(err) {

			console.log('Error accessing webcam:', err);

		});

	}

	 $("#status,.status,.switch").bootstrapSwitch();

     switch(ctrl_page[1]){ // Retail masters

		case 'branch_floor':

						set_floor_table();

					break;

		case 'day_close':

			set_dayclose_table();

		break;

		case 'web_devices':

			get_web_device_list();

			get_ActiveCounter();

			//get_ActiveBranchFloor();

		break;

		case 'floor_counter':

				        set_branchfloor_table();

						get_Activefloors();

	 				break;

		case 'making_type':

						set_makingtype_table();

					break;

		case 'theme':

						set_theme_table();

					break;

		case 'material':

						set_material_table();

					break;

		case 'stone':

						set_stone_table();

						get_uomlist();

					break;

		case 'uom':

						set_uom_table();

					break;

		case 'weight':

						set_weight_table();

						get_Activeproduct('');

						getActive_uom();

						get_weight_range_product('');

						$("#units,#ed_units").select2(

                        {

                        		placeholder:"Select Units",

                        		allowClear: true

                        });

                        	$("#weight_design,#ed_wt_range_des").select2(

                        	{

                        		placeholder:"Select Design",

                        		allowClear: true

                        	});

                        	$("#weight_sub_design,#ed_wt_range_sub_des").select2(

                            {

                            	placeholder:"Select Sub Design",

                            	allowClear: true

                            });

							if(ctrl_page[2] == 'Edit')

							{

						get_weight_edit_data(ctrl_page[3]);

							}

		   break;

		case 'category':

						set_category_table();

						get_ActiveMetal();

						get_ActivePurity();

						get_taxgroup();

					break;

		case 'karigar':

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

		                if(ctrl_page[2]=='add' || ctrl_page[2]=='edit')

		                {

		                    $("#karigar_for").select2(

                        	{

                        		placeholder:"Select Units",

                        		allowClear: true

                        	});

							$("#karigar_calc_type").select2(

							{

								placeholder:"Select Calc Type",

								allowClear: true

							});

							get_ActiveCategory();

							get_country();

							get_active_charges();

							get_ActiveKYC();

							get_ActiveStoneType();

							getActive_uom();

							getActive_quality_code();

		                }

		                if(ctrl_page[2]=='edit')

		                {

		                    get_karigar_product_details(ctrl_page[3]);

							get_stone_details(ctrl_page[3]);

							get_kyc_details(ctrl_page[3]);

							$('.vendor_det').show();

		                }

		                get_Activeproduct('');

		                if(ctrl_page[2]=='list')

		                {

		                    $("#karigar_for").select2(

                        	{

                        		placeholder:"Karigar For",

                        		allowClear: true

                        	});

                        	$("#karigar_type").select2(

                        	{

                        		placeholder:"Karigar Type",

                        		allowClear: true

                        	});

		                    set_karigar_table();

		                }

                        $("input[name='karigar[is_tds]']:radio").on('change',function(){

                            $('#tds_tax').prop("disabled",true);

							$('#tds_tax').val("");

                            if(this.value==1)

                            {

                                $('#tds_tax').val();

                                $('#tds_tax').prop("disabled",false);

                            }

                        });

                        $("input[name='karigar[is_tcs]']:radio").on('change',function(){

                            $('#tcs').prop("disabled",true);

							$('#tcs').val("");

                            if(this.value==1)

                            {

                                $('#tcs').val();

                                $('#tcs').prop("disabled",false);

                            }

                        });

						$("#select_product").select2(

						{

							placeholder:"Select Product",

							allowClear: true

						});

						$("#des_select").select2(

						{

							placeholder:"Select Design",

							allowClear: true

						});

						$("#select_sub_design").select2(

						{

							placeholder:"Select Sub Design",

							allowClear: true

						});

						if($('#user_type').val()==0)

						{

						    $('.registered_type').css('display','none');

						    $('#company_karigar').removeAttr("required");

						    $('#gst_number_karigar').removeAttr("required");

						    $('#pan').removeAttr("required");

						}else

						{

						    $('.registered_type').css('display','block');

						    $('#company_karigar').attr("required", "true");

						    $('#gst_number_karigar').attr("required", "true");

						    $('#pan').attr("required", "true");

						}

						$(document).on('click','.add_charges,.add_karigar_charges',function()

						{

							add_karigar_charges();

						});

						$(document).on('click', '.remove_karigar_charges', function() {

							remove_karigar_charges($(this));

						});

		break;

		case 'attribute':

						let cur_page = ctrl_page[2];

						set_attribute_table();

						if(cur_page == "add") {

							add_attribute();

						} else if(cur_page == "edit") {

							add_attribute_buttons();

						}

						$(document).on('click', '.add_attribute, .add_attributes', function() {

							add_attribute();

						});

						$(document).on('click', '.remove_attribute', function() {

							remove_attribute($(this));

						});

					break;

		case 'material_rate':

						set_materialrate_table();

						get_ActiveMaterial();

						get_materialSelLst();

						$('#add_material_rate').prop('disabled',true);

							$(window).scroll(function() {

							var height = $(window).scrollTop();

							if(height  > 300) {

							$(".stickyBlk").css({"position": "fixed"});

							} else{

							$(".stickyBlk").css({"position": "static"});

							}

						});

					break;

		case 'tag':

						set_tag_table();

					break;

		case 'tax':

						set_tax_table();

					break;

		case 'ret_product':

						set_ret_product_table();

						get_ActiveMetal();

						get_ActiveUOM();

						get_taxgroup();

						$("#product_section_select,#section_sel").select2(

                        {

                            placeholder:"Select section",

                            allowClear: true

                        });

                        $('#section_sel').on('change',function(){

                            if(this.value!=''){

                                $("#id_section").val(this.value);

                            }else{

                                $("#id_section").val('');

                            }

                        });

                        // getRetailSections();

						get_product_RetailSections();

                        getActiveSections();

						get_ActiveCharges();

						$(document).on('change', '.charges_product', function() {

							if($(this).val() > 0) {

								getChargesForProduct($(this));

							} else {

								let parent_class = $(this).parent().parent().parent();

								$(parent_class).find(".prod_charge_value").empty();

								$(parent_class).find(".prod_charge_value").select2("val",'');

							}

						});

						$(document).on('change', '.prod_charge_name', function (e) {

							var row = $(this).closest('.row_prod_charges');

							add_prod_charges_value(row);

						});

						$(document).on('click', '.addProdCharge, .add_prod_charge', function() {

							add_product_charges();

						});

						$(document).on('click', '.removeProdCharge', function() {

							var row = $(this).closest('.row_prod_charges');

							remove_product_charges(row);

						});

					break;

		case 'bulkprodupdated':

						set_bulkprodupdated_table();

						get_taxgroup();

						get_Activeproduct('');

					break;

		case 'ret_sub_product':

						set_ret_sub_product_table();

						get_ActiveMetal();

						get_taxgroup();

					break;

		case 'tgrp':

						set_tgrp_table();

						get_activeTax();

					break;

		case 'metal':

						set_metal_table();

						get_taxgroup();

					break;

		case 'screw':

						set_screw_table();

					break;

		case 'hook':

						set_hook_table();

					break;

		case 'ret_design':

                    if(ctrl_page[2]='list')

		              {

		               set_design_table();

		              }

		              if(ctrl_page[2]='add')

		              {

		               get_Activeproduct();

		               get_ActiveTheme();

					   get_ActiveKarigar();

					   get_hooktype();

					   get_ScrewType();

					   get_ActivePurity();

					   get_ActiveMaterial();

					   get_ActiveUOM();

					   get_Activesize();

		              }

		              if(ctrl_page[2]='bulk_edit')

		              {

		                get_ActiveRetMasters();

		                $("#select_sub_design").select2(

                		{

                			placeholder:"Select Sub Design",

                			allowClear: true

                		});

		              }

		               get_ActiveCategory();

					   $(document).on("click","input[name='mc_va_attr_update']",function() {

							let mc_va_attr_value = $(this).val();

							if(mc_va_attr_value == 1) {

								$("#attribute_block").css("display","none");

								$(".mcva_filters").css("display","block");

								$("#mc_va_block").css("display","block");

							} else {

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

						$(document).on("click","#add_des_attribute",function() {

							add_des_attribute();

						});

						$(document).on('click', '.remove_des_attribute', function() {

							remove_des_attribute($(this));

						});

						$(document).on('change', '.des_upd_attr_name',function() {

							if($(this).val() > 0) {

								get_attributeValues($(this));

							} else {

								let _parent = $(this).closest('tr');

								$(_parent).find(".des_upd_attr_value").empty();

								$(_parent).find(".des_upd_attr_value").select2("val",'');

							}

						});

						$(document).on('click', '.delete_design_weight_range', function(e) {

							e.preventDefault();

							let delete_href = $(this).attr('data-href');

							let parent_id = $(this).data('parent');

							let del_status = delete_design_settings(delete_href);

							if(del_status) {

								let des_attr_rows = $("#wcdetail tbody tr").length;

								if(des_attr_rows == 1) {

									$(this).closest('tr').remove();

									$("#edit_mas_design_list tbody tr").find(`[data-id='${parent_id}'].btn-wc-view`).remove();

									$('#wastage').modal('hide');

								} else {

									$(this).closest('tr').remove();

								}

							}

						});

						$(document).on('click', '.delete_design_attribute', function(e) {

							e.preventDefault();

							let delete_href = $(this).attr('data-href');

							let parent_id = $(this).data('parent');

							let del_status = delete_design_settings(delete_href);

							if(del_status) {

								let des_attr_rows = $("#des_attribute_detail tbody tr").length;

								if(des_attr_rows == 1) {

									$(this).closest('tr').remove();

									$("#edit_mas_design_list tbody tr").find(`[data-id='${parent_id}'].btn-attribute-view`).remove();

									$('#des_attributes').modal('hide');

								} else {

									$(this).closest('tr').remove();

								}

							}

						});

					break;

			case 'ret_sub_design' :

		        get_sub_design_details();

		        get_ActiveSubDesigns();

		        if(ctrl_page[2]=='edit')

		        {

		            getsub_design_images();

		        }

		    break;

		case 'ret_products_mapping' :

	        get_product_mapping_details();

	        get_Activeproduct();

	        get_ActiveDesign();

	    break;

	    case 'ret_subdesign_mapping' :

	        get_sub_design_mapping_details();

	        get_Activeproduct();

	        get_ActiveDesign();

	        get_SubDesigns();

	        get_ActiveKarigar();

	    break;

		case 'financial_year':

		               get_financial_year_list();

		break;

		case 'karigar_approval':

			//get_ActiveKarigar();

			getActive_uom();

			getActive_quality_code();

			get_WastApprvKarigars()

			setTimeout(function(){get_KarigarWastages()

			},1000);

			$('#karigar_sel').select2({

				placeholder:'Select Karigar',

				allowClear:true

			});

			get_active_charges();

		break;

		case 'reorder_settings':

    		switch(ctrl_page[2])

    		{

    			case 'list':

    		        get_reorder_settings();

                    get_Activeproduct('');

                    get_Activesize();

					getRetailSections();

                    $('#ret_design').select2();

                    $('#ret_sub_design').select2();

                    $('#weight_range').select2();

					$("#section_sel").select2(

                        {

                            placeholder:"Select section",

                            allowClear: true

                        });

                    $("#ret_design").select2(

                    {

                    placeholder:"Select Design",

                    allowClear: true

                    });

                    $("#ret_sub_design").select2(

                    {

                    placeholder:"Select Sub Design",

                    allowClear: true

                    });

                    $("#weight_range").select2(

                    {

                    placeholder:"Select Weight Range",

                    allowClear: true

                    });

    			break;

    			case 'new':

    				$("#product_select").select2(

    				{

    				placeholder:"Select Product",

    				allowClear: true

    				});

    				$("#des_select").select2(

    				{

    				placeholder:"Select Design",

    				allowClear: true

    				});

					$("#section_sel").select2(

                        {

                            placeholder:"Select section",

                            allowClear: true

                        });

    				get_ProductforReorder();

    				get_reorder_branch_details();

					getRetailSections();

    			break;

				case 'edit':

    				$("#product_select").select2(

    				{

    				placeholder:"Select Product",

    				allowClear: true

    				});

					$('#product_select_reorder').select2(

						{

							placeholder:"Select Product",

							allowClear: true

						});

    				$("#des_select").select2(

    				{

    				placeholder:"Select Design",

    				allowClear: true

    				});

					$("#section_sel").select2(

                        {

                            placeholder:"Select section",

                            allowClear: true

                        });

					  getRetailSections();

    				  get_reorder_branch_details();

					  get_reorder_sub_designs();

				 	  $('#add_reorderitems_info').css('display','none');

					   get_ProductforReorder();

					   get_designforReorder('');

				      setTimeout(function(){

						getReorderDetails(ctrl_page[3]);

					},1000);

    			break;

    		}

		break;

		case 'ret_delivery':

		               get_delivery_details();

		break;

		case 'ret_size':

		               get_size_details();

		break;

		case 'metal_type':

		                get_ActiveMetal();

		               get_metal_type_list();

		break;

		case 'old_metal_cat':

		               get_ActiveOldMetal();

		               get_old_metal_cat_list();

        case 'old_metal_rate':

		    get_old_metal_rate();

		 break;

		case 'ret_section':

		            //    get_branches();

		               get_section_details();

		break;

		case 'ret_qc_cancel_reason':

					get_cancel_reason_details();

		break;

		case 'ret_pay_device':

			   get_pay_device();

		break;

			case 'ret_account':

			   get_acc_head();

		break;

		case 'ret_breakeven_logs':

				var date = new Date();

                		    var firstDay  = new Date(date.getFullYear(), date.getMonth(),date.getDate() - 29, 1);

                			var from_date=(firstDay.getDate()+"-"+(firstDay.getMonth() + 1)+"-"+firstDay.getFullYear());

                			var to_date=(date.getDate()+"-"+(date.getMonth() + 1)+"-"+date.getFullYear());

            		        $('#fl_date1').html(from_date);

                            $('#fl_date2').html(to_date);

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

            		              startDate: moment().subtract(29, 'days'),

            		              endDate: moment()

            		            },

            		          function (start, end) {

            		          $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

            						$('#fl_date1').text(start.format('YYYY-MM-DD'));

            						$('#fl_date2').text(end.format('YYYY-MM-DD'));

            		          }

            			    );

			   get_acc_brlog();

		break;

		case 'stock_issue_type':

			   get_stock_issue();

		break;

		case 'feedback':

		               get_feedback_details();

		break;

		case 'ret_karigar_product' :

	        get_Activeproduct();

	        get_ActiveDesign();

	        get_ActiveKarigar();

	        set_karigar_product_list();

	    break;

	    case 'ret_collection':

 		    set_collection_list();

 		break;

 		case 'repair_master':

	        get_repair_list();

         break;

         case 'ret_metalpurity':

	        Metal_purity_table();

	        Metalrate();

	        get_MetalName();

         break;

         case 'diamond_rate':

			get_Activequality();

			set_diamond_rate_table();

		 break;

		 case 'selling_diamond_rate':

			get_Activequality();

			set_selling_diamond_rate_table();

		 break;

         case 'wastage_mc_settings':

             if(ctrl_page[2]== "edit"){

				$.when(get_wastage_mc_settings(ctrl_page[3])).then(function (){

					//  get_branchname();

					 get_ActiveRetMasters();

				});

			}

			if(ctrl_page[2]== "add"){

				get_ActiveRetMasters();

			}

			if(ctrl_page[2]== "list"){

				get_ActiveRetMasters();

				get_Activeproduct();

				get_wastage_settings_details();

			}

			$("#select_sub_design").select2(

				{

					placeholder:"Select Sub Design",

					allowClear: true

				});

			// get_branchname();

			change_wastage_type();

         break;

		 case 'ret_stone_rate_settings':

			switch(ctrl_page[2])

			{

				case 'list':

					get_ActiveStoneTypeForRate();

					getActive_quality_codeForRate();

					getLooseStonesProduct();

					get_stone_rates_settings_details();

				break;

				case 'add':

					get_reorder_branch_details();

					get_ActiveStoneType();

					getActive_uom();

					getActive_quality_code();

					getLooseStonesProduct();

				break;

				case 'edit':

					get_reorder_branch_details();

					if(ctrl_page[4]==1)

					{

						setTimeout(function(){getStoneRateSettings(ctrl_page[3])},1000);

					}

					else

					{

						setTimeout(function(){getLooseProductRateSettings(ctrl_page[3])},1000);

						var currentTab = 1;

						currentTab +=1;

						var next_tab = $('.nav-tabs > .active').next('li').find('a');

						if(next_tab.length > 0)

						{

							next_tab.trigger('click');

						}

					}

				break;

			}

		break;

		case 'product_grouping':

				

    		set_product_grp_table();

    					

    		get_ActiveMetal();

    

    	break;

		case 'charges':

			get_charges_list();

		case 'cover_up':

		var date = new Date();

		var firstDay  = new Date(date.getFullYear(), date.getMonth(),date.getDate() - 0, 1);

		var from_date=(firstDay.getDate()+"-"+(firstDay.getMonth() + 1)+"-"+firstDay.getFullYear());

		var to_date=(date.getDate()+"-"+(date.getMonth() + 1)+"-"+date.getFullYear());

		$('#rpt_from_date').html(from_date);

		$('#rpt_to_date').html(to_date);

		$('#coverup_date').daterangepicker(

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

		//$('#coverup_date span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

				$('#rpt_from_date').text(start.format('YYYY-MM-DD'));

				$('#rpt_to_date').text(end.format('YYYY-MM-DD'));

				get_cover_up_list();

			}

		);

			get_cover_up_list();

			get_ActiveMetal();

	 }

	 switch(ctrl_page[2])

	 {

	 	case 'old_metal_rate':

						$(window).scroll(function() {

							var height = $(window).scrollTop();

							if(height  > 300) {

								$(".stickyBlk").css({"position": "fixed"});

							} else{

								$(".stickyBlk").css({"position": "static"});

							}

						});

						get_metal();

					break;

	 }

     prod_info = [];

	 switch(ctrl_page[0])

	 {

		 	case 'purity':

		 					set_purity_table();

		 					break;

		 	case 'color':

		 					set_color_table();

		 					break;

		 	case 'cut':

		 					set_cut_table();

		 					break;

		 	case 'clarity':

		 					set_clarity_table();

		 					break;

		 	case 'carat':

		 					set_carat_table();

		 					break;

		 	case 'product_division':

							set_product_division_table();

			break;

			case 'deposit':

					set_bnk_deposit_table();

					$('#dep_date').datetimepicker({

						format: 'dd-mm-yyyy hh:ii:ss'

					});

					$('#cash_date').datepicker({

						format: 'dd-mm-yyyy'

					});

					$( "#depositForm" ).submit(function( event ) {

						$(".btn_submit").prop("disabled",'disabled');

					});

					let dep_branch = $("#id_branch").val();

					getDepositBranch(dep_branch);

					let cash_type = $("#cash_type").val();

					get_cash_in_hand(dep_branch, cash_type);

					break;

	 }

	 $('.branch_filter').select2().on('change', function() {

		if(this.value!='')

	    {

		    switch(ctrl_page[1]){

				case 'branch_floor':

								set_floor_table(this.value,$("#fl_date1").html(),$("#fl_date2").html());

								break;

				case 'floor_counter':

								set_branchfloor_table(this.value,$('#counter1').val(),$("#counter2").html(),$("#fl_date2").html());

			 					break;

			}

	    }

	 });

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

	/**

	*	Master :: Financial Year

	*	Starts

	*/

	if($('#fin_year_from').length>0)

	{

		$('#fin_year_from').datepicker({

		format: 'yyyy-mm-dd'

		})

		.on('changeDate', function(ev){

		$(this).datepicker('hide');

		});

	}

	if($('#fin_year_to').length>0)

	{

		$('#fin_year_to').datepicker({

		format: 'yyyy-mm-dd'

		})

		.on('changeDate', function(ev){

		$(this).datepicker('hide');

		});

	}

	/**

	*	Master :: Financial Year

	*	Ends

	*/

	/**

	*	Master :: Charges

	*	Starts

	*/

	$("#charges_add").on("hidden.bs.modal", function(){

		$('#charge_name').val('');

		$('#charge_code').val('');

		$('#charge_description').val('');

		$('#error-msg').html('');

	   });

	   $("#edit_charges").on("hidden.bs.modal", function(){

		 $('#error').html('');

	   });

	   $("#charges").on('click',function()

	   {

		   $('#charges_add').modal('show');

	   });

	   $('#charges_date').daterangepicker(

	   {

				   ranges: {

					 'Today': [moment(), moment()],

					 'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],

					 'Last 7 Days': [moment().subtract(6, 'days'), moment()],

					 'Last 30 Days': [moment().subtract(29, 'days'), moment()],

					 'This Month': [moment().startOf('month'), moment().endOf('month')],

					 'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')						]

				   },

				   startDate: moment().subtract(29, 'days'),

				   endDate: moment()

		 },

		 function(start, end)

		 {

				get_charges_list(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));

		 }

		 );

	   $("#charge_save_and_close").on('click',function()

	   {

		 if($("#charge_name").val()==null || $("#charge_name").val()=="" || $("#charge_name").val() == 0)

		 {

			 msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter the Charge name.</div>';

							$("div.overlay").css("display", "none");

							$('#error-msg').html(msg);

							return false;

		 }

		 else if($("#charge_code").val()==null || $("#charge_code").val()=="" || $("#charge_code").val() == 0)

		 {

			 msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter the Charge code.</div>';

							$("div.overlay").css("display", "none");

							$('#error-msg').html(msg);

							return false;

		 }

		 add_charges();

	   });

	   $("#charge_save_and_new").on('click',function()

	   {

		   if($("#charge_name").val()==null || $("#charge_name").val()=="" || $("#charge_name").val() == 0)

			 {

				 msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter the Charge name.</div>';

								$("div.overlay").css("display", "none");

								$('#error-msg').html(msg);

								return false;

			 }

			 else if($("#charge_code").val()==null || $("#charge_code").val()=="" || $("#charge_code").val() == 0)

			 {

				 msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter the Charge code.</div>';

								$("div.overlay").css("display", "none");

								$('#error-msg').html(msg);

								return false;

			 }

			 add_charges_save_and_new();

	   });

	   $("#update_charge").on('click',function()

	   {

		 if($("#charge_name_edit").val()==null || $("#charge_name_edit").val()=="" || $("#charge_name_edit").val() == 0)

		 {

			 msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter the Charge name.</div>';

							$("div.overlay").css("display", "none");

							$('#error').html(msg);

							return false;

		 }

		 else if($("#charge_code_edit").val()==null || $("#charge_code_edit").val()=="" || $("#charge_code_edit").val() == 0)

		 {

			 msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter the Charge code.</div>';

							$("div.overlay").css("display", "none");

							$('#error').html(msg);

							return false;

		 }

		 update_charges();

	   });

	   $(document).on('click', "#charges_list a.btn-edit", function(e) {

		   e.preventDefault();

		   id=$(this).data('id');

		   edit_charges(id);

		   $("#edit-id-charges").val(id);

	   });

	   /**

	   *	Master :: Charges

	   *	Ends

	   */

	/**

	*	Master :: Floor

	*	Starts

	*/

	$("#save_newfloor,#save_floor").on('click',function(){

			if($("#floor_branch").val() != null && $("#floor_branch").val() != '' && $("#fl_name").val() != '' && $("#fl_shortcode").val() != ''){

				add_floor($('#floor_branch').val(),$('#fl_name').val(),$('#fl_shortcode').val(),$('#branch_fl_status').val());

				$('#id_branch').val('');

				$('#fl_name').val('');

				$('#fl_shortcode').val('');

			}

			else if($("#floor_branch").val() == null || $("#floor_branch").val() == ''){

				msg='<div class ="alert alert-danger"><a href = "#" class= "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Branch Name .</div>';

				$("div.overlay").css("display", "none");

				$('#error-msg').html(msg);

			return false;

			}

			else if($("#fl_name").val() == ''){

				msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Floor Name .</div>';

				$("div.overlay").css("display", "none");

				$('#error-msg').html(msg);

			return false;

			}

			else if($("#fl_shortcode").val() == ''){

				msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Short Code .</div>';

				$("div.overlay").css("display", "none");

				$('#error-msg').html(msg);

			return false;

			}

	});

	$('#fl_branchid').on('change',function(e){

		if(this.value!='')

		{

			$("#id_branch").val(this.value);

		}

		else

		{

			$("#id_branch").val('');

		}

	});

	$('#edbranchid').on('change',function(e){

		if(this.value!='')

		{

			$("#id_branch").val(this.value);

		}

		else

		{

			$("#id_branch").val('');

		}

	});

	/*$('#des_cat_name').on('change',function(e){

	    var cat_id = this.value;

	    $("#des_prod_name option").remove();

	    $("#des_des_name option").remove();

		if(this.value!='')

		{

			$.each(ret_master_data, function (key, item) {

			    if(item.id_ret_category == cat_id){

		        	$.each(item.prodata, function (pkey, pitem) {

		        	    $("#des_prod_name").append(

                		    $("<option></option>")

                		    .attr("value", pitem.pro_id)

                		    .text(pitem.product_name)

                		  );

		        	});

		        	ret_selected_product = item.prodata;

		        	ret_selected_category = item;

			    }

    		});

    		$("#des_prod_name").select2(

    		{

    			placeholder:"Select Product",

    			allowClear: true

    		});

    		 $("#des_prod_name").select2("val",'');

    		 $("#get_tag_details").attr("disabled", true);

    		 ret_selected_design = [];

		}else{

		        $("#des_prod_name").select2("val",'');

		        $("#des_des_name").select2("val",'');

		        ret_selected_design = [];

		        ret_selected_category = [];

		}

	}); */

	$('#des_cat_name').on('change',function(e){

	    var cat_id = this.value;

	    $("#des_prod_name option").remove();

	    $("#des_des_name option").remove();

	    $("#select_sub_design option").remove();

	    $("#des_prod_name").select2("val",'');

        $("#des_des_name").select2("val",'');

        $("#select_sub_design").select2("val",'');

		if(this.value!='')

		{

			get_Activeproduct(this.value);

		}

		else

		{

            $("#des_prod_name").select2("val",'');

            $("#des_des_name").select2("val",'');

            $("#select_sub_design").select2("val",'');

		}

	});

	$('#des_des_name').on('change',function(e){

	    var des_id = this.value;

	    $('#select_sub_design option').remove();

	    if(this.value!='')

		{

    		 get_ActiveSubDesingns();

		}else{

		    //$("#get_tag_details").attr("disabled", true);

		}

	});

	/*$("#des_prod_name").on('change',function(e){

	    var pro_id = this.value;

	    $("#des_des_name option").remove();

		if(this.value!='')

		{

			$.each(ret_selected_product, function (pkey, pitem) {

			    if(pitem.pro_id == pro_id){

		        	$.each(pitem.design, function (dkey, ditem) {

		        	    $("#des_des_name").append(

                		    $("<option></option>")

                		    .attr("value", ditem.design_no)

                		    .text(ditem.design_name)

                		  );

		        	});

		        	ret_selected_design = pitem.design;

		        	ret_pro_sellecgted = pitem;

			    }

    		});

        	$("#des_des_name").select2(

    		{

    			placeholder:"Select Design",

    			allowClear: true

    		});

    		 $("#des_des_name").select2("val",'');

		}else{

		     $("#get_tag_details").attr("disabled", true);

		     ret_selected_design = [];

		}

	});*/

	$("#des_prod_name").on('change',function(e){

	    var pro_id = this.value;

	    $("#des_des_name option").remove();

	    $("#select_sub_design option").remove();

	    $("#select_sub_design").select2("val",'');

		if(this.value!='')

		{

			get_active_design_products();

		}

		else{

           //$("#des_des_name").select2("val",'');

		}

	});

	function get_active_design_products()

    {

    	$('#des_select option').remove();

    	$.ajax({

    	type: 'POST',

    	url: base_url+'index.php/admin_ret_catalog/get_active_design_products',

    	data :{'id_product':$('#des_prod_name').val()},

    	dataType:'json',

    	success:function(data){

    	        var id_des =$("#des_id").val();

				if(ctrl_page[1]=='wastage_mc_settings' && ctrl_page[2] =="add" ){

					$("#des_des_name").append(

						$("<option></option>")

						.attr("value", 0)

						.text('All')

						);

				}

        		$.each(data, function (key, item) {

        		    $("#des_des_name").append(

        		    $("<option></option>")

        		    .attr("value", item.design_no)

        		    .text(item.design_name)

        		    );

        		});

        		$("#des_des_name").select2(

        		{

        			placeholder:"Select Design",

        			allowClear: true

        		});

        		$("#des_des_name").select2("val",(id_des!='' && id_des>0?id_des:''));

        		//$("#des_des_name").select2("val",'');

    		}

    	});

    }

	$('#get_tag_details').on('click',function(){

        get_design_settings_details();

	});

	function get_design_settings_details()

	{

	     my_Date = new Date();

	     $.ajax({

    	 url:base_url+ "index.php/admin_ret_catalog/get_DesignSettingsDetails?nocache=" + my_Date.getUTCSeconds(),

    	 dataType:"JSON",

    	 type:"POST",

    	 data:{'id_product':$('#des_prod_name').val(),'id_design':$('#des_des_name').val(),'id_sub_design':$('#select_sub_design').val(),'id_mc_type':$('#old_mc_type').val(),'mc_value':$('#old_mc_value').val(),'wast_per':$('#old_wast_per').val()},

    	 success:function(data){

    	 var list=data;

    	     var oTable = $('#edit_mas_design_list').DataTable();

    		 oTable.clear().draw();

			 if (list!= null && list.length > 0)

				{

			        	oTable = $('#edit_mas_design_list').dataTable({

						"bDestroy": true,

						"bInfo": true,

						"bFilter": true,

						"bSort": true,

						"order": [[ 0, "desc" ]],

						"buttons" : ['excel','print'],

						"dom": 'lBfrtip',

						"buttons": [

							 {

							   extend: 'print',

							   footer: true,

							   title: "M.C & V.A Settings",

							   customize: function ( win ) {

									$(win.document.body).find( 'table' )

										.addClass( 'compact' )

										.css( 'font-size', 'inherit' );

								},

							 },

							 {

								extend:'excel',

								footer: true,

							    title: "M.C & V.A Settings",

							  }

							 ],

						"aaData": list,

						"aoColumns": [

						                { "mDataProp": function ( row, type, val, meta ){

                		                	chekbox='<input type="checkbox" class="id_sub_design_mapping" name="id_sub_design_mapping[]" value="'+row.id_sub_design_mapping+'"/>'

                		                	return chekbox+" "+row.id_sub_design_mapping;

                		                }},

										{ "mDataProp": "category_name" },

										{ "mDataProp": "product_name" },

										{ "mDataProp": "design_name" },

										{ "mDataProp": "sub_design_name" },

										{ "mDataProp": function ( row, type, val, meta ){

                						        if(row.mc_cal_type == 1){

                						            return "Per Pcs";

                						        }else if(row.mc_cal_type == 2){

                						            return "Per Grm";

                						        }else if(row.mc_cal_type == 3){

                						            return "% of Price";

                						        }else{

                						            return '-';

                						        }

                					    	}

                						},

                                        { "mDataProp": function ( row, type, val, meta ) {

                                            id= row.id_sub_design_mapping;

                                            if(row.wastage_type==1)

                                            {

                                                return  row.wastag_value + "/" + row.mc_cal_value;

                                            }

                                            else {

												if(row.total_wc > 0)

												{

                                                	action_content= row.wastage_type == 2 ? '<a href="#" class="btn btn-primary btn-wc-view" id="edit" role="button" data-toggle="modal" data-id='+id+' data-target="#wastage"><i>view</i></a>' : '-';

													return action_content;

												}

												else {

													return '';

												}

                                            	}

                                            }

                                        },

										{ "mDataProp": function ( row, type, val, meta ) {                                            id= row.id_sub_design_mapping;

											if(row.wastage_type==1) {

											return  row.wastag_min;

											}

											else {

											return '';

											}

											}

											},

											{ "mDataProp": function ( row, type, val, meta ) {

											id= row.id_sub_design_mapping;

											if(row.wastage_type==1) {

											return  row.mc_min;

											}

											else {

											return '';

											}

											}

											},

										{ "mDataProp": "margin_mrp" },

										{ "mDataProp": function ( row, type, val, meta ) {

                                            id= row.id_sub_design_mapping;

                                            if(row.total_attr > 0)

                                            {

												action_content= '<a href="#" class="btn btn-primary btn-attribute-view" id="edit" role="button" data-toggle="modal" data-id='+id+' data-target="#des_attributes"><i>View</i></a>';

                                                return action_content;

                                            }

                                            else {

                                                return '';

                                            }

                                            }

                                        },

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

	$('#design_bedit_submit').on('click',function(){

		let mc_va_attr_value = $("input[name='mc_va_attr_update']:checked").val();

		let mc_va_update = mc_va_attr_value == 1 ? 1 : 0;

		let attribute_update = mc_va_attr_value == 2 ? 1 : 0;

		if(mc_va_update == 1 || attribute_update == 1)

		{

			let mc_validated = (mc_va_update == 1 ? false : true);

			let attribute_validated = (attribute_update == 1 ? false : true);

			if(mc_va_update == 1)

			{

				mc_validated = true;

			}

			if(attribute_update == 1)

			{

				let attribute_type = $("#attribute_type").val();

				if(attribute_type == 1)

				{

					let des_attr_rows = $("#update_attribute_detail tbody tr").length;

					if(des_attr_rows > 0)

					{

						if(validate_desAttr_row())

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

			}

			else

			{

				attribute_validated = true;

			}

			if(mc_validated == true && attribute_validated == true)

			{

				let total_selected_no = $("#edit_mas_design_list tbody tr").find(".id_sub_design_mapping:checked").length;

				if(total_selected_no > 0)

				{

					$("#bulk_edit_confirm_modal").modal('show');

				}

				else

				{

					$.toaster({ priority : 'danger', title : 'Warning!', message : "Please select design(s)"});

				}

			}

			else

			{

				if(mc_validated == false)

				{

					$.toaster({ priority : 'danger', title : 'Warning!', message : "Please select / enter proper MC/VA values to update"});

				}

				if(attribute_validated == false)

				{

					$.toaster({ priority : 'danger', title : 'Warning!', message : "Please select proper attribute values to update"});

				}

			}

		}

		else

		{

			$.toaster({ priority : 'danger', title : 'Warning!', message : "Please select MC/VA or Attributes option."});

		}

	});

	$('#confirm_update_bulk_design').on('click',function(){

	    if($("input[name='id_sub_design_mapping[]']:checked").val())

        {

            $(".overlay").css('display','block');

            $('#design_bedit_submit').prop('disabled',true);

            var selected = [];

    	    var allow_update = false;

    	    var wastage=[];

			console.log(wastage);

			let attributes = [];

			let mc_va_attr_value = $("input[name='mc_va_attr_update']:checked").val();

			let mc_va_update = mc_va_attr_value == 1 ? 1 : 0;

			let attribute_update = mc_va_attr_value == 2 ? 1 : 0;

			let attribute_type = $("#attribute_type").val();

			if(attribute_type == 1)

			{

				let des_attr_rows = $("#update_attribute_detail tbody tr");

				$.each(des_attr_rows, function (attrkey, attritem) {

					let attr_name = $(attritem).find('.des_upd_attr_name').val();

					let attr_value = $(attritem).find('.des_upd_attr_value').val();

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

			var wastageType = $("input[name='design[wastage_type]']:checked").val();

			if(wastageType == 1)

			{

				wastage = $("#wastag_value").val();

			}

			else

			{

				$("#wc_detail tbody tr").each(function(index, value){

					var wastages = {

									"from_wt"    :  $(value).find(".from_wt").val(),

									"to_wt"      :  $(value).find(".to_wt").val(),

									"wc_percent" :  $(value).find(".wc_percent").val(),

									"wc_min" 	  :  $(value).find(".wc_min").val(),

									"wc_max"     :  $(value).find(".wc_max").val(),

									"mc"         :  $(value).find(".mc_percent").val(),

									"mcrg_min"   :  $(value).find(".mcrg_min").val(),

									"mcrg_max"   :  $(value).find(".mcrg_max").val()

									};

					wastage.push(wastages);

				});

			}

    		$("#edit_mas_design_list tbody tr").each(function(index, value){

        		if($(value).find("input[name='id_sub_design_mapping[]']:checked").is(":checked"))

        		{

    		    	transData = {

            			'id_sub_design_mapping'   	 :  $(value).find(".id_sub_design_mapping").val(),

            			'mc_cal_type'    :  $('#update_mc_type').val(),

            			'mc_cal_value'   :  $('#mc_value').val(),

						'mc_min'   		 :  $('#mc_min').val(),

						'mc_max'   		 :  $('#mc_max').val(),

						'wastageType'    :  wastageType,

						'wastag_value'   :  wastage,

						'wastag_min'   	 :  $('#wastag_min').val(),

						'wastag_max'   	 :  $('#wastag_max').val(),

						'margin_mrp'   	 :  $('#margin_mrp').val(),

						'mc_va_update'   :  mc_va_update,

						'attribute_update' : attribute_update,

						'attribute_type' : attribute_type,

						'attributes' : attributes

            		};

                	selected.push(transData);

                    allow_update = true;

        		}

    		});

            	my_Date = new Date();

            	$.ajax({

            			 url:base_url+ "index.php/admin_ret_catalog/ret_design/ajax_update_bulk_retdesign?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),

            			 type:"POST",

            			 data:  {  'req_data' : selected },

            			 dataType: "json",

            			 async:true,

            			 	  success:function(data){

            			 	  	if(data.status)

            			 	  	{

            			 	  	   $.toaster({ priority : 'success', title : 'Success!', message : data.msg});

            			 	  	   $('#design_bedit_submit').prop('disabled',false);

            			 	  	   $("#bulk_edit_confirm_modal").modal('hide');

            			 	  	   $(".overlay").css('display',"none");

            			 	  	   window.location.reload();

            			 	  	}

            			 	  	else

            			 	  	{

            			 	  	    $.toaster({ priority : 'danger', title : 'Warning!', message : data.msg});

            			 	  	    $('#design_bedit_submit').prop('disabled',false);

            			 	  	    $("#bulk_edit_confirm_modal").modal('hide');

            			 	  	    $(".overlay").css('display',"none");

            			 	  	}

            				  },

            				  error:function(error)

            				  {

            					 $(".overlay").css('display',"none");

            				  }

            		  });

        }else{

             $.toaster({ priority : 'danger', title : 'Warning!', message : "Please select design(s)"});

        }

	});

	function set_design_update_list(desdata, catname, proname)

    {

       var oTable = $('#edit_mas_design_list').DataTable();

    	 oTable.clear().draw();

    	 	oTable = $('#edit_mas_design_list').dataTable({

    			"bDestroy": true,

    			"bInfo": true,

    			"order": [[ 0, "desc" ]],

    			"bFilter": true,

    			"bSort": true,

    			"dom": 'lBfrtip',

    			"buttons" : ['excel','print'],

    			"lengthMenu": [ [ 100, 250, 500, -1], [100, 250, 500, "All"] ],

    			"tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },

    			"aaData": desdata,

    			"aoColumns": [ { "mDataProp": function ( row, type, val, meta ){

    		                	chekbox='<input type="checkbox" class="bedit_des_id" name="bedit_des_id[]" value="'+row.design_no+'"/><input type="hidden" class="design_no" value="'+row.design_no+'"/>'

    		                	return chekbox+" "+row.design_no;

    		                }},

    					    { "mDataProp": function ( row, type, val, meta ){ return catname; } },

    					    { "mDataProp": function ( row, type, val, meta ){ return proname; } },

    					    { "mDataProp": "design_name" },

    						{ "mDataProp": function ( row, type, val, meta ){

    						        if(row.mc_cal_type == 1){

    						            return "Per Pcs";

    						        }else if(row.mc_cal_type == 2){

    						            return "Per Grm";

    						        }else if(row.mc_cal_type == 3){

    						            return "% of Price";

    						        }

    					    	}

    						},

    						{ "mDataProp": "mc_cal_value" },

    						{ "mDataProp" : 'wastag_value'}

    						]

    		});

    }

	$("#fl_status").on('switchChange.bootstrapSwitch', function (event, state) {

		var x = $(this).data('on-text');

		var y = $(this).data('off-text');

		if($("#fl_status").is(':checked') && x=='YES')

		{

			$("#branch_fl_status").val(1);

		}

		else if(y == 'NO')

		{

			$("#branch_fl_status").val(0);

		}

	});

	$("#ed_status").on('switchChange.bootstrapSwitch', function (event, state) {

		var x = $(this).data('on-text');

		var y = $(this).data('off-text');

		if($("#ed_status").is(':checked') && x=='YES')

		{

		$("#ed_fl_status").val(1);

		}

		else if(y == 'NO')

		{

		$("#ed_fl_status").val(0);

		}

	});

	$('.btn_date_range').daterangepicker(

	{

	 ranges: {

		'Today'  : [moment(), moment()],

		'Yesterday'  : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],

		'Last 7 Days' : [moment().subtract(6, 'days'), moment()],

		'Last 30 Days': [moment().subtract(29, 'days'), moment()],

		'This Month'  : [moment().startOf('month'), moment().endOf('month')],

		'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]

		 },

		 startDate: moment().subtract(29, 'days'),

		 endDate: moment()

		},

		function (start, end) {

		$('#fl_date1').text(moment().startOf('month').format('YYYY-MM-DD'));

		$('#fl_date2').text(moment().endOf('month').format('YYYY-MM-DD'));

		set_floor_table($('.branch_filter').val(),start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));

		 }

	);

	$(document).on('click', "#floor_list a.btn-edit", function(e) {

		$("#id_branch").val('');

		e.preventDefault();

		id=$(this).data('id');

	    get_floor(id);

	    $("#edit-id").val(id);

	});

    $("#update_floor").on('click',function(){

	        //$("div.overlay").css("display", "block");

			var branch_id=$("#id_branch").val();

			var id=$("#edit-id").val();

			var floor_name=$("#ed_floorname").val();

			var floor_short_code=$("#ed_floorshortcode").val();

			var status=$("#ed_fl_status").val();

			if($("#id_branch").val() != '' && $("#ed_floorname").val() != '' && $("#ed_floorshortcode").val() != ''){

		    update_floor(branch_id,floor_name,floor_short_code,status);

		    }

		    else if($("#id_branch").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Branch Name .</div>';

			$("div.overlay").css("display", "none");

	        $('#error-msg1').html(msg);

	        return false;

			}

			else if($("#ed_floorname").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Enter Floor Name .</div>';

			$("div.overlay").css("display", "none");

	        $('#error-msg1').html(msg);

	        return false;

			}

			else if($("#ed_floorshortcode").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Short Code .</div>';

			$("div.overlay").css("display", "none");

	        $('#error-msg1').html(msg);

	        return false;

			}

	});

	/**

	*	Master :: Floor

	*	Ends

	*/

	/**

	*	Master :: Counter

	*	Starts

	*/

	$("#add_countnew,#add_newcount").on('click',function(){

		if($("#counter_flr_id").val() != '' && $("#counter_name").val() != '' && $("#counter_short_code").val() != ''){

		    add_counter($('#counter_flr_id').val(),$('#counter_name').val(),$('#counter_short_code').val(),$('#counter_status').val());

			$('#counter_flr_id').val('');

			$('#counter_name').val('');

			$('#counter_short_code').val('');

		}

		else if($("#counter_flr_id").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Select floor name.</div>';

			$("div.overlay").css("display", "none");

	        $('#error-msg').html(msg);

	        return false;

		}

		else if($("#counter_name").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Counter name.</div>';

			$("div.overlay").css("display", "none");

	        $('#error-msg').html(msg);

	        return false;

		}

		else if($("#counter_short_code").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Counter Short Name.</div>';

			$("div.overlay").css("display", "none");

	        $('#error-msg').html(msg);

	        return false;

		}

	 });

	$("#counter_status").on('change',function(e){

		alert(this.value);

	});

	$("#counter_flr_sel,#ed_counter_flr_sel").select2(

	{

		placeholder:"Select floor name",

		allowClear: true

	});

	$("#counter_flr_sel,#ed_counter_flr_sel").on('change',function(e){

		if(this.value!='')

		{

			$("#counter_flr_id,#ed_counter_flr_id").val(this.value);

			if(ctrl_page[1]=='web_devices')

			{

			    get_ActiveCounter();

			}

		}

		else

		{

			$("#counter_flr_id,#ed_counter_flr_id").val('');

		}

	});

	$('#floor_filter').select2().on('change', function() {

		if(this.value!='' && this.value>0)

	    {

			var today = new Date();

			set_branchfloor_table($('.branch_filter').val(),this.value);

		}

	});

	$('#counter_date').daterangepicker(

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

			var flr = $("#floor_filter").val();

				$('#counter1').text(start.format('YYYY-MM-DD'));

				$('#counter2').text(end.format('YYYY-MM-DD'));

		     set_branchfloor_table($('.branch_filter').val(),flr,start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));

		}

	);

	$("#ctr_status").on('switchChange.bootstrapSwitch', function (event, state) {

        var x=$(this).data('on-text');

        var y=$(this).data('off-text');

		if($("#ctr_status").is(':checked') && x=='YES')

		{

					$("#counter_status").val(1);

        } else if(y=='NO')

		{

          $("#counter_status").val(0);

		}

    });

 	$("#ed_ctr_status").on('switchChange.bootstrapSwitch', function (event, state) {

        var x=$(this).data('on-text');

        var y=$(this).data('off-text');

		if($("#ed_ctr_status").is(':checked') && x=='YES')

		{

					$("#ed_counter_status").val(1);

        } else if(y=='NO')

		{

		  $("#ed_counter_status").val(0);

		}

    });

	$(document).on('click', "#counter_list a.btn-edit", function(e) {

		$("#ed_counter_id").val('');

		e.preventDefault();

		id=$(this).data('id');

	    get_counter(id);

	    $("#edit-id").val(id);

	});

	//update counter

	$("#update_counter").on('click',function(){

		var floor_id=$("#ed_counter_flr_id").val();

		var id=$("#edit-id").val();

		var counter_name=$("#ed_counter_name").val();

		var counter_short_code=$("#ed_counter_short_code").val();

		var counter_status=$("#ed_counter_status").val();

		if($("#ed_counter_flr_id").val() != '' && $("#ed_counter_name").val() != '' && $("#ed_counter_short_code").val() != ''){

			   update_counter(floor_id,counter_name,counter_short_code,counter_status);

			}

			else if($("#ed_counter_flr_id").val() == ''){

				msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Select floor name.</div>';

				$("div.overlay").css("display", "none");

		        $('#error').html(msg);

		        return false;

			}

			else if($("#ed_counter_name").val() == ''){

				msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Counter name.</div>';

				$("div.overlay").css("display", "none");

		        $('#error').html(msg);

		        return false;

			}

			else if($("#ed_counter_short_code").val() == ''){

				msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Counter Short Name.</div>';

				$("div.overlay").css("display", "none");

		        $('#error').html(msg);

		        return false;

			}

	});

	/**

	*	Master :: Counter

	*	Ends

	*/

	/**

	* Master :: Making Type

	* Starts

	*/

	 $("#addnew_type,#add_newtype").on('click',function(){

		if($("#making_name").val() != '' && $("#making_short_code").val() != ''){

		    add_making_type($('#making_name').val(),$('#making_short_code').val(),$('#making_status').val());

			$("#making_name").val('');

			$("#making_short_code").val('');

		}

		else if($("#making_name").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Making name.</div>';

			$("div.overlay").css("display", "none");

	        $('#error-msg').html(msg);

	        return false;

		}

		else if($("#making_short_code").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Short name.</div>';

			$("div.overlay").css("display","none");

	        $('#error-msg').html(msg);

	        return false;

		}

	});

	$(document).on('click', "#making_type_list a.btn-edit", function(e) {

		e.preventDefault();

		id=$(this).data('id');

	    get_maketype(id);

	    $("#edit-id").val(id);

	});

	//update make type

	$("#update_type").on('click',function(){

		var id=$("#edit-id").val();

		var making_name=$("#ed_making_name").val();

		var making_short_code=$("#ed_making_short_code").val();

		var making_status=$("#ed_making_status").val();

		if($("#ed_making_name").val() != '' && $("#ed_making_short_code").val() != ''){

			   update_making_type(making_name,making_short_code,making_status);

			}

			else if($("#ed_making_name").val() == ''){

				msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Making type name.</div>';

				$("div.overlay").css("display", "none");

		        $('#error').html(msg);

		        return false;

			}

			else if($("#ed_making_short_code").val() == ''){

				msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Short name.</div>';

				$("div.overlay").css("display", "none");

		        $('#error').html(msg);

		        return false;

			}

	});

	$("#make_switch").on('switchChange.bootstrapSwitch', function (event, state) {

	    var x=$(this).data('on-text');

	    var y=$(this).data('off-text');

		if($("#make_switch").is(':checked') && x=='YES')

		{

			$("#making_status").val(1);

	    } else if(y=='NO')

		{

	      $("#making_status").val(0);

		}

	});

	$("#ed_make_switch").on('switchChange.bootstrapSwitch', function (event, state) {

	    var x=$(this).data('on-text');

	    var y=$(this).data('off-text');

		if($("#ed_make_switch").is(':checked') && x=='YES')

		{

			$("#ed_making_status").val(1);

	    } else if(y=='NO')

		{

		  $("#ed_making_status").val(0);

		}

	});

	$('#make_date').daterangepicker(

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

			$('#make1').text(start.format('YYYY-MM-DD'));

			$('#make2').text(end.format('YYYY-MM-DD'));

		 	set_makingtype_table(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));

		}

	);

	/**

	*	Master :: Making Type

	*	Ends

	*/

	/**

	*	Master :: Theme

	*	Starts

	*/

	$("#add_newtheme,#add_theme").on('click',function(){

		if($("#theme_code").val() != '' && $("#theme_name").val() != '' && $("#theme_desc").val() != ''){

			add_theme($('#theme_name').val(),$('#theme_code').val(),$('#theme_desc').val(),$('#adtheme_status').val());

			$('#theme_name').val('');

			$('#theme_code').val('');

			$('#theme_desc').val('');

		}

		else if($("#theme_name").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Theme Name .</div>';

			$("div.overlay").css("display", "none");

			$('#error-msg').html(msg);

		return false;

		}

		else if($("#theme_code").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Theme Code .</div>';

			$("div.overlay").css("display", "none");

			$('#error-msg').html(msg);

		return false;

		}

	});

	$("#adtheme_status").on('change',function(e){

		alert(this.value);

	});

	$('#theme-dt-btn').daterangepicker(

	{

		  ranges: {

			'Today'		  : [moment(), moment()],

			'Yesterday'	  : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],

			'Last 7 Days' : [moment().subtract(6, 'days'), moment()],

			'Last 30 Days': [moment().subtract(29, 'days'), moment()],

			'This Month'  : [moment().startOf('month'), moment().endOf('month')],

			'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]

		  },

		  startDate: moment().subtract(29, 'days'),

		  endDate: moment()

	},

	function (start, end) {

				set_theme_table(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));

				$('#theme_date1').text(moment().startOf('month').format('YYYY-MM-DD'));

				$('#theme_date2').text(moment().endOf('month').format('YYYY-MM-DD'));

		  }

	);

	$(document).on('click', "#theme_list a.btn-edit", function(e) {

		//$("#id_branch").val('');

		e.preventDefault();

		id=$(this).data('id');

	    get_theme(id);

	    $("#edit-id").val(id);

	});

	 $("#update_theme").on('click',function(){

        //$("div.overlay").css("display", "block");

		var id             = $("#edit-id").val();

		var theme_name     = $("#ed_themename").val();

		var theme_code     = $("#ed_themeshortcode").val();

		var theme_desc     = $("#ed_theme_desc").val();

		var theme_status   = $('#ed_theme_status').val();

		if($("#ed_themename").val() != '' && $("#ed_themeshortcode").val() != '' && $("#ed_theme_desc").val() != ''){

	    	update_theme(theme_name,theme_code,theme_desc,theme_status);

	    }

		else if($("#ed_themename").val() == ''){

		msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Enter Theme Name .</div>';

		$("div.overlay").css("display", "none");

        $('#error-msg1').html(msg);

        return false;

		}

		else if($("#ed_themeshortcode").val() == ''){

		msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Theme Code .</div>';

		$("div.overlay").css("display", "none");

        $('#error-msg1').html(msg);

        return false;

		}

		 else if($("#ed_theme_desc").val() == ''){

		msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Theme Description .</div>';

		$("div.overlay").css("display", "none");

        $('#error-msg1').html(msg);

        return false;

		}

    });

    $("#ad_themestatus").on('switchChange.bootstrapSwitch', function (event, state) {

	  var x = $(this).data('on-text');

	  var y = $(this).data('off-text');

		if($("#ad_themestatus").is(':checked') && x=='YES')

		{

			$("#adtheme_status").val(1);

		}

		else if(y=='NO')

		{

			$("#adtheme_status").val(0);

		}

    });

    $("#ed_themestatus").on('switchChange.bootstrapSwitch', function (event, state) {

	  var x = $(this).data('on-text');

	  var y = $(this).data('off-text');

		if($("#ed_themestatus").is(':checked') && x=='YES')

		{

			$("#ed_theme_status").val(1);

		}

		else if(y == 'NO')

		{

			$("#ed_theme_status").val(0);

		}

    });

	/**

	*	Master :: Theme

	*	Ends

	*/

	/**

	*	Master :: Material

	*	Starts

	*/

	$("#newmtr_add,#add_newmtr").on('click',function(){

		if($("#material_name").val() != '' && $("#material_code").val() != ''){

			add_material($('#material_name').val(),$('#material_code').val(),$('#ad_mtrl_status').val());

			$('#material_name').val('');

			$('#material_code').val('');

		}

		else if($("#material_name").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Material Name .</div>';

			$("div.overlay").css("display", "none");

			$('#error-msg').html(msg);

		return false;

		}

		else if($("#material_code").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Material Code .</div>';

			$("div.overlay").css("display", "none");

			$('#error-msg').html(msg);

		return false;

		}

	});

	$("#ad_mtrl_status").on('change',function(e){

		alert(this.value);

	});

	$(document).on('click', "#material_list a.btn-edit", function(e) {

		e.preventDefault();

		id=$(this).data('id');

	    get_material(id);

	    $("#edit-id").val(id);

	});

//update material

    $("#update_material").on('click',function(){

        //$("div.overlay").css("display", "block");

		var id                = $("#edit-id").val();

		var material_name     = $("#ed_material_name").val();

		var material_code     = $("#ed_material_code").val();

		var material_status   = $('#ed_material_status').val();

		if($("#ed_material_name").val() != '' && $("#ed_material_code").val() != ''){

	    	update_material(material_name,material_code,material_status);

	    }

		else if($("#ed_material_name").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Enter Material Name .</div>';

			$("div.overlay").css("display", "none");

	        $('#error-msg1').html(msg);

	    	return false;

		}

		else if($("#ed_material_code").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Material Code .</div>';

			$("div.overlay").css("display", "none");

	        $('#error-msg1').html(msg);

	        return false;

		}

	});

	$('#material_date').daterangepicker(

	{

		  ranges: {

			'Today'		  : [moment(), moment()],

			'Yesterday'	  : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],

			'Last 7 Days' : [moment().subtract(6, 'days'), moment()],

			'Last 30 Days': [moment().subtract(29, 'days'), moment()],

			'This Month'  : [moment().startOf('month'), moment().endOf('month')],

			'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]

		  },

		  startDate: moment().subtract(29, 'days'),

		  endDate: moment()

	},

	function (start, end) {

				set_material_table(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));

				$('#theme_date1').text(moment().startOf('month').format('YYYY-MM-DD'));

				$('#theme_date2').text(moment().endOf('month').format('YYYY-MM-DD'));

		  }

	);

    $("#ad_mtr_status").on('switchChange.bootstrapSwitch', function (event, state) {

		var x = $(this).data('on-text');

		var y = $(this).data('off-text');

		if($("#ad_mtr_status").is(':checked') && x=='YES')

		{

			$("#ad_mtrl_status").val(1);

		}

		else if(y=='NO')

		{

			$("#ad_mtrl_status").val(0);

		}

    });

    $("#ed_mtr_status").on('switchChange.bootstrapSwitch', function (event, state) {

		var x = $(this).data('on-text');

		var y = $(this).data('off-text');

		if($("#ed_mtr_status").is(':checked') && x=='YES')

		{

			$("#ed_material_status").val(1);

		}

		else if(y == 'NO')

		{

			$("#ed_material_status").val(0);

		}

	});

	var dateToday =new Date();

		    $("#mtr_eff_date,#ed_eff_date").datepicker({

				dateFormat: 'yyyy-mm-dd',

				"setValue": new Date(),

			}).datepicker("setDate", dateToday);

	$("#ad_mtrrate_name,#ed_mtrrate_name").select2(

	{

		placeholder:"Select Material name",

		allowClear: true

	});

	$("#ad_mtrrate_name,#ed_mtrrate_name").on('change',function(e){

		if(this.value!='' && this.value>0)

		{

			$("#mtrid_rate,#edmtrrate_id").val(this.value);

		}

		else

		{

			$("#mtrid_rate,#edmtrrate_id").val('');

		}

	});

	$(document).on('click', "#mtrrate_list a.btn-edit", function(e) {

		e.preventDefault();

		id=$(this).data('id');

	    get_materialrate(id);

	    $("#edit-id").val(id);

	});

    $("#update_mtrrate").on('click',function(){

	        //$("div.overlay").css("display", "block");

			var id             = $("#edit-id").val();

			var material_id  = $("#edmtrrate_id").val();

			var mat_rate       = $('#ed_material_rate').val();

			var effective_date = $('#ed_eff_date').val();

			if($("#ed_material_rate").val() != ''){

			update_mtrrate(material_id,mat_rate,effective_date);

		    }

			else if($("#edmtrrate_id").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Enter Material Name .</div>';

			$("div.overlay").css("display", "none");

	        $('#error-msg1').html(msg);

	        return false;

			}

			else if($("#ed_material_rate").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Enter Material Rate .</div>';

			$("div.overlay").css("display", "none");

	        $('#error-msg1').html(msg);

	        return false;

			}

		});

	$('#mtrrate_date').daterangepicker(

	{

		  ranges: {

			'Today'		  : [moment(), moment()],

			'Yesterday'	  : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],

			'Last 7 Days' : [moment().subtract(6, 'days'), moment()],

			'Last 30 Days': [moment().subtract(29, 'days'), moment()],

			'This Month'  : [moment().startOf('month'), moment().endOf('month')],

			'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]

		  },

		  startDate: moment().subtract(29, 'days'),

		  endDate: moment()

	},

	function (start,end) {

				$('#mtr1').text(moment().startOf('month').format('YYYY-MM-DD'));

				$('#mtr2').text(moment().endOf('month').format('YYYY-MM-DD'));

				set_materialrate_table($("#material_filter").val(),start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));

		  }

	);

	$('#material_filter').select2().on('change', function() {

		if(this.value!='' && this.value>0)

		{

			var material_id = $('#material_filter').val();

			set_materialrate_table(material_id);

		}

	});

	$('#addmatrt_lst').select2().on('change', function() {

		if(this.value!='' && this.value>0)

		{

			$('#add_material_rate').prop('disabled',false);

		}

	});

	$("#ad_mtrrate_name,#ed_mtrrate_name").select2(

	{

		placeholder:"Select Material name",

		allowClear: true

	});

	$('#rate_submit').on('click',function(){

    if($("input[name='mat_rate_id[]']:checked").val())

    {

		if(validateMtrrateRow()){

		$("#rate_submit").prop('disabled',true);

	    $(".overlay").css('display','none');

	    var selected = [];

	    var allow_update=true;

		$("#mtrrate_list tbody tr").each(function(index, value){

			if($(value).find("input[name='mat_rate_id[]']:checked").is(":checked"))

			{

				transData = {

					'mat_rate_id'   : $(value).find(".mat_rate_id").val(),

					'mat_rate'   	: $(value).find(".mat_rate").val(),

					'material_id'   : $(value).find(".material_id").val()

				}

				selected.push(transData);

			}

			})

		}else{

			alert("Please fill material rate field");

		}

		req_data = selected;

		if(allow_update)

		{

				update_mrrate_data(req_data);

		}

	}

	else

	{

		alert('Select Any Product');

		$("#rate_submit").prop('disabled',false);

	}

});

	/**

	*	Master :: Material

	*	Ends

	*/

	/**

	*	Master :: Hook

	*	Starts

	*/

	$("#new_addhook,#add_newhook").on('click',function(){

		if($("#hook_name").val() != '' && $("#hook_code").val() != ''){

			add_hook($('#hook_name').val(),$('#hook_code').val(),$('#add_hook_status').val());

			$('#hook_name').val('');

			$('#hook_code').val('');

		}

		else if($("#hook_name").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Hook Name .</div>';

			$("div.overlay").css("display", "none");

			$('#error-msg').html(msg);

		return false;

		}

		else if($("#hook_code").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Hook Code .</div>';

			$("div.overlay").css("display", "none");

			$('#error-msg').html(msg);

		return false;

		}

	});

	$(document).on('click', "#hook_list a.btn-edit", function(e) {

		e.preventDefault();

		id=$(this).data('id');

	    get_hook(id);

	    $("#edit-id").val(id);

	});

    $("#update_hook").on('click',function(){

        //$("div.overlay").css("display", "block");

		var id              = $("#edit-id").val();

		var hook_name        = $("#ed_hook_name").val();

		var hook_short_code  = $("#ed_hook_code").val();

		var hook_status      = $('#edit_hook_status').val();

		if($("#ed_hook_name").val() != '' && $("#ed_hook_code").val() != ''){

	    	update_hook(hook_name,hook_short_code,hook_status);

	    }

		else if($("#ed_hook_name").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Enter Hook Name .</div>';

			$("div.overlay").css("display", "none");

	        $('#error-msg1').html(msg);

	        return false;

		}

		else if($("#ed_hook_code").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Hook Code .</div>';

			$("div.overlay").css("display", "none");

	        $('#error-msg1').html(msg);

	        return false;

		}

	});

	$('#hook_date').daterangepicker(

	{

		  ranges: {

			'Today'		  : [moment(), moment()],

			'Yesterday'	  : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],

			'Last 7 Days' : [moment().subtract(6, 'days'), moment()],

			'Last 30 Days': [moment().subtract(29, 'days'), moment()],

			'This Month'  : [moment().startOf('month'), moment().endOf('month')],

			'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month'				).endOf('month')]

		  },

		  startDate: moment().subtract(29, 'days'),

		  endDate: moment()

	},

	function (start, end) {

				set_hook_table(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));

				$('#hook1').text(moment().startOf('month').format('YYYY-MM-DD'));

				$('#hook2').text(moment().endOf('month').format('YYYY-MM-DD'));

		  }

	);

	$("#ad_hook_status").on('switchChange.bootstrapSwitch', function (event, state) {

	  var x = $(this).data('on-text');

	  var y = $(this).data('off-text');

		if($("#ad_hook_status").is(':checked') && x=='YES')

		{

			$("#add_hook_status").val(1);

		}

		else if(y=='NO')

		{

			$("#add_hook_status").val(0);

		}

	});

	$("#ed_hook_status").on('switchChange.bootstrapSwitch', function (event, state) {

	  var x = $(this).data('on-text');

	  var y = $(this).data('off-text');

		if($("#ed_hook_status").is(':checked') && x=='YES')

		{

			$("#edit_hook_status").val(1);

		}

		else if(y == 'NO')

		{

			$("#edit_hook_status").val(0);

		}

	});

	/**

	*	Master :: Hook

	*	Ends

	*/

	/**

	*	Master :: Stone

	*	Starts

	*/

$("#add_newstone").on('click',function(){

		if($("#section_name").val() == '')

		{

				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'please select Enter UOM Name..'});

		}

		else if($('#stone_name').val()=='')

		{

			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'please Enter Stone Name..'});

		}

		else if($('#stone_code').val()=='')

		{

			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter Stone Code..'});

		}

		else

		{

			$("#add_newstone").prop('disabled',true);

			add_stone();

		}

	});

$("#add_stonenew").on('click', function () {

    if ($("#uom_id").val() == '') {

        $.toaster({ priority: 'danger', title: 'Warning!', message: 'Please select Enter UOM Name.' });

    }

    else if ($('#stone_name').val() == '') {

        $.toaster({ priority: 'danger', title: 'Warning!', message: 'Please enter Stone name.' });

    }

    else if ($('#stone_code').val() == '') {

        $.toaster({ priority: 'danger', title: 'Warning!', message: 'Please select Stone Code.' });

    }

    else {

        $("#add_stonenew").prop('disabled', true);

		$("#stone_sel").val(null).trigger("change");

        add_new_stone();

    }

});

function add_new_stone() {

    my_Date = new Date();

    $.ajax({

        data: {

            "uom_id": $("#uom_id").val(),

            "stone_name": $("#stone_name").val(),

            "stone_code": $("#stone_code").val(),

            "stone_type": $("input[name='stone_type']:checked").val(),

            "is_certificate_req": $("#is_certificate_req").val(),

            "is_4c_req": $("#is_4c_req").val(),

            "stone_status": $("#stone_status").val()

        },

        url: base_url + "index.php/admin_ret_catalog/stone/add?nocache=" + my_Date.getUTCSeconds(),

        type: "POST",

        dataType: 'json',

        success: function (data) {

            $("#add_new_stone").prop('disabled', false);

            if (data['status'] == true) {

                msg = '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>' + data['message'] + '</strong></div>';

            } else {

                msg = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>' + data['message'] + '</strong></div>';

			}

			$("#uom_id").val('');

			$("#stone_name").val('');

			$("#stone_code").val('');

			$("input[name='stone_type']:checked").val('');

			$("#is_certificate_req").val('');

			$("#is_4c_req").val('');

            $('#error-msg').html(msg);

            set_stone_table();

        }

    });

}

 function add_stone()

{

	my_Date = new Date();

	$.ajax({

		data:{"uom_id":$("#uom_id").val(),"stone_name":$("#stone_name").val(),"stone_code":$("#stone_code").val(),"stone_type":$("input[name='stone_type']:checked").val(),"is_certificate_req":$("#is_certificate_req").val(),"is_4c_req":$("#is_4c_req").val(),"stone_status":$("#stone_status").val()},

		url: base_url+"index.php/admin_ret_catalog/stone/add?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		cache:false,

		success:function(data){

			$("#uom_id").val('');

			$("#stone_name").val('');

			$("#stone_code").val('');

			$("input[name='stone_type']:checked").val('');

			$("#is_certificate_req").val('');

			$("#is_4c_req").val('');

			window.location.reload(true);

		}

	});

}

		$("#stone_sel,#ed_stone_sel").select2(

		{

			placeholder:"Select uom name"

		});

	$("#stone_sel,#ed_stone_sel").on('change',function(e){

		if(this.value!='')

		{

		$("#uom_id,#ed_uom_id").val(this.value);

		//alert(this.value);

		}

		else

		{

		$("#uom_id,#ed_uom_id").val('');

		}

	});

	$(document).on('click', "#stone_list a.btn-edit", function(e) {

			e.preventDefault();

			id=$(this).data('id');

		    get_stone(id);

		    $("#edit-id").val(id);

		});

	//update stone

	$("#update_stone").on('click',function(){

		var id=$("#edit-id").val();

		var uom_id=$("#ed_uom_id").val();

		var stone_name=$("#ed_stone_name").val();

		var stone_code=$("#ed_stone_code").val();

		var stone_type=$("input[name='ed_stone_type']:checked").val();

		var is_certificate_req=$("#ed_is_certificate_req").val();

		var is_4c_req=$("#ed_is_4c_req").val();

		var stone_status=$("#ed_stone_status").val();

		if($("#ed_uom_id").val() != '' && $("#ed_stone_name").val() != '' && $("#ed_stone_code").val() != '' && $("#ed_stone_type").val() != '' && $("#ed_is_certificate_req").val() != '' && $("#ed_is_4c_req").val() != ''){

			    update_stone(uom_id,stone_name,stone_code,stone_type,is_certificate_req,is_4c_req,stone_status);

			}

			else if($("#ed_uom_id").val() == ''){

				msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter UOM name.</div>';

				$("div.overlay").css("display", "none");

		        $('#error').html(msg);

		        return false;

			}

			else if($("#ed_stone_name").val() == ''){

				msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Stone name.</div>';

				$("div.overlay").css("display", "none");

		        $('#error').html(msg);

		        return false;

			}

			else if($("#ed_stone_code").val() == ''){

				msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Stone code.</div>';

				$("div.overlay").css("display", "none");

		        $('#error').html(msg);

		        return false;

			}

			else if($("#ed_stone_type").val() == ''){

				msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Select stone type.</div>';

				$("div.overlay").css("display", "none");

		        $('#error').html(msg);

		        return false;

			}

	});

	$("#stone_switch").on('switchChange.bootstrapSwitch', function (event, state) {

	        var x=$(this).data('on-text');

	        var y=$(this).data('off-text');

					if($("#stone_switch").is(':checked') && x=='YES')

					{

						$("#stone_status").val(1);

	        } else if(y=='NO')

			{

	          $("#stone_status").val(0);

			}

	    });

		$("#ed_stone_switch").on('switchChange.bootstrapSwitch', function (event, state) {

	        var x=$(this).data('on-text');

	        var y=$(this).data('off-text');

					if($("#ed_stone_switch").is(':checked') && x=='YES')

					{

						$("#ed_stone_status").val(1);

	        } else if(y=='NO')

			{

	          $("#ed_stone_status").val(0);

			}

	    });

	    $("#ce_switch").bootstrapSwitch('state', false);

		$("#ce_switch").on('switchChange.bootstrapSwitch', function (event, state) {

	        var x=$(this).data('on-text');

	        var y=$(this).data('off-text');

					if($("#ce_switch").is(':checked') && x=='YES')

					{

						$("#is_certificate_req").val(1);

	        } else if(y=='NO')

			{

	          $("#is_certificate_req").val(0);

			}

	    });

		$("#ed_ce_switch").on('switchChange.bootstrapSwitch', function (event, state) {

	        var x=$(this).data('on-text');

	        var y=$(this).data('off-text');

					if($("#ed_ce_switch").is(':checked') && x=='YES')

					{

						$("#ed_is_certificate_req").val(1);

	        } else if(y=='NO')

			{

	          $("#ed_is_certificate_req").val(0);

			}

	    });

	    $("#4c_switch").bootstrapSwitch('state', false);

		$("#4c_switch").on('switchChange.bootstrapSwitch', function (event, state) {

	        var x=$(this).data('on-text');

	        var y=$(this).data('off-text');

					if($("#4c_switch").is(':checked') && x=='YES')

					{

						$("#is_4c_req").val(1);

	        } else if(y=='NO')

			{

	          $("#is_4c_req").val(0);

			}

	    });

	$("#ed_isreq_switch").on('switchChange.bootstrapSwitch', function (event, state) {

        var x=$(this).data('on-text');

        var y=$(this).data('off-text');

		if($("#ed_isreq_switch").is(':checked') && x=='YES')

		{

			$("#ed_is_4c_req").val(1);

        } else if(y=='NO')

		{

          $("#ed_is_4c_req").val(0);

		}

    });

 	$('#stone_date').daterangepicker(

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

	     set_stone_table(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));

			$('#stone1').text(start.format('YYYY-MM-DD'));

			$('#stone2').text(end.format('YYYY-MM-DD'));

	}

	);

	/**

	*	Master :: Stone

	*	Ends

	*/

	/**

	*	Master :: UOM

	*	Starts

	*/

	$("#new_adduom,#add_newuom").on('click',function(){

		if($("#uom_name").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter UOM Name .</div>';

			$("div.overlay").css("display", "none");

			$('#error-msg').html(msg);

			return false;

		}

		else if($("#uom_code").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter UOM Code .</div>';

			$("div.overlay").css("display", "none");

			$('#error-msg').html(msg);

			return false;

		}

		else if($("#uom_name").val() != '' && $("#uom_code").val() != ''){

			add_uom($('#uom_name').val(),$('#uom_code').val(),$('#add_uom_status').val());

			$('#uom_name').val('');

			$('#uom_code').val('');

			$('#add_uom_status').val('');

			if(this.id == "add_newuom"){

				return true;

			}

			else{

				return false;

			}

		}

	});

	$(document).on('click', "#uom_list a.btn-edit", function(e) {

		e.preventDefault();

		id=$(this).data('id');

	    get_uom(id);

	    $("#edit-id").val(id);

	});

    $("#update_uom").on('click',function(){

        //$("div.overlay").css("display", "block");

		var id              = $("#edit-id").val();

		var uom_name        = $("#ed_uom_name").val();

		var uom_short_code  = $("#ed_uom_code").val();

		var uom_status      = $('#edit_uom_status').val();

		if($("#ed_uom_name").val() != '' && $("#ed_uom_code").val() != ''){

	    	update_uom(uom_name,uom_short_code,uom_status);

	    }

		else if($("#ed_uom_name").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Enter UOM Name .</div>';

			$("div.overlay").css("display", "none");

	        $('#error-msg1').html(msg);

	        return false;

		}

		else if($("#ed_uom_code").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter UOM Code .</div>';

			$("div.overlay").css("display", "none");

	        $('#error-msg1').html(msg);

	        return false;

		}

	});

	$('#uom_date').daterangepicker(

	{

		  ranges: {

			'Today'		  : [moment(), moment()],

			'Yesterday'	  : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],

			'Last 7 Days' : [moment().subtract(6, 'days'), moment()],

			'Last 30 Days': [moment().subtract(29, 'days'), moment()],

			'This Month'  : [moment().startOf('month'), moment().endOf('month')],

			'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month'				).endOf('month')]

		  },

		  startDate: moment().subtract(29, 'days'),

		  endDate: moment()

	},

	function (start, end) {

				set_uom_table(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));

				$('#uom1').text(moment().startOf('month').format('YYYY-MM-DD'));

				$('#uom2').text(moment().endOf('month').format('YYYY-MM-DD'));

		  }

	);

	$("#ad_uom_status").on('switchChange.bootstrapSwitch', function (event, state) {

	  var x = $(this).data('on-text');

	  var y = $(this).data('off-text');

		if($("#ad_uom_status").is(':checked') && x=='YES')

		{

			$("#add_uom_status").val(1);

		}

		else if(y=='NO')

		{

			$("#add_uom_status").val(0);

		}

	});

	$("#ed_uom_status").on('switchChange.bootstrapSwitch', function (event, state) {

	  var x = $(this).data('on-text');

	  var y = $(this).data('off-text');

		if($("#ed_uom_status").is(':checked') && x=='YES')

		{

			$("#edit_uom_status").val(1);

		}

		else if(y == 'NO')

		{

			$("#edit_uom_status").val(0);

		}

	});

	/**

	*	Master :: UOM

	*	Ends

	*/

	/**

	*	Master :: Category

	*	Starts

	*/

	$('#category_date').daterangepicker(

	{

		  ranges: {

			'Today'		  : [moment(), moment()],

			'Yesterday'	  : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],

			'Last 7 Days' : [moment().subtract(6, 'days'), moment()],

			'Last 30 Days': [moment().subtract(29, 'days'), moment()],

			'This Month'  : [moment().startOf('month'), moment().endOf('month')],

			'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]

		  },

		  startDate: moment().subtract(29, 'days'),

		  endDate: moment()

	},

	function (start, end) {

				set_category_table(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));

				$('#category1').text(moment().startOf('month').format('YYYY-MM-DD'));

				$('#category2').text(moment().endOf('month').format('YYYY-MM-DD'));

		  }

	);

	$("#add_categorynew,#add_newcategory").on('click',function(){

		var cat_type	  = $('input[name="category_type"]:checked').val();

		var is_multimetal = $("input[name='is_multimetal']:checked").val();

		if($("#category_name").val() != '' && $("#id_metal_category").val() != '' && $("#cat_code").val() != '' && $("#pur_id").val() != '' && $("#hsn_code").val() != '' && $("#tgrp_sel").val() != '' && (is_multimetal == 0 || is_multimetal == 1) && cat_type != ''){

			var file = $("#categorymtr_img")[0].files[0];

			add_categorymtr($('#category_name').val(),$('#cat_code').val(),$('#category_desc').val(),$('#id_metal_category').val(),$('#pur_id').val(),$('#add_category_status').val(),file,$("#hsn_code").val(), is_multimetal,$("#tgrp_sel").val(), cat_type)

			$('#category_name').val('');

			$('#hsn_code').val('');

			$('#cat_code').val('');

			$('#category_desc').val('');

			$('#id_metal_category').val('');

			$('#pur_id').val('');

			$('#categorymtr_img').val('');

			$('#add_category_status').val('');

			$("#multimetal_no").prop('checked', true);

			$("input[name='category_type'][value=1]").prop('checked', true);

		}

		else if($("#tgrp_sel").val()=='')

		{

		    msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Select  The Tax Group .</div>';

			$("div.overlay").css("display", "none");

			$('#error-msg').html(msg);

			return false;

		}

        else if($("#category_name").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Category Name .</div>';

			$("div.overlay").css("display", "none");

			$('#error-msg').html(msg);

			return false;

		}

		else if($("#cat_code").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Category Code .</div>';

			$("div.overlay").css("display", "none");

			$('#error-msg').html(msg);

			return false;

		}

		else if($("#id_metal_category").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Select Metal .</div>';

			$("div.overlay").css("display", "none");

			$('#error-msg').html(msg);

			return false;

		}

	});

	$(document).on('click', "#categorymtr_list a.btn-edit", function(e) {

		e.preventDefault();

		id = $(this).data('id');

	    get_categorymtr(id);

	    $("#edit-id").val(id);

	});

    $("#update_category").on('click',function(){

		var file		  = $("#ed_categorymtr_img")[0].files[0];

		var id            = $("#edit-id").val();

		var name          = $("#ed_category_name").val();

		var hsn_code      = $("#ed_hsn_code").val();

		var cat_code      = $("#ed_cate_code").val();

		var cat_type	  = $('input[name="ed_category_type"]:checked').val();

		var description   = $("#ed_category_desc").val();

		var id_metal      = $('#id_metal_cate').val();

		var id_purity     = $('#ed_pur_id').val();

		var status        = $('#edit_category_status').val();

		var is_multimetal = $("input[name='ed_is_multimetal']:checked").val();

		if($("#ed_category_name").val() != ''  && $("#id_metal_cate").val() != '' && $("#ed_cate_code").val() != '' && $("#ed_hsn_code").val() != '' && $('#ed_tgrp_sel').val()!='' && $('#ed_tgrp_sel').val()!=null && (is_multimetal == 0 || is_multimetal == 1) && cat_type != ''){

	    	update_categorymtr(name,cat_code,description,id_metal,id_purity,status,file,hsn_code,is_multimetal,$('#ed_tgrp_sel').val(),cat_type);

	    }

		else if($("#ed_category_name").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Enter Category Name .</div>';

			$("div.overlay").css("display", "none");

	        $('#error-msg1').html(msg);

	        return false;

		}

		else if($("#ed_tgrp_sel").val() == '' || $("#ed_tgrp_sel").val() == null){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Select Tax Group</div>';

			$("div.overlay").css("display", "none");

	        $('#error-msg1').html(msg);

	        return false;

		}

		else if($("#id_metal_cate").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Select Metal.</div>';

			$("div.overlay").css("display", "none");

	        $('#error-msg1').html(msg);

	        return false;

		}

		else if($("#ed_cate_code").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Enter Category Code.</div>';

			$("div.overlay").css("display", "none");

	        $('#error-msg1').html(msg);

	        return false;

		}

		else if(cat_type == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Choose Category Type.</div>';

			$("div.overlay").css("display", "none");

	        $('#error-msg1').html(msg);

	        return false;

		}

	});

    $("#ad_category_status").on('switchChange.bootstrapSwitch', function (event, state) {

	  var x = $(this).data('on-text');

	  var y = $(this).data('off-text');

		if($("#ad_category_status").is(':checked') && x=='YES')

		{

			$("#add_category_status").val(1);

		}

		else if(y=='NO')

		{

			$("#add_category_status").val(0);

		}

	});

    $("#ed_category_status").on('switchChange.bootstrapSwitch', function (event, state) {

	  var x = $(this).data('on-text');

	  var y = $(this).data('off-text');

		if($("#ed_category_status").is(':checked') && x=='YES')

		{

			$("#edit_category_status").val(1);

		}

		else if(y == 'NO')

		{

			$("#edit_category_status").val(0);

		}

	});

	$("#categorymtr_img,#ed_categorymtr_img").change( function(e){

	    e.preventDefault();

	    valCategory_Image(this);

	});

	/**

	*	Master :: Category

	*	Ends

	*/

/**

	*	Master :: Karigar

	*	Ends

	*/

	$('#email_karigar').on('blur onchange',function(){

		var emailRegEx = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i;

	   if (this.value.search(emailRegEx) == -1)

	   {

			$(this).val('');

		  $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Enter valid email id..'});

	   }

	 else

	 {

		   checkEmailAvail(this.value);

	 }

 });

  function checkEmailAvail(email)

  {

	  $("div.overlay").css("display", "block");

	  $.ajax({

		  type: 'POST',

		  data:{'email':email},

		  url:  base_url+'index.php/admin_ret_catalog/karigar/email_available',

		  dataType: 'json',

		  success: function(data)

		  {

			  if(data.status==false)

			  {

				  $('#email_karigar').val('');

				  $('#email_karigar').focus();

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

  $('#karigar_mobile').on('blur onchange',function(){

	  if(this.value!='')

	  {

		  if(this.value.length != mob_no_len)

		  {

			  $('#karigar_mobile').val('');

			  $('#karigar_mobile').focus();

			  $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Valid Mobile Number..'});

		  }else if(this.value=='')

		  {

			  $('#karigar_mobile').val('');

			  $('#karigar_mobile').focus();

			  $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Mobile Number..'});

		  }else {

			  checkKarigarMobileAvail(this.value);

		  }

	  }

  });

function checkKarigarMobileAvail(mobile)

{

  $("div.overlay").css("display", "block");

  $.ajax({

	  type: 'POST',

	  data:{'mobile':mobile },

	  url:  base_url+'index.php/admin_ret_catalog/karigar/mobile_available',

	  dataType: 'json',

	  success: function(data) {

		  if(data.status==false)

		  {

			  $('#karigar_mobile').val('');

			  $('#karigar_mobile').focus();

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

	function get_ActiveproductForKarigar(itemObj) {

		$(itemObj).empty();

		let karg_wast_prod_href = base_url+"index.php/admin_ret_catalog/ret_product/active_list/?nocache=" + my_Date.getUTCSeconds();

		let karg_wast_prod_data = {

			'id_category': ''

		}

		_ajaxCallPost(karg_wast_prod_href, karg_wast_prod_data)

		.then((data) => {

			$.each(data, function (key, item) {

				$(itemObj).append(

					$("<option></option>")

					.attr("value", item.pro_id)

					.text(item.product_name)

				);

			});

			$(itemObj).select2({

				placeholder:"Select Product",

				allowClear: true

			});

			$(itemObj).select2("val",'');

		})

		.catch((error) => {

			console.log("Error On wastage_product onLoad ",error);

			$.toaster({ priority : 'danger', title : 'Warning!', message : "Error occured in retriving data."});

		});

	}

	function getActiveDesignsForKarigar(itemObj) {

		let parent_class = $(itemObj).parent().parent().parent();

		$(parent_class).find(".wastage_design").empty();

		let karigar_wast_product_id = $(itemObj).val();

		let karg_wast_design_href = base_url+'index.php/admin_ret_catalog/get_active_design_products';

		let karg_wast_design_data = {

			"id_product" : karigar_wast_product_id

		}

		_ajaxCallPost(karg_wast_design_href, karg_wast_design_data)

		.then((data) => {

			$.each(data, function (key, item) {

				$(parent_class).find(".wastage_design").append(

					$("<option></option>")

					.attr("value", item.design_no)

					.text(item.design_name)

				);

			});

			$(parent_class).find(".wastage_design").select2({

				placeholder:"Select Design",

				allowClear: true

			});

			$(parent_class).find(".wastage_design").select2("val",'');

		})

		.catch((error) => {

			console.log("Error On wastage_product onChange ",error);

			$.toaster({ priority : 'danger', title : 'Warning!', message : "Error occured in retriving data."});

		});

	}

	function getActiveSubDesignsForKarigar(itemObj) {

		let parent_class = $(itemObj).parent().parent().parent();

		$(parent_class).find(".wastage_sub_design").empty();

		let karigar_wast_design_id = $(parent_class).find(".wastage_design").val();

		let karigar_wast_product_id = $(parent_class).find(".wastage_product").val();

		let karg_wast_sub_design_href = base_url+'index.php/admin_ret_catalog/get_ActiveSubDesigns';

		let karg_wast_sub_design_data = {

			'design_no'	: karigar_wast_design_id,

			'product_id' : karigar_wast_product_id

		}

		_ajaxCallPost(karg_wast_sub_design_href, karg_wast_sub_design_data)

		.then((data) => {

			$.each(data, function (key, item) {

				$(parent_class).find(".wastage_sub_design").append(

					$("<option></option>")

					.attr("value", item.id_sub_design)

					.text(item.sub_design_name)

				);

			});

			$(parent_class).find(".wastage_sub_design").select2({

				placeholder:"Select Sub Design",

				allowClear: true

			});

			$(parent_class).find(".wastage_sub_design").select2("val",'');

		})

		.catch((error) => {

			console.log("Error On wastage_design onChange ",error);

			$.toaster({ priority : 'danger', title : 'Warning!', message : "Error occured in retriving data."});

		});

	}

	$(document).on('change',".wast_percent",function(e){

	    var cuRrow = $(this).closest('tr');

	    var wast_per    = cuRrow.find('.wast_percent').val();

	    if(parseFloat(wast_per)>parseFloat(100))

	    {

	        cuRrow.find('.wast_percent').val("");

	        $.toaster({ priority : 'danger', title : 'Warning!', message : "Please Enter The Valid Percentage."});

	    }

	});

	/*$(document).on('change',".pur_touch",function(e){

	    var cuRrow = $(this).closest('tr');

	    var wast_per    = cuRrow.find('.pur_touch').val();

	    if(parseFloat(wast_per)>parseFloat(100))

	    {

	        cuRrow.find('.pur_touch').val("");

	        $.toaster({ priority : 'danger', title : 'Warning!', message : "Please Enter The Valid Touch."});

	    }

	});*/

	$(document).on('change','.kar_calc_type',function()

	{

		var row = $(this).closest('tr');

		row.find('.wast_percent').prop("disabled",false);

		row.find('.mc').prop("disabled",false);

		row.find('.mc_type').prop("disabled",false);

		row.find('.va_type').prop("disabled",false);

		row.find('.wast_wgt').prop("disabled",false);

		if(this.value==2)

		{

			row.find('.wast_percent').prop("disabled",true);

			//row.find('.mc').prop("disabled",true);

			//row.find('.mc_type').prop("disabled",true);

			row.find('.va_type').prop("disabled",true);

			row.find('.wast_wgt').prop("disabled",true);

		}

	});

	function add_karigar_wastage()

	{

	    var karigar_calc_type = $('#karigar_calc_type').val();

		if(validate_karigar_wastages()) {

			let _html_add = '<div class="row wastages">'+

								'<div class="col-md-2">'+

									'<div class="form-group">'+

										'<select class="form-control wastage_product"  name="karigar[wastage][product][]" placeholder="Product Name"></select>'+

									'</div>'+

								'</div>'+

								'<div class="col-md-2">'+

									'<div class="form-group">'+

										'<select class="form-control wastage_design"  name="karigar[wastage][design][]" placeholder="Design Name"></select>'+

									'</div>'+

								'</div>'+

								'<div class="col-md-2">'+

									'<div class="form-group">'+

										'<select class="form-control wastage_sub_design"  name="karigar[wastage][sub_design][]" placeholder="Sub Design Name"></select>'+

									'</div>'+

								'</div>'+

								'<div class="col-md-1">'+

									'<div class="form-group">'+

										'<input class="form-control wastage_value" style="width:80px;" name="karigar[wastage][value][]"  type="number" placeholder="Wastage Value" '+(karigar_calc_type==2 ? 'disabled' :'')+' />'+

									'</div>'+

								'</div>'+

								'<div class="col-md-2">'+

									'<div class="form-group">'+

									'<select class="form-control mc_type" name="karigar[wastage][mc_type][]" placeholder="Mc Type" '+(karigar_calc_type==2 ? 'disabled' :'')+'><option value="1">Per Grm</option><option value="2">Per Pcs</option></select>'+

									'</div>'+

								'</div>'+

								'<div class="col-md-1">'+

									'<div class="form-group">'+

										'<input class="form-control mc_value" style="width:80px;" name="karigar[wastage][mc_value][]"  type="number" placeholder="MC Value" '+(karigar_calc_type==2 ? 'disabled' :'')+'/>'+

									'</div>'+

								'</div>'+

								'<div class="col-md-1">'+

									'<div class="form-group">'+

										'<input class="form-control pur_touch" style="width:80px;" name="karigar[wastage][pur_touch][]"  type="number" placeholder="purchase touch" />'+

									'</div>'+

								'</div>'+

								'<div class="col-md-1 karigar_wastage_buttons">'+

									'<div class="form-group">'+

										'<button type="button" class="btn btn-success add_karigar_wastage"><i class="fa fa-plus"></i></button>'+

										'<button type="button" class="btn btn-danger remove_karigar_wastage"><i class="fa fa-trash"></i></button>'+

									'</div>'+

								'</div>'+

							'</div>';

			$(".tab-wastage").append(_html_add);

			$('.tab-wastage .wastages:not(:last-child) .add_karigar_wastage').remove();

			$('.tab-wastage .wastages:last-child').find('.wastage_product').select2();

			$('.tab-wastage .wastages:last-child').find('.wastage_design').select2();

			$('.tab-wastage .wastages:last-child').find('.wastage_sub_design').select2();

			get_ActiveproductForKarigar($('.tab-wastage .wastages:last-child').find('.wastage_product'));

		} else {

			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill The Required Fields..'});

		}

	}

	function remove_karigar_wastage(itemObj) {

		$(itemObj).parent().parent().parent().remove();

		$('.tab-wastage .wastages .add_karigar_wastage').remove();

		$('.tab-wastage .wastages:last-child .karigar_wastage_buttons > div').prepend('<button type="button" class="btn btn-success add_karigar_wastage"><i class="fa fa-plus"></i></button>');

	}

	function validate_karigar_wastages() {

		let wastages_validated = true;

		$('.tab-wastage').children('.wastages').each(function () {

			let wast_prod =  ($(this).find('.wastage_product').val());

			let wast_desn =  ($(this).find('.wastage_design').val());

			let wast_sub_desn 	=  ($(this).find('.wastage_sub_design').val());

			let wast_val 		=  ($(this).find('.wastage_value').val());

			let pur_touch 		=  ($(this).find('.pur_touch').val());

			if(wast_prod =='' || wast_desn =='' || wast_sub_desn=='')

			{

				wastages_validated = false;

				return false;

			}

			else if($('#karigar_calc_type').val()==2 && (pur_touch==0 || pur_touch=='') )

			{

			    wastages_validated = false;

				return false;

			}

			else if(wast_val=='' && $('#karigar_calc_type').val()!=2)

			{

			    wastages_validated = false;

				return false;

			}

		});

		return wastages_validated;

	}

/*Karigar Stones */

	function add_karigar_stones()

	{

		if(validate_karigar_stones()) {

		let _html_add = '<div class="row stones">'+

							'<div class="col-md-2">'+

								'<div class="form-group">'+

									'<select class="form-control stone_type"  name="karigar[stone][stn_type][]" placeholder="Stone Type"></select>'+

								'</div>'+

							'</div>'+

							'<div class="col-md-2">'+

								'<div class="form-group">'+

									'<select class="form-control stone_name"  name="karigar[stone][stn_name][]" placeholder="Stone Name"></select>'+

								'</div>'+

							'</div>'+

							'<div class="col-md-2">'+

								'<div class="form-group">'+

									'<select class="form-control uom_id"  name="karigar[stone][uom_id][]" placeholder="UOM Name"></select>'+

								'</div>'+

							'</div>'+

							'<div class="col-md-2">'+

								'<div class="form-group">'+

									'<select class="form-control stn_calc_type" name="karigar[stone][stn_calc_type][]" placeholder=" Type"><option value="1">Wt</option><option value="2">Pcs</option></select>'+

								'</div>'+

							'</div>'+

							'<div class="col-md-2">'+

								'<div class="form-group">'+

								  '<input class="form-control stone_rate"  name="karigar[stone][stn_rate][]"  type="number" placeholder="Rate" />'+

								'</div>'+

							'</div>'+

							'<div class="col-md-2 karigar_stones_buttons">'+

								'<div class="form-group">'+

									'<button type="button" class="btn btn-success add_karigar_stones"><i class="fa fa-plus"></i></button>'+

									'<button type="button" class="btn btn-danger remove_karigar_stones"><i class="fa fa-trash"></i></button>'+

								'</div>'+

							'</div>'+

		              	'</div>';

				$(".tab-stones").append(_html_add);

				$('.tab-stones .stones:not(:last-child) .add_karigar_stones').remove();

				$('.tab-stones .stones:last-child').find('.stone_type').select2();

				$('.tab-stones .stones:last-child').find('.stone_name').select2();

				$('.tab-stones .stones:last-child').find('.uom_id').select2();

				$('.tab-stones .stones:last-child').find('.stn_calc_type').select2();

				get_ActiveStoneTypeForKarigar($('.tab-stones .stones:last-child').find('.stone_type'));

				get_ActiveUomForKarigar($('.tab-stones .stones:last-child').find('.uom_id'));

			}

		else

		{

			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill The Required Fields..'});

		}

	}

	function validate_karigar_stones() {

		let stones_validated = true;

		$('.tab-stones').children('.stones').each(function () {

			let stn_type =  $.trim($(this).find('.stone_type').val());

			let stn_name =  $.trim($(this).find('.stone_name').val());

			let stn_calc_type 	=  $.trim($(this).find('.stn_calc_type').val());

			let stone_rate 		=  $.trim($(this).find('.stone_rate').val());

			if(!stn_type > 0 || !stn_name > 0 || !stn_calc_type > 0 || !stone_rate > 0)

			{

				stones_validated = false;

				return false;

			}

		});

		return stones_validated;

	}

	function remove_karigar_stones(itemObj) {

		$(itemObj).parent().parent().parent().remove();

		$('.tab-stones .stones .add_karigar_stones').remove();

		$('.tab-stones .stones:last-child .karigar_stones_buttons > div').prepend('<button type="button" class="btn btn-success add_karigar_stones"><i class="fa fa-plus"></i></button>');

	}

	function get_ActiveStoneTypeForKarigar(itemObj) {

		$(itemObj).empty();

		let karg_stn_type_href = base_url + "index.php/admin_ret_tagging/getStoneTypes";

		_ajaxCallPost(karg_stn_type_href)

		.then((data) => {

			$.each(data, function (key, item) {

				$(itemObj).append(

					$("<option></option>")

					.attr("value", item.id_stone_type)

					.text(item.stone_type)

				);

			});

			$(itemObj).select2({

				placeholder:"Select Stone Type",

				allowClear: true

			});

			$(itemObj).select2("val",'');

		})

		.catch((error) => {

			console.log("Error On wastage_product onLoad ",error);

			$.toaster({ priority : 'danger', title : 'Warning!', message : "Error occured in retriving data."});

		});

	}

	function get_ActiveUomForKarigar(itemObj)

	{

		$(itemObj).empty();

		let karg_stn_uom_href = base_url + 'index.php/admin_ret_tagging/get_ActiveUOM';

		_ajaxCallPost(karg_stn_uom_href)

		.then((data) => {

			$.each(data, function (key, item) {

				$(itemObj).append(

					$("<option></option>")

					.attr("value", item.uom_id)

					.text(item.uom_name)

				);

			});

			$(itemObj).select2({

				placeholder:"Select UOM",

				allowClear: true

			});

			$(itemObj).select2("val",'');

		})

		.catch((error) => {

			console.log("Error On Uom onLoad ",error);

			$.toaster({ priority : 'danger', title : 'Warning!', message : "Error occured in retriving data."});

		});

	}

	function getActiveStonesForKarigar(itemObj)

	{

		let parent_class = $(itemObj).parent().parent().parent();

		$(parent_class).find(".stone_name").empty();

		let karigar_stn_type_id = $(itemObj).val();

		let karg_stn_name_href = base_url+'index.php/admin_ret_tagging/getStoneItems';

		let karg_stone_data = {

			"stone_type" : karigar_stn_type_id

		}

		_ajaxCallPost(karg_stn_name_href, karg_stone_data)

		.then((data) => {

			$.each(data, function (key, item) {

				$(parent_class).find(".stone_name").append(

					$("<option></option>")

					.attr("value", item.stone_id)

					.text(item.stone_name)

				);

			});

			$(parent_class).find(".stone_name").select2({

				placeholder:"Select Stones",

				allowClear: true

			});

			$(parent_class).find(".stone_name").select2("val",'');

		})

		.catch((error) => {

			console.log("Error On Stone Type onChange ",error);

			$.toaster({ priority : 'danger', title : 'Warning!', message : "Error occured in retriving data."});

		});

	}

/*Karigar Stones*/

/*Karigar Charges*/

function add_karigar_charges()

	{

		if(validate_karigar_charges()) {

		let _html_add = '<div class="row charges">'+

							'<div class="col-md-3">'+

								'<div class="form-group">'+

									'<select class="form-control charge_name"  name="karigar[charges][charge_name][]" placeholder="Charge  Name"></select>'+

								'</div>'+

							'</div>'+

							'<div class="col-md-3">'+

								'<div class="form-group">'+

								  '<input class="form-control charge_value"  name="karigar[charges][charge_value][]"  type="number" placeholder="Value" />'+

								'</div>'+

							'</div>'+

							'<div class="col-md-2 karigar_charges_buttons">'+

								'<div class="form-group">'+

									'<button type="button" class="btn btn-success add_karigar_charges"><i class="fa fa-plus"></i></button>'+

									'<button type="button" class="btn btn-danger remove_karigar_charges"><i class="fa fa-trash"></i></button>'+

								'</div>'+

							'</div>'+

		              	'</div>';

				$(".tab-charges").append(_html_add);

				$('.tab-charges .charges:not(:last-child) .add_karigar_charges').remove();

				$('.tab-charges .charges:last-child').find('.charge_name').select2();

				get_ActiveChargesForKarigar($('.tab-charges .charges:last-child').find('.charge_name'));

			}

		else

		{

			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill The Required Fields..'});

		}

	}

	function validate_karigar_charges() {

		let charges_validated = true;

		$('.tab-charges').children('.charges').each(function () {

			let ch_name =  $.trim($(this).find('.charge_name').val());

			let ch_value =  $.trim($(this).find('.charge_value').val());

			if(!ch_name > 0 || !ch_value > 0 )

			{

				charges_validated = false;

				return false;

			}

		});

		return charges_validated;

	}

	function remove_karigar_charges(itemObj) {

		$(itemObj).parent().parent().parent().remove();

		$('.tab-charges .charges .add_karigar_charges').remove();

		$('.tab-charges .charges:last-child .karigar_charges_buttons > div').prepend('<button type="button" class="btn btn-success add_karigar_charges"><i class="fa fa-plus"></i></button>');

	}

	function get_ActiveChargesForKarigar(itemObj) {

		$(itemObj).empty();

		let karg_charges_href = base_url + 'index.php/admin_ret_catalog/charges/getActiveChargesList/?nocache=' + my_Date.getUTCSeconds();

		_ajaxCallPost(karg_charges_href)

		.then((data) => {

			kar_charges_list=data;

			$.each(data, function (key, item) {

				$(itemObj).append(

					$("<option></option>")

					.attr("value", item.id_charge)

					.text(item.name_charge)

				);

			});

			$(itemObj).select2({

				placeholder:"Select Charges",

				allowClear: true

			});

			$(itemObj).select2("val",'');

		})

		.catch((error) => {

			console.log("Error On charges  onLoad ",error);

			$.toaster({ priority : 'danger', title : 'Warning!', message : "Error occured in retriving data."});

		});

	}

	$(document).on('change',".charge_name",function()

	{

	let parent_class = $(this).parent().parent().parent();

	var charge_type=this.value;

	$(kar_charges_list).each(function(idx, charges){

			if(charges.id_charge == charge_type)

			{

				$(parent_class).find(".charge_value").val(charges.value_charge);

			}

		});

	});

/*Karigar Charges*/

	$("input[name='karigar[user_type]']:radio").on('change',function(){

        if(this.value==0)

        {

            $('.registered_type').css('display','none');

        }else if(this.value==1)

        {

            $('.registered_type').css('display','block');

        }

    });

   $('#add_newuser').on('click', function () {

		var user_type = $("input[name='karigar[user_type]']:checked").val();

		if ($('#first_name').val() == ''){

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Enter First Name..' });

		}

		/*else if($('#karigar_code').val() == ''){

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Enter Karigar Code..' });

		}*/

		else if(user_type == 1 && ($('#company_karigar').val() == '' )){

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Enter Company Name..' });

		}

		else if(user_type == 1 && $('#gst_number_karigar').val() == ''){

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Enter GST no..' });

		}

		/*else if($('#pan').val() == ''){

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Enter PAN no..' });

		}*/

		else if($('#karigar_mobile').val() == ''){

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Enter Karigar Mobile..' });

		}

		else if($('#address1').val() == ''){

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Enter Address 1..' });

		}

		/*else if($('#address2_karigar').val() == ''){

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Enter Address 2..' });

		}

		else if($('#address3').val() == ''){

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Enter Address 3..' });

		}*/

		else if(!validateOrderDetailRow()){

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Wastages Not Added.Please Fill The Required Fields..' });

		}

		else if($('#id_country').val() == '' || $('#ed_id_country').val() == ''){

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Select Country..' });

		}

		else if($('#id_state').val() == '' || $('#ed_id_state').val() == ''){

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Select State..' });

		}

		else if($('#id_city').val() == '' || $('#ed_id_city').val() == ''){

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Select City..' });

		}

		else if($('#kar_pincode').val() == ''){

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Enter PIN Code..' });

		}

		/*else if(($('#karigar_for').val() == 1 || $('#karigar_for').val()==2 || $('#karigar_for').val()==5 || $('#karigar_for').val()==6) && ($('#karigar_calc_type').val()=='' || $('#karigar_calc_type').val()==null) ){

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Select Calculation Type..' });

		}*/

		else if(!validateKYCDetailRow())

		{

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'KYC Not Added.Please Fill The Required Fields..' });

		}

		else{

		    $('#add_newuser').prop("disabled",true);

			$('#karigar_form').submit();

		}

	});

    function formcheck(){

        var isrequired = true;

        var fields = $('.item-required').serializeArray();

        $.each(fields, function (i, field) {

            if (!field.value) {

                $.toaster({ priority : 'danger', title : 'Warning!', message : "" + field['name'] + ' is required'});

                isrequired = false;

            }

        });

        return isrequired;

    }

  $("#country,#ed_country").on('change', function () {

		if (this.value != '') {

			$('#id_country,#ed_id_country').val(this.value);

			get_state(this.value);

		} else {

			$('#id_country,#ed_id_country').val('');

			$('#id_state,ed_id_state').val('');

			$('#id_city,#ed_id_city').val('');

			$('#state').select2('val','');

			$('#city').select2('val','');

			$("#state").select2(

				{

					placeholder: "Select State",

					allowClear: true

				});

			$("#city").select2(

				{

					placeholder: "Select City",

					allowClear: true

				});

		}

	});

  $("#state,#ed_state").on('change', function () {

		if (this.value != '') {

			$('#id_state,#ed_id_state').val(this.value);

			get_city(this.value);

		} else {

			$('#id_state,#ed_id_state').val('');

		}

	});

	$("#city,#ed_city").on('change', function () {

		if (this.value != '') {

			$('#id_city,#ed_id_city').val(this.value);

		} else {

			$('#id_city,#ed_id_city').val('');

		}

	});

  $("#user_img,#ed_user_img").change( function(e){

	  e.preventDefault();

	  validate_user_Image(this);

  });

  $("#city,#ed_city,#kar_city").on('change', function() {

	if(this.value!=''){

		$('#id_city,#ed_id_city,#kar_id_city').val(this.value);

	}else{

		$('#id_city,#ed_id_city,#kar_id_city').val('');

	}

  });

  $("#user").on('switchChange.bootstrapSwitch', function (event, state) {

	  var x=$(this).data('on-text');

	  var y=$(this).data('off-text');

	  if($("#user").is(':checked') && x=='YES')

	  {

		  $("#user_status").val(1);

	  } else if(y=='NO')

	  {

		$("#user_status").val(0);

	  }

  });

  $("#ed_user").on('switchChange.bootstrapSwitch', function (event, state) {

	  var x=$(this).data('on-text');

	  var y=$(this).data('off-text');

	  if($("#ed_user").is(':checked') && x=='YES')

	  {

		  $("#ed_user_status").val(1);

	  } else if(y=='NO')

	  {

		$("#ed_user_status").val(0);

	  }

  });

  $('#user_date').daterangepicker(

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

		   set_karigar_table(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));

			  $('#user1').text(start.format('YYYY-MM-DD'));

			  $('#user2').text(end.format('YYYY-MM-DD'));

		}

   );

  /**

  *	Master :: Karigar

  *	Ends

  */

	/**

	*	Master :: Tag Type

	*	Starts

	*/

	$("#add_newtag,#add_tag").on('click',function(){

		if($("#tag_name").val() != ''){

			add_tag($('#tag_name').val(),$('#adtag_status').val());

			$('#tag_name').val('');

		}

		else if($("#tag_name").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Tag Name .</div>';

			$("div.overlay").css("display", "none");

			$('#error-msg').html(msg);

		return false;

		}

	});

	$("#adtag_status").on('change',function(e){

		alert(this.value);

	});

	$(document).on('click', "#tag_list a.btn-edit", function(e) {

		e.preventDefault();

		id=$(this).data('id');

	    get_tag(id);

	    $("#edit-id").val(id);

	});

    $("#update_tag").on('click',function(){

        //$("div.overlay").css("display", "block");

		var id              = $("#edit-id").val();

		var tag_name        = $("#ed_tagname").val();

		var tag_status      = $('#ed_tag_status').val();

		if($("#ed_tagname").val() != ''){

	    update_tag(tag_name,tag_status);

	    }

		else if($("#ed_tagname").val() == ''){

		msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Enter Tag Name .</div>';

		$("div.overlay").css("display", "none");

        $('#error-msg1').html(msg);

        return false;

		}

	});

	$('#tag-dt-btn').daterangepicker(

	{

		  ranges: {

			'Today'		  : [moment(), moment()],

			'Yesterday'	  : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],

			'Last 7 Days' : [moment().subtract(6, 'days'), moment()],

			'Last 30 Days': [moment().subtract(29, 'days'), moment()],

			'This Month'  : [moment().startOf('month'), moment().endOf('month')],

			'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]

		  },

		  startDate: moment().subtract(29, 'days'),

		  endDate: moment()

	},

	function (start, end) {

				set_tag_table(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));

				$('#tag1').text(moment().startOf('month').format('YYYY-MM-DD'));

				$('#tag2').text(moment().endOf('month').format('YYYY-MM-DD'));

		  }

	);

//begin tag

	$('.alert-status').bootstrapSwitch('state', true);

	$("#ad_tagstatus").on('switchChange.bootstrapSwitch', function (event, state) {

		  var x = $(this).data('on-text');

		  var y = $(this).data('off-text');

			if($("#ad_tagstatus").is(':checked') && x=='YES')

			{

				$("#adtag_status").val(1);

			}

			else if(y=='NO')

			{

				$("#adtag_status").val(0);

			}

	});

	$('.alert-status').bootstrapSwitch('state', true);

	$("#ed_tagstatus").on('switchChange.bootstrapSwitch', function (event, state) {

		  var x = $(this).data('on-text');

		  var y = $(this).data('off-text');

			if($("#ed_tagstatus").is(':checked') && x=='YES')

			{

				$("#ed_tag_status").val(1);

			}

			else if(y == 'NO')

			{

				$("#ed_tag_status").val(0);

			}

	});

	/**

	*	Master :: Tag Type

	*	Ends

	*/

	/**

	*	Master :: Tax

	*	Starts

	*/

	$("#add_newtax,#add_tax").on('click',function(){

	 if($("#tax_name").val() != '' && $("#tax_code").val() != '' && $("#tax_percentage").val() != ''){

			add_tax($('#tax_name').val(),$('#tax_code').val(),$('#tax_percentage').val(),$('#adtax_status').val());

			$('#tax_name').val('');

			$('#tax_code').val('');

			$('#tax_percentage').val('');

		}

		else if($("#tax_name").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Tax Name .</div>';

			$("div.overlay").css("display", "none");

			$('#error-msg').html(msg);

		return false;

		}

		else if($("#tax_code").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Tax Code .</div>';

			$("div.overlay").css("display", "none");

			$('#error-msg').html(msg);

		return false;

		}

		else if($("#tax_percentage").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Tax Percentage .</div>';

			$("div.overlay").css("display", "none");

			$('#error-msg').html(msg);

		return false;

		}

	});

	$("#adtax_status").on('change',function(e){

			alert(this.value);

	});

	$(document).on('click', "#tax_list a.btn-edit", function(e) {

		e.preventDefault();

		id=$(this).data('id');

	    get_tax(id);

	    $("#edit-id").val(id);

	});

    $("#update_tax").on('click',function(){

			var id              = $("#edit-id").val();

			var tax_name        = $("#ed_taxname").val();

			var tax_code        = $("#ed_tax_code").val();

			var tax_percentage  = $("#ed_tax_percentage").val();

			var tax_status      = $('#ed_tax_status').val();

			if($("#ed_taxname").val() != '' && $("#ed_tax_code").val() != '' && $("#ed_tax_percentage").val() != ''){

		    update_tax(tax_name,tax_code,tax_percentage,tax_status);

		    }

			else if($("#ed_taxname").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Enter Tax Name .</div>';

			$("div.overlay").css("display", "none");

	        $('#error-msg1').html(msg);

	        return false;

			}

			else if($("#ed_tax_code").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Enter Tax Code .</div>';

			$("div.overlay").css("display", "none");

	        $('#error-msg1').html(msg);

	        return false;

			}

			else if($("#ed_tax_percentage").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Enter Tax Percentage .</div>';

			$("div.overlay").css("display", "none");

	        $('#error-msg1').html(msg);

	        return false;

			}

		});

		$('#tax-dt-btn').daterangepicker(

		{

			  ranges: {

				'Today'		  : [moment(), moment()],

				'Yesterday'	  : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],

				'Last 7 Days' : [moment().subtract(6, 'days'), moment()],

				'Last 30 Days': [moment().subtract(29, 'days'), moment()],

				'This Month'  : [moment().startOf('month'), moment().endOf('month')],

				'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]

			  },

			  startDate: moment().subtract(29, 'days'),

			  endDate: moment()

		},

		function (start, end) {

					set_tax_table(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));

					$('#tax1').text(moment().startOf('month').format('YYYY-MM-DD'));

					$('#tax2').text(moment().endOf('month').format('YYYY-MM-DD'));

			  }

		);

		$('.alert-status').bootstrapSwitch('state', true);

	   $("#ad_taxstatus").on('switchChange.bootstrapSwitch', function (event, state) {

		  var x = $(this).data('on-text');

		  var y = $(this).data('off-text');

			if($("#ad_taxstatus").is(':checked') && x=='YES')

			{

				$("#adtax_status").val(1);

			}

			else if(y=='NO')

			{

				$("#adtax_status").val(0);

			}

	  });

		$('.alert-status').bootstrapSwitch('state', true);

	   $("#ed_taxstatus").on('switchChange.bootstrapSwitch', function (event, state) {

		  var x = $(this).data('on-text');

		  var y = $(this).data('off-text');

			if($("#ed_taxstatus").is(':checked') && x=='YES')

			{

				$("#ed_tax_status").val(1);

			}

			else if(y == 'NO')

			{

				$("#ed_tax_status").val(0);

			}

	});

	$('#tgrp-dt-btn').daterangepicker(

	{

		  ranges: {

			'Today'		  : [moment(), moment()],

			'Yesterday'	  : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],

			'Last 7 Days' : [moment().subtract(6, 'days'), moment()],

			'Last 30 Days': [moment().subtract(29, 'days'), moment()],

			'This Month'  : [moment().startOf('month'), moment().endOf('month')],

			'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]

		  },

		  startDate: moment().subtract(29, 'days'),

		  endDate: moment()

	},

	function (start, end) {

		set_tgrp_table(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));

				$('#tgrp1').text(moment().startOf('month').format('YYYY-MM-DD'));

				$('#tgrp2').text(moment().endOf('month').format('YYYY-MM-DD'));

		  }

	);

	/**

	*	Master :: Tax

	*	Ends

	*/

	/**

	*	Master :: Product

	*	Starts

	*/

	$("#calculation_based_on").select2({

		placeholder :"Select calculation"

	});

	$("input[name='product[has_stone]']:radio").on('change',function(){

			   if($(this).val()==1)

			   {

				   	$('#stone_board_rate_cal').attr('checked',true);

					 $('#less_stone_wt').attr('checked',true);

			   }

			   else

			   {

				 	$('#stone_board_rate_cal').attr('checked',false);

					 $('#less_stone_wt').attr('checked',false);

			   }

	});

	$("#category_sel").select2(

	{

		placeholder:"Select category",

		allowClear: true

	});

	$('#category_sel').on('change',function(){

        if(this.value!='')

        {

            $('#category_id').val(this.value);

        }

        else{

            $('#category_id').val('');

        }

    });

	$('#metal_sel').on('change',function(e){

		if(this.value!='')

		{

			$('#metal_id').val(this.value);

			get_ActiveCatByMetal(this.value);

		}else{

			$('#metal_id').val();

		}

	});

	$(".status").bootstrapSwitch();

	$(".status").on('switchChange.bootstrapSwitch', function (event, state) {

        var x=$(this).data('on-text');

        var y=$(this).data('off-text');

		if($(".status").is(':checked') && x=='ACTIVE')

		{

			$("#product_status").val(1);

        } else if(y=='INACTIVE')

		{

          $("#product_status").val(0);

		}

    });

	$('#product_date').daterangepicker(

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

			 set_ret_product_table(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));

				$('#product1').text(start.format('YYYY-MM-DD'));

				$('#product2').text(end.format('YYYY-MM-DD'));

		  }

	 );

	 $("#filterproduct_sel,#filtertax_sel,#filterprod_status").select2().on('change', function() {

	    switch(ctrl_page[1]){

			case 'bulkprodupdated':

							set_bulkprodupdated_table();

							break;

		}

	 });//filter

	 $(document).on('click', '#select_prodata', function(e){

		$("tbody tr td input[type='checkbox']").prop('checked',true);

	 });

	$("#product_status").on('switchChange.bootstrapSwitch', function (event, state) {

		var x = $(this).data('on-text');

		var y = $(this).data('off-text');

		if($("#product_status").is(':checked') && x=='ACTIVE')

		{

			$("#up_prod_status").val(1);

		}

		else if(y == 'INACTIVE')

		{

			$("#up_prod_status").val(0);

		}

	});

	$(document).on('click', '.prod_update', function(e){

		if($("input[name='product_id[]']:checked").val())

		{

			var selected = [];

			$("input[name='product_id[]']:checked").each(function() {

				selected.push($(this).val());

			});

			update_product(selected);

		}

    });

	$('#prod-dt-btn').daterangepicker(

	{

		  ranges: {

			'Today'		  : [moment(), moment()],

			'Yesterday'	  : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],

			'Last 7 Days' : [moment().subtract(6, 'days'), moment()],

			'Last 30 Days': [moment().subtract(29, 'days'), moment()],

			'This Month'  : [moment().startOf('month'), moment().endOf('month')],

			'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]

		  },

		  startDate: moment().subtract(29, 'days'),

		  endDate: moment()

	},

	function (start, end) {

				set_bulkprodupdated_table(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));

				$('#prod1').text(moment().startOf('month').format('YYYY-MM-DD'));

				$('#prod2').text(moment().endOf('month').format('YYYY-MM-DD'));

		  }

	);

	$("#up_tax_sel").select2(

	{

		placeholder:"Tax Group",

		allowClear: true

	});

	$("#up_prod_status").select2(

	{

		placeholder:"Status",

		allowClear: true

	});

	/**

	*	Master :: Product

	*	Ends

	*/

	/**

	*	Master :: Sub Product

	*	Starts

	*/

	$('#subproduct').daterangepicker(

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

			 set_ret_sub_product_table(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));

				$('#date1').text(start.format('YYYY-MM-DD'));

				$('#date2').text(end.format('YYYY-MM-DD'));

		  }

	 );

	$("#switch").on('switchChange.bootstrapSwitch', function (event, state) {

        var x=$(this).data('on-text');

        var y=$(this).data('off-text');

		if($("#switch").is(':checked') && x=='ACTIVE')

		{

			$("#subproduct_status").val(1);

        } else if(y=='INACTIVE')

		{

          $("#subproduct_status").val(0);

		}

    });

	/**

	*	Master :: Sub Product

	*	Ends

	*/

	/**

	*	Master :: Metal

	*	Starts

	*/

	$("#tgrp_sel,#ed_tgrp_sel,#pur_tgrp_sel").select2();

	$("#tgrp_sel,#ed_tgrp_sel").on('change',function(e){

		if(this.value!='')

		{

			$("#tgrp_id,#ed_tgrp_id").val(this.value);

		}

		else

		{

			$("#tgrp_id,#ed_tgrp_id").val('');

		}

	});

	$('#pur_tgrp_sel').on('change',function(e)

	{

		if(this.value!='')

		{

			$("#pur_tgrp_sel").val(this.value);

		}

		else

		{

			$("#pur_tgrp_sel").val('');

		}

	})

	$("#newmetal,#add_newmetal").on('click',function(){

		if($("#metal_name").val() != '' && $("#metal_code").val() != '' && $("#tgrp_id").val() != ''){

			add_metal($('#metal_name').val(),$('#metal_code').val(),$('#addmetal_status').val(),$('#tgrp_id').val());

			$("#metal_name").val('');

			$("#metal_code").val('');

			$("#tgrp_id").val('');

			}

		else if($("#tgrp_id").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Select Purity.</div>';

			$("div.overlay").css("display", "none");

			$('#error-msg').html(msg);

		return false;

		}

		else if($("#metal_name").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Metal Name.</div>';

			$("div.overlay").css("display", "none");

			$('#error-msg').html(msg);

		return false;

		}

		else if($("#metal_code").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Metal Code .</div>';

			$("div.overlay").css("display", "none");

			$('#error-msg').html(msg);

		return false;

		}

	});

	$('#metal_date').daterangepicker(

	{

		  ranges: {

			'Today'		  : [moment(), moment()],

			'Yesterday'	  : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],

			'Last 7 Days' : [moment().subtract(6, 'days'), moment()],

			'Last 30 Days': [moment().subtract(29, 'days'), moment()],

			'This Month'  : [moment().startOf('month'), moment().endOf('month')],

			'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]

		  },

		  startDate: moment().subtract(29, 'days'),

		  endDate: moment()

	},

	function (start, end) {

				set_metal_table(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));

				$('#metal1').text(moment().startOf('month').format('YYYY-MM-DD'));

				$('#metal2').text(moment().endOf('month').format('YYYY-MM-DD'));

		  }

	);

	$("#ad_metalstatus").on('switchChange.bootstrapSwitch', function (event, state) {

	  var x = $(this).data('on-text');

	  var y = $(this).data('off-text');

		if($("#ad_metalstatus").is(':checked') && x=='YES')

		{

			$("#addmetal_status").val(1);

		}

		else if(y=='NO')

		{

			$("#addmetal_status").val(0);

		}

	});

	$("#ed_metalstatus").on('switchChange.bootstrapSwitch', function (event, state) {

	  var x = $(this).data('on-text');

	  var y = $(this).data('off-text');

		if($("#ed_metalstatus").is(':checked') && x=='YES')

		{

			$("#edmetal_status").val(1);

		}

		else if(y == 'NO')

		{

			$("#edmetal_status").val(0);

		}

	});

	$(document).on('click', "#metal_list a.btn-edit", function(e) {

		e.preventDefault();

		id=$(this).data('id');

	    get_metals(id);

	    $("#edit-id").val(id);

	});

    $("#update_metal").on('click',function(){

		var id          = $("#edit-id").val();

		var metal       = $("#ed_metal_name").val();

		var metal_code  = $("#ed_metal_code").val();

		var metal_status= $("#edmetal_status").val();

		var purity = $("#ed_pur_id").val();

		var tgrp_id=$('#tgrp_id').val()

		if($("#ed_metal_name").val() != '' && $("#ed_metal_code").val() != '' && tgrp_id!=''){

	    update_metal(metal,metal_code,metal_status,tgrp_id);

	    }

		else if($("#ed_metal_name").val() == ''){

		msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Enter Metal Name .</div>';

		$("div.overlay").css("display", "none");

        $('#error-msg1').html(msg);

        return false;

		}

		else if($("#ed_metal_code").val() == ''){

		msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Metal Code .</div>';

		$("div.overlay").css("display", "none");

        $('#error-msg1').html(msg);

        return false;

		}

	});

	/**

	*	Master :: Metal

	*	Ends

	*/

	/**

	*	Master :: Screw

	*	Starts

	*/

	$("#new_addscrew,#add_newscrew").on('click',function(){

		if($("#screw_name").val() != '' && $("#screw_code").val() != ''){

			add_screw($('#screw_name').val(),$('#screw_code').val(),$('#add_screw_status').val());

			$('#screw_name').val('');

			$('#screw_code').val('');

		}

		else if($("#screw_name").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Screw Name .</div>';

			$("div.overlay").css("display", "none");

			$('#error-msg').html(msg);

		return false;

		}

		else if($("#screw_code").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Screw Code .</div>';

			$("div.overlay").css("display", "none");

			$('#error-msg').html(msg);

		return false;

		}

	});

	$(document).on('click', "#screw_list a.btn-edit", function(e) {

		e.preventDefault();

		id=$(this).data('id');

	    get_screw(id);

	    $("#edit-id").val(id);

	});

    $("#update_screw").on('click',function(){

        //$("div.overlay").css("display", "block");

		var id              = $("#edit-id").val();

		var screw_name        = $("#ed_screw_name").val();

		var screw_short_code  = $("#ed_screw_code").val();

		var screw_status      = $('#edit_screw_status').val();

		if($("#ed_screw_name").val() != '' && $("#ed_screw_code").val() != ''){

	    	update_screw(screw_name,screw_short_code,screw_status);

	    }

		else if($("#ed_screw_name").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Enter Screw Name .</div>';

			$("div.overlay").css("display", "none");

	        $('#error-msg1').html(msg);

	        return false;

		}

		else if($("#ed_screw_code").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Screw Code .</div>';

			$("div.overlay").css("display", "none");

	        $('#error-msg1').html(msg);

	        return false;

		}

	});

	$('#screw_date').daterangepicker(

	{

		  ranges: {

			'Today'		  : [moment(), moment()],

			'Yesterday'	  : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],

			'Last 7 Days' : [moment().subtract(6, 'days'), moment()],

			'Last 30 Days': [moment().subtract(29, 'days'), moment()],

			'This Month'  : [moment().startOf('month'), moment().endOf('month')],

			'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month'				).endOf('month')]

		  },

		  startDate: moment().subtract(29, 'days'),

		  endDate: moment()

	},

	function (start, end) {

				set_screw_table(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));

				$('#screw1').text(moment().startOf('month').format('YYYY-MM-DD'));

				$('#screw2').text(moment().endOf('month').format('YYYY-MM-DD'));

		  }

	);

	$("#ad_screw_status").on('switchChange.bootstrapSwitch', function (event, state) {

	  var x = $(this).data('on-text');

	  var y = $(this).data('off-text');

		if($("#ad_screw_status").is(':checked') && x=='YES')

		{

			$("#add_screw_status").val(1);

		}

		else if(y=='NO')

		{

			$("#add_screw_status").val(0);

		}

	});

	$("#ed_screw_status").on('switchChange.bootstrapSwitch', function (event, state) {

	  var x = $(this).data('on-text');

	  var y = $(this).data('off-text');

		if($("#ed_screw_status").is(':checked') && x=='YES')

		{

			$("#edit_screw_status").val(1);

		}

		else if(y == 'NO')

		{

			$("#edit_screw_status").val(0);

		}

	});

	/**

	*	Master :: Screw

	*	Ends

	*/

	/**

	*	Master :: Design

	*	Starts

	*/

	$('#hook_sel').select2({

		placeholder: "Select Hook",

		allowClear: true

	});

	$('#screw_sel').select2({

		placeholder: "Select screw",

		allowClear: true

	});

	$('#product_sel').on('change',function(e){

		if(this.value!='')

		{

		    $('#product').val(this.value);

			get_productData(this.value);

		}else

		{

		    $('#product').val('');

		}

	});

	$('#select_product').on('change',function(e){

	        if(this.value!='' && ctrl_page[1]=='ret_subdesign_mapping')

	        {

	            get_ProductDesign();

	        }

	        else if(this.value!='' && ctrl_page[1]=='karigar')

	        {

	            get_design_product(this.value);

	        }

	});

	$('#theme_sel').on('change',function(e){

		if(this.value!='')

		{

			$("#theme_id").val(this.value);

		}

		else

		{

			$("#theme_id").val('');

		}

	});

	$("#karigar_sel").change(function() {

		 var data = $("#karigar_sel").select2('data');

		 selectedValue = $(this).val();

		 $("#karigar").val(selectedValue);

	}) ;

	$("#material_sel").change(function() {

		var data = $("#material_sel").select2('data');

		selectedValue = $(this).val();

		$("#material_id").val(selectedValue);

	}) ;

	$('#hook_sel').on('change',function(e){

		if(this.value!='')

		{

			$("#hook_id").val(this.value);

		}

		else

		{

			$("#hook_id").val('');

		}

	});

	$('#screw_sel').on('change',function(e){

		if(this.value!='')

		{

			$("#screw_id").val(this.value);

		}

		else

		{

			$("#screw_id").val('');

		}

	});

	$("#purity_sel").change(function() {

       var data = $("#purity_sel").select2('data');

       selectedValue = $(this).val();

       $("#pur_id").val(selectedValue);

     }) ;

     $("#ed_purity_sel").change(function() {

       var data = $("#ed_purity_sel").select2('data');

       selectedValue = $(this).val();

       $("#ed_pur_id").val(selectedValue);

     });

	$("#size_sel").change(function() {

       var data = $("#size_sel").select2('data');

       selectedValue = $(this).val();

       $("#size").val(selectedValue);

    }) ;

	$('#design').daterangepicker(

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

		    var id_product=$('#select_product').val();

			 set_design_table(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'),id_product);

				$('#design1').text(start.format('YYYY-MM-DD'));

				$('#design2').text(end.format('YYYY-MM-DD'));

		  }

	 );

	$("#designstatus").bootstrapSwitch();

	$("#designstatus").on('switchChange.bootstrapSwitch', function (event, state) {

        var x=$(this).data('on-text');

        var y=$(this).data('off-text');

		if($("#designstatus").is(':checked') && x=='ACTIVE')

		{

			$("#d_status").val(1);

        } else if(y=='INACTIVE')

		{

          $("#d_status").val(0);

		}

    });

	$("#design_img").change(function(e){

    	e.preventDefault();

    	validate_design_Image(this);

	});

	$("#uploadDesign").on('click',function(){

		var count = $("#imgCount").val();

		$("#imgCount").val(++count);

		console.log(count);

		$("#uploadArea").append('<input type="file" id="design'+count+'" name="design'+count+'" /><br/>');

		$("#uploadArea").append('<img src="'+base_url+'assets/img/no_image.png" alt="Upload Image" class="img-thumbnail" id="design'+count+'_Preview" alt="Product Image" width="200" height="200"><br/>');

	 	$("#uploadArea").append('<button class="btn btn-small remove-btn" id="image'+count+'" type="button" title="Remove Image"><i class="fa fa-trash" ></i></button>');

	 	$('#design'+count+'').on('change',function() {

			validateImage(this);

		});

		$("#image"+count+"").on('click',function(e){

			e.preventDefault();

			$('#design'+count+'_Preview').attr('src', ""+base_url+"assets/img/no_image.png");

			$("#design"+count+"").val("");

		});

	});

$("#remove_img").on('click',function(e){

	e.preventDefault();

   $('#design_img_preview').attr('src', ""+base_url+"assets/img/no_image.png");

   $("#design_img").val("");

});

    $("#add_stone_info").on('click',function(){

			var html = "";

			var a = $("#s_increment").val();

			var i = ++a;

			$("#s_increment").val(i);

			html+="<tr id='stone"+i+"'><td>"+i+"</td></td>"+i+"<td><select name='stone["+i+"][stone_id]' style='width:100%;' id='stone_name"+i+"' class='form-control'></select></td>"+i+"<td><input name='stone["+i+"][stone_pcs]' style='width:100%;' id='stone_pcs"+i+"' class='form-control' placeholder='no of pieces' type='number'></td><td><button type='button' class='btn btn-danger' onclick='s_remove("+i+")'><i class='fa fa-trash'></i></button></td>";

				$.ajax({

					type: 'GET',

					url: base_url+'index.php/admin_ret_catalog/stone/active_stones',

					dataType:'json',

					success:function(data){

							$.each(data, function (key, item) {

							$('#stone_name'+i+'').append(

							$("<option></option>")

							.attr("value", item.stone_id)

							.text(item.stone_name)

							);

						});

					   $('#stone_name'+i+'').select2({

						    placeholder: "Select stone",

						    allowClear: true

					   });

					   $(".overlay").css("display", "none");

			   		}

			  });

  			 $('#stone_detail').append(html);

   });

   $("#add_size_info").on('click',function(){

			var html = "";

			var a = $("#si_increment").val();

			var i = ++a;

			$("#si_increment").val(i);

			html+="<tr id='size"+i+"'><td>"+i+"</td></td>"+i+"<td><div class='form-group' ><div class='input-group'><input class='form-control' id='size"+i+"' name='size["+i+"][size]' type='number' step=any style='width:100%;'  placeholder='Enter size'><span class='input-group-addon input-sm no-padding'><select id='uom"+i+"' name='size["+i+"][uom_id]'></select></span></div></div></td><td><button type='button' class='btn btn-danger' onclick='size_remove("+i+")'><i class='fa fa-trash'></i></button></td>";

				   	$.ajax({

					type: 'GET',

					url: base_url+'index.php/admin_ret_catalog/uom/active_uom',

					dataType:'json',

					success:function(data){

						$.each(data, function (key, item) {

							$('#uom'+i+'').append(

							$("<option></option>")

							.attr("value", item.uom_id)

							.text(item.code)

							);

						});

					   $(".overlay").css("display", "none");

			   		}

			  });

  			 $('#size_detail').append(html);

   });

	/**

	*	Master :: Design

	*	Ends

	*/

	//Dynamic image upload

$("#uploadImg").on('click',function(){

	var count = $("#imgCount").val();

	$("#imgCount").val(++count);

	console.log(count);

	$("#uploadArea").append('<input type="file" id="prodImg'+count+'" name="prodImg'+count+'" /><br/>');

	$("#uploadArea").append('<img src="" alt="Upload Image" class="img-thumbnail" id="prodImg'+count+'_Preview" alt="Product Image" width="150" height="75"><br/>');

 	$('#prodImg'+count+'').on('change',function() {

		validateImage(this);

	});

});

//End of delete uploaded image

$(document).on('click', "#purity_list a.btn-edit", function(e) {

		$("#ed_purity").val('');

		e.preventDefault();

		id=$(this).data('id');

	    get_purity(id);

	    $("#edit-id").val(id);

	});

 $("#add_newpurity").on('click',function(){

						if($("#purity").val() != '')

						{

							add_purity($('#purity').val(),$('#desc').val());

							$('#purity').val('');

							$('#desc').val('');

						}

						else if($("#purity").val() == ''){

						msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Purity.</div>';

						$("div.overlay").css("display", "none");

						$('#error-msg').html(msg);

					    return false;

					}

						});

//update purity

$("#update_purity").on('click',function(){

	var purity=($("#ed_purity").val());

	var id=$("#edit-id").val();

	var desc=$("#ed_desc").val();

	if($("#ed_purity").val() != '')

	{

	update_purity(purity,id,desc);

	}

	else if($("#ed_purity").val() == ''){

	msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Purity.</div>';

	$("div.overlay").css("display", "none");

	$('#error').html(msg);

	return false;

			}

});

$(document).on('click', "#carat_list a.btn-edit", function(e) {

		$("#ed_carat").val('');

		e.preventDefault();

		id=$(this).data('id');

	    get_carat(id);

	    $("#edit-id").val(id);

	});

//update carat

$("#update_carat").on('click',function(){

	var carat=($("#ed_carat").val());

	var id=$("#edit-id").val();

	var desc=$("#ed_desc").val();

	update_carat(carat,id,desc);

});

$(document).on('click', "#clarity_list a.btn-edit", function(e) {

		$("#ed_clarity").val('');

		e.preventDefault();

		id=$(this).data('id');

	    get_clarity(id);

	    $("#edit-id").val(id);

	});

 $("#add_newclarity").on('click',function(){

						if($('#clarity').val() != '')

						{

							add_clarity($('#clarity').val(),$('#desc').val());

							$('#clarity').val('');

						}

						else if($("#clarity").val() == ''){

						msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Clarity name.</div>';

						$("div.overlay").css("display", "none");

						$('#error-msg').html(msg);

					return false;

					}

						});

//update clarity

$("#update_clarity").on('click',function(){

	var clarity=($("#ed_clarity").val());

	var id=$("#edit-id").val();

	var desc=$("#ed_desc").val();

	if($("#ed_clarity").val() != '')

	{

	update_clarity(clarity,id,desc);

	}

	else if($("#ed_clarity").val() == ''){

	msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Clarity Name.</div>';

	$("div.overlay").css("display", "none");

	$('#error').html(msg);

	return false;

			}

});

$(document).on('click', "#cut_list a.btn-edit", function(e) {

		$("#ed_cut").val('');

		e.preventDefault();

		id=$(this).data('id');

	    get_cut(id);

	    $("#edit-id").val(id);

	});

 $("#add_newcut").on('click',function(){

						if($('#cut').val() != '')

						{

							add_cut($('#cut').val(),$('#desc').val());

							$('#cut').val('');

						}

						else if($("#cut").val() == ''){

						msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Cut name.</div>';

						$("div.overlay").css("display", "none");

						$('#error-msg').html(msg);

						return false;

			}

						});

//update cut

$("#update_cut").on('click',function(){

	var cut=($("#ed_cut").val());

	var id=$("#edit-id").val();

	var desc=$("#ed_desc").val();

	if($("#ed_cut").val() != '')

	{

	update_cut(cut,id,desc);

	}

	else if($("#ed_cut").val() == ''){

	msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Cut Name.</div>';

	$("div.overlay").css("display", "none");

	$('#error').html(msg);

	return false;

			}

});

$(document).on('click', "#color_list a.btn-edit", function(e) {

		$("#ed_color").val('');

		e.preventDefault();

		id=$(this).data('id');

	    get_color(id);

	    $("#edit-id").val(id);

	});

//update color

$("#update_color").on('click',function(){

	var color=($("#ed_color").val());

	var id=$("#edit-id").val();

	var desc=$("#ed_desc").val();

	if($("#ed_color").val() != '')

	{

	update_color(color,id,desc);

	}

	else if($("#ed_color").val() == ''){

	msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Color Name.</div>';

	$("div.overlay").css("display", "none");

	$('#error').html(msg);

	return false;

			}

});

//Image validation

$('#category_img,#sub_category_img,#default_prod_img').on('change',function() {

		validateImage(this);

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

						 get_purity_options(i,item.purity);

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

	function get_productData(pro_id)

	{

			$("span.modal-overlay").css("display", "block");

				my_Date = new Date();

				$.ajax({

				data:{'pro_id':pro_id},

				url: base_url+"index.php/admin_ret_catalog/get_productData?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),

				type:"POST",

				cache:false,

				dataType:"json",

				success:function(data){

					console.log(data);

					var screw = data.has_screw;

					var hook = data.has_hook;

					var stone = data.has_stone;var size = data.has_size;

					if(screw == 0)

					{

						$("#des_screw").hide();

					}

					else{

						$("#des_screw").show();

					}

					if(hook == 0)

					{

						$("#des_hook").hide();

					}

					else{

						$("#des_hook").show();

					}

					if(stone == 0)

					{

						$("#des_stone").hide();

					}

					else{

						$("#des_stone").show();

					}

					if(size == 0)

					{

						$("#des_size").hide();

					}

					else{

						$("#des_size").show();

					}

				}

			});

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

	function delete_design(file) {

		var id =  $('#editid_design').val();

		$("div.overlay").css("display", "block");

		$.ajax({

			url:base_url+"index.php/admin_ret_catalog/removeDesign_img/"+file+"/"+id,

			type : "POST",

			success : function(data) {

				window.location.reload();

			},

			error : function(error){

				$("div.overlay").css("display", "none");

			}

		});

	}

	$("#ret_product_img").change( function(){

		event.preventDefault();

		validateImage(this);

		});

	function validateImage()

	 {

				switch(arguments[0].id){

					case 'category_img':

							var preview = $('#category_img_preview');

							break;

					case 'sub_category_img':

							var preview = $('#sub_category_img_preview');

							break;

					case 'default_prod_img':

							var preview = $('#default_img_preview');

							break;

					case 'ret_product_img':

							var preview = $('#ret_product_img_preview');

							break;

					default:

					console.log(arguments[0].id);

							var preview = $('#'+arguments[0].id+'_Preview');

							break;

				}

				if(arguments[0].files[0].size > 1048576)

				{

				  alert('File size cannot be greater than 1 MB');

				  arguments[0].value = "";

				  preview.css('display','none');

				}

				else

				{

					var fileName =arguments[0].value;

					var ext = fileName.substring(fileName.lastIndexOf('.') + 1);

					ext = ext.toLowerCase();

					if(ext != "jpg" && ext != "png" && ext != "jpeg")

					{

						alert("Upload JPG or PNG Images only");

						arguments[0].value = "";

						preview.css('display','none');

					}

					else

					{

						var file    = arguments[0].files[0];

						var reader  = new FileReader();

						  reader.onloadend = function () {

							preview.prop('src',reader.result);

						  }

						  if (file)

						  {

						 	reader.readAsDataURL(file);

							preview.css('display','');

						  }

						  else

						  {

						  	preview.prop('src','');

							preview.css('display','none');

						  }

					}

				}

		}

 //preview selected images

function readURL(input, preview) {

    if (input.files && input.files[0]) {

        var reader = new FileReader();

        reader.onload = function (e) {

            $('#'+ preview).attr('src', e.target.result);

        }

        reader.readAsDataURL(input.files[0]);

    }

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

//to load values to table

function set_purity_table()

{

	my_Date = new Date();

	$.ajax({

			 url:base_url+ "index.php/purity/ajax?nocache=" + my_Date.getUTCSeconds(),

			 dataType:"JSON",

			 type:"POST",

			 success:function(data){

		 	console.log(data);

				 var purity 	= data.purity;

				 var access		= data.access;

				 $('#total_purity').text(purity.length);

				 if(access.add == '0')

				 {

					$('#add_purity').attr('disabled','disabled');

				 }

			 var oTable = $('#purity_list').DataTable();

				 oTable.clear().draw();

						 if (purity!= null && purity.length > 0)

							{

							oTable = $('#purity_list').dataTable({

									"bDestroy": true,

									"bInfo": true,

									"bFilter": true,

									"bSort": true,

									"order": [[ 0, "desc" ]],

									"aaData": purity,

									"aoColumns": [	{ "mDataProp": "id_purity" },

													{ "mDataProp": "purity" },

													{ "mDataProp": function ( row, type, val, meta ){

					                	    		status_url = (access.edit =='1')?base_url+"index.php/admin_ret_catalog/purity_status/"+(row.status==1?0:1)+"/"+row.id_purity:'#';

							                		return "<a href='"+status_url+"'><i class='fa "+(row.status==1?'fa-check':'fa-remove')+"' style='color:"+(row.status==1?'green':'red')+"'></i></a>"

						                				}

								              		},

													{ "mDataProp": function ( row, type, val, meta ) {

														 id= row.id_purity;

														 edit_target=(access.edit=='0'?"":"#confirm-edit");

														 delete_url=(access.delete=='1' ? base_url+'index.php/purity/delete/'+id : '#' );

														 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

														//  action_content='<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i> Edit</a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> Delete</a>'

														var action_content=''

														if(access.edit == '1'){

														

											        	action_content+=  '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i> Edit</a>' ;

														

													    }

														if(access.delete == '1'){

														 

														 action_content+=' <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> Delete</a>'

														}

														 return action_content;

														 }

													}]

								});

							}

					  },

					  error:function(error)

					  {

						 $("div.overlay").css("display", "none");

					  }

			      });

}

//to add purity

function add_purity(purity,desc)

{

	my_Date = new Date();

    var wt=purity;

	$.ajax({

		data:{"purity":wt,"description":desc},

		url: base_url+"index.php/purity/add?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		cache:false,

		success:function(data){

			$('#purity').val('');

			location.reload(true);

		}

	});

}

//update purity

function update_purity(purity,id,desc)

{

	 $("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		data:{"purity":purity,"description":desc},

		url: base_url+"index.php/purity/update/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		cache: false,

		success:function(){

				 $("div.overlay").css("display", "none");

			location.reload(true);

		}

	});

}

//get purity by id

function get_purity(id)

{

   my_Date = new Date();

	$.ajax({

		type:"GET",

		url: base_url+"index.php/purity/edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		cache:false,

		dataType:"JSON",

		success:function(data){

			wt=data.purity;

		    $('#ed_purity').val(wt);

		    $('#ed_desc').val(data.description);

		   // console.log(wt);

		}

	});

}

//update carat

function update_carat(carat,id,desc)

{

	 $("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		data:{"carat":carat,"description":desc},

		url: base_url+"index.php/carat/update/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		cache: false,

		success:function(){

				 $("div.overlay").css("display", "none");

			window.location.reload(true);

		}

	});

}

//get carat by id

function get_carat(id)

{

   my_Date = new Date();

	$.ajax({

		type:"GET",

		url: base_url+"index.php/carat/edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		cache:false,

		dataType:"JSON",

		success:function(data){

			wt=data.carat;

		    $('#ed_carat').val(wt);

		    $('#ed_desc').val(data.description);

		}

	});

}

//to load values to table

function set_color_table()

{

	my_Date = new Date();

	$.ajax({

			 url:base_url+ "index.php/color/ajax?nocache=" + my_Date.getUTCSeconds(),

			 dataType:"JSON",

			 type:"POST",

			 success:function(data){

		 	console.log(data);

				 var color 	= data.color;

				 var access		= data.access;

				 $('#total_color').text(color.length);

				 if(access.add == '0')

				 {

					$('#add_color').attr('disabled','disabled');

				 }

				 else{

					 $("#add_newcolor").on('click',function(){

						if($('#color').val() != '')

						{

							add_color($('#color').val(),$('#desc').val());

							$('#color').val('');

						}

						else if($("#color").val() == ''){

				msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Color Name.</div>';

				$("div.overlay").css("display", "none");

		        $('#error-msg').html(msg);

		        return false;

			  }

						});

				 }

			 var oTable = $('#color_list').DataTable();

				 oTable.clear().draw();

						 if (color!= null && color.length > 0)

							{

							oTable = $('#color_list').dataTable({

									"bDestroy": true,

									"bInfo": true,

									"bFilter": true,

									"bSort": true,

									"order": [[ 0, "desc" ]],

									"aaData": color,

									"aoColumns": [	{ "mDataProp": "id_color" },

													{ "mDataProp": "color" },

													{ "mDataProp": function ( row, type, val, meta ){

					                	    		status_url = (access.edit == '1')? base_url+"index.php/admin_ret_catalog/color_status/"+(row.status==1?0:1)+"/"+row.id_color:'#';

							                		return "<a href='"+status_url+"'><i class='fa "+(row.status==1?'fa-check':'fa-remove')+"' style='color:"+(row.status==1?'green':'red')+"'></i></a>"

						                				}

								              		},

													{ "mDataProp": function ( row, type, val, meta ) {

														 id= row.id_color;

														 edit_target=(access.edit=='0'?"":"#confirm-edit");

														 delete_url=(access.delete=='1' ? base_url+'index.php/color/delete/'+id : '#' );

														 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

														//  action_content='<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i> Edit</a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> Delete</a>'

														var action_content=''

														if(access.edit == '1'){

														

											        	action_content+=  '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i> Edit</a>' ;

														

													    }

														if(access.delete == '1'){

														 

														 action_content+=' <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> Delete</a>'

														}

														 return action_content;

														 }

													}]

								});

							}

					  },

					  error:function(error)

					  {

						 $("div.overlay").css("display", "none");

					  }

			      });

}

//to add color

function add_color(color,desc)

{

	my_Date = new Date();

    var wt=color;

	$.ajax({

		data:{"color":wt,"description":desc},

		url: base_url+"index.php/color/add?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		cache:false,

		success:function(data){

			$('#color').val('');

			location.reload(true);

		}

	});

}

//update color

function update_color(color,id,desc)

{

	 $("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		data:{"color":color,"description":desc},

		url: base_url+"index.php/color/update/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		cache: false,

		success:function(){

			  location.reload(true);

		}

	});

}

//get color by id

function get_color(id)

{

   my_Date = new Date();

	$.ajax({

		type:"GET",

		url: base_url+"index.php/color/edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		cache:false,

		dataType:"JSON",

		success:function(data){

			wt=data.color;

		    $('#ed_color').val(wt);

		    $('#ed_desc').val(data.description);

		}

	});

}

//to load values to table

function set_cut_table()

{

	my_Date = new Date();

	$.ajax({

			 url:base_url+ "index.php/cut/ajax?nocache=" + my_Date.getUTCSeconds(),

			 dataType:"JSON",

			 type:"POST",

			 success:function(data){

		 	console.log(data);

				 var cut 	= data.cut;

				 var access		= data.access;

				 $('#total_cut').text(cut.length);

				 if(access.add == '0')

				 {

					$('#add_cut').attr('disabled','disabled');

				 }

			 var oTable = $('#cut_list').DataTable();

				 oTable.clear().draw();

						 if (cut!= null && cut.length > 0)

							{

							oTable = $('#cut_list').dataTable({

									"bDestroy": true,

									"bInfo": true,

									"bFilter": true,

									"bSort": true,

									"order": [[ 0, "desc" ]],

									"aaData": cut,

									"aoColumns": [	{ "mDataProp": "id_cut" },

													{ "mDataProp": "cut" },

													{ "mDataProp": function ( row, type, val, meta ){

					                	    		status_url = (access.edit =='1')?base_url+"index.php/admin_ret_catalog/cut_status/"+(row.status==1?0:1)+"/"+row.id_cut:'#';

							                		return "<a href='"+status_url+"'><i class='fa "+(row.status==1?'fa-check':'fa-remove')+"' style='color:"+(row.status==1?'green':'red')+"'></i></a>"

						                				}

								              		},

													{ "mDataProp": function ( row, type, val, meta ) {

														 id= row.id_cut;

														 edit_target=(access.edit=='0'?"":"#confirm-edit");

														 delete_url=(access.delete=='1' ? base_url+'index.php/cut/delete/'+id : '#' );

														 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

														//  action_content='<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i> Edit</a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> Delete</a>'

														var action_content=''

														if(access.edit == '1'){

														

											        	action_content+=  '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i> Edit</a>' ;

														

													    }

														if(access.delete == '1'){

														 

														 action_content+='<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> Delete</a>'

														}

														 return action_content;

														 }

													}]

								});

							}

					  },

					  error:function(error)

					  {

						 $("div.overlay").css("display", "none");

					  }

			      });

}

//to add cut

function add_cut(cut,desc)

{

	my_Date = new Date();

    var wt=cut;

	$.ajax({

		data:{"cut":wt,"description":desc},

		url: base_url+"index.php/cut/add?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		cache:false,

		success:function(data){

			$('#cut').val('');

			location.reload(true);

		}

	});

}

//update cut

function update_cut(cut,id,desc)

{

	 $("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		data:{"cut":cut,"description":desc},

		url: base_url+"index.php/cut/update/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		cache: false,

		success:function(){

				 $("div.overlay").css("display", "none");

			window.location.reload(true);

		}

	});

}

//get cut by id

function get_cut(id)

{

   my_Date = new Date();

	$.ajax({

		type:"GET",

		url: base_url+"index.php/cut/edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		cache:false,

		dataType:"JSON",

		success:function(data){

			wt=data.cut;

		    $('#ed_cut').val(wt);

		    $('#ed_desc').val(data.description);

		}

	});

}

//to load values to table

function set_clarity_table()

{

	my_Date = new Date();

	$.ajax({

			 url:base_url+ "index.php/clarity/ajax?nocache=" + my_Date.getUTCSeconds(),

			 dataType:"JSON",

			 type:"POST",

			 success:function(data){

		 	console.log(data);

				 var clarity 	= data.clarity;

				 var access		= data.access;

				 $('#total_clarity').text(clarity.length);

				 if(access.add == '0')

				 {

					$('#add_clarity').attr('disabled','disabled');

				 }

			 var oTable = $('#clarity_list').DataTable();

				 oTable.clear().draw();

						 if (clarity!= null && clarity.length > 0)

							{

							oTable = $('#clarity_list').dataTable({

									"bDestroy": true,

									"bInfo": true,

									"bFilter": true,

									"bSort": true,

									"order": [[ 0, "desc" ]],

									"aaData": clarity,

									"aoColumns": [	{ "mDataProp": "id_clarity" },

													{ "mDataProp": "clarity" },

													{ "mDataProp": function ( row, type, val, meta ){

					                	    		status_url =(access.edit == '1')? base_url+"index.php/admin_ret_catalog/clarity_status/"+(row.status==1?0:1)+"/"+row.id_clarity:'#';

							                		return "<a href='"+status_url+"'><i class='fa "+(row.status==1?'fa-check':'fa-remove')+"' style='color:"+(row.status==1?'green':'red')+"'></i></a>"

						                				}

								              		},

													{ "mDataProp": function ( row, type, val, meta ) {

														 id= row.id_clarity;

														 edit_target=(access.edit=='0'?"":"#confirm-edit");

														 delete_url=(access.delete=='1' ? base_url+'index.php/clarity/delete/'+id : '#' );

														 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

														//  action_content='<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i> Edit</a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> Delete</a>'

														var action_content=''

														if(access.edit == '1'){

														

											        	action_content+=  '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i> Edit</a>' ;

														

													    }

														if(access.delete == '1'){

														 

														 action_content+='<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> Delete</a>'

														}

														 return action_content;

														 }

													}]

								});

							}

					  },

					  error:function(error)

					  {

						 $("div.overlay").css("display", "none");

					  }

			      });

}

//to add clarity

function add_clarity(clarity,desc)

{

	my_Date = new Date();

    var wt=clarity;

	$.ajax({

		data:{"clarity":wt,"description":desc},

		url: base_url+"index.php/clarity/add?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		cache:false,

		success:function(data){

			$('#clarity').val('');

			 msg='<div class = "alert alert-success"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Success!</strong>clarity added successfully.</div>';

			$('#chit_alert').html(msg);

			location.reload(true);

		}

	});

}

//update clarity

function update_clarity(clarity,id,desc)

{

	 $("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		data:{"clarity":clarity,"description":desc},

		url: base_url+"index.php/clarity/update/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		cache: false,

		success:function(){

				 $("div.overlay").css("display", "none");

			window.location.reload(true);

		}

	});

}

//get clarity by id

function get_clarity(id)

{

   my_Date = new Date();

	$.ajax({

		type:"GET",

		url: base_url+"index.php/clarity/edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		cache:false,

		dataType:"JSON",

		success:function(data){

			wt=data.clarity;

		    $('#ed_clarity').val(wt);

		    $('#ed_desc').val(data.description);

		}

	});

}

//to load values to table

function set_carat_table()

{

	my_Date = new Date();

	$.ajax({

			 url:base_url+ "index.php/carat/ajax?nocache=" + my_Date.getUTCSeconds(),

			 dataType:"JSON",

			 type:"POST",

			 success:function(data){

		 	console.log(data);

				 var carat 	= data.carat;

				 var access		= data.access;

				 $('#total_carat').text(carat.length);

				 if(access.add == '0')

				 {

					$('#add_carat').attr('disabled','disabled');

				 }

				 else{

					 $("#add_newcarat").on('click',function(){

						if($('#carat').length>0)

						{

							add_carat($('#carat').val(),$('#desc').val());

							$('#carat').val('');

						}

						});

				 }

			 var oTable = $('#carat_list').DataTable();

				 oTable.clear().draw();

						 if (carat!= null && carat.length > 0)

							{

							oTable = $('#carat_list').dataTable({

									"bDestroy": true,

									"bInfo": true,

									"bFilter": true,

									"bSort": true,

									"aaData": carat,

									"aoColumns": [	{ "mDataProp": "id_carat" },

													{ "mDataProp": "carat" },

													{ "mDataProp": function ( row, type, val, meta ){

					                	    		status_url = base_url+"index.php/carat/status/"+(row.status==1?0:1)+"/"+row.id_carat;

							                		return "<a href='"+status_url+"'><i class='fa "+(row.status==1?'fa-check':'fa-remove')+"' style='carat:"+(row.status==1?'green':'red')+"'></i></a>"

						                				}

								              		},

													{ "mDataProp": function ( row, type, val, meta ) {

														 id= row.id_carat;

														 edit_target=(access.edit=='0'?"":"#confirm-edit");

														 delete_url=(access.delete=='1' ? base_url+'index.php/carat/delete/'+id : '#' );

														 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

														 action_content='<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i> Edit</a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> Delete</a>'

														 return action_content;

														 }

													}]

								});

							}

					  },

					  error:function(error)

					  {

						 $("div.overlay").css("display", "none");

					  }

			      });

}

//to add carat

function add_carat(carat,desc)

{

	my_Date = new Date();

    var wt=carat;

	$.ajax({

		data:{"carat":wt,"description":desc},

		url: base_url+"index.php/carat/add?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		cache:false,

		success:function(data){

			$('#carat').val('');

			 msg='<div class = "alert alert-success"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Success!</strong>carat added successfully.</div>';

			$('#chit_alert').html(msg);

			location.reload(true);

		}

	});

}

//update carat

function update_carat(carat,id,desc)

{

	 $("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		data:{"carat":carat,"description":desc},

		url: base_url+"index.php/carat/update/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		cache: false,

		success:function(){

				 $("div.overlay").css("display", "none");

			window.location.reload(true);

		}

	});

}

//get carat by id

function get_carat(id)

{

   my_Date = new Date();

	$.ajax({

		type:"GET",

		url: base_url+"index.php/carat/edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		cache:false,

		dataType:"JSON",

		success:function(data){

			wt=data.carat;

		    $('#ed_carat').val(wt);

		    $('#ed_desc').val(data.description);

		}

	});

}

function get_color_options(id,id_color)

{

	console.log(prod_info.color);

	/*  $.each(prod_info.color, function (key, item) {

			   		$('#'+id+'').append(

						$("<option></option>")

						  .attr("value", item.id_color)

						  .text(item.color)

					);

			});

			$('#'+id+'').select2({

			    placeholder: "Select color",

			    allowClear: true

			});

			$('#'+id+'').select2("val",(id_color!='' && id_color>0?id_color:''));*/

	$(".overlay").css('display','block');

	$.ajax({

		type: 'GET',

		url: base_url+'index.php/get/active_color',

		dataType:'json',

		success:function(data){

		   $.each(data.color, function (key, item) {

			   		$('#'+id+'').append(

						$("<option></option>")

						  .attr("value", item.id_color)

						  .text(item.color)

					);

			});

			$('#'+id+'').select2({

			    placeholder: "Select color",

			    allowClear: true

			});

			$('#'+id+'').select2("val",(id_color!='' && id_color>0?id_color:''));

			 $(".overlay").css("display", "none");

		}

	});

}

function get_purity_options(i,id_purity)

{

	$(".overlay").css('display','block');

	$.ajax({

		type: 'GET',

		url: base_url+'index.php/get/active_purity',

		dataType:'json',

		success:function(data){

		   $.each(data.purity, function (key, item) {

			   		$('#purity_select'+i+'').append(

						$("<option></option>")

						  .attr("value", item.id_purity)

						  .text(item.purity)

					);

			});

			$("#purity_select"+i+"").select2({

			    placeholder: "Select purity",

			    allowClear: true

			});

			$("#purity_select"+i+"").select2("val",(id_purity!='' && id_purity>0?id_purity:''));

			 $(".overlay").css("display", "none");

		}

	});

}

function get_cut_options(id,id_cut)

{

	$(".overlay").css('display','block');

	$.ajax({

		type: 'GET',

		url: base_url+'index.php/get/active_cut',

		dataType:'json',

		success:function(data){

		   $.each(data, function (key, item) {

			   		$('#'+id+'').append(

						$("<option></option>")

						  .attr("value", item.id_cut)

						  .text(item.cut)

					);

			});

			$('#'+id+'').select2({

			    placeholder: "Select cut",

			    allowClear: true

			});

			$('#'+id+'').select2("val",(id_cut!='' && id_cut>0?id_cut:''));

			 $(".overlay").css("display", "none");

		}

	});

}

function get_clarity_options(id,id_clarity)

{

	$(".overlay").css('display','block');

	$.ajax({

		type: 'GET',

		url: base_url+'index.php/get/active_clarity',

		dataType:'json',

		success:function(data){

		   $.each(data, function (key, item) {

			   		$('#'+id+'').append(

						$("<option></option>")

						  .attr("value", item.id_clarity)

						  .text(item.clarity)

					);

			});

			$('#'+id+'').select2({

			    placeholder: "Select clarity",

			    allowClear: true

			});

			$('#'+id+'').select2("val",(id_clarity!='' && id_clarity>0?id_clarity:''));

			 $(".overlay").css("display", "none");

		}

	});

}

function get_carat_options(id,id_carat)

{

	$(".overlay").css('display','block');

	$.ajax({

		type: 'GET',

		url: base_url+'index.php/get/active_carat',

		dataType:'json',

		success:function(data){

		   $.each(data, function (key, item) {

			   		$('#'+id+'').append(

						$("<option></option>")

						  .attr("value", item.id_carat)

						  .text(item.carat)

					);

			});

			$('#'+id+'').select2({

			    placeholder: "Select carat",

			    allowClear: true

			});

			$('#'+id+'').select2("val",(id_carat!='' && id_carat>0?id_carat:''));

			 $(".overlay").css("display", "none");

		}

	});

}

$('#add_floor').on('click',function(){

    $('#confirm-add').modal('show');

    setTimeout(function(){

     getFloorBranch();

    },1000);

});

function getFloorBranch()

{

	$('#total_items tbody ').empty();

	$('#ed_branch_select option').remove();

	$.ajax({

		type: 'GET',

		url: base_url+'index.php/branch/branchname_list',

		dataType:'json',

		success:function(data)

		{

    		var id_branch =  $('#id_branch').val();

    		var branch =  $('#floor_branch').val();

    		var edbranchid =  $('#ed_fl_branch_id').val();

    		$.each(data.branch, function (key, item) {

    		$("#floor_branch,#edbranchid").append(

    		$("<option></option>")

    		.attr("value", item.id_branch)

    		.text(item.name )

    		);

    		});

    		$("#floor_branch,#edbranchid").select2({

    		placeholder: "Select Branch",

    		allowClear: true

    		});

    		$("#floor_branch").select2("val",(branch!='' && branch>0?branch:''));

    		$("#edbranchid").select2("val",(edbranchid!='' && edbranchid>0?edbranchid:''));

	    }

	});

}

function set_floor_table(id_branch="",from_date="",to_date="")

{

	my_Date = new Date();

	$.ajax({

			 url:base_url+ "index.php/admin_ret_catalog/branch_floor?nocache=" + my_Date.getUTCSeconds(),

			 dataType:"JSON",

			 data:{'from_date':from_date,'to_date':to_date,'branch_id':id_branch},

			 type:"POST",

			 cache:false,

			 success:function(data){

				var floor 	    = data.floor;

				var access		= data.access;

				$('#total_floor').text(floor.length);

				if(access.add == '0')

				{

					$('#add_floor').attr('disabled','disabled');

				}

			 var oTable = $('#floor_list').DataTable();

			 oTable.clear().draw();

			 if (floor!= null && floor.length > 0)

			 {

				oTable = $('#floor_list').dataTable({

						"bDestroy": true,

						"bInfo": true,

						"bFilter": true,

						"bSort": true,

						"order": [[ 0, "desc" ]],

						"dom": 'lBfrtip',

						"buttons" : ['excel','print'],

						"tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },

						"aaData"  : floor,

						"aoColumns": [	{ "mDataProp": "floor_id" },

										{ "mDataProp": "floor_name" },

										{ "mDataProp": "floor_short_code" },

										{ "mDataProp": "branch_name" },

										{ "mDataProp": function ( row, type, val, meta ){

					                	 status_url = (access.edit =='1')?base_url+"index.php/admin_ret_catalog/branch_floor/update_status/"+row.floor_id+"/"+(row.floor_status==1?0:1):'#';

							             return "<a href='"+status_url+"'><i class='fa "+(row.floor_status==1?'fa-check':'fa-remove')+"' style='color:"+(row.floor_status==1?'green':'red')+"'></i></a>"

						                }

							      		},

										{ "mDataProp": function ( row, type, val, meta ) {

											 id= row.floor_id;

											 edit_target=(access.edit=='0'?"":"#confirm-edit");

											 delete_confirm=(access.delete=='1' ?'#confirm-delete':'');

											 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/branch_floor/delete/'+id : '#' );

											// action_content='<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

											var action_content=''

											if(access.edit == '1'){

											

											action_content+=  '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a>' ;

											

											}

											if(access.delete == '1'){

											 

											 action_content+=' <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

											}

											return action_content;

											 }

										}]

					});

				}

		  },

		  error:function(error)

		  {

			 $("div.overlay").css("display", "none");

		  }

	});

}

//add floor

function add_floor(branch_id,floor_name,floor_short_code,floor_status)

{

	my_Date = new Date();

	$.ajax({

		url: base_url+"index.php/admin_ret_catalog/branch_floor/add?nocache=" + my_Date.getUTCSeconds(),

		data:{"branch_id":branch_id,"floor_name":floor_name,"floor_short_code":floor_short_code,floor_status},

		type:"POST",

		dataType:"JSON",

		cache:false,

		success:function(data){

			$('#floor_id').val('');

			msg='<div class ="alert alert-'+data.class+'"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>   '+data.title+'   </strong> '+data.message+' </div>';

			$('#chit_alert').html(msg);

			set_floor_table();

		}

	});

}

//update floor

function update_floor(branch_id,floor_name,floor_short_code,status)

{

	 $("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		url: base_url+"index.php/admin_ret_catalog/branch_floor/update/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		data:{"branch_id":branch_id,"floor_name":floor_name,"floor_short_code":floor_short_code,"floor_status":status},

		type:"POST",

		//cache: false,

		success:function(){

				$("div.overlay").css("display", "none");

				window.location.reload(true);

		}

	});

}

function get_floor(id)

{

   my_Date = new Date();

	$.ajax({

		type:"GET",

		url: base_url+"index.php/admin_ret_catalog/branch_floor/edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		cache:false,

		dataType:"JSON",

		success:function(data){

			//console.log(data);

			id = data.floor_id;

			$("#edbranchid").select2("val",(data.branch_id!='' && data.branch_id>0?data.branch_id:''));

			$('#sel_fl_branchid').val(data.branch_id);

			$('#ed_floorname').val(data.floor_name);

			$('#ed_floorshortcode').val(data.floor_short_code);

			var floor_status = data.floor_status;

			if(floor_status == 1)

			{

				$('#ed_status').bootstrapSwitch('state', true);

			}

			else

			{

				$('#ed_status').bootstrapSwitch('state', false);

			}

			// console.log(wt);

		}

	});

}

//ends branch floor

function set_branchfloor_table(branch="",floor="",from_date="",to_date="")

{

		my_Date = new Date();

	$.ajax({

			 url:base_url+ "index.php/admin_ret_catalog/floor_counter?nocache=" + my_Date.getUTCSeconds(),

			 data: ( {'floor_id':floor,'id_branch':branch,'from_date':from_date,'to_date':to_date}),

			 dataType:"JSON",

			 type:"POST",

			 success:function(data){

				 var counter 	= data.counter;

				 var access		= data.access;

				 $('#total_count').text(counter.length);

				 if(access.add == '0')

				 {

					$('#add_count').attr('disabled','disabled');

				 }

			 var oTable = $('#counter_list').DataTable();

				 oTable.clear().draw();

						 if (counter!= null && counter.length > 0)

							{

							oTable = $('#counter_list').dataTable({

									"bDestroy": true,

				                    "bInfo": true,

				                    "bFilter": true,

									"order": [[ 0, "desc" ]],

				                    "scrollX":'100%',

				                    "bSort": true,

				                "dom": 'lBfrtip',

           		                "buttons" : ['excel','print'],

						        "tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },

									"aaData": counter,

									"aoColumns": [	{ "mDataProp": "counter_id" },

													{ "mDataProp": "counter_name" },

													{ "mDataProp": "system_fp_id" },

													{ "mDataProp": "counter_short_code" },

													{ "mDataProp": "floor_name" },

													{ "mDataProp": "name" },

													{ "mDataProp": function ( row, type, val, meta ){

					                	    		status_url = (access.edit =='1')?base_url+"index.php/admin_ret_catalog/floor_counter/update_status/"+row.counter_id+"/"+(row.counter_status==1?0:1):'#';

							                		return "<a href='"+status_url+"'><i class='fa "+(row.counter_status==1?'fa-check':'fa-remove')+"' style='color:"+(row.counter_status==1?'green':'red')+"'></i></a>"

						                				}

								              		},

													{ "mDataProp": function ( row, type, val, meta ) {

														 id= row.counter_id;

														 edit_target=(access.edit=='0'?"":"#confirm-edit");

														 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/floor_counter/delete/'+id : '#' );

														 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

														//  action_content='<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

														var action_content=''

														if(access.edit == '1'){

														

											        	action_content+=  '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a>' ;

														

													    }

														if(access.delete == '1'){

														 

														 action_content+='<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

														}

														 return action_content;

														 }

													}]

								});

							}

					  },

					  error:function(error)

					  {

						 $("div.overlay").css("display", "none");

					  }

			      });

}

function add_counter(floor_id,counter_name,counter_short_code,counter_status)

{

	my_Date = new Date();

	$.ajax({

		url: base_url+"index.php/admin_ret_catalog/floor_counter/add?nocache=" + my_Date.getUTCSeconds(),

		data:{"floor_id":floor_id,"counter_name":counter_name,"counter_short_code":counter_short_code,"counter_status":counter_status},

		type:"POST",

		dataType:"JSON",

		cache:false,

		success:function(data){

			$('#counter_id').val('');

			msg='<div class ="alert alert-'+data.class+'"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>   '+data.title+'   </strong> '+data.message+' </div>';

			$('#chit_alert').html(msg);

			set_branchfloor_table();

		}

	});

}

//update counter

function update_counter(floor_id,counter_name,counter_short_code,counter_status)

{

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		data:{"floor_id":floor_id,"counter_name":counter_name,"counter_short_code":counter_short_code,"counter_status":counter_status},

		url: base_url+"index.php/admin_ret_catalog/floor_counter/update/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		//cache: false,

		success:function(){

				$("div.overlay").css("display", "none");

				location.reload(true);

		}

	});

}

//get counter by id

function get_counter(id)

{

   my_Date = new Date();

	$.ajax({

		type:"GET",

		url: base_url+"index.php/admin_ret_catalog/floor_counter/edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		cache:false,

		dataType:"JSON",

		success:function(data){

			id=data.counter_id;

		    $('#ed_counter_flr_id').val(data.floor_id);

		    $("#ed_counter_flr_sel").select2("val",(data.floor_id!='' && data.floor_id>0?data.floor_id:''));

			$('#ed_counter_name').val(data.counter_name);

			$('#ed_counter_short_code').val(data.counter_short_code);

			$('#ed_counter_status').val(data.counter_status);

			if(data.counter_status == 1)

			{

				$('#ed_ctr_status').bootstrapSwitch('state', true);

			}

			else

			{

			 	$('#ed_ctr_status').bootstrapSwitch('state', false);

			}

		}

	});

}

function get_Activefloors(){

    $('#counter_flr_sel option').remove();

    $('#ed_counter_flr_sel option').remove();

	$.ajax({

	type: 'GET',

	url: base_url+'index.php/admin_ret_catalog/branch_floor/active_floors',

	dataType:'json',

	success:function(data){

		var id =  $("#counter_flr_id,#ed_counter_flr_id").val();

		var id_floor =  $("#counter_flr_id,#id_floor").val();

		$.each(data, function (key, item) {

		    $("#counter_flr_sel,#ed_counter_flr_sel").append(

		    $("<option></option>")

		    .attr("value", item.floor_id)

		    .text(item.floor_name)

		    );

		    $("#floor_filter").append(

		    $("<option></option>")

		    .attr("value", item.floor_id)

		    .text(item.floor_name)

		    );

		});

		$("#floor_filter").select2(

		{

			placeholder:"Select floor name",

			allowClear: true

		});

		if($("#floor_filter").length)

		{

		    $("#floor_filter").select2("val",'');

		}

		if($("#counter_flr_sel").length)

		{

		    $("#counter_flr_sel").select2("val",(id!='' && id>0?id:''));

		}

		if($("#counter_flr_sel").length)

		{

		    $("#counter_flr_sel").select2("val",(id_floor!='' && id_floor>0?id_floor:''));

		}

		if($("#ed_counter_flr_sel").length)

		{

		    $("#ed_counter_flr_sel").select2("val",(id!='' && id>0?id:''));

		}

		    $(".overlay").css("display", "none");

		}

	});

}

function set_makingtype_table(from_date="",to_date="")

{

		my_Date = new Date();

	$.ajax({

			 url:base_url+ "index.php/admin_ret_catalog/making_type?nocache=" + my_Date.getUTCSeconds(),

			 data: ( {'from_date':from_date,'to_date':to_date}),

			 dataType:"JSON",

			 type:"POST",

			 success:function(data){

		 	console.log(data);

				 var make 	= data.make;

				 var access	= data.access;

				 $('#total_count').text(make.length);

				 if(access.add == '0')

				 {

					$('#add_mak_type').attr('disabled','disabled');

				 }

			 var oTable = $('#making_type_list').DataTable();

				 oTable.clear().draw();

					 if (make!= null && make.length > 0)

						{

						oTable = $('#making_type_list').dataTable({

								"bDestroy": true,

			                "bInfo": true,

			                "bFilter": true,

			                "scrollX":'100%',

			                "bSort": true,

							"order": [[ 0, "desc" ]],

			                "dom": 'lBfrtip',

       		                "buttons" : ['excel','print'],

					        "tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },

								"aaData": make,

								"aoColumns": [	{ "mDataProp": "mak_id" },

												{ "mDataProp": "mak_name" },

												{ "mDataProp": "mak_short_code" },

												{ "mDataProp": function ( row, type, val, meta ){

				                	    		status_url = (access.edit =='1')?base_url+"index.php/admin_ret_catalog/making_type/update_status/"+row.mak_id+"/"+(row.mak_status==1?0:1):'#';

						                		return "<a href='"+status_url+"'><i class='fa "+(row.mak_status==1?'fa-check':'fa-remove')+"' style='color:"+(row.mak_status==1?'green':'red')+"'></i></a>"

					                				}

							              		},

												{ "mDataProp": function ( row, type, val, meta ) {

													 id= row.mak_id;

													 edit_target=(access.edit=='0'?"":"#confirm-edit");

													 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/making_type/delete/'+id : '#' );

													 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

													//  action_content='<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

													var action_content=''

													if(access.edit == '1'){

													

													action_content+=  '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a>' ;

													

													}

													if(access.delete == '1'){

													 

													 action_content+='<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

													}

													 return action_content;

													 }

												}]

							});

						}

					  },

					  error:function(error)

					  {

						 $("div.overlay").css("display", "none");

					  }

			      });

}

//to add making type

function add_making_type(making_name,making_short_code,making_status)

{

	my_Date = new Date();

	$.ajax({

		url: base_url+"index.php/admin_ret_catalog/making_type/add?nocache=" + my_Date.getUTCSeconds(),

		data:{"mak_name":making_name,"mak_short_code":making_short_code,"mak_status":making_status},

		type:"POST",

		dataType:"JSON",

		cache:false,

		success:function(data){

			$('#mak_id').val('');

			msg='<div class ="alert alert-'+data.class+'"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>   '+data.title+'   </strong> '+data.message+' </div>';

			$('#chit_alert').html(msg);

			set_makingtype_table();

		}

	});

}

//update making type

function update_making_type(making_name,making_short_code,making_status)

{

	 $("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		data:{"mak_name":making_name,"mak_short_code":making_short_code,"mak_status":making_status},

		url: base_url+"index.php/admin_ret_catalog/making_type/update/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		//cache: false,

		success:function(){

				$("div.overlay").css("display", "none");

				location.reload(true);

		}

	});

}

//get maketype by id

function get_maketype(id)

{

   my_Date = new Date();

	$.ajax({

		type:"GET",

		url: base_url+"index.php/admin_ret_catalog/making_type/edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		cache:false,

		dataType:"JSON",

		success:function(data){

			//console.log(data);

			id=data.mak_id;

			$('#ed_making_name').val(data.mak_name);

			$('#ed_making_short_code').val(data.mak_short_code);

			$('#ed_make').val(data.mak_status);

			if(data.mak_status==1)

			{

				$('#ed_make_switch').bootstrapSwitch('state', true);

			}

			else

			{

			 $('#ed_make_switch').bootstrapSwitch('state', false);

			}

		}

	});

}

// Theme

function set_theme_table(from_date="",to_date="")

{

	my_Date = new Date();

	$.ajax({

			 url:base_url+ "index.php/admin_ret_catalog/theme?nocache=" + my_Date.getUTCSeconds(),

			 data:{'from_date':from_date,'to_date':to_date},

			 dataType:"JSON",

			 type:"POST",

			 cache:false,

			 success:function(data){

				var theme 	    = data.theme;

				var access		= data.access;

				$('#total_theme').text(theme.length);

				if(access.add == '0')

				{

					$('#save_theme').attr('disabled','disabled');

				}

			 var oTable = $('#theme_list').DataTable();

			 oTable.clear().draw();

			 if (theme!= null && theme.length > 0)

			 {

				oTable = $('#theme_list').dataTable({

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

						"aaData"  : theme,

						"aoColumns": [	{ "mDataProp": "id_theme" },

										{ "mDataProp": "theme_name" },

										{ "mDataProp": "theme_code" },

										{ "mDataProp": "theme_desc"},

										{ "mDataProp": function ( row, type, val, meta ){

					                	 status_url = base_url+"index.php/admin_ret_catalog/theme/update_status/"+row.id_theme+"/"+(row.theme_status==1?0:1);

							             return "<a href='"+status_url+"'><i class='fa "+(row.theme_status==1?'fa-check':'fa-remove')+"' style='color:"+(row.theme_status==1?'green':'red')+"'></i></a>"

						                }

							      		},

										{ "mDataProp": function ( row, type, val, meta ) {

											id= row.id_theme;

											edit_target=(access.edit=='0'?"":"#confirm-edit");

											delete_confirm=(access.delete=='1' ?'#confirm-delete':'');

											delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/theme/delete/'+id : '#' );

											action_content='<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

											return action_content;

											}

										}]

					});

				}

		  },

		  error:function(error)

		  {

			 $("div.overlay").css("display", "none");

		  }

	});

}

function add_theme(theme_name,theme_code,theme_desc,theme_status)

{

	my_Date = new Date();

	var theme_det=[];

	$.ajax({

		data:theme_det,

		url: base_url+"index.php/admin_ret_catalog/theme/add?nocache=" + my_Date.getUTCSeconds(),

		data:{"theme_name":theme_name,"theme_code":theme_code,"theme_desc":theme_desc,"theme_status":theme_status},

		type:"POST",

		dataType:"JSON",

		cache:false,

		success:function(data){

			msg='<div class ="alert alert-'+data.class+'"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>   '+data.title+'   </strong> '+data.message+' </div>';

			$('#chit_alert').html(msg);

			set_theme_table();

		}

	});

}

//update floor

function update_theme(theme_name,theme_code,theme_desc,theme_status)

{

	 $("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		data:{"theme_name":theme_name,"theme_code":theme_code,"theme_desc":theme_desc,"theme_status":theme_status},

		url: base_url+"index.php/admin_ret_catalog/theme/update/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		cache: false,

		success:function(){

				$("div.overlay").css("display", "none");

				window.location.reload(true);

		}

	});

}

function get_theme(id)

{

   my_Date = new Date();

	$.ajax({

		type:"GET",

		url: base_url+"index.php/admin_ret_catalog/theme/edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		cache:false,

		dataType:"JSON",

		success:function(data){

			id = data.id_theme;

			$('#ed_themename').val(data.theme_name);

			$('#ed_themeshortcode').val(data.theme_code);

			$('#ed_theme_desc').val(data.theme_desc);

			var theme_status = data.theme_status;

			if(theme_status == 1)

			{

			$('#ed_themestatus').bootstrapSwitch('state', true);

			}

			else

			{

			$('#ed_themestatus').bootstrapSwitch('state', false);

			}

		}

	});

}

//material

function set_material_table(from_date="",to_date="")

{

	my_Date = new Date();

	$.ajax({

			 url:base_url+ "index.php/admin_ret_catalog/material?nocache=" + my_Date.getUTCSeconds(),

			 data:{'from_date':from_date,'to_date':to_date},

			 dataType:"JSON",

			 type:"POST",

			 cache:false,

			 success:function(data){

				var material 	= data.material;

				var access		= data.access;

				$('#total_material').text(material.length);

				if(access.add == '0')

				{

					$('#add_material').attr('disabled','disabled');

				}

			 var oTable = $('#material_list').DataTable();

			 oTable.clear().draw();

			 if (material!= null && material.length > 0)

			 {

				oTable = $('#material_list').dataTable({

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

						"aaData"  : material,

						"aoColumns": [	{ "mDataProp": "material_id" },

										{ "mDataProp": "material_name" },

										{ "mDataProp": "material_code" },

										{ "mDataProp": function ( row, type, val, meta ){

					                	 status_url = (access.edit =='1')?base_url+"index.php/admin_ret_catalog/material/update_status/"+row.material_id+"/"+(row.material_status==1?0:1):'#';

							             return "<a href='"+status_url+"'><i class='fa "+(row.material_status==1?'fa-check':'fa-remove')+"' style='color:"+(row.material_status==1?'green':'red')+"'></i></a>"

						                }

							      		},

										{ "mDataProp": function ( row, type, val, meta ) {

											id= row.material_id;

											edit_target=(access.edit=='0'?"":"#confirm-edit");

											delete_confirm=(access.delete=='1' ?'#confirm-delete':'');

											delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/material/delete/'+id : '#' );

											// action_content='<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

											var action_content=''

											if(access.edit == '1'){

											

											action_content+=  '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a>' ;

											

											}

											if(access.delete == '1'){

											 

											 action_content+='<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

											}

											return action_content;

											}

										}]

					});

				}

		  },

		  error:function(error)

		  {

			 $("div.overlay").css("display", "none");

		  }

	});

}

function add_material(material_name,material_code,material_status)

{

	my_Date = new Date();

    var material_id=material_id;

	$.ajax({

		url: base_url+"index.php/admin_ret_catalog/material/add?nocache=" + my_Date.getUTCSeconds(),

		data:{"material_name":material_name,"material_code":material_code,"material_status":material_status},

		type:"POST",

		dataType:"JSON",

		cache:false,

		success:function(data){

			$('#material_id').val('');

			msg='<div class ="alert alert-'+data.class+'"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>   '+data.title+'   </strong> '+data.message+' </div>';

			$('#chit_alert').html(msg);

			set_material_table();

		}

	});

}

function update_material(material_name,material_code,material_status)

{

	 $("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		data:{"material_name":material_name,"material_code":material_code,"material_status":material_status},

		url: base_url+"index.php/admin_ret_catalog/material/update/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		cache: false,

		success:function(){

				$("div.overlay").css("display", "none");

				window.location.reload(true);

		}

	});

}

function get_material(id)

{

   my_Date = new Date();

	$.ajax({

		type:"GET",

		url: base_url+"index.php/admin_ret_catalog/material/edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		cache:false,

		dataType:"JSON",

		success:function(data){

			//console.log(data);

			id = data.material_id;

			$('#ed_material_name').val(data.material_name);

			$('#ed_material_code').val(data.material_code);

			$('#ed_material_status').val(data.material_status);

			var material_status = data.material_status;

			if(material_status == 1)

			{

			$('#ed_mtr_status').bootstrapSwitch('state', true);

			}

			else

			{

			$('#ed_mtr_status').bootstrapSwitch('state', false);

			}

		}

	});

}

/*function set_materialrate_table(material_id="",from_date="",to_date="")

{

	my_Date = new Date();

	$.ajax({

			 url:base_url+ "index.php/admin_ret_catalog/material_rate?nocache=" + my_Date.getUTCSeconds(),

			 data:({'from_date':from_date,'to_date':to_date,'material_id':material_id}),

			 dataType:"JSON",

			 type:"POST",

			 cache:false,

			 success:function(data){

			    //console.log(data);

				var mtrrate 	= data.mtrrate;

				var access	    = data.access;

				$('#total_mtrrate').text(mtrrate.length);

				if(access.add == '0')

				{

					$('#add_mtrlrate').attr('disabled','disabled');

				}

				else{

					$("#add_newmtrrate,#add_mtrrate").on('click',function(){

					if($("#mtrid_rate").val() != '' && $("#mtr_rate").val() != ''){

						add_mtrrate($('#mtrid_rate').val(),$('#mtr_rate').val(),$('#mtr_eff_date').val());

						$('#mtrid_rate').val();

						$('#mtr_rate').val('');

						$('#mtr_eff_date').val();

					}

					else if($("#mtrid_rate").val() == ''){

						msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Material Name .</div>';

						$("div.overlay").css("display", "none");

						$('#error-msg').html(msg);

					return false;

					}

					else if($("#mtr_rate").val() == ''){

						msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Material Rate .</div>';

						$("div.overlay").css("display", "none");

						$('#error-msg').html(msg);

					return false;

					}

					});

				}

			 var oTable = $('#mtrrate_list').DataTable();

			 oTable.clear().draw();

			 if (mtrrate!= null && mtrrate.length > 0)

			 {

				oTable = $('#mtrrate_list').dataTable({

						"bDestroy": true,

						"bInfo": true,

						"bFilter": true,

						"bSort": true,

						"dom": 'lBfrtip',

						"buttons" : ['excel','print'],

						"tableTools": { "buttons": [ { "sExtends": "xls",

						"oSelectorOpts": { page: 'current'

						} },

						{ "sExtends": "pdf",

						"oSelectorOpts": { page: 'current'

						} } ] },

						"aaData"  : mtrrate,

						"aoColumns": [	{ "mDataProp": "mat_rate_id" },

										{ "mDataProp": "material_name" },

										{ "mDataProp": "mat_rate" },

										{ "mDataProp": "effective_date" },

										{ "mDataProp": function ( row, type, val, meta ) {

											id= row.mat_rate_id;

											edit_target=(access.edit=='0'?"":"#confirm-edit");

											delete_confirm=(access.delete=='1' ?'#confirm-delete':'');

											delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/material_rate/delete/'+id : '#' );

											action_content='<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i> Edit</a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> Delete</a>'

											return action_content;

											}

										}]

					});

				}

		  },

		  error:function(error)

		  {

			 $("div.overlay").css("display","none");

		  }

	});

}*/

function add_mtrrate(material_id,mat_rate,effective_date)

{

	my_Date = new Date();

    var material_id=material_id;

	$.ajax({

		url: base_url+"index.php/admin_ret_catalog/material_rate/add?nocache=" + my_Date.getUTCSeconds(),

		data:{"material_id":material_id,"mat_rate":mat_rate,"effective_date":effective_date},

		type:"POST",

		dataType:"JSON",

		cache:false,

		success:function(data){

			$('#mat_rate_id').val('');

			msg='<div class ="alert alert-'+data.class+'"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>   '+data.title+'   </strong> '+data.message+' </div>';

			$('#chit_alert').html(msg);

			set_materialrate_table();

		}

	});

}

function update_mtrrate(material_id,mat_rate,effective_date)

{

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		data:{"material_id":material_id,"mat_rate":mat_rate,"effective_date":effective_date},

		url: base_url+"index.php/admin_ret_catalog/material_rate/update/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		cache: false,

		success:function(){

				$("div.overlay").css("display", "none");

			    window.location.reload(true);

		}

	});

}

function get_materialSelLst(){

	$.ajax({

	type: 'GET',

	url: base_url+'index.php/admin_ret_catalog/material_rate/material_lst',

	dataType:'json',

	success:function(data){

		mat_det = data;

		$.each(data, function (key, item) {

		    $("#addmatrt_lst").append(

		    $("<option></option>")

		    .attr("value", item.material_id)

		    .text(item.material_name)

		    );

		});

		$("#addmatrt_lst").select2(

		{

			placeholder:"Select Material name",

			allowClear: true

		});

		 $("#addmatrt_lst").select2("val",'');

		$(".overlay").css("display", "none");

		}

	});

}

function get_materialrate(id)

{

   my_Date = new Date();

	$.ajax({

		type:"GET",

		url: base_url+"index.php/admin_ret_catalog/material_rate/edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		cache:false,

		dataType:"JSON",

		success:function(data){

			console.log(data);

			id = data.mat_rate_id;

			$('#edmtrrate_id').val(data.material_id);

			$("#ed_mtrrate_name").select2("val",(data.material_id!='' && data.material_id>0?data.material_id:''));

			$('#ed_material_rate').val(data.mat_rate);

			$('#ed_eff_date').val(data.effective_date);

		}

	});

}

//end of material

function get_uomlist()

{

	$.ajax({

		type: 'GET',

		url: base_url+'index.php/admin_ret_catalog/uom/active_uom',

		dataType:'json',

		success:function(data){

			var id =  $("#uom_id,#ed_uom_id").val();

			$.each(data, function (key, item) {

				$("#stone_sel,#ed_stone_sel").append(

				$("<option></option>")

				.attr("value",item.uom_id)

				.text(item.uom_name)

		    	);

		    });

		    $("#stone_sel,#ed_stone_sel").select2("val",(id!='' && id>0?id:''));

		    $(".overlay").css("display", "none");

		}

	});

}

function set_stone_table(from_date="",to_date="")

{

	my_Date = new Date();

	$.ajax({

			 url:base_url+ "index.php/admin_ret_catalog/stone?nocache=" + my_Date.getUTCSeconds(),

			 data: ( {'from_date':from_date,'to_date':to_date}),

			 dataType:"JSON",

			 type:"POST",

			 success:function(data){

				 var stone 	= data.stone;

				 var access		= data.access;

				 $('#total_count').text(stone.length);

				 if(access.add == '0')

				 {

					$('#add_stone').attr('disabled','disabled');

				 }

			 var oTable = $('#stone_list').DataTable();

				 oTable.clear().draw();

						 if (stone!= null && stone.length > 0)

							{

							oTable = $('#stone_list').dataTable({

								"bDestroy": true,

				                "bInfo": true,

				                "bFilter": true,

								"order": [[ 0, "desc" ]],

				                "scrollX":'100%',

				                "bSort": true,

								"order": [[ 0, "desc" ]],

				                "dom": 'lBfrtip',

           		                "buttons" : ['excel','print'],

						        "tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },

								"aaData": stone,

								"aoColumns": [	{ "mDataProp": "stone_id" },

												{ "mDataProp": "stone_name" },

												{ "mDataProp": "stone_code" },

												{ "mDataProp": "uom_name" },

												{ "mDataProp": function ( row, type, val, meta ){

				                	    		status_url = (access.edit =='1')?base_url+"index.php/admin_ret_catalog/stone/update_status/"+row.stone_id+"/"+(row.stone_status==1?0:1):'#';

						                		return "<a href='"+status_url+"'><i class='fa "+(row.stone_status==1?'fa-check':'fa-remove')+"'style='color:"+(row.stone_status==1?'green':'red')+"'></i></a>"

					                				}

							              		},

												{ "mDataProp": function ( row, type, val, meta ) {

													 id= row.stone_id;

													 edit_target=(access.edit=='0'?"":"#confirm-edit");

													 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/stone/delete/'+id : '#' );

													 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

													//  action_content='<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i> </a><a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

													var action_content=''

														if(access.edit == '1'){

														

											        	action_content+=  '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i> </a>' ;

														

													    }

														if(access.delete == '1'){

														 

														 action_content+='<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

														}

													 return action_content;

													 }

												}]

								});

							}

					  },

					  error:function(error)

					  {

						 $("div.overlay").css("display", "none");

					  }

			      });

}

//update stone

function update_stone(uom_id,stone_name,stone_code,stone_type,is_certificate_req,is_4c_req,stone_status)

{

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		data:{"uom_id":uom_id,"stone_name":stone_name,"stone_code":stone_code,"stone_type":stone_type,"is_certificate_req":is_certificate_req,"is_4c_req":is_4c_req,"stone_status":stone_status},

		url: base_url+"index.php/admin_ret_catalog/stone/update/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		success:function(){

				$("div.overlay").css("display", "none");

				location.reload(true);

		}

	});

}

//get stone by id

function get_stone(id)

{

   my_Date = new Date();

	$.ajax({

		type:"GET",

		url: base_url+"index.php/admin_ret_catalog/stone/edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		cache:false,

		dataType:"JSON",

		success:function(data){

			//console.log(data);

			id=data.stone_id;

			$("#ed_stone_sel").select2("val",(data.uom_id!='' && data.uom_id>0?data.uom_id:''));

			$('#ed_stone_name').val(data.stone_name);

			$('#ed_stone_code').val(data.stone_code);

			var x = data.stone_type;

			if(x == 1)

			{

				$('input:radio[name="ed_stone_type"][value="1"]').attr('checked',true);

			}else if(x == 2)

			{

				$('input:radio[name="ed_stone_type"][value="2"]').attr('checked',true);

			}else if(x == 3)

			{

				$('input:radio[name="ed_stone_type"][value="3"]').attr('checked',true);

			}

			var certificate=data.is_certificate_req;

			var ce_status=data.is_4c_req;

			var s_status= data.stone_status;

			if(certificate==1)

			{

				$("#ed_ce_switch").bootstrapSwitch('state', true);

			}

			else

			{

			    $("#ed_ce_switch").bootstrapSwitch('state', false);

			}

			if(ce_status==1)

			{

				$("#ed_isreq_switch").bootstrapSwitch('state', true);

			}

			else

			{

			 $("#ed_isreq_switch").bootstrapSwitch('state', false);

			}

			if(s_status==1)

			{

				$("#ed_stone_switch").bootstrapSwitch('state', true);

			}

			else

			{

			 $("#ed_stone_switch").bootstrapSwitch('state', false);

			}

		}

	});

}

function set_uom_table(from_date="",to_date="")

{

	my_Date = new Date();

	$.ajax({

			 url:base_url+ "index.php/admin_ret_catalog/uom?nocache=" + my_Date.getUTCSeconds(),

			 data:{'from_date':from_date,'to_date':to_date},

			 dataType:"JSON",

			 type:"POST",

			 cache:false,

			 success:function(data){

				var uom 	= data.uom;

				var access	= data.access;

				$('#total_uom').text(uom.length);

				if(access.add == '0')

				{

					$('#add_uom').attr('disabled','disabled');

				}

			 var oTable = $('#uom_list').DataTable();

			 oTable.clear().draw();

			 if (uom!= null && uom.length > 0)

			 {

				oTable = $('#uom_list').dataTable({

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

						"aaData"  : uom,

						"aoColumns": [	{ "mDataProp": "uom_id" },

										{ "mDataProp": "uom_name" },

										{ "mDataProp": "uom_short_code" },

										{ "mDataProp": function ( row, type, val, meta ){

					                	 status_url = (access.edit =='1')?base_url+"index.php/admin_ret_catalog/uom/update_status/"+row.uom_id+"/"+(row.uom_status==1?0:1):'#';

							             return "<a href='"+status_url+"'><i class='fa "+(row.uom_status==1?'fa-check':'fa-remove')+"' style='color:"+(row.uom_status==1?'green':'red')+"'></i></a>"

						                }

							      		},

										{ "mDataProp": function ( row, type, val, meta ) {

											id= row.uom_id;

											edit_target=(access.edit=='0'?"":"#confirm-edit");

											delete_confirm=(access.delete=='1' ?'#confirm-delete':'');

											delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/uom/delete/'+id : '#' );

											// action_content='<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

											var action_content=''

														if(access.edit == '1'){

														

											        	action_content+=  '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a>' ;

														

													    }

														if(access.delete == '1'){

														 

														 action_content+=' <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

														

														}

											return action_content;

											}

										}]

					});

				}

		  },

		  error:function(error)

		  {

			 $("div.overlay").css("display", "none");

		  }

	});

}

function add_uom(uom_name,uom_short_code,uom_status)

{

	my_Date = new Date();

	$.ajax({

		url: base_url+"index.php/admin_ret_catalog/uom/add?nocache=" + my_Date.getUTCSeconds(),

		data:{"uom_name":uom_name,"uom_short_code":uom_short_code,"uom_status":uom_status},

		type:"POST",

		dataType:"JSON",

		cache:false,

		success:function(data){

			$('#uom_id').val('');

			msg='<div class ="alert alert-'+data.class+'"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>   '+data.title+'   </strong> '+data.message+' </div>';

			$('#chit_alert').html(msg);

			set_uom_table();

		}

	});

}

//update uom

function update_uom(uom_name,uom_short_code,uom_status)

{

	 $("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		data:{"uom_name":uom_name,"uom_short_code":uom_short_code,"uom_status":uom_status},

		url: base_url+"index.php/admin_ret_catalog/uom/update/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		cache: false,

		success:function(){

				$("div.overlay").css("display", "none");

				window.location.reload(true);

		}

	});

}

function get_uom(id)

{

   my_Date = new Date();

	$.ajax({

		type:"GET",

		url: base_url+"index.php/admin_ret_catalog/uom/edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		cache:false,

		dataType:"JSON",

		success:function(data){

			//console.log(data);

			id = data.material_id;

			$('#ed_uom_name').val(data.uom_name);

			$('#ed_uom_code').val(data.uom_short_code);

			var uom_status = data.uom_status;

			if(uom_status == 1)

			{

			$('#ed_uom_status').bootstrapSwitch('state', true);

			}

			else

			{

			$('#ed_uom_status').bootstrapSwitch('state', false);

			}

		}

	});

}

function set_category_table(from_date="",to_date="")

{

	my_Date = new Date();

	$.ajax({

			 url:base_url+ "index.php/admin_ret_catalog/category?nocache=" + my_Date.getUTCSeconds(),

			 data:{'from_date':from_date,'to_date':to_date},

			 dataType:"JSON",

			 type:"POST",

			 cache:false,

			 success:function(data){

				var categorymtr = data.categorymtr;

				var access		= data.access;

				$('#total_category').text(categorymtr.length);

				// if(access.add == '0')

				// {

				// 	$('#add_category').attr('disabled','disabled');

				// }

			 var oTable = $('#categorymtr_list').DataTable();

			 oTable.clear().draw();

			 if (categorymtr!= null && categorymtr.length > 0)

			 {

				oTable = $('#categorymtr_list').dataTable({

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

						"aaData"  : categorymtr,

						"aoColumns": [	{ "mDataProp": "id_ret_category" },

										{ "mDataProp": "name" },

										{ "mDataProp": "cat_code" },

										{ "mDataProp": "metal" },

										{ "mDataProp": "multimetal" },

										{ "mDataProp": function ( row, type, val, meta ){

					                	 status_url = (access.edit ==1) ? base_url+"index.php/admin_ret_catalog/category/update_status/"+row.id_ret_category+"/"+(row.status==1?0:1):'#';

							             return "<a href='"+status_url+"'><i class='fa "+(row.status==1?'fa-check':'fa-remove')+"' style='color:"+(row.status==1?'green':'red')+"'></i></a>"

						                }

							      		},

										{ "mDataProp": function ( row, type, val, meta ) {

											id= row.id_ret_category;

											edit_target=(access.edit=='0'?"":"#confirm-edit");

											delete_confirm=(access.delete=='1' ?'#confirm-delete':'');

											delete_url =

                        access.delete == "1"

                          ? base_url +

                            "index.php/admin_ret_catalog/category/delete/" +

                            id

                          : "#";

                      //action_content='<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i> </a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> </a>'

                      var action_content = "";

                      if (access.edit == "1") {

                        action_content +=

                          '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id=' +

                          id +

                          "  data-target=" +

                          edit_target +

                          '><i class="fa fa-edit" ></i> </a> ';

                      }

                      if (access.delete == "1") {

                        action_content +=

                          '<a href="#" class="btn btn-danger btn-del" data-href=' +

                          delete_url +

                          ' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> </a>';

                      }

                      return action_content;

											}

										}]

					});

				}

		  },

		  error:function(error)

		  {

			 $("div.overlay").css("display", "none");

		  }

	});

}

function add_categorymtr(name,cat_code,description,id_metal,id_purity,status,file,hsn_code,is_multimetal,tag_group,cat_type)

{

	my_Date = new Date();

	var file =file;

	var data = new FormData();

	var form_data = new FormData();

	form_data.append('name', name);

	form_data.append('cat_code', cat_code);

	form_data.append('description', description);

	form_data.append('id_metal', id_metal);

	form_data.append('id_purity', id_purity);

	form_data.append('file', file);

	form_data.append('status', status);

	form_data.append('hsn_code', hsn_code);

	form_data.append('is_multimetal', is_multimetal);

	form_data.append('tgrp_id', tag_group);

	form_data.append('cat_type', cat_type);

	$.ajax({

		data:form_data,

		url: base_url+"index.php/admin_ret_catalog/category/add?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		dataType:"JSON",

		cache:false,

		enctype: 'multipart/form-data',

		contentType : false,

		processData : false,

		success:function(data){

			console.log(data);

			msg='<div class ="alert alert-'+data.class+'"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>   '+data.title+'   </strong> '+data.message+' </div>';

			$('#chit_alert').html(msg);

			set_category_table();

		}

	});

}

function get_categorymtr(id)

{

   my_Date = new Date();

	$.ajax({

		type:"GET",

		url: base_url+"index.php/admin_ret_catalog/category/edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		cache:false,

		dataType:"JSON",

		success:function(data){

			console.log(data);

			id = data.id_category ;

			$('#ed_category_name').val(data.name);

			$('#ed_hsn_code').val(data.hsn_code);

			$('#ed_cate_code').val(data.cat_code);

			$("input[name='ed_category_type'][value="+data.cat_type+"]").prop('checked', true);

			$('#ed_category_desc').val(data.description);

			$('#id_metal_cate').val(data.id_metal);

			$("#metal_category1").select2("val",(data.id_metal!='' && data.id_metal>0?data.id_metal:''));

			 $('#ed_purity_sel').select2("val",data.purity);

			 $('#ed_tgrp_sel').select2("val",data.tgrp_id);

			 $('#ed_tgrp_id').val(data.tgrp_id);

			if(data.is_multimetal == 1) {

				$('#ed_multimetal_yes').prop('checked', true);

			} else {

				$('#ed_multimetal_no').prop('checked', true);

			}

			if($('#pur_id,#ed_pur_id').data('id_purity'))

			{

			   var ar = $('#pur_id,#ed_pur_id').data('id_purity');

               $("#purity_sel,#ed_purity_sel").select2('val',ar);

			}

			else

			{

			  $("#purity_sel,#ed_purity_sel").select2('val','');

			}

			var c_status = data.status;

			if(c_status == 1)

			{

			$('#ed_category_status').bootstrapSwitch('state', true);

			}

			else

			{

			$('#ed_category_status').bootstrapSwitch('state', false);

			}

			image = data.image;

		    if(image!="" && image!=null)

		    {

		    	 var img=base_url+"assets/img/ret_category/"+image;

		    	 $("#ed_categorymtr_img_preview").attr('src',img);

		    }

		    else

		    {

		    	var img=base_url+"assets/img/no_image.png";

		    	$("#ed_categorymtr_img_preview").attr('src',img);

		    }

		}

	});

}

function update_categorymtr(name,cat_code,description,id_metal,id_purity,status,file,hsn_code,is_multimetal,tgrp_id,cat_type)

{

	 $("div.overlay").css("display", "block");

	my_Date = new Date();

    var data = new FormData();

	var form_data = new FormData();

    form_data.append('name', name);

	form_data.append('cat_code', cat_code);

    form_data.append('description', description);

    form_data.append('id_metal', id_metal);

	form_data.append('id_purity', id_purity);

    form_data.append('file', file);

    form_data.append('status', status);

    form_data.append('hsn_code', hsn_code);

    form_data.append('tgrp_id', tgrp_id);

	form_data.append('is_multimetal', is_multimetal);

	form_data.append('cat_type', cat_type);

	$.ajax({

		data:form_data,

		url: base_url+"index.php/admin_ret_catalog/category/update/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		cache: false,

		enctype: 'multipart/form-data',

		contentType : false,

		processData : false,

		success:function(data){

				$("div.overlay").css("display", "none");

				window.location.reload(true);

		}

	});

}

function valCategory_Image()

{

	  var height=($(this).height());

  	  var width=($(this).width());

   	  if(arguments[0].id == 'ed_categorymtr_img')

      {

		 var preview = $('#ed_categorymtr_img_preview');

	  }

	  else if(arguments[0].id == 'categorymtr_img')

	  {

		var preview = $('#categorymtr_img_preview');

	  }

	  if(arguments[0].files[0].size > 1048578)

	  {

		 alert('File size cannot be greater than 1 MB');

		 arguments[0].value = "";

		 preview.css('display','none');

	  }

	  else

	  {

		var fileName =arguments[0].value;

		var ext = fileName.substring(fileName.lastIndexOf('.') + 1);

		ext = ext.toLowerCase();

		if(ext != "jpg" && ext != "png" && ext != "jpeg" && ext != "svg")

		{

			alert("Upload JPG or PNG Images only");

			arguments[0].value = "";

			preview.css('display','none');

		}

		else

		{

			var file    = arguments[0].files[0];

			var reader  = new FileReader();

			  reader.onloadend = function () {

				preview.prop('src',reader.result);

			  }

			  if (file)

			  {

			 	reader.readAsDataURL(file);

				preview.css('display','');

			  }

			 else

			  {

			  	preview.prop('src','');

				preview.css('display','none');

			  }

	 	}

	  }

}

function validate_user_Image()

   	{

		var height=($(this).height());

		var width=($(this).width());

		if(arguments[0].id == 'ed_user_img')

		{

			var preview = $('#ed_user_img_preview');

		}

		else if(arguments[0].id == 'user_img')

		{

			var preview = $('#user_img_preview');

		}

		if(arguments[0].files[0].size > 1048578)

		{

			alert('File size cannot be greater than 1 MB');

			arguments[0].value = "";

			preview.css('display','none');

		}

		else

			{

			var fileName =arguments[0].value;

			var ext = fileName.substring(fileName.lastIndexOf('.') + 1);

			ext = ext.toLowerCase();

			if(ext != "jpg" && ext != "png" && ext != "jpeg" && ext != "svg")

			{

				alert("Upload JPG or PNG Images only");

				arguments[0].value = "";

				preview.css('display','none');

			}

			else

			{

				var file    = arguments[0].files[0];

				var reader  = new FileReader();

				  reader.onloadend = function () {

				  preview.prop('src',reader.result);

				  }

				  if (file)

				  {

					reader.readAsDataURL(file);

					preview.css('display','');

				  }

				 else

				  {

					preview.prop('src','');

					preview.css('display','none');

				  }

			}

		}

    }

//to get countries

function get_country() {

	my_Date = new Date();

	$.ajax({

		type: 'GET',

		url: base_url + 'index.php/settings/company/getcountry?nocache=' + my_Date.getUTCSeconds(),

		dataType: 'json',

		cache: false,

		success: function (country) {

			$("#country").select2(

				{

					placeholder: "Select Country",

				});

			var ed_id_country = $('#ed_id_country').val();

			$.each(country, function (key, country) {

						if(ed_id_country==''){

							if (country.is_default == 1) {

								$('#id_country').val(country.id);

							}

						}

						$("#country").append(

							$("<option></option>")

								.attr("value", country.id)

								.text(country.name)

						);

			});

			if (ed_id_country!='') {

				$('#country').select2("val",ed_id_country);

			}

			if ($('#id_country').val()!= '') {

				$('#country').select2("val",$('#id_country').val());

			}

		}

	});

}

//to get state details based on country selection

function get_state(id) {

	my_Date = new Date();

	$('#state option').remove();

	$.ajax({

		type: 'POST',

		data: { 'id_country': id },

		url: base_url + 'index.php/settings/company/getstate/?nocache=' + my_Date.getUTCSeconds(),

		cache: false,

		dataType: 'json',

		success: function (state) {

			var ed_id_state = $('#ed_id_state').val();

			$.each(state, function (key, state) {

			if(ed_id_state==''){

				if (state.is_default == 1) {

					$('#id_state').val(state.id);

				}

			}

				$("#state").append(

					$("<option></option>")

						.attr("value", state.id)

						.text(state.name)

				);

			});

			var ed_id_state = $('#ed_id_state').val();

			$("#state").select2(

				{

					placeholder: "Select State",

				});

			if ($('#id_state').val()) {

				$('#state').select2("val", $('#id_state').val() != '' && $('#id_state').val() > 0 ? $('#id_state').val() : '');

			}

			if (ed_id_state != '') {

				$('#state').select2("val", ed_id_state != '' && ed_id_state > 0 ? ed_id_state : '');

			}

			get_city(id_state);

		}

	});

}

//to get city based on state selection

function get_city(id) {

	$('#city option').remove();

	my_Date = new Date();

	$.ajax({

		type: 'POST',

		data: { 'id_state': id },

		url: base_url + "index.php/settings/company/getcity?nocache=" + my_Date.getUTCSeconds(),

		cache: false,

		dataType: 'json',

		success: function (city) {

			$("#city").select2(

				{

					placeholder: "Select City",

				});

			$.each(city, function (key, city) {

				$("#city").append(

					$("<option></option>")

						.attr("value", city.id)

						.text(city.name)

				);

			});

			var id_city = $('#id_city').val();

			var ed_id_city = $('#ed_id_city').val();

			if (id_city != '' && ed_id_city == '') {

				$('#city').select2("val", ((id_city != '' && id_city > 0) ? id_city : ''));

			}

			if (ed_id_city != '') {

				$('#city').select2("val", ((ed_id_city != '' && ed_id_city > 0) ? ed_id_city : ''));

			}

		}

	});

}

$('#supplier_search').on('click',function(){

    set_karigar_table();

});

function set_karigar_table(from_date="",to_date="")

{

	my_Date = new Date();

	$.ajax({

			 url:base_url+ "index.php/admin_ret_catalog/karigar?nocache=" + my_Date.getUTCSeconds(),

			 data: ( {'from_date':from_date,'to_date':to_date,'karigar_for':$('#karigar_for').val(),'karigar_type':$('#karigar_type').val()}),

			 dataType:"JSON",

			 type:"POST",

			 success:function(data){

		 	//console.log(data);

				 var karigar 	= data.karigar;

				 var access		= data.access;

				 $('#total_count').text(karigar.length);

				 if(access.add == '0')

				 {

					$('#karigar_add').attr('disabled','disabled');

				 }

			 var oTable = $('#karigar_list').DataTable();

				 oTable.clear().draw();

						 if (karigar!= null && karigar.length > 0)

							{

							oTable = $('#karigar_list').dataTable({

								"bDestroy": true,

				                "bInfo": true,

								"order": [[ 0, "desc" ]],

				                "bFilter": true,

				                "scrollX":'100%',

				                "bSort": true,

				                "dom": 'lBfrtip',

								"buttons": [

									{

										extend: 'print',

										footer: true,

										title: "Karigar List",

										customize: function (win) {

											$(win.document.body).find('table')

												.addClass('compact')

												.css('font-size', 'inherit');

										},

									},

									{

										extend: 'excel',

										footer: true,

										title: "Karigar List",

									}

								],

						        // "tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },

									"aaData": karigar,

									"aoColumns": [	{ "mDataProp": "id_karigar" },

													{ "mDataProp": "firstname" },

													{ "mDataProp": "urname" },

													{ "mDataProp": "contactno1" },

													{ "mDataProp": "usertype" },

													{ "mDataProp": "karigartype" },

													{ "mDataProp": function ( row, type, val, meta ){

					                	    		status_url =(access.edit == '1') ? base_url+"index.php/admin_ret_catalog/karigar/update_status/"+row.id_karigar+"/"+(row.status_karigar==1?0:1):'#';

							                		return "<a href='"+status_url+"'><i class='fa "+(row.status_karigar==1?'fa-check':'fa-remove')+"'style='color:"+(row.status_karigar==1?'green':'red')+"'></i></a>"

						                				}

								              		},

													{ "mDataProp": function ( row, type, val, meta ) {

														 id= row.id_karigar;

														 print_url=base_url+'index.php/admin_ret_catalog/karigar/print/'+id;

														//  '<a href="'+base_url+'index.php/admin_ret_purchase/grnentry/edit/'+id+'" class="btn btn-primary btn-edit"><i class="fa fa-edit" ></i></a>' : '' );

														//  <a href="'+edit_url+'" class="btn btn-primary btn-edit"><i class="fa fa-edit" ></i></a>

														 edit_url=(access.edit=='1' ? '<a href="'+base_url+'index.php/admin_ret_catalog/karigar/edit/'+id+'" class="btn btn-primary btn-edit"><i class="fa fa-edit" ></i></a>':''  );

														 delete_url=(access.delete=='1' ? '<a href="'+base_url+'index.php/admin_ret_catalog/karigar/delete/'+id+'" class="btn btn-danger btn-del" > <i class="fa fa-trash"></i></a>': '' );

														 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

														 action_content='<a href="'+print_url+'" target="_blank" class="btn btn-info btn-print" data-toggle="tooltip" title="karigar print"><i class="fa fa-print" ></i></a>'+ edit_url + delete_url;

														 return action_content;

														 }

													}]

								});

							}

					  },

					  error:function(error)

					  {

						 $("div.overlay").css("display", "none");

					  }

			      });

}

function create_new_empty_bank_kyc_detail()

{

	if(validateSupplierAccountDetails())

	{

	    var html = "";

    	var a = $("#nb_increment").val();

    	var i = ++a;

    	$("#nb_increment").val(i);

    	html+="<tr id='bank_detail"+i+"' class='"+i+"'>"+

    		'<td><input class="form-control bank_name" id="bank_name'+i+'"  name="bank[bank_name][]" type="text" placeholder="Bank Name" value=""/></td>'+

    		'<td><input class="form-control acc_holder" id="acc_holder'+i+'" type="text" name="bank[acc_holder][]"  type="text" placeholder="Account Holder Name" value=""/></td>'+

    	    '<td><input class="form-control acc_number" id="acc_number'+i+'"  name="bank[acc_number][]" type="text" placeholder="Account Number" value="" /></td>'+

    		'<td><input class="form-control ifsc_code" id="ifsc_code'+i+'" name="bank[ifsc_code][]" type="text" placeholder="IFSC Code" value="" style="text-transform:uppercase"/></td>'+

    		'<td><a href="#" onClick="create_new_empty_bank_kyc_detail();"class="btn btn-success"><i class="fa fa-plus"></i><br></button><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'+

    	    '</tr>';

		$('#bank_list tbody').append(html);

	}else

	{

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill the required fields..'});

	}

}

function validateSupplierAccountDetails()

{

    var validate = true;

	$('#bank_list > tbody >tr').each(function(index, tr) {

		if($(this).find('.bank_name').val() == "" || $(this).find('.acc_holder').val() == "" || $(this).find('.acc_number').val() == "" || $(this).find('.ifsc_code').val() == "")

		{

			validate = false;

		}

	});

	return validate;

}

$('.add_bank').on('click',function()

{

	if(validateSupplierAccountDetails())

    {

    	create_new_empty_bank_kyc_detail();

	}

	else

	{

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill the required fields..'});

	}

});

//to add user

function add_user(firstname,lastname,user_name,password,code,address1,address2,address3,country,state,city,email,phone,mobile,company,status,file)

{

	my_Date = new Date();

    	var file=file;

    var data = new FormData();

	var form_data = new FormData();

    form_data.append('image',file);

    form_data.append('firstname',firstname);

	form_data.append('lastname',lastname);

    form_data.append('urname',user_name);

    form_data.append('psword',password);

	form_data.append('code_karigar',code);

    form_data.append('address1',address1);

    form_data.append('address2',address2);

	form_data.append('address3',address3);

    form_data.append('id_country',country);

    form_data.append('id_state',state);

    form_data.append('id_city',city);

    form_data.append('email', email);

    form_data.append('contactno1', phone);

    form_data.append('contactno2', mobile);

    form_data.append('company',company);

    form_data.append('status_karigar',status);

	$.ajax({

	    data:form_data,

		url: base_url+"index.php/admin_ret_catalog/karigar/add?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		dataType:'json',

        cache : false,

		enctype: 'multipart/form-data',

		contentType : false,

		processData : false,

		cache:false,

		success:function(data){

		msg='<div class ="alert alert-'+data.class+'"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>   '+data.title+'   </strong> '+data.message+' </div>';

		$('#chit_alert').html(msg);

	    set_karigar_table();

		}

	});

}

//update karigar

function update_user(firstname,lastname,user_name,password,code,address1,address2,address3,country,state,city,email,phone,mobile,company,status,file)

{

	my_Date = new Date();

    	var file=file;

    var data = new FormData();

	var form_data = new FormData();

    form_data.append('image',file);

    form_data.append('firstname',firstname);

	form_data.append('lastname',lastname);

    form_data.append('urname',user_name);

    form_data.append('psword',password);

	form_data.append('code_karigar',code);

    form_data.append('address1',address1);

    form_data.append('address2',address2);

	form_data.append('address3',address3);

    form_data.append('id_country',country);

    form_data.append('id_state',state);

    form_data.append('id_city',city);

    form_data.append('email', email);

    form_data.append('contactno1', phone);

    form_data.append('contactno2', mobile);

    form_data.append('company',company);

    form_data.append('status_karigar',status);

	$.ajax({

	    data:form_data,

		url: base_url+"index.php/admin_ret_catalog/karigar/update/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		dataType:'json',

        cache : false,

		enctype: 'multipart/form-data',

		contentType : false,

		processData : false,

		cache:false,

		success:function(data){

			//console.log(data);

		location.reload(true);

		}

	});

}

//get stone by id

function get_user(id)

{

   my_Date = new Date();

	$.ajax({

		type:"GET",

		url: base_url+"index.php/admin_ret_catalog/karigar/edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		cache:false,

		dataType:"JSON",

		success:function(data){

			//console.log(data);

			id=data.id_karigar;

			image = data.image;

			if(image!="" && image!=null)

			{

				var img=base_url+"assets/img/karigar/"+image;

				$("#ed_user_img_preview").attr('src',img);

			}

			else

			{

				var img=base_url+"assets/img/no_image.png";

				$("#ed_user_img_preview").attr('src',img);

			}

			var u_status= data.status_karigar;

			if(u_status==1)

			{

				$("#ed_user").bootstrapSwitch('state', true);

			}

			else

			{

			    $("#ed_user").bootstrapSwitch('state', false);

			}

			$('#ed_first_name').val(data.firstname);

			$('#ed_last_name').val(data.lastname);

			$('#ed_user_name').val(data.urname);

			$('#ed_password').val(data.psword);

			$('#ed_karigar_code').val(data.code_karigar);

			$('#ed_address1').val(data.address1);

			$('#ed_address2').val(data.address2);

			$('#ed_address3').val(data.address3);

			$('#countryval').val(data.id_country);

			$('#stateval').val(data.id_state);

			$('#cityval').val(data.id_city);

			$('#ed_email').val(data.email);

			$('#ed_phone').val(data.contactno1);

			$('#ed_mobile').val(data.contactno2);

			$('#ed_company').val(data.company);

			get_country();

		}

	});

}

function set_tag_table(from_date="",to_date="")

{

	my_Date = new Date();

	$.ajax({

			 url:base_url+ "index.php/admin_ret_catalog/tag?nocache=" + my_Date.getUTCSeconds(),

			 data:{'from_date':from_date,'to_date':to_date},

			 dataType:"JSON",

			 type:"POST",

			 cache:false,

			 success:function(data){

				var tag 	    = data.tag;

				var access		= data.access;

				$('#total_tag').text(tag.length);

				if(access.add == '0')

				{

					$('#save_tag').attr('disabled','disabled');

				}

			 var oTable = $('#tag_list').DataTable();

			 oTable.clear().draw();

			 if (tag!= null && tag.length > 0)

			 {

				oTable = $('#tag_list').dataTable({

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

						"aaData"  : tag,

						"aoColumns": [	{ "mDataProp": "tag_id" },

										{ "mDataProp": "tag_name" },

										{ "mDataProp": function ( row, type, val, meta ){

					                	 status_url =(access.edit =='1')? base_url+"index.php/admin_ret_catalog/tag/update_status/"+row.tag_id+"/"+(row.tag_status==1?0:1):'#';

							             return "<a href='"+status_url+"'><i class='fa "+(row.tag_status==1?'fa-check':'fa-remove')+"' style='color:"+(row.tag_status==1?'green':'red')+"'></i></a>"

						                }

							      		},

										{ "mDataProp": function ( row, type, val, meta ) {

											id= row.tag_id;

											edit_target=(access.edit=='0'?"":"#confirm-edit");

											delete_confirm=(access.delete=='1' ?'#confirm-delete':'');

											delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/tag/delete/'+id : '#' );

											// action_content='<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i> Edit</a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> Delete</a>'

											var action_content=''

														if(access.edit == '1'){

														

											        	action_content+=  '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i> Edit</a>' ;

														

													    }

														if(access.delete == '1'){

														 

														 action_content+=' <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> Delete</a>'

														}

											return action_content;

											}

										}]

					});

				}

		  },

		  error:function(error)

		  {

			 $("div.overlay").css("display", "none");

		  }

	});

}

function add_tag(tag_name,tag_status)

{

	my_Date = new Date();

    var tag_id=tag_id;

	$.ajax({

		url: base_url+"index.php/admin_ret_catalog/tag/add?nocache=" + my_Date.getUTCSeconds(),

		data:{"tag_name":tag_name,"tag_status":tag_status},

		type:"POST",

		dataType:"JSON",

		cache:false,

		success:function(data){

			msg='<div class ="alert alert-'+data.class+'"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>   '+data.title+'   </strong> '+data.message+' </div>';

			$('#chit_alert').html(msg);

			set_tag_table();

		}

	});

}

function update_tag(tag_name,tag_status)

{

	 $("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		data:{"tag_name":tag_name,"tag_status":tag_status},

		url: base_url+"index.php/admin_ret_catalog/tag/update/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		cache: false,

		success:function(){

				$("div.overlay").css("display", "none");

				window.location.reload(true);

		}

	});

}

function get_tag(id)

{

   my_Date = new Date();

	$.ajax({

		type:"GET",

		url: base_url+"index.php/admin_ret_catalog/tag/edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		cache:false,

		dataType:"JSON",

		success:function(data){

			id = data.tag_id;

			$('#ed_tagname').val(data.tag_name);

			var tag_status = data.tag_status;

			if(tag_status == 1)

			{

			$('#ed_tagstatus').bootstrapSwitch('state', true);

			}

			else

			{

			$('#ed_tagstatus').bootstrapSwitch('state', false);

			}

		   // console.log(wt);

		}

	});

}

function set_tax_table(from_date="",to_date="")

{

	my_Date = new Date();

	$.ajax({

			 url:base_url+ "index.php/admin_ret_catalog/tax?nocache=" + my_Date.getUTCSeconds(),

			 data:{'from_date':from_date,'to_date':to_date},

			 dataType:"JSON",

			 type:"POST",

			 cache:false,

			 success:function(data){

				var tax 	    = data.tax;

				var access		= data.access;

				$('#total_tax').text(tax.length);

				if(access.add == '0')

				{

					$('#save_tax').attr('disabled','disabled');

				}

			 var oTable = $('#tax_list').DataTable();

			 oTable.clear().draw();

			 if (tax!= null && tax.length > 0)

			 {

				oTable = $('#tax_list').dataTable({

						"bDestroy": true,

						"bInfo": true,

						"bFilter": true,

						"order": [[ 0, "desc" ]],

						"bSort": true,

						"dom": 'lBfrtip',

						"buttons" : ['excel','print'],

						"tableTools": { "buttons": [ { "sExtends": "xls",

						"oSelectorOpts": { page: 'current'

						} },

						{ "sExtends": "pdf",

						"oSelectorOpts": { page: 'current'

						} } ] },

						"aaData"  : tax,

						"aoColumns": [	{ "mDataProp": "tax_id" },

										{ "mDataProp": "tax_name" },

										{ "mDataProp": "tax_code" },

										{ "mDataProp": function ( row, type, val, meta ){

					                	 status_url = (access.edit =='1')?base_url+"index.php/admin_ret_catalog/tax/update_status/"+row.tax_id+"/"+(row.tax_status==1?0:1):'#';

							             return "<a href='"+status_url+"'><i class='fa "+(row.tax_status==1?'fa-check':'fa-remove')+"' style='color:"+(row.tax_status==1?'green':'red')+"'></i></a>"

						                }

							      		},

										{ "mDataProp": function ( row, type, val, meta ) {

											id= row.tax_id;

											edit_target=(access.edit=='0'?"":"#confirm-edit");

											delete_confirm=(access.delete=='1' ?'#confirm-delete':'');

											delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/tax/delete/'+id : '#' );

											// action_content='<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i> Edit</a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> Delete</a>'

											var action_content=''

														if(access.edit == '1'){

														

											        	action_content+=  '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i> Edit</a>' ;

														

													    }

														if(access.delete == '1'){

														 

														 action_content+=' <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> Delete</a>'

														}

											return action_content;

											}

										}]

					});

				}

		  },

		  error:function(error)

		  {

			 $("div.overlay").css("display", "none");

		  }

	});

}

function add_tax(tax_name,tax_code,tax_percentage,tax_status)

{

	my_Date = new Date();

    var tax_id=tax_id;

	$.ajax({

		data:{"tax_name":tax_name,"tax_code":tax_code,"tax_percentage":tax_percentage,"tax_status":tax_status},

		url: base_url+"index.php/admin_ret_catalog/tax/add?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		dataType:"JSON",

		cache:false,

		success:function(data){

			msg='<div class ="alert alert-'+data.class+'"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>   '+data.title+'   </strong> '+data.message+' </div>';

			$('#chit_alert').html(msg);

			set_tax_table();

		}

	});

}

//update tax

function update_tax(tax_name,tax_code,tax_percentage,tax_status)

{

	 $("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		data:{"tax_name":tax_name,"tax_code":tax_code,"tax_percentage":tax_percentage,"tax_status":tax_status},

		url: base_url+"index.php/admin_ret_catalog/tax/update/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		cache: false,

		success:function(){

				$("div.overlay").css("display", "none");

				window.location.reload(true);

		}

	});

}

function get_tax(id)

{

   my_Date = new Date();

	$.ajax({

		type:"GET",

		url: base_url+"index.php/admin_ret_catalog/tax/edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		cache:false,

		dataType:"JSON",

		success:function(data){

			id = data.tax_id;

			$('#ed_taxname').val(data.tax_name);

			$('#ed_tax_code').val(data.tax_code);

			$('#ed_tax_percentage').val(data.tax_percentage);

			var tax_status = data.tax_status;

			if(tax_status == 1)

			{

			$('#ed_taxstatus').bootstrapSwitch('state', true);

			}

			else

			{

			$('#ed_taxstatus').bootstrapSwitch('state', false);

			}

		}

	});

}

function get_ActiveCatByMetal(id){

	my_Date = new Date();

	$.ajax({

	type: 'GET',

	url: base_url+"index.php/admin_ret_catalog/get_category/"+id+"?nocache=" + my_Date.getUTCSeconds(),

	dataType:'json',

	success:function(data){

			var id =  $("#category_id").val();

			$("#category_sel option").remove();

			$.each(data, function (key, item) {

			    $("#category_sel").append(

			    $("<option></option>")

			    .attr("value",item.id_ret_category)

			    .attr("data-cat_type", item.cat_type)

			    .text(item.name)

			    );

			});

			$("#category_sel").select2("val",(id!='' && id>0?id:''));

		    $(".overlay").css("display", "none");

		}

	});

}

function get_ActiveMetal(){

	$.ajax({

	type: 'GET',

	url: base_url+'index.php/admin_ret_catalog/ret_product/active_metal',

	dataType:'json',

	success:function(data){

		var id =  $("#metal_id").val();

		$.each(data, function (key, item) {

		//    $("#metal_sel,#metal_category,#metal_category1").append(

			$("#metal_sel,#metal_category,#metal_category1,#metal_prodgrp,#metal_prodgrp1").append(

		   $("<option></option>")

		   .attr("value", item.id_metal)

		   .text(item.metal)

		   );

		});

		$("#metal_sel,#metal_category,#metal_category1,#metal_prodgrp,#metal_prodgrp1").select2(

		{

		placeholder:"Select metal",

		allowClear: true

		});

		   $("#metal_sel,#metal_category,#metal_category1,#metal_prodgrp,#metal_prodgrp1").select2("val",(id!='' && id>0?id:''));

		   $(".overlay").css("display", "none");

		}

	});

}

$("#metal_category,#metal_category1").on('change',function(e){

if(this.value!='')

{

$("#id_metal_category,#id_metal_cate").val(this.value);

}

else

{

$("#id_metal_category,#id_metal_cate").val('');

}

});

function get_taxgroup(){

	$.ajax({

	type: 'GET',

	url: base_url+'index.php/admin_ret_catalog/tgrp/active_tgrp',

	dataType:'json',

	success:function(data){

		var id =  $("#tax_id").val();

		var ed_tgrp_id =  $("#ed_tgrp_id").val();

		var pur_id = $('#pur_tax_id').val();

		$.each(data, function (key, item) {

		    $("#tgrp_sel,#ed_tgrp_sel,#tax_sel,#filtertax_sel,#up_tax_sel,#pur_tgrp_sel").append(

		    $("<option></option>")

		    .attr("value", item.tgrp_id)

		    .text(item.tgrp_name)

		    );

		});

		if($("#tax_sel").length > 0){

			$("#tax_sel").select2("val",(id!='' && id>0?id:''));

		}

		$("#tgrp_sel,#ed_tgrp_sel,#pur_tgrp_sel").select2(

    	{

    		placeholder:"Select Tax Group",

    		allowClear: true

    	});

    	if($("#pur_tgrp_sel").length > 0){

			$("#pur_tgrp_sel").select2("val",(pur_id!='' && pur_id>0?pur_id:''));

		}

		if($("#tgrp_sel").length > 0){

			$("#tgrp_sel").select2("val",(id!='' && id>0?id:''));

		}

		if($("#ed_tgrp_sel").length > 0){

			$("#ed_tgrp_sel").select2("val",(ed_tgrp_id!='' && ed_tgrp_id>0?ed_tgrp_id:''));

		}

		    $(".overlay").css("display", "none");

		}

	});

}

 function set_ret_product_table(from_date='',to_date='')

{

	my_Date = new Date();

	$.ajax({

			 url:base_url+ "index.php/admin_ret_catalog/ret_product?nocache=" + my_Date.getUTCSeconds(),

			 data: ( {'from_date':from_date,'to_date':to_date}),

			 dataType:"JSON",

			 type:"POST",

			 success:function(data){

		 	console.log(data);

				 var product 	= data.product;

				 var access		= data.access;

				 $('#total_count').text(product.length);

			 var oTable = $('#product_list').DataTable();

				 oTable.clear().draw();

						 if (product!= null && product.length > 0)

							{

							oTable = $('#product_list').dataTable({

								"bDestroy": true,

				                "bInfo": true,

				                "bFilter": true,

								"order": [[ 0, "desc" ]],

				                "scrollX":'100%',

				                "bSort": true,

				                "dom": 'lBfrtip',

           		                "buttons" : ['excel','print'],

						        "tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },

									"aaData": product,

									"aoColumns": [

									                { "mDataProp": function(row,type,val,meta){

                                                        id=row.pro_id;

                                                        return "<label class='checkbox-inline'><input type='checkbox' class='pro_id' name='pro_id[]' value='"+row.pro_id +"' /> "+id+" </label>";

                                                    } },

													{ "mDataProp": "product_name" },

													{ "mDataProp": "product_short_code" },

													{ "mDataProp": "hsn_code" },

													{ "mDataProp": "name" },

													{ "mDataProp": "section_name" },

													{ "mDataProp": "tgrp_name" },

													{ "mDataProp": "stock_type" },

													{ "mDataProp": "sales_based_on" },

													{ "mDataProp": "calculation_based_on" },

                                                    /*	{ "mDataProp": function ( row, type, val, meta ) {

                                                    id         = row.image;

                                                    action_content='<a href="#" class="btn-del"><img src='+id+' width="50px;" height="40px;"></a>';

                                                    return action_content;

                                                    }},*/

                                                    //	{ "mDataProp": "image" },

													{ "mDataProp": function ( row, type, val, meta ){

					                	    		status_url = (access.edit == '1') ? base_url+"index.php/admin_ret_catalog/ret_product/update_status/"+row.pro_id+"/"+(row.product_status==1?0:1):'#';

							                		return "<a href='"+status_url+"'><i class='fa "+(row.product_status==1?'fa-check':'fa-remove')+"'style='color:"+(row.product_status==1?'green':'red')+"'></i></a>"

						                				}

								              		},

													{ "mDataProp": function ( row, type, val, meta ) {

														 id= row.pro_id;

														 edit_target=(base_url+'index.php/admin_ret_catalog/ret_product/edit/'+id);

														 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/ret_product/delete/'+id : '#' );

														 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

														

														 var action_content=''

														if(access.edit == '1'){

														

											        	action_content+=  '<a href='+edit_target+' class="btn btn-primary btn-edit" id="edit" role="button" onclick="getproduct_edit('+id+')"><i class="fa fa-edit" ></i></a>' ;

														

													    }

														if(access.delete == '1'){

														 

														 action_content+='<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

														}

														// else{

														// 	action_content ='<a href='+edit_target+' class="btn btn-primary btn-edit" id="edit" role="button" onclick="getproduct_edit('+id+')"><i class="fa fa-edit" ></i></a>' 

														// 	'<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

														// }

														 return action_content;

														 }

													}]

								});

							}

					  },

					  error:function(error)

					  {

						 $("div.overlay").css("display", "none");

					  }

			      });

}

function set_ret_sub_product_table(from_date='',to_date='')

{

	my_Date = new Date();

	$.ajax({

			 url:base_url+ "index.php/admin_ret_catalog/ret_sub_product?nocache=" + my_Date.getUTCSeconds(),

			 data: ( {'from_date':from_date,'to_date':to_date}),

			 dataType:"JSON",

			 type:"POST",

			 success:function(data){

		 	 console.log(data);

				 var sub_product 	= data.sub_product;

				 var access		= data.access;

				 $('#total_count').text(sub_product.length);

			 var oTable = $('#subproduct_list').DataTable();

				 oTable.clear().draw();

						 if (sub_product!= null && sub_product.length > 0)

							{

							oTable = $('#subproduct_list').dataTable({

								"bDestroy": true,

				                "bInfo": true,

				                "bFilter": true,

								"order": [[ 0, "desc" ]],

				                "scrollX":'100%',

				                "bSort": true,

				                "dom": 'lBfrtip',

           		                "buttons" : ['excel','print'],

						        "tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },

									"aaData": sub_product,

									"aoColumns": [	{ "mDataProp": "sub_pro_id" },

													{ "mDataProp": "sub_pro_name" },

													{ "mDataProp": "sub_pro_code" },

													{ "mDataProp": function ( row, type, val, meta ){

					                	    		status_url = base_url+"index.php/admin_ret_catalog/ret_sub_product/update_status/"+row.sub_pro_id+"/"+(row.sub_pro_status==1?0:1);

							                		return "<a href='"+status_url+"'><i class='fa "+(row.sub_pro_status==1?'fa-check':'fa-remove')+"'style='color:"+(row.sub_pro_status==1?'green':'red')+"'></i></a>"

						                				}

								              		},

													{ "mDataProp": function ( row, type, val, meta ) {

														 id= row.sub_pro_id;

														 edit_target=(base_url+'index.php/admin_ret_catalog/ret_sub_product/edit/'+id);

														 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/ret_sub_product/delete/'+id : '#' );

														 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

														 action_content='<a href='+edit_target+' class="btn btn-primary btn-edit" id="edit" role="button" onclick="getproduct_edit('+id+')"><i class="fa fa-edit" ></i></a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

														 return action_content;

														 }

													}]

								});

							}

					  },

					  error:function(error)

					  {

						 $("div.overlay").css("display", "none");

					  }

			      });

}

$(document).on('change',".select_tax_det", function(){

		var selectId = this.value;

		var row = $(this).closest('tr');

		if(selectId != ""){

			$.each(tax_det,function(key, item){

				if(item.tax_id == selectId){

					row.find('td:eq(0) .select_tax_det').html(item.tax_name);

					$(row).find("td:eq(0) input[type='hidden']").val(item.tax_id);

				}

			});

		}

		else{

			row.find('td:eq(0) .select_tax_det').html("");

			$(row).find("td:eq(0) input[type='hidden']").val("");

		}

});

function get_activeTax(){

	$.ajax({

	 	type     : 'GET',

	 	url      : base_url + 'index.php/admin_ret_catalog/tax/active_tax',

	 	dataType : 'json',

	 	success  : function(data){

		 	tax_det = data;

			console.log("tax_det", tax_det);

		}

	});

}

$("#tax_grp").on('click',function(){

			var select_op = "";

			var a = $("#grp_increment").val();

			var i = ++a;

			$("#grp_increment").val(i);

			select_op+="<tr id='tgi"+i+"'><td>"+i+"</td></td>"+i+"<td><select class='form-control select_tax_det' name='tgi["+i+"][tax_id]' style='width:100%;' id='tax_name"+i+"' class='form-control'></select></td><td><select name='tgi["+i+"][tgi_calculation]' style='width:100%;' id='tgi_calculation"+i+"' class='form-control'><option value='1'>Base value</option><option value='2'>Arrived value</option></select></td>"+i+"<td><select name='tgi["+i+"][tgi_type]' style='width:100%;' id='tgi_type"+i+"' class='form-control'><option value='1'>+</option><option value='2'>-</option></select></td><td><button type='button' class='btn btn-danger' onclick='tgrp_remove("+i+")'><i class='fa fa-trash'></i></button></td></tr>";

			var selected_data = [];

			var op_length = 0;

			$('#tax_detail > tbody  > tr').each(function(index, tr) {

				selected_data.push({ "tax_id" : $(this).find('td:eq(1) .select_tax_det').val()});

			});

			if(selected_data.length != tax_det.length){

				$('#tax_detail').append(select_op);

				$.each(tax_det,function(key, item){

					var exist_flag = false;

					$.each(selected_data, function(stkey, stval){

						if(stval.tax_id == item.tax_id){

							exist_flag = true;

						}

					});

					if(!exist_flag){

						$('#tax_name'+i+'').append(

						$("<option></option>")

						.attr("value", item.tax_id)

						.text(item.tax_name)

						);

						op_length++;

					}

				});

			}else{

				alert("No more Tax available");

			}

			$("#tgi_type"+i+"").select2({

			   placeholder: "Select Value",

			   allowClear: true

			});

			$("#tgi_calculation"+i+"").select2({

			   placeholder: "Select calculate",

			   allowClear: true

			});

			$('#tax_name'+i+'').select2({

				placeholder: "Select Tax Name",

				allowClear: true

			});

 });

function tgrp_remove(i,id = ""){

		var	rowId= "tgi"+i;

		$('#'+rowId+'').remove();

		if(id){

			deletetgrp(id);

		}

}

function deletetgrp(id){

	my_Date = new Date();

		$("div.overlay").css("display", "block");

		$.ajax({

			 url:base_url+"index.php/admin_ret_catalog/tgrp/delete/"+id+"?nocache=" + my_Date.getUTCSeconds(),

			 type:"POST",

			 success:function(data){

					 $("div.overlay").css("display", "none");

				  },

				  error:function(error)

				  {

					alert('error');

					 $("div.overlay").css("display", "none");

				  }

			  });

}

   $("#tgrp_status").on('switchChange.bootstrapSwitch', function (event, state) {

		  var x = $(this).data('on-text');

		  var y = $(this).data('off-text');

			if($("#tgrp_status").is(':checked') && x=='YES')

			{

				$("#ad_tgrp_status").val(1);

			}

			else if(y == 'NO')

			{

				$("#ad_tgrp_status").val(0);

			}

	});

function set_tgrp_table(from_date="",to_date="")

{

	my_Date = new Date();

	$.ajax({

			 url:base_url+ "index.php/admin_ret_catalog/tgrp?nocache=" + my_Date.getUTCSeconds(),

			 data:{'from_date':from_date,'to_date':to_date},

			 dataType:"JSON",

			 type:"POST",

			 cache:false,

			 success:function(data){

				var tgrp 	    = data.tgrp;

				var access		= data.access;

				$('#total_tgrp').text(tgrp.length);

				if(access.add == '0')

				{

					$('#save_tgrp').attr('disabled','disabled');

				}

				else{

					$("#save_tgrp").on('click',function(){

						var tgrp_name =$('#tgrp_name').val();

						var tgrp_status =$('#adtgrp_status').val();

					if($("#tgrp_name").val() != ''){

						add_tgrp(tgrp_name,tgrp_status);

					}

					else if($("#tgrp_name").val() == ''){

						msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Tax Group Name .</div>';

						$("div.overlay").css("display", "none");

						$('#error-msg').html(msg);

					return false;

					}

					});

				}

			 var oTable = $('#tgrp_list').DataTable();

			 oTable.clear().draw();

			 if (tgrp!= null && tgrp.length > 0)

			 {

				oTable = $('#tgrp_list').dataTable({

						"bDestroy": true,

						"bInfo": true,

						"bFilter": true,

						"bSort": true,

						"dom": 'lBfrtip',

						"order": [[ 0, "desc" ]],

						"buttons" : ['excel','print'],

						"tableTools": { "buttons": [ { "sExtends": "xls",

						"oSelectorOpts": { page: 'current'

						} },

						{ "sExtends": "pdf",

						"oSelectorOpts": { page: 'current'

						} } ] },

						"aaData"  : tgrp,

						"aoColumns": [	{ "mDataProp": "tgrp_id" },

										{ "mDataProp": "tgrp_name" },

										{ "mDataProp": function ( row, type, val, meta ){

					                	 status_url = (access.edit =='1')?base_url+"index.php/admin_ret_catalog/tgrp/update_status/"+row.tgrp_id+"/"+(row.tgrp_status==1?0:1):'#';

							             return "<a href='"+status_url+"'><i class='fa "+(row.tgrp_status==1?'fa-check':'fa-remove')+"' style='color:"+(row.tgrp_status==1?'green':'red')+"'></i></a>"

						                }

							      		},

										{ "mDataProp": function ( row, type, val, meta ) {

											id= row.tgrp_id;

											edit_url=(base_url+'index.php/admin_ret_catalog/tgrp/edit/'+id);

											delete_confirm=(access.delete=='1' ?'#confirm-delete':'');

											delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/tgrp/delete/'+id : '#' );

											// action_content='<a href='+edit_url+' class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'><i class="fa fa-edit" ></i></a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

											var action_content=''

														if(access.edit == '1'){

														

											        	action_content+=  '<a href='+edit_url+' class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'><i class="fa fa-edit" ></i></a>' ;

														

													    }

														if(access.delete == '1'){

														 

														 action_content+='<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

														}

											return action_content;

											}

										}]

					});

				}

		  },

		  error:function(error)

		  {

			 $("div.overlay").css("display", "none");

		  }

	});

}

function set_metal_table(from_date="",to_date="")

{

	my_Date = new Date();

	$.ajax({

			 url:base_url+ "index.php/admin_ret_catalog/metal?nocache=" + my_Date.getUTCSeconds(),

			 data:{'from_date':from_date,'to_date':to_date},

			 dataType:"JSON",

			 type:"POST",

			 cache:false,

			 success:function(data){

				var metal 	= data.metal;

				var access	= data.access;

				$('#total_metal').text(metal.length);

				// if(access.add == '0')

				// {

				// 	$('#add_metal').attr('disabled','disabled');

				// }

			 var oTable = $('#metal_list').DataTable();

			 oTable.clear().draw();

			 if (metal!= null && metal.length > 0)

			 {

				oTable = $('#metal_list').dataTable({

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

						"aaData"  : metal,

						"aoColumns": [	{ "mDataProp": "id_metal" },

										{ "mDataProp": "metal" },

										{ "mDataProp": "metal_code" },

										{ "mDataProp": "tgrp_name" },

										{ "mDataProp": function ( row, type, val, meta ){

					                	 status_url = (access.edit == 1 ) ? base_url+"index.php/admin_ret_catalog/metal/update_status/"+row.id_metal+"/"+(row.metal_status==1?0:1):'#';

							             return "<a href='"+status_url+"'><i class='fa "+(row.metal_status==1?'fa-check':'fa-remove')+"' style='color:"+(row.metal_status==1?'green':'red')+"'></i></a>"

						                }

							      		},

										{ "mDataProp": function ( row, type, val, meta ) {

											id= row.id_metal;

											edit_target=(access.edit=='0'?"":"#confirm-edit");

											delete_confirm=(access.delete=='1' ?'#confirm-delete':'');

											delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/metal/delete/'+id : '#' );

											// action_content='<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i> </a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> </a>'

											var action_content='';

											if(access.edit == '1'){

														

												action_content+=  '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i> </a>' ;

												

												}

												if(access.delete == '1'){

												 

												 action_content+='<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> </a>'

												}

											return action_content;

											}

										}]

					});

			 }

		  },

		  error:function(error)

		  {

			 $("div.overlay").css("display", "none");

		  }

	});

}

function add_metal(metal,metal_code,metal_status,tgrp_id)

{

	my_Date = new Date();

	$.ajax({

		data:{"metal":metal,"metal_code":metal_code,"metal_status":metal_status,"tgrp_id":tgrp_id},

		url: base_url+"index.php/admin_ret_catalog/metal/add?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		dataType:"JSON",

		cache:false,

		success:function(data){

			msg='<div class ="alert alert-'+data.class+'"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>   '+data.title+'   </strong> '+data.message+' </div>';

			$('#chit_alert').html(msg);

			set_metal_table();

		}

	});

}

function update_metal(metal,metal_code,metal_status,tgrp_id)

{

	 $("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		data:{"metal":metal,"metal_code":metal_code,"metal_status":metal_status,"tgrp_id":tgrp_id},

		url: base_url+"index.php/admin_ret_catalog/metal/update/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		cache: false,

		success:function(data){

				$("div.overlay").css("display", "none");

				window.location.reload(true);

		}

	});

}

function get_metals(id)

{

   my_Date = new Date();

	$.ajax({

		type:"GET",

		url: base_url+"index.php/admin_ret_catalog/metal/edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		cache:false,

		dataType:"JSON",

		success:function(data){

			//console.log(data);

			id = data.id_metal;

			$('#ed_tgrp_id').val(data.tgrp_id);

		    $("#ed_tgrp_sel").select2("val",(data.tgrp_id!='' && data.tgrp_id>0?data.tgrp_id:''));

			$('#ed_metal_name').val(data.metal);

			$('#ed_metal_code').val(data.metal_code);

			var metal_status = data.metal_status;

			if(metal_status == 1)

			{

			$('#ed_metalstatus').bootstrapSwitch('state', true);

			}

			else

			{

			$('#ed_metalstatus').bootstrapSwitch('state', false);

			}

		}

	});

}

function get_purities()

{

		$.ajax({

						type: 'GET',

						url: base_url+'index.php/admin_ret_catalog/purity/active_purities',

						dataType:'json',

						success:function(data){

							console.log(data);

						  var id =  $("#pur_id,#ed_pur_id").val();

						   $.each(data,function (key, item) {

							   		$("#purity_sel,#ed_purity_sel").append(

										$("<option></option>")

										  .attr("value", item.id_purity)

										  .text(item.purity)

									);

							});

							$("#purity_sel,#ed_purity_sel").select2({

								placeholder:"Select Purity",

								allowClear:true

							});

							 $("#purity_sel").select2("val",'');

							 $("#purity_sel,#ed_purity_sel").select2("val",(id!='' && id>0?id:''));

							 $(".overlay").css("display", "none");

						}

					});

}

function get_ActiveRetMasters()

{

    $.ajax({

	type: 'GET',

	url: base_url+'index.php/admin_ret_catalog/ret_design/ajax_get_retmaster',

	dataType:'json',

	success:function(data){

		ret_master_data =  data;

		var id = $('#cat_id').val();

		$("#des_prod_name").select2(

		{

			placeholder:"Select Product",

			allowClear: true

		});

		$("#des_prod_name").select2("val",'');

		$("#des_des_name").select2(

		{

			placeholder:"Select Design",

			allowClear: true

		});

		$("#des_des_name").select2("val",'');

		    $(".overlay").css("display", "none");

		    $.each(ret_master_data, function (key, item) {

		    $("#des_cat_name").append(

		    $("<option></option>")

		    .attr("value", item.id_ret_category)

		    .text(item.name)

		    );

		});

		$("#des_cat_name").select2(

		{

			placeholder:"Select Category",

			allowClear: true

		});

		//$("#des_cat_name").select2("val",'');

		$("#des_cat_name").select2("val",(id!='' && id>0?id:''));

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

		var id =  $("#select_category").val();

		cat_details = data;

        if(ctrl_page[1]=="karigar")

		{

			$("#wast_cat").append(

				$("<option></option>")

				.attr("value",0)

		        .text('All')

			);

		}

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

$('#select_category').on('change',function(){

   if(this.value!='')

   {

       $("#select_product option").remove();

       $("#design_list > tbody > tr").remove();

       get_Activeproduct(this.value);

   }

});

function get_Activeproduct(id_category='')

{

     $(".overlay").css("display", "block");

     $('#ed_prod_select option').remove();

     $('#prod_select option').remove();

     my_Date = new Date();

	$.ajax({

	type: 'POST',

	url: base_url+"index.php/admin_ret_catalog/ret_product/active_list/?nocache=" + my_Date.getUTCSeconds(),

	data :{'id_category':id_category},

	dataType:'json',

	success:function(data){

	    prod_details = data;

	     $("#id_product").val('');

		var id              =  $("#id_product").val();

		var ed_product      =  $("#ed_product").val();

		var product         =  $("#product").val();

		var size_product    =  $("#size_product").val();

		var pro_id          =  $("#pro_id").val();

		$.each(data, function (key, item) {

		    $("#product_sel,#prod_filter,#filterproduct_sel,#select_product,#sel_prod,#prod_select,#ed_prod_select,#ret_product,#ed_size_product,#multi_prod_sel,#design_product,#des_prod_name").append(

		    $("<option></option>")

		    .attr("value", item.pro_id)

		    .text(item.product_name)

		    );

		});

		$("#multi_prod_sel,#select_product,#design_product,#prod_filter,#des_prod_name").select2(

		{

			placeholder:"Select Product",

			allowClear: true

		});

	    if($('#multi_prod_sel').length)

		{

			   var ar = $('#product').data('product');

               $("#multi_prod_sel").select2('val',ar);

		}

		if($('#design_product').length)

		{

		    $("#design_product").select2("val",'');

		}

		if($('#des_prod_name').length)

		{

		    //$("#des_prod_name").select2("val",'');

		    $("#des_prod_name").select2("val",(pro_id!='' && pro_id>0?pro_id:''));

		}

		$("#product_sel,#filterproduct_sel,#select_product,#sel_prod,#prod_select,#ed_prod_select,#ret_product,#ed_size_product,#design_product,#prod_filter").select2(

		{

			placeholder:"Select Product",

			allowClear: true

		});

		if($('#product_sel').length || $('#filterproduct_sel').length || $('#select_product').length || $('#sel_prod').length || $('#ret_product').length || $('#prod_select').length)

		{

		    $("#product_sel,#filterproduct_sel,#select_product,#sel_prod,#ret_product,#prod_select").select2("val",(id!='' && id>0?id:''));

		}

		    if(ed_product!=undefined && ed_product!='')

		    {

		        $("#ed_prod_select").select2("val",(ed_product!='' && ed_product>0?ed_product:''));

		    }else if(product!=undefined && product!='')

		   	{

		   			$("#product_sel").select2("val",(product!='' && product>0?product:''));

		   	}else if(size_product!=undefined && size_product!='')

		   	{

		   			$("#ed_size_product").select2("val",(size_product!='' && size_product>0?size_product:''));

		   	}

		   	if($('#select_product').length)

		   	{

		   	    $("#select_product").select2("val",'');

		   	}

		   	if($('#prod_filter').length)

		   	{

		   	    $("#prod_filter").select2("val",'');

		   	}

		    $(".overlay").css("display", "none");

		}

	});

}

function get_ActiveTheme()

{

	$.ajax({

	type: 'GET',

	url: base_url+'index.php/admin_ret_catalog/theme/active_theme',

	dataType:'json',

	success:function(data){

		var id =  $("#theme_id").val();

		$.each(data, function (key, item) {

		    $("#theme_sel").append(

		    $("<option></option>")

		    .attr("value", item.id_theme)

		    .text(item.theme_name)

		    );

		});

		$("#theme_sel").select2(

		{

			placeholder:"Select Theme",

			allowClear: true

		});

		    $("#theme_sel").select2("val",(id!='' && id>0?id:''));

		    $(".overlay").css("display", "none");

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

		var id =  $("#karigar").val();

		$.each(data, function (key, item) {

		    $("#karigar_sel").append(

		    $("<option></option>")

		    .attr("value", item.id_karigar)

		    .text(item.karigar)

		    );

		});

		$("#karigar_sel").select2(

		{

			placeholder:"Select Karigar",

			allowClear:true,

			//closeOnSelect: false

		});

		if($('#karigar').data('karigar'))

		{

			   var ar = $('#karigar').data('karigar');

               $("#karigar_sel").select2('val',ar);

		}

		else

		{

			  $("#karigar_sel").select2('val','');

		}

		}

	});

}

function get_ActivePurity()

{

	$.ajax({

	type: 'GET',

	url: base_url+'index.php/admin_ret_catalog/purity/active_purities',

	dataType:'json',

	success:function(data){

		var id =  $("#pur_id,#ed_pur_id").val();

		$.each(data, function (key, item) {

		    $("#purity_sel,#ed_purity_sel").append(

		    $("<option></option>")

		    .attr("value", item.id_purity)

		    .text(item.purity)

		    );

		});

		$("#purity_sel,#ed_purity_sel").select2(

		{

			placeholder:"Select Purity",

			allowClear: true

		});

		if($('#pur_id,#ed_pur_id').data('id_purity'))

		{

			   var ar = $('#pur_id,#ed_pur_id').data('id_purity');

			   console.log(ar);

               $("#purity_sel,#ed_purity_sel").select2('val',ar);

		}

		else

		{

			  $("#purity_sel,#ed_purity_sel").select2('val','');

		}

		}

	});

}

function get_ActiveMaterial()

{

	$.ajax({

	type: 'GET',

	url: base_url+'index.php/admin_ret_catalog/material/active_material',

	dataType:'json',

	success:function(data){

		var id =  $("#material_id").val();

		$.each(data, function (key, item) {

		    $("#material_sel,#material_filter").append(

		    $("<option></option>")

		    .attr("value", item.material_id)

		    .text(item.material_name)

		    );

		});

		$("#material_sel,#material_filter").select2(

		{

			placeholder:"Select Material",

			allowClear: true

		});

		if($('#material_id').data('material'))

		{

			   var ar = $('#material_id').data('material');

               $("#material_sel,#material_filter").select2('val',ar);

		}

		else

		{

		    if($("#material_sel").length || $("#material_filter").length)

		    {

		        $("#material_sel,#material_filter").select2('val','');

		    }

		}

		}

	});

}

function get_hooktype()

{

		$.ajax({

						type: 'GET',

						url: base_url+'index.php/admin_ret_catalog/hook/active_hook',

						dataType:'json',

						success:function(data){

							console.log(data);

						  var id =  $('#hook_id').val();

						   $.each(data,function (key, item) {

							   		$('#hook_sel').append(

										$("<option></option>")

										  .attr("value", item.hook_id)

										  .text(item.hook_name)

									);

							});

							 $('#hook_sel').select2("val",(id!='' && id>0?id:''));

							 $(".overlay").css("display", "none");

						}

					});

}

function get_ScrewType()

{

	$.ajax({

	type: 'GET',

	url: base_url+'index.php/admin_ret_catalog/screw/active_screw',

	dataType:'json',

	success:function(data){

		var id =  $("#screw_id").val();

		$.each(data, function (key, item) {

		    $("#screw_sel").append(

		    $("<option></option>")

		    .attr("value", item.screw_id)

		    .text(item.screw_name)

		    );

		});

		$("#screw_sel").select2(

		{

			placeholder:"Select Screw",

			allowClear: true

		});

		    $("#screw_sel").select2("val",(id!='' && id>0?id:''));

		    $(".overlay").css("display", "none");

		}

	});

}

function get_ActiveUOM()

{

	$.ajax({

	type: 'GET',

	url: base_url+'index.php/admin_ret_catalog/uom/active_uom',

	dataType:'json',

	success:function(data){

		var id =  $("#size").val();

		$.each(data, function (key, item) {

		    $("#size_sel").append(

		    $("<option></option>")

		    .attr("value", item.uom_id)

		    .text(item.uom_name)

		    );

		});

		$("#size_sel").select2(

		{

			placeholder:"Select Size",

			allowClear: true

		});

		if($("#size_sel").length)

		{

		    $("#size_sel").select2("val",(id!='' && id>0?id:''));

		}

		    $(".overlay").css("display", "none");

		}

	});

}

function s_remove(i,id = ""){

	var	rowId= "stone"+i;

	$('#'+rowId+'').remove();

	if(id){

		deleteProdDetail(id);

	}

}

function size_remove(i,id = ""){

	var	rowId= "size"+i;

	$('#'+rowId+'').remove();

	if(id){

		deletesize(id);

	}

}

function deleteProdDetail(id){

	my_Date = new Date();

	$("div.overlay").css("display", "block");

	$.ajax({

		 url:base_url+"index.php/admin_ret_catalog/stone/delete/"+id+"?nocache=" + my_Date.getUTCSeconds(),

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

	var id =  $('#editid_design').val();

	$("div.overlay").css("display", "block");

	$.ajax({

	   url:base_url+"index.php/admin_ret_catalog/removeDesign_img/"+file+"/"+id,

	   type : "POST",

	   success : function(result) {

		console.log(result);

	   	$("div.overlay").css("display", "none");

		window.location.reload();

	   },

	   error : function(error){

		$("div.overlay").css("display", "none");

	   }

	});

}

function set_design_table(from_date='',to_date='',id_product='')

{

    $("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

			 url:base_url+ "index.php/admin_ret_catalog/ret_design?nocache=" + my_Date.getUTCSeconds(),

			 data: ( {'from_date':from_date,'to_date':to_date,'id_product':$('#select_product').val()}),

			 dataType:"JSON",

			 type:"POST",

			 success:function(data){

		 	 console.log(data);

				 var design 	= data.design;

				 var access		= data.access;

				 $('#total_count').text(design.length);

			 var oTable = $('#design_list').DataTable();

				 oTable.clear().draw();

						 if (design!= null && design.length > 0)

							{

							oTable = $('#design_list').dataTable({

								"bDestroy": true,

				                "bInfo": true,

				                "bFilter": true,

				                "scrollX":'100%',

								"order": [[ 0, "desc" ]],

				                "bSort": true,

				                "dom": 'lBfrtip',

           		                "buttons" : ['excel','print'],

						        "tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },

									"aaData": design,

									"aoColumns": [

									                { "mDataProp": "design_no" },

													{ "mDataProp": "design_name" },

													{ "mDataProp": "design_code" },

													{ "mDataProp": function ( row, type, val, meta ){

					                	    		status_url = (access.edit ==1 ) ? base_url+"index.php/admin_ret_catalog/ret_design/update_status/"+row.design_no+"/"+(row.design_status==1?0:1):'#';

							                		return "<a href='"+status_url+"'><i class='fa "+(row.design_status==1?'fa-check':'fa-remove')+"'style='color:"+(row.design_status==1?'green':'red')+"'></i></a>"

						                				}

								              		},

													{ "mDataProp": function ( row, type, val, meta ) {

														 id= row.design_no;

														 edit_target=(base_url+'index.php/admin_ret_catalog/ret_design/edit/'+id);

														 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/ret_design/delete/'+id : '#' );

														 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

														 

														 var action_content=''

														 //action_content='<a href='+edit_target+' class="btn btn-primary btn-edit" id="edit" role="button" onclick="getproduct_edit('+id+')"><i class="fa fa-edit" ></i></a> <a href="#" class="btn btn-danger btn-del" onclick="delete_design('+id+')"  ><i class="fa fa-trash"></i></a>'

														 if(access.edit == '1'){

														

															action_content+=  '<a href='+edit_target+' class="btn btn-primary btn-edit" id="edit" role="button" onclick="getproduct_edit('+id+')"><i class="fa fa-edit" ></i></a>' ;

															

															}

															if(access.delete == '1'){

															 

															 action_content+='<a href="#" class="btn btn-danger btn-del" onclick="delete_design('+id+')" ><i class="fa fa-trash"></i></a>'

															}

														 return action_content;

														 }

													}]

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

function delete_design(design_no)

{

    $('#confirm-delete').modal('show');

    $('#design_no').val(design_no);

}

$('#remove_design').on('click',function(){

    my_Date = new Date();

	$.ajax({

		type: 'POST',

		url:base_url+ "index.php/admin_ret_catalog/ret_design/delete?nocache=" + my_Date.getUTCSeconds(),

		dataType:'json',

		data:{'design_no':$('#design_no').val()},

		success:function(data){

			alert(data.message);

			$('#design_no').val('');

			$('#confirm-delete').modal('toggle');

			set_design_table();

		}

	});

});

function validate_design_Image()

{

	var height=($(this).height());

	var width=($(this).width());

	arguments[0].id == 'design_img';

	var preview = $('#design_img_preview');

	if(arguments[0].files[0].size > 1048578)

	{

		alert('File size cannot be greater than 1 MB');

		arguments[0].value = "";

		preview.css('display','none');

	}

	else

		{

		var fileName =arguments[0].value;

		var ext = fileName.substring(fileName.lastIndexOf('.') + 1);

		ext = ext.toLowerCase();

		if(ext != "jpg" && ext != "png" && ext != "jpeg" && ext != "svg")

		{

			alert("Upload JPG or PNG Images only");

			arguments[0].value = "";

			preview.css('display','none');

		}

		else

		{

			var file    = arguments[0].files[0];

			var reader  = new FileReader();

			  reader.onloadend = function () {

				preview.prop('src',reader.result);

			  }

			  if (file)

			  {

				reader.readAsDataURL(file);

				preview.css('display','');

			  }

			 else

			  {

				preview.prop('src','');

				preview.css('display','none');

			  }

		}

	}

}

function set_screw_table(from_date="",to_date="")

{

	my_Date = new Date();

	$.ajax({

			 url:base_url+ "index.php/admin_ret_catalog/screw?nocache=" + my_Date.getUTCSeconds(),

			 data:{'from_date':from_date,'to_date':to_date},

			 dataType:"JSON",

			 type:"POST",

			 cache:false,

			 success:function(data){

				var screw 	= data.screw;

				var access	= data.access;

				$('#total_screw').text(screw.length);

				if(access.add == '0')

				{

					$('#add_screw').attr('disabled','disabled');

				}

			 var oTable = $('#screw_list').DataTable();

			 oTable.clear().draw();

			 if (screw!= null && screw.length > 0)

			 {

				oTable = $('#screw_list').dataTable({

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

						"aaData"  : screw,

						"aoColumns": [	{ "mDataProp": "screw_id" },

										{ "mDataProp": "screw_name" },

										{ "mDataProp": "screw_short_code" },

										{ "mDataProp": function ( row, type, val, meta ){

					                	 status_url =(access.edit =='1')? base_url+"index.php/admin_ret_catalog/screw/update_status/"+row.screw_id+"/"+(row.screw_status==1?0:1):'#';

							             return "<a href='"+status_url+"'><i class='fa "+(row.screw_status==1?'fa-check':'fa-remove')+"' style='color:"+(row.screw_status==1?'green':'red')+"'></i></a>"

						                }

							      		},

										{ "mDataProp": function ( row, type, val, meta ) {

											id= row.screw_id;

											edit_target=(access.edit=='0'?"":"#confirm-edit");

											delete_confirm=(access.delete=='1' ?'#confirm-delete':'');

											delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/screw/delete/'+id : '#' );

											// action_content='<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

											var action_content=''

											if(access.edit == '1'){

											

											action_content+=  '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a>' ;

											

											}

											if(access.delete == '1'){

												

												action_content+=' <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

											}

											return action_content;

											}

										}]

					});

				}

		  },

		  error:function(error)

		  {

			 $("div.overlay").css("display", "none");

		  }

	});

}

function add_screw(screw_name,screw_short_code,screw_status)

{

	my_Date = new Date();

	$.ajax({

		data:{"screw_name":screw_name,"screw_short_code":screw_short_code,"screw_status":screw_status},

		url: base_url+"index.php/admin_ret_catalog/screw/add?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		dataType:"JSON",

		cache:false,

		success:function(data){

			$('#screw_id').val('');

			msg='<div class ="alert alert-'+data.class+'"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>   '+data.title+'   </strong> '+data.message+' </div>';

			$('#chit_alert').html(msg);

			set_screw_table();

		}

	});

}

function update_screw(screw_name,screw_short_code,screw_status)

{

	 $("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		data:{"screw_name":screw_name,"screw_short_code":screw_short_code,"screw_status":screw_status},

		url: base_url+"index.php/admin_ret_catalog/screw/update/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		cache: false,

		success:function(){

				$("div.overlay").css("display", "none");

				window.location.reload(true);

		}

	});

}

function get_screw(id)

{

   my_Date = new Date();

	$.ajax({

		type:"GET",

		url: base_url+"index.php/admin_ret_catalog/screw/edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		cache:false,

		dataType:"JSON",

		success:function(data){

			id = data.screw_id;

			$('#ed_screw_name').val(data.screw_name);

			$('#ed_screw_code').val(data.screw_short_code);

			var screw_status = data.screw_status;

			if(screw_status == 1)

			{

			$('#ed_screw_status').bootstrapSwitch('state', true);

			}

			else

			{

			$('#ed_screw_status').bootstrapSwitch('state', false);

			}

		}

	});

}

function set_hook_table(from_date="",to_date="")

{

	my_Date = new Date();

	$.ajax({

			 url:base_url+ "index.php/admin_ret_catalog/hook?nocache=" + my_Date.getUTCSeconds(),

			 data:{'from_date':from_date,'to_date':to_date},

			 dataType:"JSON",

			 type:"POST",

			 cache:false,

			 success:function(data){

				var hook 	= data.hook;

				var access	= data.access;

				$('#total_hook').text(hook.length);

				if(access.add == '0')

				{

					$('#add_hook').attr('disabled','disabled');

				}

			 var oTable = $('#hook_list').DataTable();

			 oTable.clear().draw();

			 if (hook != null && hook.length > 0)

			 {

				oTable = $('#hook_list').dataTable({

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

						"aaData"  : hook,

						"aoColumns": [	{ "mDataProp": "hook_id" },

										{ "mDataProp": "hook_name" },

										{ "mDataProp": "hook_short_code" },

										{ "mDataProp": function ( row, type, val, meta ){

					                	 status_url =(access.edit =='1')? base_url+"index.php/admin_ret_catalog/hook/update_status/"+row.hook_id+"/"+(row.hook_status==1?0:1):'#';

							             return "<a href='"+status_url+"'><i class='fa "+(row.hook_status==1?'fa-check':'fa-remove')+"' style='color:"+(row.hook_status==1?'green':'red')+"'></i></a>"

						                }

							      		},

										{ "mDataProp": function ( row, type, val, meta ) {

											id= row.hook_id;

											edit_target=(access.edit=='0'?"":"#confirm-edit");

											delete_confirm=(access.delete=='1' ?'#confirm-delete':'');

											delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/hook/delete/'+id : '#' );

											// action_content='<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

											var action_content=''

											if(access.edit == '1'){

											

											action_content+=  '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a>' ;

											

											}

											if(access.delete == '1'){

											 

											 action_content+='<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

											}

											return action_content;

											}

										}]

					});

				}

		  },

		  error:function(error)

		  {

			 $("div.overlay").css("display", "none");

		  }

	});

}

function add_hook(hook_name,hook_short_code,hook_status)

{

	my_Date = new Date();

	$.ajax({

		data:{"hook_name":hook_name,"hook_short_code":hook_short_code,"hook_status":hook_status},

		url: base_url+"index.php/admin_ret_catalog/hook/add?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		dataType:"JSON",

		cache:false,

		success:function(data){

			$('#hook_id').val('');

			msg='<div class ="alert alert-'+data.class+'"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>   '+data.title+'   </strong> '+data.message+' </div>';

			$('#chit_alert').html(msg);

			set_hook_table();

		}

	});

}

function update_hook(hook_name,hook_short_code,hook_status)

{

	 $("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		data:{"hook_name":hook_name,"hook_short_code":hook_short_code,"hook_status":hook_status},

		url: base_url+"index.php/admin_ret_catalog/hook/update/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		cache: false,

		success:function(){

				$("div.overlay").css("display", "none");

				window.location.reload(true);

		}

	});

}

function get_hook(id)

{

   my_Date = new Date();

	$.ajax({

		type:"GET",

		url: base_url+"index.php/admin_ret_catalog/hook/edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		cache:false,

		dataType:"JSON",

		success:function(data){

			id = data.hook_id;

			$('#ed_hook_name').val(data.hook_name);

			$('#ed_hook_code').val(data.hook_short_code);

			var hook_status = data.hook_status;

			if(hook_status == 1)

			{

			$('#ed_hook_status').bootstrapSwitch('state', true);

			}

			else

			{

			$('#ed_hook_status').bootstrapSwitch('state', false);

			}

		}

	});

}

function get_financial_year_list()

{

	my_Date = new Date();

	$("div.overlay").css("display", "block");

	$.ajax({

		 url:base_url+"index.php/admin_ret_catalog/financial_year/ajax?nocache=" + my_Date.getUTCSeconds(),

		 dataType:"JSON",

		 type:"POST",

		 success:function(data){

   			set_financial_year_list(data);

   			 $("div.overlay").css("display", "none");

		  },

		  error:function(error)

		  {

			 $("div.overlay").css("display", "none");

		  }

	});

}

function set_financial_year_list(data)

{

   $("div.overlay").css("display", "none");

   var tagging = data.list;

   var access = data.access;

   var oTable = $('#financial_list').DataTable();

   $("#total_tagging").text(tagging.length);

    if(access.add == '0')

	 {

		$('#add_financialYear').attr('disabled','disabled');

	 }

	 oTable.clear().draw();

   	 if (tagging!= null && tagging.length > 0)

	 {

	 	oTable = $('#financial_list').dataTable({

			"bDestroy": true,

			"bInfo": true,

			"bFilter": true,

			"bSort": true,

			"order": [[ 0, "desc" ]],

			"dom": 'lBfrtip',

			"buttons" : ['excel','print'],

			"tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },

			"aaData": tagging,

			"aoColumns": [{ "mDataProp":"fin_id" },

						{ "mDataProp": "fin_code" },

						{ "mDataProp": "fin_year_code" },

						{ "mDataProp": "fin_year" },

						{ "mDataProp": function ( row, type, val, meta ){

						active_url =(access.edit =='1')?base_url+"index.php/admin_ret_catalog/financial_status/"+(row.fin_status==1?0:1)+"/"+row.fin_id:'#';

						return "<a href='"+active_url+"'><i class='fa "+(row.fin_status==1?'fa-check':'fa-remove')+"' style='color:"+(row.fin_status==1?'green':'red')+"'></i></a>"

						}

						},

						{ "mDataProp": "created_on" },

						{ "mDataProp": function ( row, type, val, meta ) {

							 id= row.fin_id

							 edit_url=(access.edit=='1' ? base_url+'index.php/admin_ret_catalog/financial_year/edit/'+id : '#' );

							 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/financial_year/delete/'+id : '#' );

							 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

							//  action_content='<a href="'+edit_url+'" class="btn btn-primary btn-edit"><i class="fa fa-edit" ></i></a><a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>';

							var action_content=''

							if(access.edit == '1'){

							

							action_content+=  '<a href="'+edit_url+'" class="btn btn-primary btn-edit"><i class="fa fa-edit" ></i></a>' ;

							

							}

							if(access.delete == '1'){

							 

							 action_content+='<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

							}

							return action_content;

						}

					}]

		});

	}

}

function set_bulkprodupdated_table(from_date="",to_date="")

{

	pro_id = $("#filterproduct_sel").val();

	tax_group_id = $("#filtertax_sel").val();

	product_status = $("#filterprod_status").val();

	from_date = from_date;

	to_date = to_date;

	my_Date = new Date();

	$.ajax({

		url:base_url+ "index.php/admin_ret_catalog/bulkprodupdated?nocache=" + my_Date.getUTCSeconds(),

		data: ({'pro_id':pro_id,'tax_group_id':tax_group_id,'product_status':product_status,'from_date':from_date,'to_date':to_date}),

		dataType:"JSON",

		type:"POST",

		success:function(data){

			var product = data.product;

			var access = data.access;

			$('#total_prod').text(product.length);

			var oTable = $('#txprod_list').DataTable();

			oTable.clear().draw();

			if (product!= null && product.length > 0)

			{

				oTable = $('#txprod_list').dataTable({

						"bDestroy": true,

						"bInfo": true,

						"bFilter": true,

						"scrollX":'100%',

						"bSort": true,

						"dom": 'lBfrtip',

						"buttons" : ['excel','print'],

						"tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },

						"aaData": product,

						"aoColumns": [

							{ "mDataProp":function ( row, type, val, meta ){

								return '<input type="checkbox" name="product_id[]" id="product_id" class="product_id"  value="'+row.pro_id+'">';

							}},

							{ "mDataProp": "product_name" },

							{ "mDataProp": "product_short_code" },

							{ "mDataProp": "tgrp_name" },

							{ "mDataProp": function ( row, type, val, meta ){

								status = '';

								if(row.product_status == 0){

									status = 'Inactive';

								}else if(row.product_status == 1 || row.product_status == 0){

									status = 'Active';

								}

								return status;

							}},

						]

				});

			}

		 }

    });

}

function update_product(product_ids="")

{

	my_Date = new Date();

	 $("div.overlay").css("display", "block");

	$.ajax({

			  url:base_url+ "index.php/admin_ret_catalog/bulkprodupdated/update?nocache=" + my_Date.getUTCSeconds(),

			 data:  {'status':$("#up_prod_status").val(),'tax_grp':$("#up_tax_sel").val(),'product_ids':product_ids},

			 type:"POST",

			 async:false,

			 	 success:function(data){

						 $("div.overlay").css("display", "none");

						 $("#select_prodata").val("");

						 set_bulkprodupdated_table();

					  },

					  error:function(error)

					  {

						 $("div.overlay").css("display", "none");

					  }

		  });

}

// KarthikB

//Old Metal Rate list

/*$('#metal_select').on('change',function(){

	if(this.value!='')

	{

		$('#id_metal').val(this.value);

		var id_branch=$('#id_branch').val();

		get_old_metal_rate(id_branch,this.value);

		$('#add_block').css('display','block');

	}

	else

	{

		$('#id_metal').val('');

		$('#add_block').css('display','none');

	}

});*/

$('#branch_select').on('change',function(){

	if(this.value!='')

	{

		$('#id_branch').val(this.value);

		var id_metal=$('#id_metal').val();

		get_old_metal_rate(this.value,id_metal);

		if(ctrl_page[1]=='web_devices')

		{

		    get_ActiveBranchFloor();

		}

	}

	else

	{

		$('#id_branch').val('');

	}

	if(this.value==0)

	{

		$('#submit').prop('disabled',true);

	}

	else

	{

		$('#submit').prop('disabled',false);

	}

});

function set_metal_rate(data)

{

	var id_metal =  $('#id_metal').val();

	$.each(data,function(key,item){

		$('#metal_select').append(

		$("<option></option>")

		.attr("value",item.id_metal)

		.text(item.metal)

		);

		});

	$("#metal_select").select2({

	placeholder: "Select Metal Type",

	allowClear: true

	});

	$("#metal_select").select2("val",(id_metal!='' && id_metal>0?id_metal:''));

}

function get_purity_list()

{

   var my_Date = new Date();

	$.ajax({

		 url:base_url+"index.php/admin_ret_catalog/get_all_purity?nocache=" + my_Date.getUTCSeconds(),

		 dataType:"JSON",

		 type:"post",

		 success:function(data){

		 	var id_purity='';

				$.each(data,function(key,item){

				$('.purity').append(

				$("<option></option>")

				.attr("value",item.id_purity)

				.text(item.purity)

				);

				});

				$(".purity").select2({

				placeholder: "Purity",

				allowClear: true

				});

				$(".purity").select2("val",(id_purity!='' && id_purity>0?id_purity:''));

   			 $("div.overlay").css("display", "none");

		  },

		  error:function(error)

		  {

			 $("div.overlay").css("display", "none");

		  }

	});

}

function set_old_metal_rate_list(data)

{

	  var oTable = $('#oldmetal_rate_list').DataTable();

	  		oTable = $('#oldmetal_rate_list').dataTable({

			"bDestroy": true,

			"bInfo": true,

			"bFilter": true,

			"bSort": true,

			"dom": 'lBfrtip',

			 "pageLength": 100,

			"order": [[ 0, "desc" ]],

			"buttons" : ['excel','print'],

			"tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },

			"aaData": data,

			"tabIndex": 2,

			"aoColumns": [

						 {"mDataProp": function ( row, type, val, meta )

                        {

                        chekbox='<input type="checkbox" class="id_old_metal_rate" name="id_old_metal_rate[]" value="'+(row.id_old_metal_rate ==''? row.id_old_metal_rate:row.id_old_metal_rate)+'"/> '

                         return chekbox+" "+(row.id_old_metal_rate==undefined ? '':row.id_old_metal_rate);

                        }},

                        { "mDataProp": function ( row, type, val, meta ){

							if(row.metal!='' && row.metal!=undefined)

							{

									var metal=row.metal;

							}

							else

							{

								var metal='';

							}

					       return metal;

						}

						},

						{ "mDataProp": function ( row, type, val, meta ){

							if(row.rate!='' && row.rate!=undefined)

							{

									var rate=row.rate;

							}

							else

							{

								var rate='';

							}

					       return '<input class="rate form-control" name="rate"  value="'+rate+'"  id="rate"  type="number" tabindex="'+row.id_old_metal_rate+'"/>'

						}

						},

						{ "mDataProp": function ( row, type, val, meta ){

							id_old_metal_rate=row.id_old_metal_rate;

							var id_purity=$('#'+id_old_metal_rate+"_id_purity").val();

					      return row.html;

						}

						},

						{ "mDataProp": function ( row, type, val, meta ){

							if(row.created_on)

							{

									var created_on=row.created_on;

							}

							else

							{

								var created_on='';

							}

					       return created_on;

						}

						},

						]

		});

}

$('#submit').on('click',function(){

    if($("input[name='id_old_metal_rate[]']:checked").val())

    {

		if(validateMetalrateRow()){

	    $("#submit").prop('disabled',true);

	    $(".overlay").css('display','none');

	    var selected = [];

	    var allow_update=true;

	    var is_branchwise_rate=$('#is_branchwise_rate').val();

	    var id_branch=$('#id_branch').val();

	    var id_metal=$('#id_metal').val();

	    if(id_metal=='')

	    {

	    	alert('Plese Select Metal Type');

	    	allow_update=false;

	    	$("#submit").prop('disabled',false);

	    }

	    if(is_branchwise_rate==1 && id_branch=='')

	    {

	    		alert('Plese Select Banch');

	    		allow_update=false;

	    		$("#submit").prop('disabled',false);

	    }

		$("#oldmetal_rate_list tbody tr").each(function(index, value){

		if($(value).find("input[name='id_old_metal_rate[]']:checked").is(":checked"))

		{

			transData = {

			'id_old_metal_rate'   : $(value).find(".id_old_metal_rate").val(),

			'rate'   : $(value).find(".rate").val(),

			'id_purity'   : $(value).find(".id_purity").val(),

			}

			selected.push(transData);

		}

		})

		}else{

			alert("please fill the metal rate field");

		}

		req_data = selected;

		if(allow_update)

		{

				update_rate_data(req_data);

		}

	}

	else

	{

	alert('Select Any Product');

	$("#submit").prop('disabled',false);

	}

});

$(document).on('change', ".purity", function(e) {

	 var name=this.name;

	 if(this.name==name)

	 {

	 		 if(this.value!='')

			 {

			 		$('#'+this.id+"new_purity").val(this.value);

			 }

	 }

	 else

	 {

	 		$('#'+this.id+"_id_purity").val(this.value);

	 }

});

$('#select_all').click(function(event) {

$("tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));

event.stopPropagation();

});

	$('#addRow').on( 'click', function () {

		$('#addRow').prop('disabled',true);

		if(validateMetalrateRow()){

		var a = $("#tot_rate").val();

		var i = ++a;

		$("#tot_rate").val(i);

        var html='';

        html+='<tr><td><input type="checkbox" class="id_old_metal_rate" style="width:40%;" name="id_old_metal_rate[]" checked/></td><td></td><td><input class="rate form-control" name="rate" id="rate" style="width:40%;" type="number"/></td><td><select class="form-control purity" style="width:40%;" name="new_purity"  id="'+i+'"><input class="id_purity" style="width:40%;" type="hidden" id="'+i+'new_purity"></td><td>-</td></tr>';

        $('#oldmetal_rate_list tbody').append(html);

        var id_purity=$('#'+a+"_id_purity").val();

        $.each(PuritiesArr, function (key, item) {

					    $('#'+i).append(

					    $("<option></option>")

					    .attr("value", item.id_purity)

					    .text(item.purity)

					    );

						$("#"+i).select2({

						placeholder: "Select Purity",

						allowClear: true

						});

						$("#"+i).select2("val",(id_purity!='' && id_purity>0?id_purity:''));

					});

					}

		else{

			alert("Please fill the metal_rate field");

		}

        $('#addRow').prop('disabled',false);

    } );

    function validateMetalrateRow(){

		var ml_validate = true;

		$('#oldmetal_rate_list > tbody >tr').each(function(index, tr) {

			if($(this).find('td:eq(2) #rate').val() == ""){

				ml_validate = false;

			}

		});

		return ml_validate;

	}

function update_rate_data(req_data)

{

	my_Date = new Date();

	var id_branch=$('#id_branch').val();

	var id_metal=$('#id_metal').val();

	$.ajax({

			 url:base_url+ "index.php/admin_ret_catalog/update_rate_data?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),

			 data:  {'req_data':req_data,'id_branch':id_branch,'id_metal':id_metal},

			 type:"POST",

			 async:false,

			 	  success:function(data){

			 	     alert('Rate updated successfully');

			        $('#select_all').prop('checked',false);

			          $("#submit").prop('disabled',false);

			        get_old_metal_rate(id_branch,id_metal);

			   		$(".overlay").css('display','block');

				  },

				  error:function(error)

				  {

					 $("div.overlay").css("display", "none");

				  }

		  });

}

//Old Metal Rate list

function set_materialrate_table(material_id="",from_date="",to_date="")

{

	my_Date = new Date();

	$.ajax({

			 url:base_url+ "index.php/admin_ret_catalog/material_rate?nocache=" + my_Date.getUTCSeconds(),

			 data:({'from_date':from_date,'to_date':to_date,'material_id':material_id}),

			 dataType:"JSON",

			 type:"POST",

			 cache:false,

			 success:function(data){

				var mtrrate 	= data.mtrrate;

				var access	    = data.access;

				$('#total_mtrrate').text(mtrrate.length);

				$('#tot_m_rate').val(mtrrate.length);

				if(access.add == '0')

				{

					$('#add_mtrlrate').attr('disabled','disabled');

				}

			 $("div.overlay").css("display","add_newstone");

			 var oTable = $('#mtrrate_list').DataTable();

			 oTable.clear().draw();

			 if (mtrrate!= null && mtrrate.length > 0)

			 {

				oTable = $('#mtrrate_list').dataTable({

						"bDestroy": true,

						"bInfo": true,

						"bFilter": true,

						"bSort": true,

						"dom": 'lBfrtip',

						"buttons" : ['excel','print'],

						"order": [[ 0, "desc" ]],

						"tableTools": { "buttons": [ { "sExtends": "xls",

						"oSelectorOpts": { page: 'current'

						} },

						{ "sExtends": "pdf",

						"oSelectorOpts": { page: 'current'

						} } ] },

						"aaData"  : mtrrate,

						"aoColumns": [

						{"mDataProp": function ( row, type, val, meta )

                        {

                        chekbox='<input type="checkbox" class="mat_rate_id" name="mat_rate_id[]" value="'+(row.mat_rate_id ==''? '':row.mat_rate_id)+'"/> '

                         return chekbox+" "+(row.mat_rate_id==undefined ? '':row.mat_rate_id);

                        }},

										{ "mDataProp": "material_name" },

						{ "mDataProp": function ( row, type, val, meta ){

						if(row.mat_rate!='' && row.mat_rate!=undefined)

						{

						var mat_rate=row.mat_rate;

						}

						else

						{

						var mat_rate='';

						}

						return '<input class="mat_rate form-control" name="mat_rate"  value="'+mat_rate+'"  id="rate"  type="number" tabindex="'+row.mat_rate_id+'"/>'

						}

						},

						{ "mDataProp": "effective_date" },

						{ "mDataProp": function ( row, type, val, meta ) {

							return "-";

							}

						}]

					});

				}

		  },

		  error:function(error)

		  {

			 $("div.overlay").css("display","none");

		  }

	});

}

$('#add_material_rate').on( 'click', function () {

		$('#add_material_rate').prop('disabled',true);

		if(validateMtrrateRow()){

		var a = $("#tot_m_rate").val();

		var i = ++a;

		$("#tot_m_rate").val(i);

        var html='';

        html+='<tr id="mtrrate'+i+'"><td><input type="checkbox" class="mat_rate_id" id="mat_rate_id" name="mat_rate_id[]" checked required /></td><td><input class="id_material form-control" name="id_material" id="id_material"  type="hidden" value="'+$("#addmatrt_lst").val()+'" required/></td><td><input class="mat_rate form-control" name="mat_rate" id="mat_rate"  type="number" required/></td><td>-</td><td><button type="button" class="btn btn-danger btn-del" onclick="mtrrate_remove('+i+')"><i class="fa fa-trash" ></i>Delete</button></td></tr>';

		$('#mtrrate_list').append(html);

		}

		else{

			alert("Please fill material rate field");

		}

        var material_id=$('#'+a+"material_id").val();

        $('#add_material_rate').prop('disabled',false);

    } );

function validateMtrrateRow(){

	var mtr_validate = true;

	$('#mtrrate_list > tbody >tr').each(function(index, tr) {

		if($(this).find('td:eq(2) .mat_rate').val() == ""){

			mtr_validate = false;

		}

	});

	return mtr_validate;

}

function mtrrate_remove(i,id = ""){

		var	rowId= "mtrrate"+i;

		$('#'+rowId+'').remove();

		if(id){

			deletemtrrate(id);

		}

}

function deletemtrrate(id){

	my_Date = new Date();

		$("div.overlay").css("display", "block");

		$.ajax({

			 url:base_url+"index.php/admin_ret_catalog/material_rate/delete/"+id+"?nocache=" + my_Date.getUTCSeconds(),

			 type:"POST",

			 success:function(data){

				 console.log(data);

					 $("div.overlay").css("display", "none");

				  },

				  error:function(error)

				  {

					alert('error');

					 $("div.overlay").css("display", "none");

				  }

			  });

}

function update_mrrate_data(req_data)

{

	my_Date = new Date();

	var material_id = $('#material_filter').val();

	$.ajax({

			 url:base_url+ "index.php/admin_ret_catalog/update_mrrate_data?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),

			 data:  {'req_data':req_data,'material_id':material_id},

			 type:"POST",

			 async:false,

			 	  success:function(data){

			 	     alert('Rate updated successfully');

			        $('#select_all').prop('checked',false);

			        $("#add_material_rate").prop('disabled',false);

			         window.location.reload();

				  },

				  error:function(error)

				  {

					 $("div.overlay").css("display", "none");

				  }

		  });

}

//to load values to table

//to load values to table

function getActive_uom()

{

    $('#uom option').remove();

    $('#ed_uom option').remove();

    $.ajax({

    type: 'GET',

    url: base_url+'index.php/admin_ret_catalog/uom/active_uom',

    dataType:'json',

    success:function(data)

        {

			uom_details =data;

            var id=$('#uom').val();

            var ed_uom=$('#id_uom').val();

            $.each(data, function (key, item) {

                if(item.is_default==1)

                {

                    id=item.uom_id;

                }

                $('#uom,#ed_uom').append(

                $("<option></option>")

                .attr("value", item.uom_id)

                .text(item.code)

                );

            });

            $("#uom,#ed_uom").select2(

            {

                placeholder:"Select UOM",

                allowClear: true

            });

            $("#uom").select2("val",(id!='' && id>0?id:''));

            if(ed_uom!='')

            {

                $("#ed_uom").select2("val",(ed_uom!='' && ed_uom>0?ed_uom:''));

            }

            $(".overlay").css("display", "none");

        }

    });

}

$('#add_wt').on('click',function(){

    get_Activeproduct('');

});

$('#sel_prod').on('change',function(){

    if(this.value)

    {

        set_weight_table(this.value);

    }

});

$('#weight_design').on('change',function(){

	if(this.value!='')

	{

		get_weight_range_sub_design(this.value);

	}

});

function get_weight_range_sub_design(id_design)

{

	$('#weight_sub_design option').remove();

	$('#ed_wt_range_sub_des option').remove();

	$.ajax({

	type: 'POST',

	url: base_url+'index.php/admin_ret_catalog/get_ActiveSubDesigns',

	data :{'id_product':$('#weight_prod').val() || $('#ed_wt_range_prod').val(),'design_no':id_design},

	dataType:'json',

	success:function(data){

        		var id =  $("#weight_sub_design").val();

        		var ed_wt_range_sub_des =  $("#ed_wt_sub_des").val();

        		$.each(data, function (key, item) {

        		    $("#weight_sub_design,#ed_wt_range_sub_des").append(

						$("<option></option>")

						.attr("value", item.id_sub_design)

						.text(item.sub_design_name)

						);

        		});

        		$("#weight_sub_design,#ed_wt_range_sub_des").select2(

        		{

        			placeholder:"Select Sub Design",

        			allowClear: true

        		});

		        $("#weight_sub_design").select2("val",'');

		       if(ed_wt_range_sub_des!='' && ed_wt_range_des!=undefined  && ed_wt_range_sub_des!=null)

		        {

		            $("#ed_wt_range_sub_des").select2("val",ed_wt_range_sub_des);

		        }else{

		            $("#ed_wt_range_sub_des").select2("val",'');

		        }

		}

	});

}

function get_weight_range_product(id_category='')

{

    $(".overlay").css("display", "block");

    $('#weight_prod option').remove();

    my_Date = new Date();

    $.ajax({

        type: 'POST',

        url: base_url+"index.php/admin_ret_catalog/ret_product/active_list/?nocache=" + my_Date.getUTCSeconds(),

        data :{'id_category':id_category},

        dataType:'json',

        success:function(data){

            var wt_range_prod=$("#wt_range_prod").val();

            var ed_wt_range_prod=$("#ed_wt_range_prod").val();

            $.each(data, function (key, item) {

                $("#weight_prod,#ed_wt_range_prod").append(

                $("<option></option>")

                .attr("value", item.pro_id)

                .text(item.product_name)

                );

            });

            $("#weight_prod,#ed_wt_range_prod").select2(

            {

            placeholder:"Select Product",

            allowClear: true

            });

            $("#weight_prod").select2("val",'');

            if(ed_wt_range_prod!='' && ed_wt_range_prod!=undefined)

            {

                $("#ed_wt_range_prod").select2("val",ed_wt_range_prod);

            }

            $(".overlay").css("display", "none");

        }

    });

}

$('#weight_prod').on('change',function(){

    if(this.value!='')

    {

        get_weight_range_design(this.value);

        var wt_range_des=$('#weight_design').val();

        my_Date = new Date();

        $.ajax({

        url: base_url+"index.php/admin_ret_catalog/get_weight_range_details?nocache=" + my_Date.getUTCSeconds(),

        type:"POST",

        data:{'id_product':this.value,'id_design':wt_range_des},

        dataType: 'json',

        cache: false,

        success:function(data){

        wt_data=data;

        console.log(wt_data);

        }

        });

    }

});

function get_weight_range_design(id_product)

{

	$('#weight_design option').remove();

	$('#ed_wt_range_des option').remove();

	$.ajax({

	type: 'POST',

	url: base_url+'index.php/admin_ret_catalog/reorder_settings/active_design',

	data :{'id_product':id_product},

	dataType:'json',

	success:function(data){

        		var id =  $("#weight_design").val();

        		var ed_wt_range_des =  $("#ed_wt_range_des").val();

        		$.each(data, function (key, item) {

        		    $("#weight_design,#ed_wt_range_des").append(

        		    $("<option></option>")

        		    .attr("value", item.design_no)

        		    .text(item.design_name)

        		    );

        		});

        		$("#weight_design,#ed_wt_range_des").select2(

        		{

        			placeholder:"Select Design",

        			allowClear: true

        		});

		        // $("#weight_design").select2("val",'');

		       if(ed_wt_range_des!='' && ed_wt_range_des!=undefined  && ed_wt_range_des!=null)

		        {

		            $("#ed_wt_range_des").select2("val",ed_wt_range_des);

		        }else{

		            $("#ed_wt_range_des").select2("val",'');

		        }

		}

	});

}

//Weight Range Added

function set_weight_table(id_product)

{

	 var branch_name = ($('#branch_name').val() != '' && $('#branch_name').val() != undefined ? $('#branch_name').val() : $('#branch_select option:selected').toArray().map(item => item.text).join());

	var company_name = $('#company_name').val();

	var company_code = $('#company_code').val();

	var company_code = $('#company_code').val();

	var company_address1 = $('#company_address1').val();

	var company_address2 = $('#company_address2').val();

	var company_city = $('#company_city').val();

	var pincode = $('#pincode').val();

	var company_email = $('#company_email').val();

	var company_gst_number = $('#company_gst_number').val();

	var phone = $('#phone').val();

	var title = "<div style='text-align: center;'><b><span style='font-size:12pt;'>" + company_code + "</span></b></br>"

	+ "<span style='font-size:11pt;'>" + company_address1 + "</span></br>"

	+ "<span style='font-size:11pt;'>" + company_address2 + company_city + "-" + pincode + "</span></br>"

	+ "<span style='font-size:11pt;'>GSTIN:" + company_gst_number + ", EMAIL:" + company_email + "</span></br>"

	+ "<span style='font-size:11pt;'>Contact :" + phone + "</span></br>"

	+ "<span style=font-size:12pt;>&nbsp;&nbsp;Weight Range - &nbsp;&nbsp; "+ $('.hidden-xs').html() + "</span>";

	my_Date = new Date();

	$.ajax({

			 url:base_url+ "index.php/admin_ret_catalog/weight/?nocache=" + my_Date.getUTCSeconds(),

			 dataType:"JSON",

			 type:"POST",

			 data:{'id_product':id_product},

			 success:function(data){

				 var weight 	= data.data;

				 var access		= data.access;

				 $('#total_weights').text(weight.length);

				 if(access.add == '0')

				 {

					$('#add_wt').attr('disabled','disabled');

				 }

			 var oTable = $('#weight_list').DataTable();

				 oTable.clear().draw();

						 if (weight!= null && weight.length > 0)

							{

							oTable = $('#weight_list').dataTable({

									"bDestroy": true,

									"bInfo": true,

									"bFilter": true,

									"order": [[0, "desc"]],

									"scrollX": '100%',

									"bSort": true,

									"dom": 'lBfrtip',

									"buttons": [

										{

											extend: 'print',

											footer: true,

											title: '',

											messageTop: title,

											customize: function (win) {

												$(win.document.body).find('table')

													.addClass('compact')

													.css('font-size', 'inherit');

											},

										},

										{

											extend: 'excel',

											footer: true,

											title: 'Weight Range List',

										}

									],

									"aaData": weight,

									// "order": [[ 0, "desc" ]],

									"aoColumns": [

								                    { "mDataProp": "id_weight" },

								                    { "mDataProp": "product_name" },

								                    /*{ "mDataProp": "design_name" },

								                    { "mDataProp": "sub_design_name" },*/

								                    { "mDataProp": "weight_description" },

													{ "mDataProp": "from_weight" },

													{ "mDataProp": "to_weight" },

													{ "mDataProp": "emp_created" },

													{ "mDataProp": "created_date" },

													{ "mDataProp": "emp_updated" },

													{ "mDataProp": "updated_date" },

													{ "mDataProp": function ( row, type, val, meta ) {

														 id= row.id_weight;

														 edit_target=(access.edit=='0'?"":"#confirm-edit");

														 edit_url=(access.edit=='1' ? base_url+'index.php/admin_ret_catalog/weight/Edit/'+id : '#' );

														 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/weight/Delete/'+id : '#' );

														 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

														 action_content=(access.edit == '1' ? '<a href="'+edit_url+'" class="btn btn-primary"  role="button" data-toggle="modal" data-id='+id+' ><i class="fa fa-edit" ></i></a>':'') + (access.delete == '1' ? '<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>':"")

														 return action_content;

														 }

													}]

								});

							}

					  },

					  error:function(error)

					  {

						 $("div.overlay").css("display", "none");

					  }

			      });

}

$(document).on('click', "#weight_list a.btn-edit", function(e) {

		$("#ed_from_weight").val('');

		$("#ed_to_weight").val('');

		e.preventDefault();

		id=$(this).data('id');

	    get_weight(id);

	    $("#edit-id").val(id);

});

$("#add_weight").on('click',function(){

	// alert(1);

    var from_weight     =$('#from_weight').val();

    var to_weight       =$('#to_weight').val();

    var name            =$('#name').val();

    var id_product      =$('#weight_prod').val();

    var id_design       =$('#weight_design').val();

    var uom             =$('#uom').val();

    var id_sub_design   =$('#weight_sub_design').val();

    var weight_desc     = $('#weight_desc').val();

    var id              = $('#weight').val();

   if(name!='' && from_weight!='' && to_weight!='' && id_product!=null && weight_desc!='' && weight_desc!=null)

    {

          add_weight(name,from_weight,to_weight,id_product,id_design,uom,weight_desc,id_sub_design,id);

		 $('#name').focus();

    }

	else{

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill Required Fields'});

    }

});

$("#update_weight").on('click',function(){

		var from_weight     =$("#ed_from_weight").val();

		var to_weight       =$("#ed_to_weight").val();

		var name            =$("#ed_name").val();

		var id_product      =$("#ed_wt_range_prod").val();

		var id_design       =$("#ed_wt_range_des").val();

		var id              =$("#edit-id").val();

		var uom             =$('#ed_uom').val();

		var id_sub_design   =$("#ed_wt_range_sub_des").val();

		var weight_desc     = $('#ed_weight_desc').val();

		if(name!='' && from_weight!='' && to_weight!='' && id_product!=null)

		{

		    update_weight(name,from_weight,to_weight,id_product,id_design,uom,id, weight_desc,id_sub_design);

		}else{

			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill Required Fields'});

        }

	});

function get_weight(id)

{

   my_Date = new Date();

	$.ajax({

		type:"GET",

		url: base_url+"index.php/admin_ret_catalog/weight/Edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		cache:false,

		dataType:"JSON",

		success:function(data){

		    $('#ed_from_weight').val(data.from_weight);

		    $('#ed_to_weight').val(data.to_weight);

		    $('#ed_name').val(data.weight_value);

		    $('#ed_wt_range_prod').val(data.id_product);

		    $('#ed_wt_des').val(data.id_design);

		    $('#ed_uom').val(data.id_uom);

		    $('#id_uom').val(data.id_uom);

		    $('#ed_wt_sub_des').val(data.id_sub_design);

		    get_weight_range_product('');

		    product_design(data.id_product);

		    get_weight_range_details(data.id_product,data.id_design);

		    getActive_uom();

		}

	});

}

//to add weight

function add_weight(name,from_weight,to_weight,id_product,id_design,uom,weight_desc,id_sub_design,id)

{

	my_Date = new Date();

	if(id=="")

	{

 	var url = base_url+"index.php/admin_ret_catalog/weight/Add?nocache=" + my_Date.getUTCSeconds();

    }

	else{

      var url =  base_url+"index.php/admin_ret_catalog/weight/Update/"+id+"?nocache=" + my_Date.getUTCSeconds();

	}

	$.ajax({

		data:{'name':name,'from_weight':from_weight,'to_weight':to_weight,'id_product':id_product,'id_design':id_design,'uom':uom, 'weight_desc' : weight_desc,'id_sub_design':id_sub_design,},

		url: url,

		type:"POST",

		dataType: 'json',

		cache:false,

		success:function(data){

            if(data.status)

		    {

		          msg='<div class = "alert alert-success"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Success!</strong>'+data.msg+'</div>';

		    }else

		    {

		       	msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>'+data.msg+'</div>';

		    }

			$('#error-msg').html(msg);

			if(data.status)

		    {

				set_weight_to_preview(data.weight_id);

				$('#weight').val('');

			}

			$('#from_weight').val('');

			$('#to_weight').val('');

			$('#name').val('');

			$('#weight_desc').val('');

			$('#weight_saved').val(1);

			$('#name').focus();

		}

	});

}

function set_weight_to_preview($id)

{

     var row = "";

	 var total_row = $('#total_weight_items_preview tbody > tr').length;

	 row +='<tr id='+$('#total_weight').val()+' class='+(total_row+1)+'>'

	                +'<td><input type="hidden" class="id_weight" value='+$id+'><input type="hidden" class="product" value="'+$('#weight_prod').val()+'">'+$("#weight_prod option:selected").text()+'</td>'

	                // +'<td><input type="hidden" class="uom" value="'+$('#uom').val()+'">'+$("#uom option:selected").text()+'</td>'

	                +'<td><input type="hidden" class="value" value="'+$('#name').val()+'">'+$("#name").val()+'</td>'

	                +'<td><input type="hidden" class="from_weight" value="'+$('#from_weight').val()+'">'+$("#from_weight").val()+'</td>'

					+'<td><input type="hidden" class="to_weight" value="'+$('#to_weight').val()+'">'+$("#to_weight").val()+'</td>'

					+'<td><input type="hidden" class="weight_desc" value="'+$('#weight_desc').val()+'">'+$("#weight_desc").val()+'</td>'

	                +'<td><div style="display: flex;"><span id="items_add_'+total_row+'"><a style="" href="#" onClick="edit_weight_range($(this).closest(\'tr\'));" class="btn btn-primary btn-edit style="padding:5px;" data-toggle="tooltip" title="Edit"><i class="fa fa-edit" ></i></a></span></div></td>'

	                //onClick="remove_row($(this).closest(\'tr\'));"

	       +'</tr>';

	       if($('#total_weight_items_preview > tbody  > tr').length>0)

        	{

        	    $('#total_weight_items_preview > tbody > tr:first').before(row);

        	}else{

        	    $('#total_weight_items_preview tbody').append(row);

        	}

    set_weight_preview_class();

}

function edit_weight_range(curRow){

	console.log("curRow On Edit",curRow);

    $("div.overlay").css("display", "block");

    var item=curRow;

    // $('#weight_saved').val(item.find('#weight_saved').val());

    // $('#weight').val(item.find('#weight').val());

     $('#weight_prod').select2("val",item.find('.product').val());

	// $('#uom').select2("val",item.find('.uom').val());

	$('#weight').val(item.find('.id_weight').val());

	$('#name').val(item.find('.value').val());

	$('#from_weight').val(item.find('.from_weight').val());

	$('#to_weight').val(item.find('.to_weight').val());

	$('#weight_desc').val(item.find('.weight_desc').val());

    curRow.remove();

	// $('#total_weight_items_preview >tbody').empty();

	set_weight_preview_class();

}

function set_weight_preview_class()

{

     var total_row = $('#total_weight_items_preview tbody > tr').length;

     $('#total_weight_items_preview > tbody tr').each(function(idx, row){

          $(this).removeClass();

          $(this).closest('tr').addClass(idx.toString());

     });

}

function get_weight_edit_data(id) {

    my_Date = new Date();

    $.ajax({

        url: base_url + "index.php/admin_ret_catalog/weight/weight_Edit/" + id + "?nocache=" + my_Date.getUTCSeconds(),

        type: "GET",

        dataType: 'json',

        cache: false,

        success: function (data) {

			// $('#weight').val(id);

            if (data.weight.length > 0) {

                $.each(data.weight, function (key, item) {

					var row = "";

					var total_row = $('#total_weight_items_preview tbody > tr').length;

					row +='<tr id='+$('#total_weight').val()+' class='+(total_row+1)+'>'

	                +'<td><input type="hidden" class="id_weight"  value="'+item.id_weight+'" ><input type="hidden" class="product" value=' + item.id_product + '>'+item.product_name+'</td>'

	                +'<td><input type="hidden" class="value" value="'+item.value+'">'+item.value+'</td>'

	                +'<td><input type="hidden" class="from_weight" value="'+item.from_weight+'">'+item.from_weight+'</td>'

					+'<td><input type="hidden" class="to_weight" value="'+item.to_weight+'">'+item.to_weight+'</td>'

					+'<td><input type="hidden" class="weight_desc" value="'+item.weight_description+'">'+item.weight_description+'</td>'

	                +'<td><div style="display: flex;"><span id="items_add_'+total_row+'"><a style="" href="#" onClick="edit_weight_range($(this).closest(\'tr\'));" class="btn btn-primary btn-edit style="padding:5px;" data-toggle="tooltip" title="Edit"><i class="fa fa-edit" ></i></a></span></div></td>'

	                //onClick="remove_row($(this).closest(\'tr\'));"

	          +'</tr>';

        	    $('#total_weight_items_preview tbody').append(row);

                });

            }

        }

    });

}

//Weight Range Added

// function update_weight(name,from_weight,to_weight,id_product,id_design,uom,id,weight_desc,id_sub_design)

// {

// 	my_Date = new Date();

// 	$.ajax({

// 		data:{'name':name,'from_weight':from_weight,'to_weight':to_weight, 'id_product':id_product, 'id_design':id_design,'uom':uom, 'weight_desc' : weight_desc,'id_sub_design':id_sub_design},

// 		url: base_url+"index.php/admin_ret_catalog/weight/Update/"+id+"?nocache=" + my_Date.getUTCSeconds(),

// 		type:"POST",

// 		dataType: 'json',

// 		cache: false,

// 		success:function(data){

// 		    $('#confirm-edit').modal('toggle');

// 			$('#ed_from_weight').val('');

// 			$('#ed_to_weight').val('');

// 			$('#ed_name').val('');

// 			$('#from_weight').val('');

// 			$('#to_weight').val('');

// 			$('#name').val('');

// 			$('#weight_desc').val('');

// 			$('#weight_saved').val(1);

// 			$('#weight').val(id);

// 			$('#name').focus();

// 		    //set_weight_table();

// 			if(data.status)

// 		    {

// 		        msg='<div class = "alert alert-success"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Success!</strong>'+data.msg+'</div>';

// 				window.location.reload(true);

// 		    }else

// 		    {

// 		       	msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Success!</strong>'+data.msg+'</div>';

// 		    }

// 			$('#error-msg').html(msg);

// 			$('#name').focus();

// 		}

// 	});

// }

$('#wt_range_prod').on('change',function(){

   if(this.value!='')

   {

        product_design(this.value);

        var wt_range_des=$('#wt_range_des').val();

       	my_Date = new Date();

    	$.ajax({

    		url: base_url+"index.php/admin_ret_catalog/get_weight_range_details?nocache=" + my_Date.getUTCSeconds(),

    		type:"POST",

    		data:{'id_product':this.value,'id_design':wt_range_des},

    		dataType: 'json',

    		cache: false,

    		success:function(data){

    		    wt_data=data;

    		    console.log(wt_data);

    		}

    	});

   }

});

$('#wt_range_des').on('change',function(){

   if(this.value!='')

   {

        var wt_range_prod=$('#wt_range_prod').val();

       	my_Date = new Date();

    	$.ajax({

    		url: base_url+"index.php/admin_ret_catalog/get_weight_range_details?nocache=" + my_Date.getUTCSeconds(),

    		type:"POST",

    		data:{'id_product':wt_range_prod,'id_design':this.value},

    		dataType: 'json',

    		cache: false,

    		success:function(data){

    		    wt_data=data;

    		    console.log(wt_data);

    		}

    	});

   }

});

$('#ed_wt_range_des').on('change',function(){

	if(this.value!='')

	{

		get_weight_range_sub_design(this.value);

	}

});

function get_weight_range_details(id_product,id_design)

{

    my_Date = new Date();

    $.ajax({

    url: base_url+"index.php/admin_ret_catalog/get_weight_range_details?nocache=" + my_Date.getUTCSeconds(),

    type:"POST",

    data:{'id_product':id_product,'id_design':id_design},

    dataType: 'json',

    cache: false,

    success:function(data){

    wt_data=data;

    console.log(wt_data);

    }

    });

}

/*$('#from_weight').on('change',function(){

    var from_weight=parseFloat($('#from_weight').val()).toFixed(3);

    $.each(wt_data, function (key, item) {

        if(item.from_weight==from_weight)

        {

            $('#from_weight').val('');

            $("#from_weight").attr('placeholder','Weight is Already Used');

            $("#from_weight").focus();

            $('#add_weight').attr('disabled',true);

        }else{

            $('#add_weight').attr('disabled',false);

        }

    });

});

$('#to_weight').on('change',function(){

    var to_weight=parseFloat($('#to_weight').val()).toFixed(3);

    $.each(wt_data, function (key, item) {

        if(item.to_weight==to_weight)

        {

            $('#to_weight').val('');

            $("#to_weight").attr('placeholder','Weight is Already Used');

            $("#to_weight").focus();

            $('#add_weight').attr('disabled',true);

        }else

        {

            $('#add_weight').attr('disabled',false);

        }

    });

});

$('#ed_from_weight').on('change',function(){

    var from_weight=parseFloat($('#ed_from_weight').val()).toFixed(3);

    $.each(wt_data, function (key, item) {

        if(item.from_weight==from_weight)

        {

            $('#ed_from_weight').val('');

            $("#ed_from_weight").attr('placeholder','Weight is Already Used');

            $("#ed_from_weight").focus();

            $('#update_weight').attr('disabled',true);

        }else{

            $('#update_weight').attr('disabled',false);

        }

    });

});

$('#ed_to_weight').on('change',function(){

    var to_weight=parseFloat($('#ed_to_weight').val()).toFixed(3);

    $.each(wt_data, function (key, item) {

        if(item.to_weight==to_weight)

        {

            $('#ed_to_weight').val('');

            $("#ed_to_weight").attr('placeholder','Weight is Already Used');

            $("#ed_to_weight").focus();

            $('#update_weight').attr('disabled',true);

        }else{

            $('#update_weight').attr('disabled',false)

        }

    });

});*/

//Re-Order settings

function getFilteWeightRange()

{

    $('#weight_range option').remove();

    var wt_range_des=$('#weight_range').val();

        my_Date = new Date();

        $.ajax({

        url: base_url+"index.php/admin_ret_catalog/get_weight_range_details?nocache=" + my_Date.getUTCSeconds(),

        type:"POST",

        data:{'id_product':$('#ret_product').val(),'id_design':$('#ret_design').val(),'id_sub_design':$('#ret_sub_design').val()},

        dataType: 'json',

        cache: false,

        success:function(data){

            $.each(data, function (key, item) {

    		    $("#weight_range").append(

    		    $("<option></option>")

    		    .attr("value", item.id_weight)

    		    .text(item.weight_range)

    		    );

    		});

    		$("#weight_range").select2(

    		{

    			placeholder:"Select Weight Range",

    			allowClear: true

    		});

    		    $("#weight_range").select2("val",'');

        }

        });

}

function getFilterDesign(id_product)

{

	$('#ret_design option').remove();

	$.ajax({

	type: 'POST',

	url: base_url+'index.php/admin_ret_catalog/reorder_settings/active_design',

	data :{'id_product':id_product},

	dataType:'json',

	success:function(data)

	    {

    		$.each(data, function (key, item) {

    		    $("#ret_design").append(

    		    $("<option></option>")

    		    .attr("value", item.design_no)

    		    .text(item.design_name)

    		    );

    		});

    		$("#ret_design").select2(

    		{

    			placeholder:"Select Design",

    			allowClear: true

    		});

    		    $("#ret_design").select2("val",'');

		}

	});

}

$('#ret_product').on('change',function(){

	if(this.value!='')

	{

		getFilterDesign(this.value);

		getFilteWeightRange();

	    get_Activesize(this.value);

	}

});

$('#ret_sub_design').on('change',function(){

	if(this.value!='')

	{

		getFilteWeightRange();

	}

});

$('#ret_design').on('change',function(){

    if(this.value!='')

    {

        get_ActiveSubDesign();

        getFilteWeightRange();

    }

});

function get_ActiveSubDesign()

{

    $('#ret_sub_design option').remove();

	$.ajax({

	type: 'POST',

	url: base_url+'index.php/admin_ret_reports/get_ActiveSubDesign',

	dataType:'json',

	data :{'id_product':$('#ret_product').val(),'id_design':$('#ret_design').val()},

	success:function(data){

		var id =  $("#id_design").val();

		$.each(data, function (key, item) {

		    $("#ret_sub_design").append(

		    $("<option></option>")

		    .attr("value", item.id_sub_design)

		    .text(item.sub_design_name)

		    );

		});

		$("#ret_sub_design").select2(

		{

			placeholder:"Select Sub Design",

			allowClear: true

		});

		    $("#ret_sub_design").select2("val",(id!='' && id>0?id:''));

		}

	});

}

$('#weight_range').on('change',function(){

});

$('#reorder_set_search').on('click',function(){

    get_reorder_settings();

});

function get_ActiveSubDesingns()

{

	$('#des_select option').remove();

	$.ajax({

	type: 'POST',

	url: base_url+'index.php/admin_ret_catalog/get_ActiveSubDesigns',

	data :{'id_product':$('#des_prod_name').val(),'design_no':$('#des_des_name').val()},

	dataType:'json',

	success:function(data){

	        var id_sub_design=$('#sub_des_id').val();

			if(ctrl_page[1] ="wastage_mc_settings" && ctrl_page[2] =="add" ){

				$("#select_sub_design,#ed_sub_des_select").append(

        		    $("<option></option>")

        		    .attr("value", 0)

        		    .text("ALL")

        		    );

			}

    		$.each(data, function (key, item) {

    		    $("#select_sub_design,#ed_sub_des_select").append(

    		    $("<option></option>")

    		    .attr("value", item.id_sub_design)

    		    .text(item.sub_design_name)

    		    );

    		});

    		$("#select_sub_design,#ed_sub_des_select").select2(

    		{

    			placeholder:"Select Design",

    			allowClear: true

    		});

    		if($("#select_sub_design").length)

    		{

    		    //$("#select_sub_design").select2("val",'');

    		    $("#select_sub_design").select2("val",(id_sub_design!='' ? id_sub_design :''));

    		}

    		if($("#ed_sub_des_select").length)

    		{

    		    var id_sub_design=$('#ed_id_sub_design').val();

    		    $("#ed_sub_des_select").select2("val",(id_sub_design!='' ? id_sub_design :''));

    		}

		}

	});

}

function get_reorder_settings()

{

	var company_code = $('#company_code').val();

	var company_address1 = $('#company_address1').val();

	var company_address2 = $('#company_address2').val();

	var company_city = $('#company_city').val();

	var pincode = $('#pincode').val();

	var company_email = $('#company_email').val();

	var company_gst_number = $('#company_gst_number').val();

	var phone = $('#phone').val();

		var title = "<div style='text-align: center;'><b><span style='font-size:12pt;'>" + company_code + "</span></b></br>"

		+ "<span style='font-size:11pt;'>" + company_address1 + "</span></br>"

		+ "<span style='font-size:11pt;'>" + company_address2 + company_city + "-" + pincode + "</span></br>"

		+ "<span style='font-size:11pt;'>GSTIN:" + company_gst_number + ", EMAIL:" + company_email + "</span></br>"

		+ "<span style='font-size:11pt;'>Contact :" + phone + "</span></br>"

		+ "<span style=font-size:12pt;>&nbsp;&nbsp;Reorder Settings - &nbsp;&nbsp; "+ $('.hidden-xs').html() + "</span>";

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		data:{'id_product':$('#ret_product').val(),'id_design':$('#ret_design').val(),'id_sub_design':$('#ret_sub_design').val(),'id_wt_range':$('#weight_range').val(),'id_size':$('#select_size').val(),'id_section':$('#section_sel').val(),'id_branch':$('.ret_branch').val()},

		url: base_url+"index.php/admin_ret_catalog/reorder_settings/ajax?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		dataType: 'json',

		cache:false,

		success:function(data){

		   		var settings 	= data.responseData;

				var access	    = data.access;

				$('#total_mtrrate').text(settings.length);

				$('#tot_m_rate').val(settings.length);

				if(access.add == '0')

				{

					$('#add_wt').attr('disabled','disabled');

				}

			 $("div.overlay").css("display","add_newstone");

			 var oTable = $('#reorder_set_list').DataTable();

			 oTable.clear().draw();

			 if (settings!= null && settings.length > 0)

			 {

				oTable = $('#reorder_set_list').dataTable({

						"bDestroy": true,

						"bInfo": true,

						"bFilter": true,

						"bSort": true,

						"dom": 'lBfrtip',

						// "buttons" : ['excel','print'],

						"order": [[ 0, "desc" ]],

						"buttons": [

							{

								extend: 'print',

								footer: true,

								title: '',

								messageTop: title,

								customize: function (win) {

									$(win.document.body).find('table')

										.addClass('compact')

										.css('font-size', 'inherit');

								},

							},

							{

								extend: 'excel',

								footer: true,

								title: 'Reorder Settings',

							}

						],

						// "tableTools": { "buttons": [ { "sExtends": "xls",

						// "oSelectorOpts": { page: 'current'

						// } },

						// { "sExtends": "pdf",

						// "oSelectorOpts": { page: 'current'

						// } } ] },

						"aaData"  : settings,

						"aoColumns": [

						{ "mDataProp": "id_reorder_settings" },

						{ "mDataProp": "ref_no" },

						{ "mDataProp": "branch_name" },

						{ "mDataProp": "section_name" },

						{ "mDataProp": "product_name" },

						{ "mDataProp": "design_name" },

						{ "mDataProp": "sub_design_name" },

						{ "mDataProp": "from_weight" },

						{ "mDataProp": "to_weight" },

						{ "mDataProp": "wt_name" },

						{ "mDataProp": "size" },

						{ "mDataProp": "min_pcs" },

						{ "mDataProp": "max_pcs" },

						{ "mDataProp": function ( row, type, val, meta ) {

							 id= row.id_reorder_settings;

							 edit_target=(access.edit=='0'?"":"#confirm-edit");

							 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/reorder_settings/delete/'+id : '#' );

							 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

							 let edit_url=(access.add=='1' ? base_url+'index.php/admin_ret_catalog/reorder_settings/edit/'+id : '#' );

							//  action_content='<a href="'+edit_url+'" class="btn btn-primary btn-edit"  data-toggle="modal" data-id='+id+'><i class="fa fa-edit" ></i></a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

							var action_content=''

							if(access.edit == '1'){

							

							action_content+=  '<a href="'+edit_url+'" class="btn btn-primary btn-edit"  data-toggle="modal" data-id='+id+'><i class="fa fa-edit" ></i></a>' ;

							

							}

							if(access.delete == '1'){

								

								action_content+=' <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

							}

							 return action_content;

							 }

						}]

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

$('#add_settings').on('click',function(){

    get_settingsProduct('');

    get_branch_details();

    $("#des_select").select2("val",'');

    $("#wt_select").select2("val",'');

});

$('#product_select').on('change',function(){

    if(this.value!='')

    {

        get_design_product(this.value);

        $('#ed_wt_select option').remove();

        get_wt_range_product();

    }

     if(ctrl_page[1]=='reorder_settings')

    {

        get_Activesize(this.value);

    }

});

function get_settingsProduct(id_category)

{

        $('#ed_prod_select option').remove();

        $('#prod_select option').remove();

        my_Date = new Date();

        $.ajax({

        type: 'POST',

        url: base_url+"index.php/admin_ret_catalog/ret_product/active_list/?nocache=" + my_Date.getUTCSeconds(),

        data :{'id_category':id_category},

        dataType:'json',

        success:function(data)

        {

            var id =  $("#id_product").val();

            var ed_product =  $("#ed_product").val();

            $.each(data, function (key, item) {

            $("#product_select").append(

            $("<option></option>")

            .attr("value", item.pro_id)

            .text(item.product_name)

            );

            });

            $("#product_select").select2(

            {

                placeholder:"Select Product",

                allowClear: true

            });

                 $("#product_select").select2("val",'');

            if(ed_product!=undefined)

            {

                $("#ed_prod_select").select2("val",(ed_product!='' && ed_product>0?ed_product:''));

            }

             $(".overlay").css("display", "none");

        }

        });

}

$('#add_retsettings').on('click',function(e){

	var id_branch=$('#branch_select').val();

	var wt_range=$('#wt_select').val();

	var product=$('#product_select').val();

	var id_design=$('#des_select').val();

	var size=$('#select_size').val();

	var min_pcs=$('#min_pcs').val();

	var max_pcs=$('#max_pcs').val();

	if(id_branch!='' && wt_range!='' && product!='' && id_design!='' && max_pcs!='' && min_pcs!='')

	{

		add_retsettings(id_branch,wt_range,product,id_design,size,min_pcs,max_pcs);

	}else

	{

		alert('Please Fill The Required Fields');

	}

});

$('#add_retsettings_new').on('click',function(e){

	var id_branch=$('#branch_select').val();

	var wt_range=$('#wt_select').val();

	var product=$('#product_select').val();

	var id_design=$('#des_select').val();

	var id_sub_design=$('#select_sub_design').val();

	var size=$('#select_size').val();

	$('#chit_alert').html('');

	if(product=='' || product==null)

	{

	     msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Please Select Product</div>';

	     $('#chit_alert').html(msg);

	}

	else if(id_design=='' || id_design==null)

	{

	    msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Please Select Design</div>';

	    $('#chit_alert').html(msg);

	}

	else if(id_sub_design=='' || id_sub_design==null)

	{

	    msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Please Select Sub Design</div>';

	    $('#chit_alert').html(msg);

	}

	else if(wt_range=='' || wt_range==null)

	{

	    msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Please Select Weight Range</div>';

	    $('#chit_alert').html(msg);

	}

	else

	{

	     if($('#branch_settings').val()==1)

	    {

	        /*if(validateSettingsDetailRow())

            {

                add_retsettings_new();

            }

            else

            {

                msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Please Fill The Required Fields.</div>';

                $('#chit_alert').html(msg);

            }*/

            add_retsettings_new();

	    }

	}

});

// function validateSettingsDetailRow()

// {

//     var validate = true;

// 	$('#total_items > tbody > tr').each(function(index, tr) {

// 		var tr=$(this);

// 		//  if($(this).find('.min_pcs').val() == "" || $(this).find('.max_pcs').val() == "")

// 		 if(tr.find('.reorder_branch_select ').val() == "" || tr.find('.reorder_sub_design ').val() == "" || tr.find('.reorder_wt_select').val() == "" || tr.find('.reorder_size	').val() == null || tr.find('.min_pcs').val() == "" || tr.find('.max_pcs').val() == "" || !validate_isDuplicateRow(tr))

//             {

// 				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill the required fields..'});

//                 validate=false;

//             }

// 	});

// 	return validate;

// }

function validateSettingsDetailRow() {

	var validate = true;

	console.log('validate');

	$('#total_reorder_items > tbody  > tr').each(function (index, tr) {

		var tr=$(this);

		if(tr.find('.reorder_branch_select ').val() == "" || tr.find('.reorder_sub_design ').val() == "" || tr.find('.reorder_wt_select').val() == ""|| tr.find('.min_pcs').val() == "" || tr.find('.max_pcs').val() == ""){

			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill the required fields..'});

			validate = false;

		}

	 	 if(ctrl_page[2]!='edit')

        {

	 	if(!validate_isDuplicateRow(tr))

		{

			validate = false;

		}

	   }

	});

	return validate;

}

$('#update_retsettings').on('click',function(e){

	var id_branch       =$('#ed_branch_select').val();

	var wt_range        =$('#ed_wt_select').val();

	var product         =$('#ed_prod_select').val();

	var id_design       =$('#ed_des_select').val();

	var id_sub_design   =$('#ed_sub_des_select').val();

	var size            =$('#ed_select_size').val();

	var min_pcs         =$('#ed_min_pcs').val();

	var max_pcs         =$('#ed_max_pcs').val();

	var id              =$("#edit-id").val();

	$('#update_alert').html('');

	if(product=='' || product==null)

	{

	     $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please select Product..'});

	}

	else if(id_design=='' || id_design==null)

	{

	    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please select Design..'});

	}

	else if(id_sub_design=='' || id_sub_design==null)

	{

	    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please select Sub Design..'});

	}

	else if(wt_range=='' || wt_range==null)

	{

	    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please select Weight Range..'});

	}

	else if(min_pcs=='')

	{

	    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Min Pcs..'});

	}

	else if(max_pcs=='')

	{

	    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Max Pcs..'});

	}

	else if(id_branch=='' || id_branch==null)

	{

	    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Branch..'});

	}

	else{

	    update_retsettings(id_branch,wt_range,product,id_design,id_sub_design,size,min_pcs,max_pcs,id);

	}

});

// $(document).on('click', "#reorder_set_list a.btn-edit", function(e) {

// 		e.preventDefault();

// 		id=$(this).data('id');

// 	    $("#edit-id").val(id);

// 	    getReorderDetails(id);

// });

     function get_branch_details(){

         $('#total_items tbody ').empty();

     	$('#ed_branch_select option').remove();

     	$('#branch_select option').remove();

         	$.ajax({

             	type: 'GET',

             	url: base_url+'index.php/branch/branchname_list',

             	dataType:'json',

             	success:function(data){

             	    if($("#edit-id").val()!='')

             	    {

                        var id_branch =  $('#id_branch').val();

                        var branch =  $('#branch_select').val();

                        $.each(data.branch, function (key, item) {

                        $("#ed_branch_select").append(

                        $("<option></option>")

                        .attr("value", item.id_branch)

                        .text(item.name )

                        );

                        });

                        $("#ed_branch_select").select2({

                        placeholder: "Select Branch",

                        allowClear: true,

						width: '100%'

                        });

                        $("#ed_branch_select").select2("val",(id_branch!='' && id_branch>0?id_branch:''));

                        $.each(data.branch, function (key, item) {

                        $("#branch_select").append(

                        $("<option></option>")

                        .attr("value", item.id_branch)

                        .text(item.name )

                        );

                        });

                        $("#branch_select").select2({

                        placeholder: "Select Branch",

                        allowClear: true,

						width: '100%'

                        });

                        if($('#branch_select').length)

                        {

                            $("#branch_select").select2("val",(branch!='' && branch>0?branch:''));

                        }

             	    }else{

             	        var row='';

             	        $.each(data.branch,function(key,item){

             	            if(item.branch_type==1)

             	            {

             	                 row+='<tr>'

                 	            +'<td>'+item.name+'<input type="hidden" class="id_branch"  name="pieces['+key+'][id_branch]" value='+item.id_branch+'></td>'

                 	            +'<td><input type="number" class="min_pcs" name="pieces['+key+'][min_pcs]" value="" placeholder="Enter Min Pcs"></td>'

                 	            +'<td><input type="number" class="max_pcs" name="pieces['+key+'][max_pcs]"  value="" placeholder="Enter Max Pcs"></td>'

                 	            +'</tr>';

             	            }

             	        });

             	        $('#total_items tbody ').append(row);

             	    }

             	}

            });

        }

function getReorderDetails(id) {

    my_Date = new Date();

    $.ajax({

        url: base_url + "index.php/admin_ret_catalog/reorder_settings/edit_reorder/" + id + "?nocache=" + my_Date.getUTCSeconds(),

        type: "GET",

        dataType: 'json',

        cache: false,

        success: function (data) {

            console.log(data);

            console.log(data.reorder);

            if (data.reorder.length > 0) {

                $.each(data.reorder, function (key, item) {

					$("#product_select_reorder").val(item.id_product).trigger('change.select2');

					$("#des_select_reorder").val(item.id_design).trigger('change.select2');

				 get_reorder(item.id_product, item.id_branch, item.id_section, item.id_sub_design, item.id_weight, item.id_size);

				 $("#product_select_reorder").val(item.id_product);

				 $("#des_select_reorder").val(item.id_design);

                    var select_branch = "";

                    $.each(reorder_branch, function (key, ditem) {

                        var selected = "";

                        if (ditem.id_branch == item.id_branch) {

                            selected = "selected";

                        }

                        select_branch += "<option value='" + ditem.id_branch + "' " + selected + ">" + ditem.name + "</option>";

                    });

				   	var select_section = "";

				    	console.log(section_details);

					   console.log(data.section);

					 	$.each(data.section, function (key, ditem) {

						var selected = "";

						if (ditem.id_section == item.id_section) {

							selected = "selected"

						 }

							select_section += "<option value='" + ditem.id_section + "' " + selected + ">" + ditem.section_name + "</option>";

						 });

							var select_sub_design = "";

							$.each(reorderitems_sub_design, function (key, ditem) {

								var selected = "";

								if (ditem.id_sub_design == item.id_sub_design) {

										selected = "selected"

									}

									select_sub_design += "<option value='" + ditem.id_sub_design + "' " + selected + ">" + ditem.sub_design_name + "</option>";

							});

							var select_weight_range = "";

							$.each(data.weight, function (key, ditem) {

								var selected = "";

								if (ditem.id_weight == item.id_weight) {

										selected = "selected"

									}

									select_weight_range += "<option value='" + ditem.id_weight + "' " + selected + ">" + ditem.value + "</option>";

							});

							var select_size = "";

							$.each(data.size, function (key, ditem) {

								var selected = "";

								if(ditem.id_size == item.id_size) {

										selected = "selected"

									}

								select_size += "<option value='" + ditem.id_size + "' " + selected + ">" + ditem.name + "</option>";

						 	});

					  var delete_url=(id!=''? base_url+'index.php/admin_ret_catalog/reorder_settings/delete/'+item.id_reorder_settings: '#' );

					  var newRow = $('<tr id="detail' + key + '" class="' + key + '">'

					                    + '<td><select class="form-control reorder_section_select" name="pieces[' + key + '][id_section]" style="width: 100%;">' + select_section + '</select><input type="hidden" id="id_reorder_settings"  name="pieces[' + key + '][id_reorder_settings]"  value=' + item.id_reorder_settings + '><input type="hidden" id="id_section" name=""  value=' + item.id_section + '></td>'

					                    + '<td><select class="form-control reorder_branch_select" name="pieces[' + key + '][id_branch][]" style="width: 80%"  >' + select_branch + '</select><input type="hidden" id="id_branch" name="" value=' + item.id_branch + '></td>'

					                    + '<td><select class="form-control reorder_sub_design" name="pieces[' + key + '][id_sub_design][]" style="width: 100%" >' + select_sub_design + '</select><input type="hidden" id="id_sub_design" name="" value=' + item.id_sub_design + '></td>'

					                    + '<td><select class="form-control reorder_wt_select" name="pieces[' + key + '][weight_range]" style="width: 100%;">' + select_weight_range + '</select><input type="hidden" id="wt_range" name="" value=' + item.id_weight + '></td>'

					                    + '<td><select class="form-control reorder_size" name="pieces[' + key + '][id_size]" style="width: 100%;">' + select_size + '</select><input type="hidden" id="size" name="" value=' + item.id_size + '></td>'

					                    + '<td><input type="number" class="form-control min_pcs" name="pieces[' + key + '][min_pcs]" value=' + item.min_pcs + ' placeholder="Enter Min Pcs"></td>'

					                    + '<td><input type="number" class="form-control max_pcs" name="pieces[' + key + '][max_pcs]" value=' + item.max_pcs + ' placeholder="Enter Max Pcs"></td>'

										+ '<td> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a></td>'

					                    // + '<td><a href="#" data-href='+delete_url+' data-target="#confirm-delete" class="btn btn-danger btn-del small-btn" ><i class="fa fa-trash"></i></a></td>'

					                    + '</tr>');

						   	newRow.appendTo('#total_reorder_items tbody');

                    newRow.find('.reorder_branch_select').select2();

                    newRow.find('.reorder_section_select').select2();

                    newRow.find('.reorder_sub_design').select2();

                	newRow.find('.reorder_wt_select').select2({

						placeholder: "Weight Range",

						allowClear: true

					}).val(item.id_weight).trigger('change');

					newRow.find('.reorder_size').select2({

						placeholder: "Size",

						allowClear: true

					}).val(item.id_size).trigger('change');

                });

            }

        }

    });

}

function get_reorder(id_product,id_branch,id_section,id_sub_design,id_weight,id_size)

{

	// get_ProductforReorder();

	// get_designforReorder(id_product);

	// get_reorder_sub_designs();

	get_wtrange_forReoreder('',id_product);

	// get_Activesize_forReorder(id_product);

}

function add_retsettings(id_branch,wt_range,product,id_design,size,min_pcs,max_pcs)

{

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		data:{'id_branch':id_branch,'wt_range':wt_range,'product':product,'id_design':id_design,'size':size,'min_pcs':min_pcs,'max_pcs':max_pcs},

		url: base_url+"index.php/admin_ret_catalog/reorder_settings/add?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		dataType: 'json',

		cache:false,

		success:function(data){

		   	$("#myform").trigger('reset');

		    $('#confirm-add').modal('toggle');

		    get_reorder_settings();

			//window.location.reload();

			$("div.overlay").css("display", "none");

		}

	});

}

function add_retsettings_new()

{

var form_data=$('#add_reorder').serialize();

	my_Date = new Date();

	$.ajax({

	data: form_data,

		url: base_url+"index.php/admin_ret_catalog/reorder_settings/add?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		dataType: 'json',

		cache:false,

		success:function(data){

			if(data.status)

		    {

		        if($('#branch_settings').val()==1)

		        {

		            $('#total_items > tbody > tr').each(function(index, tr) {

                         $(this).find('.min_pcs').val('');

                         $(this).find('.max_pcs').val('');

                     });

		        }

                $('#wt_select').select2("val",'');

		        msg='<div class = "alert alert-success"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Success!</strong>'+data.msg+'</div>';

		    }else

		    {

		       	msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Success!</strong>'+data.msg+'</div>';

		    }

			$('#chit_alert').html(msg);

		}

	});

}

function update_retsettings(id_branch,wt_range,product,id_design,id_sub_design,size,min_pcs,max_pcs,id)

{

	my_Date = new Date();

	$.ajax({

		data:{'id':id,'id_branch':$('#ed_branch_select').val(),'wt_range':wt_range,'product':product,'id_design':id_design,'id_sub_design':id_sub_design,'size':size,'min_pcs':min_pcs,'max_pcs':max_pcs},

		url: base_url+"index.php/admin_ret_catalog/reorder_settings/update?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		dataType: 'json',

		cache:false,

		success:function(data){

		    $('#confirm-edit').modal('toggle');

		    $("#ed_branch_select").remove();

		    $('#ed_branch_select').val('');

		    $('#wt_select').val('');

		    $('#prod_select').val('');

		    $('#des_select').val('');

		    $('#size').val('');

		    $('#min_pcs').val('');

		    $('#max_pcs').val('');

			window.location.reload();

		}

	});

	$("div.overlay").css("display", "none");

}

/*function get_wt_range_product(id_product,id_design)

{

    my_Date = new Date();

	$('#wt_select option').remove();

	$('#ed_wt_select option').remove();

	$.ajax({

	type: 'POST',

	url: base_url+'index.php/admin_ret_catalog/reorder_settings/weight_range?nocache='+ my_Date.getUTCSeconds(),

	dataType:'json',

	data:{'id_product':id_product,'id_design':id_design},

	success:function(data){

		var id =  $("#wt_range").val();

		var ed_wt_range =  $("#ed_wt_range").val();

		$.each(data, function (key, item) {

		    $("#wt_select,#ed_wt_select").append(

		    $("<option></option>")

		    .attr("value", item.id_weight)

		    .text(item.value)

		    );

		});

		$("#wt_select,#ed_wt_select").select2(

		{

			placeholder:"Select Weight Range",

			allowClear: true

		});

		    $("#wt_select").select2("val",(id!='' && id>0?id:''));

		    $("#ed_wt_select").select2("val",(ed_wt_range!='' && ed_wt_range>0?ed_wt_range:''));

		}

	});

}*/

$('#select_sub_design,#ed_sub_des_select').on('change',function(){

	if(this.value!='')

	{

		get_wt_range_product();

	}

});

function get_wt_range_product()

{

    my_Date = new Date();

	$('#wt_select option').remove();

	$('#ed_wt_select option').remove();

	$.ajax({

	type: 'POST',

	url: base_url+'index.php/admin_ret_catalog/reorder_settings/weight_range?nocache='+ my_Date.getUTCSeconds(),

	dataType:'json',

	data:{'id_product':$('#product_select').val() || $('#ed_prod_select').val(),'id_design':$('#des_select').val() || $('#ed_des_select').val(),'id_sub_design':$('#select_sub_design').val() || $('#ed_sub_des_select').val()},

	success:function(data){

		var id =  $("#wt_range").val();

		var ed_wt_range =  $("#ed_wt_range").val();

		$.each(data, function (key, item) {

		    $("#wt_select,#ed_wt_select").append(

		    $("<option></option>")

		    .attr("value", item.id_weight)

		    .text(item.value)

		    );

		});

		$("#wt_select,#ed_wt_select").select2(

		{

			placeholder:"Select Weight Range",

			allowClear: true

		});

		    $("#wt_select").select2("val",(id!='' && id>0?id:''));

		    $("#ed_wt_select").select2("val",(ed_wt_range!='' && ed_wt_range>0?ed_wt_range:''));

		}

	});

}

$('#des_select,#ed_des_select').on('change',function(){

    var id_product=$('#ed_product').val();

    if(this.value!='')

    {

        get_wt_range_product();

    }

     if(this.value!='')

    {

        get_active_sub_designs();

    }

});

function get_active_sub_designs()

{

    $('#select_sub_design option').remove();

    	$.ajax({

    	type: 'POST',

    	url: base_url+'index.php/admin_ret_catalog/get_ActiveSubDesigns',

    	data :{'id_product':$('#product_select').val(),'design_no':$('#des_select').val()},

    	dataType:'json',

    	success:function(data){

        		$.each(data, function (key, item) {

        		    $("#select_sub_design").append(

        		    $("<option></option>")

        		    .attr("value", item.id_sub_design)

        		    .text(item.sub_design_name)

        		    );

        		});

        		$("#select_sub_design").select2(

        		{

        			placeholder:"Select Design",

        			allowClear: true

        		});

        		$("#select_sub_design").select2("val",'');

    		}

    	});

}

function get_design_product(id_product)

{

	$('#des_select option').remove();

	$.ajax({

	type: 'POST',

	url: base_url+'index.php/admin_ret_catalog/reorder_settings/active_design',

	data :{'id_product':id_product},

	dataType:'json',

	success:function(data){

		var id =  $("#id_design").val();

		var edit_id =  $("#ed_id_design").val();

		$.each(data, function (key, item) {

		    $("#des_select,#ed_des_select").append(

		    $("<option></option>")

		    .attr("value", item.design_no)

		    .text(item.design_name)

		    );

		});

		$("#des_select,#ed_des_select").select2(

		{

			placeholder:"Select Design",

			allowClear: true

		});

		    $("#des_select").select2("val",'');

		    $("#ed_des_select").select2("val",(edit_id!='' && edit_id>0?edit_id:''));

		}

	});

}

$('#ed_wt_range_prod').on('change',function(){

    if(this.value!='')

    {

        get_weight_range_design(this.value);

    }

});

function product_design(id_product)

{

	$('#wt_range_des option').remove();

	$('#ed_wt_range_des option').remove();

	$.ajax({

	type: 'POST',

	url: base_url+'index.php/admin_ret_catalog/reorder_settings/active_design',

	data :{'id_product':id_product},

	dataType:'json',

	success:function(data){

		var id =  $("#id_design").val();

		var edit_id =  $("#ed_id_design").val();

		var ed_wt_range_des =  $("#ed_wt_des").val();

		console.log(ed_wt_range_des);

		$.each(data, function (key, item) {

		    $("#wt_range_des,#ed_wt_range_des").append(

		    $("<option></option>")

		    .attr("value", item.design_no)

		    .text(item.design_name)

		    );

		});

		$("#wt_range_des,#ed_wt_range_des").select2(

		{

			placeholder:"Select Design",

			allowClear: true

		});

		 $("#ed_wt_range_des").select2("val",(ed_wt_range_des!='' && ed_wt_range_des>0?ed_wt_range_des:''));

		    if(id!='' && id!=null && id!=undefined)

		    {

		        $("#wt_range_des").select2("val",(id!='' && id>0?id:''));

		    }

		    else if(edit_id!='' && edit_id!=null && edit_id!=undefined)

		    {

		         $("#wt_range_des").select2("val",(edit_id!='' && edit_id>0?edit_id:''));

		    }else if(ed_wt_range_des!='' && ed_wt_range_des!=null && ed_wt_range_des!=undefined)

		    {

		         //$("#ed_wt_range_des").select2("val",(ed_wt_range_des!='' && ed_wt_range_des>0?ed_wt_range_des:''));

		    }

		}

	});

}

$('#prod_select,#ed_prod_select').on('change',function(){

	if(this.value!='')

	{

		$('#product').val(this.value);

		$('#ed_product').val(this.value);

		get_design_product(this.value);

		$('#ed_wt_select option').remove();

		get_wt_range_product();

	}else{

		$('#product').val('');

	}

});

$('#ed_wt_select').on('change',function(){

	if(this.value!='')

	{

		$('#ed_wt_range').val(this.value);

	}else{

		$('#ed_wt_range').val('');

	}

});

$('#ed_des_select').on('change',function(){

	if(this.value!='')

	{

		$('#ed_id_design').val(this.value);

		get_ActiveSubDesingns();

	}else{

		$('#ed_id_design').val('');

	}

});

//Re-Order settings

function get_delivery_details()

{

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		url: base_url+"index.php/admin_ret_catalog/ret_delivery/ajax?nocache=" + my_Date.getUTCSeconds(),

		type:"GET",

		dataType: 'json',

		cache:false,

		success:function(data){

		   		var list 	= data.delivery;

				var access	    = data.access;

				if(access.add == '0')

				{

					$('#add_wt').attr('disabled','disabled');

				}

			 $("div.overlay").css("display","add_newstone");

			 var oTable = $('#delivery_list').DataTable();

			 oTable.clear().draw();

			 if (list!= null && list.length > 0)

			 {

				oTable = $('#delivery_list').dataTable({

						"bDestroy": true,

						"bInfo": true,

						"bFilter": true,

						"bSort": true,

						"order": [[ 0, "desc" ]],

						"aaData"  : list,

						"aoColumns": [

						{ "mDataProp": "id_sale_delivery" },

						{ "mDataProp": "name" },

						{ "mDataProp": function ( row, type, val, meta ){

						active_url =base_url+"index.php/admin_ret_catalog/update_location/"+(row.is_default==0?1:0)+"/"+row.id_sale_delivery;

						return "<a href='"+active_url+"'><i class='fa "+(row.is_default==1?'fa-check':'fa-remove')+"' style='color:"+(row.is_default==1?'green':'red')+"'></i></a>"

						}

						},

						{ "mDataProp": function ( row, type, val, meta ) {

							 id= row.id_sale_delivery;

							 edit_target=(access.edit=='0'?"":"#confirm-edit");

							 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/ret_delivery/delete/'+id : '#' );

							 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');   

							 action_content=(access.edit == '1'? '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a>':'')+ (access.delete =='1'? '<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>':'')

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

 $("#add_newdelivery").on('click',function(){

	if($("#name").val() != '')

	{

	add_location($('#name').val());

	$('#name').val('');

	}

	else if($("#name").val() == ''){

	msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter location.</div>';

	$("div.overlay").css("display", "none");

	$('#error-msg').html(msg);

	return false;

	}

});

 $("#update_delivery").on('click',function(){

	var purity=($("#ed_name").val());

	var id=$("#edit-id").val();

	if($("#ed_name").val() != '')

	{

	update_delivery(purity,id);

	}

	else if($("#ed_name").val() == ''){

	msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter location.</div>';

	$("div.overlay").css("display", "none");

	$('#error').html(msg);

	return false;

			}

});

 $(document).on('click', "#delivery_list a.btn-edit", function(e) {

		$("#ed_name").val('');

		e.preventDefault();

		id=$(this).data('id');

	    get_delivery(id);

	    $("#edit-id").val(id);

	});

function add_location(location)

{

	my_Date = new Date();

	$.ajax({

		data:{"name":location},

		url: base_url+"index.php/admin_ret_catalog/ret_delivery/Add?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		cache:false,

		success:function(data){

			$('#name').val('');

			window.location.reload(true);

		}

	});

}

function update_delivery(name,id)

{

	 $("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		data:{"name":name},

		url: base_url+"index.php/admin_ret_catalog/ret_delivery/Update/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		cache: false,

		success:function(){

				 $("div.overlay").css("display", "none");

			location.reload(true);

		}

	});

}

function get_delivery(id)

{

   my_Date = new Date();

	$.ajax({

		type:"GET",

		url: base_url+"index.php/admin_ret_catalog/ret_delivery/Edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		cache:false,

		dataType:"JSON",

		success:function(data){

			console.log(data.name);

		    $('#ed_name').val(data.name);

		}

	});

}

// . KarthikB

function get_Activesize(id_product)

{

	$('#select_size option').remove();

    $.ajax({

        type: 'POST',

        url: base_url+'index.php/admin_ret_catalog/get_Activesize',

        dataType:'json',

        data:{'id_product':id_product},

        success:function(data){

        var size = $('#select_size').val();

        var id_size = $('#ed_id_size').val();

        $.each(data, function (key, item) {

        $("#select_size,#ed_select_size").append(

        $("<option></option>")

        .attr("value", item.id_size)

        .text(item.name )

        );

        });

        $("#select_size,#ed_select_size").select2({

        placeholder: "Select Size",

        allowClear: true

        });

        if($("#select_size").length)

        {

            $("#select_size").select2("val",'');

        }

        if(id_size!='' && id_size!=undefined)

        {

        	$("#ed_select_size").select2("val",(id_size!='' && id_size>0?id_size:''));

        }

        }

    });

}

//size

$('#add_size').on('click',function(){

	get_Activeproduct('');

});

 $("#add_new_size").on('click',function(){

	 if($("#product").val() == ''){

		msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Select product.</div>';

		$("div.overlay").css("display", "none");

		$('#error-msg').html(msg);

		return false;

		}

	else if($("#size").val() == ''){

	msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Size.</div>';

	$("div.overlay").css("display", "none");

	$('#error-msg').html(msg);

	return false;

	}

	else if($("#units").val() == ''){

		msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Units.</div>';

		$("div.overlay").css("display", "none");

		$('#error-msg').html(msg);

		return false;

		}

		else

		{

			add_newsize($('#product').val(),$('#size').val(),$('#units').val());

		}

});

$("#new_add_size").on('click',function(){

	 if($("#product").val() == ''){

		msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Select product.</div>';

		$("div.overlay").css("display", "none");

		$('#error-msg').html(msg);

		return false;

		}

	else if($("#size").val() == ''){

	msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Size.</div>';

	$("div.overlay").css("display", "none");

	$('#error-msg').html(msg);

	return false;

	}

	else if($("#units").val() == ''){

		msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Units.</div>';

		$("div.overlay").css("display", "none");

		$('#error-msg').html(msg);

		return false;

	}

		else{

			new_add_size($('#product').val(),$('#size').val(),$('#units').val());

		}

});

function add_newsize(id_product,size,units)

{

	my_Date = new Date();

	$.ajax({

		data:{"id_product":id_product,"size":size,"unit":units},

		url: base_url+"index.php/admin_ret_catalog/ret_size/Add?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		dataType:"JSON",

		cache:false,

	 	success:function(data){

			$('#name').val('');

			$('#units').val('');

			$('#size').val('');

			// $('#product_sel').select2('val','');

		 	var msg = '<div class="alert alert-' + data.class + '"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>' + data.title + '</strong> ' + data.message + ' </div>';

			$('#chit_alert').html(msg);

		}

	});

}

function new_add_size(id_product,size,units)

{

	my_Date = new Date();

	$.ajax({

		data:{"id_product":id_product,"size":size,"unit":units},

		url: base_url+"index.php/admin_ret_catalog/ret_size/Add?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		dataType:"JSON",

		cache:false,

	 	success:function(data){

			$('#name').val('');

			$('#units').val('');

			$('#size').val('');

			// $('#product_sel').select2('val','');

		 	var msg = '<div class="alert alert-' + data.class + '"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>' + data.title + '</strong> ' + data.message + ' </div>';

			$('#error-msg').html(msg);

		}

	});

}

function get_size_details()

{

	var branch_name = ($('#branch_name').val() != '' && $('#branch_name').val() != undefined ? $('#branch_name').val() : $('#branch_select option:selected').toArray().map(item => item.text).join());

	var company_name = $('#company_name').val();

	var company_code = $('#company_code').val();

	var company_code = $('#company_code').val();

	var company_address1 = $('#company_address1').val();

	var company_address2 = $('#company_address2').val();

	var company_city = $('#company_city').val();

	var pincode = $('#pincode').val();

	var company_email = $('#company_email').val();

	var company_gst_number = $('#company_gst_number').val();

	var phone = $('#phone').val();

	var title = "<div style='text-align: center;'><b><span style='font-size:12pt;'>" + company_code + "</span></b></br>"

		+ "<span style='font-size:11pt;'>" + company_address1 + "</span></br>"

		+ "<span style='font-size:11pt;'>" + company_address2 + company_city + "-" + pincode + "</span></br>"

		+ "<span style='font-size:11pt;'>GSTIN:" + company_gst_number + ", EMAIL:" + company_email + "</span></br>"

		+ "<span style='font-size:11pt;'>Contact :" + phone + "</span></br>"

		+ "<span style=font-size:12pt;>&nbsp;&nbsp;Size - &nbsp;&nbsp; "+ $('.hidden-xs').html() + "</span>";

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		url: base_url+"index.php/admin_ret_catalog/ret_size/ajax?nocache=" + my_Date.getUTCSeconds(),

		type:"GET",

		dataType: 'json',

		cache:false,

		success:function(data){

		   		var list 	= data.size;

				var access	    = data.access;

				if(access.add == '0')

				{

					$('#add_size').attr('disabled','disabled');

				}

			 $("div.overlay").css("display","add_newstone");

			 var oTable = $('#size_list').DataTable();

			 oTable.clear().draw();

			 if (list!= null && list.length > 0)

			 {

				oTable = $('#size_list').dataTable({

						"bDestroy": true,

						"bInfo": true,

						"bFilter": true,

						"bSort": true,

						"order": [[ 0, "desc" ]],

						"aaData"  : list,

						"dom": 'lBfrtip',

						"buttons": [

							{

								extend: 'print',

								footer: true,

								title: '',

								messageTop: title,

								customize: function (win) {

									$(win.document.body).find('table')

										.addClass('compact')

										.css('font-size', 'inherit');

								},

							},

							{

								extend: 'excel',

								footer: true,

							}

						],

						// "buttons" : ['excel','print'],

			            // "tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },

						"aoColumns": [

						{ "mDataProp": "id_size" },

						{ "mDataProp": "product_name" },

						{ "mDataProp": "value" },

						{ "mDataProp": "name" },

						{ "mDataProp": function ( row, type, val, meta ){

						active_url =(access.edit =='1')?base_url+"index.php/admin_ret_catalog/update_size_status/"+(row.active==0?1:0)+"/"+row.id_size:'#';

						return "<a href='"+active_url+"'><i class='fa "+(row.active==1?'fa-check':'fa-remove')+"' style='color:"+(row.active==1?'green':'red')+"'></i></a>"

						}

						},

						{ "mDataProp": function ( row, type, val, meta ) {

							 id= row.id_size;

							 edit_target=(access.edit=='0'?"":"#confirm-edit");

							 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/ret_size/delete/'+id : '#' );

							 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

							//  action_content='<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

							

							var action_content=''

							if(access.edit == '1'){

													   

							action_content+=  '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a>' ;

							   

							}

							

							if(access.delete == '1'){

								

								action_content+='<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

							   

							}

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

$(document).on('click', "#size_list a.btn-edit", function(e) {

		$("#ed_name").val('');

		e.preventDefault();

		id=$(this).data('id');

	    get_size(id);

	    $("#edit-id").val(id);

	});

function get_size(id)

{

   my_Date = new Date();

	$.ajax({

		type:"GET",

		url: base_url+"index.php/admin_ret_catalog/ret_size/Edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		cache:false,

		dataType:"JSON",

		success:function(data){

			console.log(data.name);

		    $('#size_product').val(data.id_product);

		    $('#ed_units').val(data.name);

		    $('#ed_size').val(data.value);

		    setTimeout(function(){

		    	get_Activeproduct('');

		    },1000);

		}

	});

}

 $("#update_size").on('click',function(){

	var id_product=($("#ed_size_product").val());

	var size 	=($("#ed_size").val());

	var units 	=($("#ed_units").val());

	var id=$("#edit-id").val();

	if($("#id_product").val() != '')

	{

	update_size(id_product,size,units,id);

	}

	else if(id_product == '')

	{

			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill Required Fields'});

			return false;

	}

});

 function update_size(id_product,size,units,id)

{

	 $("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		data:{"id_product":id_product,"size":size,"units":units},

		url: base_url+"index.php/admin_ret_catalog/ret_size/Update/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		cache: false,

		success:function(){

				 $("div.overlay").css("display", "none");

			location.reload(true);

		}

	});

}

//size

//Old Metal Type

function get_metal_type_list()

{

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		url: base_url+"index.php/admin_ret_catalog/metal_type/ajax?nocache=" + my_Date.getUTCSeconds(),

		type:"GET",

		dataType: 'json',

		cache:false,

		success:function(data){

		   		var list 	= data.list;

				var access	    = data.access;

				// if(access.add == '0')

				// {

				// 	$('#add_metal').attr('disabled','disabled');

				// }

			 $("div.overlay").css("display","add_newstone");

			 var oTable = $('#metal_list').DataTable();

			 oTable.clear().draw();

			 if (list!= null && list.length > 0)

			 {

				oTable = $('#metal_list').dataTable({

						"bDestroy": true,

						"bInfo": true,

						"bFilter": true,

						"bSort": true,

						"order": [[ 0, "desc" ]],

						"aaData"  : list,

						"aoColumns": [

						{ "mDataProp": "id_metal_type" },

						{ "mDataProp": "metal" },

						{ "mDataProp": "metal_type" },

						{ "mDataProp": function ( row, type, val, meta ) {

							 id= row.id_metal_type;

							 edit_target=(access.edit=='0'?"":"#confirm-edit");

							 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/metal_type/delete/'+id : '#' );

							 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

							 action_content=(access.edit =='1' ? '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a>':'') +(access.delete == '1' ? '<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>':'')

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

$('#add_metal_type').on('click',function(){

    if($('#metal_type').val()=='')

    {

         msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Success!</strong>Enter The Metal Type.</div>';

		 $('#chit_alert1').html(msg);

    }

    else if($('#metal_sel').val()=='' || $('#metal_sel').val()==null)

    {

         msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Success!</strong>Select Metal Type</div>';

		 $('#chit_alert1').html(msg);

    }

    else

    {

        var form_data=$('#metal_crerate').serialize();

        my_Date = new Date();

        $.ajax({

        data:form_data,

        url: base_url+"index.php/admin_ret_catalog/metal_type/add/?nocache=" + my_Date.getUTCSeconds(),

        type:"POST",

        cache: false,

        success:function(){

            $("div.overlay").css("display", "none");

            //location.reload(true);

        }

        });

    }

});

$('#add_new_metal_type').on('click',function(){

    if($('#metal_type').val()=='')

    {

         msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Success!</strong>Enter The Metal Type.</div>';

		 $('#chit_alert1').html(msg);

    }

    else if($('#metal_sel').val()=='' || $('#metal_sel').val()==null)

    {

         msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Success!</strong>Select Metal Type</div>';

		 $('#chit_alert1').html(msg);

    }

    else

    {

        var form_data=$('#metal_crerate').serialize();

        my_Date = new Date();

        $.ajax({

        data:form_data,

        url: base_url+"index.php/admin_ret_catalog/metal_type/add/?nocache=" + my_Date.getUTCSeconds(),

        type:"POST",

        cache: false,

        success:function(){

            $("div.overlay").css("display", "none");

            location.reload(true);

        }

        });

    }

});

$(document).on('click', "#metal_list a.btn-edit", function(e) {

		$("#ed_name").val('');

		e.preventDefault();

		id=$(this).data('id');

	    get_old_metal_type(id);

	    $("#id_metal_type").val(id);

});

function get_old_metal_type(id)

{

   my_Date = new Date();

	$.ajax({

		type:"GET",

		url: base_url+"index.php/admin_ret_catalog/metal_type/edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		cache:false,

		dataType:"JSON",

		success:function(data){

			console.log(data.name);

		    $('#ed_metal_name').val(data.metal_type);

		    $('#metal_id').val(data.id_metal);

		    $('#metal_category').select2("val",data.id_metal);

		    setTimeout(function(){

		    	get_ActiveMetal();

		    },1000);

		}

	});

}

$('#update_metal_type').on('click',function(){

     var form_data=$('#metal_update').serialize();

        my_Date = new Date();

        $.ajax({

        data:form_data,

        url: base_url+"index.php/admin_ret_catalog/metal_type/update/?nocache=" + my_Date.getUTCSeconds(),

        type:"POST",

        cache: false,

        success:function(){

            $("div.overlay").css("display", "none");

            location.reload(true);

        }

        });

});

//Old Metal Type

//Old Metal Category

function get_old_metal_cat_list()

{

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		url: base_url+"index.php/admin_ret_catalog/old_metal_cat/ajax?nocache=" + my_Date.getUTCSeconds(),

		type:"GET",

		dataType: 'json',

		cache:false,

		success:function(data){

		   		var list 	= data.list;

				var access	    = data.access;

				if(access.add == '0')

				{

					$('#add_old_metal_cat').attr('disabled','disabled');

				}

			 //$("div.overlay").css("display","add_newstone");

			 var oTable = $('#old_metal_cat_list').DataTable();

			 oTable.clear().draw();

			 if (list!= null && list.length > 0)

			 {

				oTable = $('#old_metal_cat_list').dataTable({

						"bDestroy": true,

						"bInfo": true,

						"bFilter": true,

						"bSort": true,

						"order": [[ 0, "desc" ]],

						"aaData"  : list,

						"aoColumns": [

						{ "mDataProp": "id_old_metal_cat" },

						{ "mDataProp": "metal" },

						{ "mDataProp": "metal_type" },

						{ "mDataProp": "old_metal_cat" },

						{ "mDataProp": "old_metal_perc" },

						{ "mDataProp": "old_metal_discount" },

						{ "mDataProp": function ( row, type, val, meta ) {

							 id= row.id_old_metal_cat;

							 edit_target=(access.edit=='0'?"":"#confirm-edit");

							 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/old_metal_cat/delete/'+id : '#' );

							 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

							 action_content=(access.edit == '1' ? '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a>':'') +(access.delete == '1' ?'<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>':"")

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

$('#add_new_old_metal_category').on('click',function(){

    if($('#old_metal_cat').val()=='')

    {

         msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Success!</strong>Enter The Old Metal Category.</div>';

		 $('#chit_alert1').html(msg);

    }

	else if($('old_metal_perc').val()=='')

	{

		msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Success!</strong>Enter The Old Metal Percentage.</div>';

		$('#chit_alert1').html(msg);

	}

    else if($('#id_old_metal_type').val()=='' || $('#id_old_metal_type').val()==null)

    {

         msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Success!</strong>Select Old Metal Type</div>';

		 $('#chit_alert1').html(msg);

    }

    else

    {

        var form_data=$('#old_metal_cat_create').serialize();

        my_Date = new Date();

        $.ajax({

        data:form_data,

        url: base_url+"index.php/admin_ret_catalog/old_metal_cat/add/?nocache=" + my_Date.getUTCSeconds(),

        type:"POST",

        cache: false,

        success:function(){

            $("div.overlay").css("display", "none");

			$("#old_metal_cat").val('');

			$("#old_metal_perc").val('');

			$("#id_old_metal_type").val('');

			get_old_metal_cat_list();

        }

        });

    }

});

$('#add_old_metal_category').on('click',function(){

    if($('#old_metal_cat').val()=='')

    {

         msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Success!</strong>Enter The Old Metal Category.</div>';

		 $('#chit_alert1').html(msg);

    }

	else if($('old_metal_perc').val()=='')

	{

		msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Success!</strong>Enter The Old Metal Percentage.</div>';

		$('#chit_alert1').html(msg);

	}

    else if($('#id_old_metal_type').val()=='' || $('#id_old_metal_type').val()==null)

    {

         msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Success!</strong>Select Old Metal Type</div>';

		 $('#chit_alert1').html(msg);

    }

    else

    {

        var form_data=$('#old_metal_cat_create').serialize();

        my_Date = new Date();

        $.ajax({

        data:form_data,

        url: base_url+"index.php/admin_ret_catalog/old_metal_cat/add/?nocache=" + my_Date.getUTCSeconds(),

        type:"POST",

        cache: false,

        success:function(){

            $("div.overlay").css("display", "none");

            location.reload(true);

        }

        });

    }

});

$(document).on('click', "#old_metal_cat_list a.btn-edit", function(e) {

		$("#ed_old_metal_cat").val('');

		$("#ed_old_metal_perc").val('');

		$("#ed_id_old_metal_type").val('');

		e.preventDefault();

		id=$(this).data('id');

	    get_old_metal_category(id);

	    $("#id_old_metal_cat").val(id);

});

function get_old_metal_category(id)

{

   my_Date = new Date();

	$.ajax({

		type:"GET",

		url: base_url+"index.php/admin_ret_catalog/old_metal_cat/edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		cache:false,

		dataType:"JSON",

		success:function(data){

			console.log(data.name);

		    $('#ed_old_metal_cat').val(data.old_metal_cat);

		    $('#ed_old_metal_perc').val(data.old_metal_perc);

			$('#ed_old_metal_discount').val(data.old_metal_discount);

		    $('#ed_id_old_metal_type').select2("val",data.id_old_metal_type);

		    setTimeout(function(){

		    	get_ActiveMetal();

		    },1000);

		}

	});

}

$('#update_old_metal_cat').on('click',function(){

     var form_data=$('#old_metal_cat_update').serialize();

        my_Date = new Date();

        $.ajax({

        data:form_data,

        url: base_url+"index.php/admin_ret_catalog/old_metal_cat/update/?nocache=" + my_Date.getUTCSeconds(),

        type:"POST",

        cache: false,

        success:function(){

            $("div.overlay").css("display", "none");

            location.reload(true);

        }

        });

});

function get_ActiveOldMetal(){

	$.ajax({

	type: 'GET',

	url: base_url+'index.php/admin_ret_catalog/old_metal_cat/active_oldmetal',

	dataType:'json',

	success:function(data){

		var id =  $("#metal_id").val();

		$.each(data, function (key, item) {

		   $("#id_old_metal_type,#ed_id_old_metal_type").append(

		   $("<option></option>")

		   .attr("value", item.id_metal_type)

		   .text(item.metal_type)

		   );

		});

		$("#id_old_metal_type,#ed_id_old_metal_type").select2(

		{

		placeholder:"Select old metal",

		allowClear: true

		});

		   $("#id_old_metal_type,#ed_id_old_metal_type").select2("val",(id!='' && id>0?id:''));

		   $(".overlay").css("display", "none");

		}

	});

}

//Old Metal Category

//Old Metal Rate

function get_old_metal_rate()

{

     var my_Date = new Date();

	$.ajax({

		 url:base_url+"index.php/admin_ret_catalog/old_metal_rate?nocache=" + my_Date.getUTCSeconds(),

		 dataType:"JSON",

		 type:"post",

		 success:function(data){

		     var list=data.list;

		     var access=data.access;

		 	var oTable = $('#old_metal_list').DataTable();

	  		oTable = $('#old_metal_list').dataTable({

			"bDestroy": true,

			"bInfo": true,

			"bFilter": true,

			"bSort": true,

			"dom": 'lBfrtip',

			 "pageLength": 100,

			"order": [[ 0, "desc" ]],

			"buttons" : ['excel','print'],

			"tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },

			"aaData": list,

			"tabIndex": 2,

		     "aoColumns": [

    						{ "mDataProp": "id_old_metal_rate" },

    						{ "mDataProp": "date_add" },

    						{ "mDataProp": "metal" },

    						{ "mDataProp": "rate"},

    						{ "mDataProp": "status"},

    						{ "mDataProp": "emp_created"},

    						{ "mDataProp": function ( row, type, val, meta ) {

							 id= row.id_old_metal_rate;

							 edit_target=(access.edit=='0'?"":"#confirm-edit");

							 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/metal_type/delete/'+id : '#' );

							 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

							 action_content=(access.edit =='1' ? '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a>':'') +(access.delete == '1' ? '<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>':'')

							//  var action_content=''

							//  if(access.edit == '1'){

														

							// 	action_content+=  (access.edit =='1' ? '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a>' :"");

								

							// 	}

							// 	if(access.delete == '1'){

								 

							// 	 action_content+=(access.delete == '1' ? '<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>':'')

							// 	}

							 return action_content;

							 }

						    },

						 ]

		});

		 	$('#tot_rate').val(data.length);

   			 $("div.overlay").css("display", "none");

		  },

		  error:function(error)

		  {

			 $("div.overlay").css("display", "none");

		  }

	});

}

$(document).on('click', "#old_metal_list a.btn-edit", function(e) {

		$("#ed_name").val('');

		e.preventDefault();

		id=$(this).data('id');

	    edit_old_metal_rate(id);

	    $("#id_metal_type").val(id);

});

function edit_old_metal_rate()

{	 

    my_Date = new Date();

	$.ajax({

		type:"GET",

		url: base_url+"index.php/admin_ret_catalog/old_metal_rate/edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		cache:false,

		dataType:"JSON",

		success:function(data){

		  if(data.id_metal==1)

		  {

		      $('#ed_gold_rate').val(data.rate);

		      $('#gold_row').css('display','block');

		      $('#silver_row').css('display','none');

		      $('#platinum_row').css('display','none');

		  }else if(data.id_metal==2)

		  {

		       $('#ed_silver_rate').val(data.rate);

		      $('#silver_row').css('display','block');

		      $('#gold_row').css('display','none');

			  $('#platinum_row').css('display','none');

		  }

		  else

		  {

		       $('#ed_platinum_rate').val(data.rate);

		      $('#silver_row').css('display','none');

		      $('#gold_row').css('display','none');

			  $('#platinum_row').css('display','block');

		  }

		  $('#id_old_metal_rate').val(data.id_old_metal_rate);

		  $('#id_metal').val(data.id_metal);

		}

	});

}

$('#add_old_rate').on('click',function(){

     //$(".overlay").css("display", "block");

    if($('#gold_rate').val()=='')

    {

         msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Success!</strong>Enter The Gold Rate.</div>';

		 $('#chit_alert').html(msg);

    }

    else if($('#silver_rate').val()=='' )

    {

         msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Success!</strong>Enter The Silver Rate</div>';

		 $('#chit_alert').html(msg);

    }

    else if($('#platinum_rate').val()=='' )

    {

         msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Success!</strong>Enter The Platinum Rate</div>';

		 $('#chit_alert').html(msg);

    }

    else

    {

        var form_data=$('#old_metal_rate').serialize();

        my_Date = new Date();

        $.ajax({

        data:form_data,

        url: base_url+"index.php/admin_ret_catalog/old_metal_rate/add/?nocache=" + my_Date.getUTCSeconds(),

        type:"POST",

        cache: false,

        success:function(){

            $(".overlay").css("display", "none");

            location.reload(true);

        }

        });

    }

});

$('#old_metal_close').on('click',function(){

    $('#gold_rate').val('');

    $('#silver_rate').val('');

});

$('#update_old_metal').on('click',function(){

     var form_data=$('#old_metal_update').serialize();

        my_Date = new Date();

        $.ajax({

        data:form_data,

        url: base_url+"index.php/admin_ret_catalog/old_metal_rate/update/?nocache=" + my_Date.getUTCSeconds(),

        type:"POST",

        cache: false,

        success:function(){

            $("div.overlay").css("display", "none");

            location.reload(true);

        }

        });

});

//Old Metal Rate

//Section Master

$("#add_section").on('click',function(){

	if($("#section_name").val() == '')

	{

    		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'please select Enter Section Name..'});

	}

	else if($('#section_ShortCode').val()=='')

	{

	    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'please Section Short Code..'});

	}

	else if($('#id_branch').val()=='')

	{

	    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select branch..'});

	}

	else{

	    $("#add_section").prop('disabled',true);

	    add_section();

	}

});

$("#add_new_section").on('click',function(){

    if($("#section_name").val() == '')

	{

      $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'please select Enter Section Name..'});

	}

	else if($('#section_ShortCode').val()=='')

	{

	    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'please Section Short Code..'});

	}

	else if($('#id_branch').val()=='')

	{

	    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select branch..'});

	}

	else{

	    $("#add_new_section").prop('disabled',true);

	    add_new_section();

	}

});

function add_new_section()

{

    var is_home_bill_counter = $("input[name='is_home_bill_counter']:checked").val();

	my_Date = new Date();

	$.ajax({

		data:{"section_name":$("#section_name").val(),"short_code":$('#section_ShortCode').val(),"section_status":$('#section_status').val(),"id_branch":$('#branch_select').val(),'is_home_bill_counter':is_home_bill_counter},

		url: base_url+"index.php/admin_ret_catalog/ret_section/Add?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		cache:false,

		dataType: 'json',

		success:function(data){

		    $("#add_new_section").prop('disabled',false);

			$('#section_name').val('');

			$('#section_ShortCode').val('');

			if(data['status']==true)

			{

			   msg='<div class = "alert alert-success"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>'+data['message']+'</strong></div>';

			}else{

			    msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>'+data['message']+'</strong> </div>';

			}

			$('#error-msg').html(msg);

		    get_section_details();

		}

	});

}

function add_section()

{

    var is_home_bill_counter = $("input[name='is_home_bill_counter']:checked").val();

	my_Date = new Date();

	$.ajax({

		data:{"section_name":$("#section_name").val(),"short_code":$('#section_ShortCode').val(),"section_status":$('#section_status').val(),"id_branch":$('#branch_select').val(),'is_home_bill_counter':is_home_bill_counter},

		url: base_url+"index.php/admin_ret_catalog/ret_section/Add?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		cache:false,

		success:function(data){

			$('#section_name').val('');

			window.location.reload(true);

		}

	});

}

function get_section_details()

{

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		url: base_url+"index.php/admin_ret_catalog/ret_section/ajax?nocache=" + my_Date.getUTCSeconds(),

		type:"GET",

		dataType: 'json',

		cache:false,

		success:function(data){

		   		var list 	= data.list;

				var access	    = data.access;

				if(access.add == '0')

				{

					$('#add_size').attr('disabled','disabled');

				}

			 var oTable = $('#section_list').DataTable();

			 oTable.clear().draw();

			 if (list!= null && list.length > 0)

			 {

				oTable = $('#section_list').dataTable({

						"bDestroy": true,

						"bInfo": true,

						"bFilter": true,

						"bSort": true,

						"order": [[ 0, "desc" ]],

						"aaData"  : list,

						"aoColumns": [

						{ "mDataProp": "id_section" },

						{ "mDataProp": "section_name" },

						{ "mDataProp": "section_short_code" },

						{ "mDataProp": function ( row, type, val, meta ){

                        	active_url =(access.edit == '1') ? base_url+"index.php/admin_ret_catalog/ret_section/update_status/"+row.id_section+"/"+(row.status==0?1:0):"#";

                        	return "<a href='"+active_url+"'><i class='fa "+(row.status==1?'fa-check':'fa-remove')+"' style='color:"+(row.status==1?'green':'red')+"'></i></a>"

                        	}

                        },

						{ "mDataProp": function ( row, type, val, meta ) {

							 id= row.id_section;

							 edit_target=(access.edit=='0'?"":"#confirm-edit");

							 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/ret_section/delete/'+id : '#' );

							 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

							//  action_content='<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

							

							var action_content=''

							if(access.edit == '1'){

													   

							action_content+=  '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a>' ;

							   

							   }

							if(access.delete == '1'){

								

							action_content+=' <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

							   

							}

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

$(document).on('click', "#section_list a.btn-edit", function(e) {

		$("#ed_name").val('');

		e.preventDefault();

		id=$(this).data('id');

	    get_section(id);

	    $("#edit-id").val(id);

});

function get_section(id)

{

   my_Date = new Date();

	$.ajax({

		type:"GET",

		url: base_url+"index.php/admin_ret_catalog/ret_section/Edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		cache:false,

		dataType:"JSON",

		success:function(data){

            $('#ed_section_name').val(data.section_name);

            $('#ed_section_ShortCode').val(data.section_short_code);

            $("input[name='is_ed_home_bill_counter'][value="+data.is_home_bill_counter+"]").prop('checked', true);

            var section_status = data.status;

			if(section_status == 1)

			{

			$('#ed_sec_status').bootstrapSwitch('state', true);

			}

			else

			{

			$('#ed_sec_status').bootstrapSwitch('state', false);

			}

			$("#ed_section_status").val(section_status);

			$('#ed_branch_select').select2("val",data.branch);

			if($('#branch_select,#ed_branch_select').data('id_branch'))

			{

			   var ar = $('#branch_select,#ed_branch_select').data('id_branch');

               		   $("#branch_select,#ed_branch_select").select2('val',ar);

			}

			else

			{

			  $("#branch_select,#ed_branch_select").select2('val','');

			}

		}

	});

}

 $("#update_section").on('click',function(){

	var id=$("#edit-id").val();

	 if($("#ed_section_name").val() == '')

	{

    		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'please select Enter Section Name..'});

	}

	else if($('#ed_section_ShortCode').val()=='')

	{

	    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'please Section Short Code..'});

	}

	else if($('#ed_branch_select').val()=='')

	{

	    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select branch..'});

	}else

	{

	    $("#update_section").prop('disabled',true);

	    update_section(id);

	}

});

 function update_section(id)

{

    var is_home_bill_counter = $("input[name='is_ed_home_bill_counter']:checked").val();

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		data:{"section_name":$("#ed_section_name").val(),"short_code":$('#ed_section_ShortCode').val(),"section_status":$('#ed_section_status').val(),"id_branch":$('#ed_branch_select').val(),'is_home_bill_counter':is_home_bill_counter},

		url: base_url+"index.php/admin_ret_catalog/ret_section/Update/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		cache: false,

		success:function(){

			$("div.overlay").css("display", "none");

			location.reload(true);

		}

	});

}

$("#sec_status").on('switchChange.bootstrapSwitch', function (event, state) {

	var x=$(this).data('on-text');

	var y=$(this).data('off-text');

	if($("#sec_status").is(':checked') && x=='YES')

	{

				$("#section_status").val(1);

	} else if(y=='NO')

	{

	  $("#section_status").val(0);

	}

});

$("#ed_sec_status").on('switchChange.bootstrapSwitch', function (event, state) {

	var x=$(this).data('on-text');

	var y=$(this).data('off-text');

	if($("#ed_sec_status").is(':checked') && x=='YES')

	{

				$("#ed_section_status").val(1);

	} else if(y=='NO')

	{

	  $("#ed_section_status").val(0);

	}

});

//Section Master

//Pay Device

$("#add_dev").on('click',function(){

	if($("#device_name").val() == '')

	{

    		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'please select Enter Device Name..'});

	}

	else{

	    $("#add_dev").prop('disabled',true);

	    add_dev();

	}

});

function add_dev()

{

    var device_type = $("input[name='device_type']:checked").val();

	my_Date = new Date();

	$.ajax({

		data:{"device_name":$("#device_name").val(),"dev_status":$('#dev_status').val(),'device_type':device_type},

		url: base_url+"index.php/admin_ret_catalog/ret_pay_device/Add?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		dataType : "JSON",

		cache:false,

		success:function(data){

			if(data.status){

				$('#device_name').val('');

				window.location.reload(true);

			}else{

				/* debugger */

				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message});

			}

		}

	});

}

function get_pay_device()

{

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		url: base_url+"index.php/admin_ret_catalog/ret_pay_device/ajax?nocache=" + my_Date.getUTCSeconds(),

		type:"GET",

		dataType: 'json',

		cache:false,

		success:function(data){

		   		var list 	= data.list;

				var access	    = data.access;

				if(access.add == '0')

				{

					$('#add_size').attr('disabled','disabled');

				}

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

						"aaData"  : list,

						"aoColumns": [

						{ "mDataProp": "id_device" },

						{ "mDataProp": "device_name" },

						{ "mDataProp": function ( row, type, val, meta ){

                        	/* active_url =base_url+"index.php/admin_ret_catalog/ret_pay_device/update_status/"+row.device_type+"/"+(row.status==0?1:0); */

                        	return (row.device_type ==1? "Wallet":"Bank")

                        	} },

						{ "mDataProp": function ( row, type, val, meta ){

                        	active_url =(access.edit=='1')?base_url+"index.php/admin_ret_catalog/ret_pay_device/update_status/"+row.id_device+"/"+(row.status==0?1:0):'#';

                        	return "<a href='"+active_url+"'><i class='fa "+(row.status==1?'fa-check':'fa-remove')+"' style='color:"+(row.status==1?'green':'red')+"'></i></a>"

                        	}

                        },

						{ "mDataProp": function ( row, type, val, meta ) {

							 id= row.id_device;

							 edit_target=(access.edit=='0'?"":"#confirm-edit");

							 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/ret_pay_device/delete/'+id : '#' );

							 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

							//  action_content='<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

							var action_content=''

							if(access.edit == '1'){

														

								action_content+=  '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a>' ;

								

								}

								if(access.delete == '1'){

								

								action_content+='<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

								}

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

	$("#ed_dev_sts").on('switchChange.bootstrapSwitch', function (event, state) {

		var x=$(this).data('on-text');

		var y=$(this).data('off-text');

		if($("#ed_dev_sts").is(':checked') && x=='YES')

		{

					$("#ed_dev_status").val(1);

		} else if(y=='NO')

		{

		  $("#ed_dev_status").val(0);

		}

	});

}

 $("#update_dev").on('click',function(){

	var id=$("#edit-id").val();

	 if($("#ed_device_name").val() == '')

	{

    		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'please select Enter Device Name..'});

	}

	else

	{

	    $("#update_dev").prop('disabled',true);

	    update_dev(id);

	}

});

 function update_dev(id)

{

    var device_type = $("input[name='ed_device_type']:checked").val();

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		data:{"device_name":$("#ed_device_name").val(),"dev_status":$('#ed_dev_status').val(),'device_type':device_type},

		url: base_url+"index.php/admin_ret_catalog/ret_pay_device/Update/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		cache: false,

		success:function(){

			$("div.overlay").css("display", "none");

			location.reload(true);

		}

	});

}

$("#dev_sts").on('switchChange.bootstrapSwitch', function (event, state) {

	var x=$(this).data('on-text');

	var y=$(this).data('off-text');

	if($("#dev_sts").is(':checked') && x=='YES')

	{

				$("#dev_status").val(1);

	} else if(y=='NO')

	{

	  $("#dev_status").val(0);

	}

});

$(document).on('click', "#payment_list a.btn-edit", function(e) {

		$("#ed_name").val('');

		e.preventDefault();

		id=$(this).data('id');

	    get_dev(id);

	    $("#edit-id").val(id);

});

function get_dev(id)

{

   my_Date = new Date();

	$.ajax({

		type:"GET",

		url: base_url+"index.php/admin_ret_catalog/ret_pay_device/Edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		cache:false,

		dataType:"JSON",

		success:function(data){

            $('#ed_device_name').val(data.device_name);

            $("input[name='ed_device_type'][value="+data.device_type+"]").prop('checked', true);

            var ed_dev_status = data.status;

			if(ed_dev_status == 1)

			{

			$('#ed_dev_sts').bootstrapSwitch('state', true);

			}

			else

			{

			$('#ed_dev_sts').bootstrapSwitch('state', false);

			}

			$("#ed_dev_status").val(ed_dev_status);

		}

	});

}

//Pay Device End

//Account Head

$("#add_acc_name").on('click',function(){

	if($("#name").val() == '')

	{

    		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'please select Enter Device Name..'});

	}

	else{

	    $("#add_acc_name").prop('disabled',true);

	    add_acc();

	}

});

function add_acc()

{

    //var device_type = $("input[name='device_type']:checked").val();

	my_Date = new Date();

	$.ajax({

		data:{"name":$("#name").val(),"account_status":$('#account_status').val()},

		url: base_url+"index.php/admin_ret_catalog/ret_account/Add?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		dataType : "JSON",

		cache:false,

		success:function(data){

			if(data.status){

				$('#name').val('');

				window.location.reload(true);

			}else{

				/* debugger */

				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message});

			}

		}

	});

}

function get_acc_head()

{

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		url: base_url+"index.php/admin_ret_catalog/ret_account/ajax?nocache=" + my_Date.getUTCSeconds(),

		type:"GET",

		dataType: 'json',

		cache:false,

		success:function(data){

		   		var list 	= data.list;

				var access	    = data.access;

				if(access.add == '0')

				{

					$('#add_size').attr('disabled','disabled');

				}

			 var oTable = $('#accounthed_list').DataTable();

			 oTable.clear().draw();

			 if (list!= null && list.length > 0)

			 {

				oTable = $('#accounthed_list').dataTable({

						"bDestroy": true,

						"bInfo": true,

						"bFilter": true,

						"bSort": true,

						"order": [[ 0, "desc" ]],

						"aaData"  : list,

						"aoColumns": [

						{ "mDataProp": "id_acc_head" },

						{ "mDataProp": "name" },

						{ "mDataProp": function ( row, type, val, meta ){

                        	active_url =(access.edit =='1')?base_url+"index.php/admin_ret_catalog/ret_account/update_status/"+row.id_acc_head+"/"+(row.status==0?1:0):'#';

                        	return "<a href='"+active_url+"'><i class='fa "+(row.status==1?'fa-check':'fa-remove')+"' style='color:"+(row.status==1?'green':'red')+"'></i></a>"

                        	}

                        },

						{ "mDataProp": function ( row, type, val, meta ) {

							 id= row.id_acc_head;

							 edit_target=(access.edit=='0'?"":"#confirm-edit");

							 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/ret_account/delete/'+id : '#' );

							 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

							//  action_content='<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

							var action_content=''

						if(access.edit == '1'){

													

							action_content+=  '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a>' ;

							

							}

							if(access.delete == '1'){

							

							action_content+='<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

							}

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

	$("#ed_acc_status").on('switchChange.bootstrapSwitch', function (event, state) {

		var x=$(this).data('on-text');

		var y=$(this).data('off-text');

		if($("#ed_acc_status").is(':checked') && x=='YES')

		{

					$("#ed_account_status").val(1);

		} else if(y=='NO')

		{

		  $("#ed_account_status").val(0);

		}

	});

}

 $("#update_accsection").on('click',function(){

	var id=$("#edit-id").val();

	 if($("#ed_account_name").val() == '')

	{

    		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'please select Enter Device Name..'});

	}

	else

	{

	    $("#update_accsection").prop('disabled',true);

	    update_acc(id);

	}

});

 function update_acc(id)

{

    //var device_type = $("input[name='ed_device_type']:checked").val();

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		data:{"name":$("#ed_account_name").val(),"acc_status":$('#ed_account_status').val()},

		url: base_url+"index.php/admin_ret_catalog/ret_account/Update/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		cache: false,

		success:function(){

			$("div.overlay").css("display", "none");

			location.reload(true);

		}

	});

}

$("#acc_status").on('switchChange.bootstrapSwitch', function (event, state) {

	var x=$(this).data('on-text');

	var y=$(this).data('off-text');

	if($("#acc_status").is(':checked') && x=='YES')

	{

				$("#account_status").val(1);

	} else if(y=='NO')

	{

	  $("#account_status").val(0);

	}

});

/* $("#ed_acc_status").on('switchChange.bootstrapSwitch', function (event, state) {

	var x=$(this).data('on-text');

	var y=$(this).data('off-text');

	if($("#ed_acc_status").is(':checked') && x=='YES')

	{

				$("#ed_account_status").val(1);

	} else if(y=='NO')

	{

	  $("#ed_account_status").val(0);

	}

}); */

$(document).on('click', "#accounthed_list a.btn-edit", function(e) {

		$("#ed_name").val('');

		e.preventDefault();

		id=$(this).data('id');

	    get_acc(id);

	    $("#edit-id").val(id);

});

function get_acc(id)

{

   my_Date = new Date();

	$.ajax({

		type:"GET",

		url: base_url+"index.php/admin_ret_catalog/ret_account/Edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		cache:false,

		dataType:"JSON",

		success:function(data){

            $('#ed_account_name').val(data.name);

           // $("input[name='ed_device_type'][value="+data.device_type+"]").prop('checked', true);

            var ed_account_status = data.status;

			if(ed_account_status == 1)

			{

			$('#ed_acc_status').bootstrapSwitch('state', true);

			}

			else

			{

			$('#ed_acc_status').bootstrapSwitch('state', false);

			}

			$("#ed_account_status").val(ed_account_status);

		}

	});

}

//Account Head End

//Breakeven Logs

$('#add_breakeven').on('click',function(){

    breakeve_branch_details();

    $("#des_select").select2("val",'');

    $("#wt_select").select2("val",'');

});

function breakeve_branch_details(){

         $('#brevn_items tbody ').empty();

     	$('#ed_branch_select option').remove();

     	//$('#branch_select option').remove();

         	$.ajax({

             	type: 'GET',

             	url: base_url+'index.php/branch/branchname_list',

             	dataType:'json',

             	success:function(data){

             	        var row='';

             	        $.each(data.branch,function(key,item){

             	            if(item.branch_type==1)

             	            {

             	                 row+='<tr>'

                 	            +'<td>'+item.name+'<input type="hidden" class="id_branch"  name="pieces['+key+'][id_branch]" value='+item.id_branch+'></td>'

                 	            +'<td><input type="number" class="gold_value" name="pieces['+key+'][goldvalue]" value="" placeholder="Enter Gold Value"></td>'

                 	            +'<td><input type="number" class="silver_value" name="pieces['+key+'][silvervalue]"  value="" placeholder="Enter Silver Value"></td>'

								+'<td><input type="number" class="dimond_value" name="pieces['+key+'][dimondvalue]"  value="" placeholder="Enter Diamond Value"></td>'

                 	            +'</tr>';

             	            }

             	        });

             	        $('#brevn_items tbody ').append(row);

             	}

            });

        }

$('#add_breakeven_name').on('click',function(e){

	var id_branch=$('#branch_select').val();

	//$('#chit_alert').html('');

	    if($('#branch_settings').val()==1)

	    {

	        if(validateSettingsDetailRow())

            {

                add_breakeven_name();

            }

            else

            {

                //msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Please Fill The Required Fields.</div>';

                 $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill The Required Fields.'});

            }

	    }

});

function validateSettingsDetailRow()

{

    var validate = true;

	$('#brevn_items > tbody > tr').each(function(index, tr) {

		 if($(this).find('.gold_value').val() == "" || $(this).find('.silver_value').val() == "" || $(this).find('.dimond_value').val() == "")

            {

                validate=false;

            }

	});

	return validate;

}

function add_breakeven_name()

{

    var form_data=$('#myform').serialize();

	my_Date = new Date();

	$.ajax({

	data: form_data,

		url: base_url+"index.php/admin_ret_catalog/ret_breakeven_logs/add?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		dataType: 'json',

		cache:false,

		success:function(data){

			if(data.status)

		    {

				window.location.reload(true);

		        if($('#branch_settings').val()==1)

		        {

		            $('#brevn_items > tbody > tr').each(function(index, tr) {

                         $(this).find('.gold_value').val('');

                         $(this).find('.silver_value').val('');

						 $(this).find('.dimond_value').val('');

                     });

		        }

                //$('#wt_select').select2("val",'');

		        msg='<div class = "alert alert-success"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Success!</strong>'+data.msg+'</div>';

		    }else

		    {

		       	msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Success!</strong>'+data.msg+'</div>';

		    }

			//$('#chit_alert').html(msg);

		}

	});

}

$('#breakeven_search').on('click',function(){

    get_acc_brlog();

});

function get_acc_brlog()

{

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		url: base_url+"index.php/admin_ret_catalog/ret_breakeven_logs/ajax?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		data: ( {'from_date':$('#fl_date1').html(),'to_date':$('#fl_date2').html(),'id_branch':($('#branch_filter').val()!='' && $('#branch_filter').val()!=undefined ? $('#branch_filter').val(): $("#branch_select").val())}),

		dataType: 'json',

		cache:false,

		success:function(data){

		   		var list 	= data.list;

				var access	    = data.access;

				if(access.add == '0')

				{

					$('#add_size').attr('disabled','disabled');

				}

			 var oTable = $('#breakeven_list').DataTable();

			 oTable.clear().draw();

			 if (list!= null && list.length > 0)

			 {

				oTable = $('#breakeven_list').dataTable({

						"bDestroy": true,

						"bInfo": true,

						"bFilter": true,

						"bSort": true,

						"order": [[ 0, "desc" ]],

						"aaData"  : list,

						"columnDefs": [

							{

                            targets: [3,4,5],

                            className: 'dt-body-right'

							},

							{

                            targets: [2],

                            className: 'dt-body-left'

							},

						],

						"aoColumns": [

						{ "mDataProp": "brevn_log_id" },

						{ "mDataProp": "brevn_log_date" },

						{ "mDataProp": "branch_name" },

						{

                            "mDataProp": function (row, type, val, meta) {

                                return money_format_india(parseFloat(row.brevn_log_gold_val).toFixed(3));

                            }

                        },

						{

                            "mDataProp": function (row, type, val, meta) {

                                return money_format_india(parseFloat(row.brevn_log_silver_val).toFixed(3));

                            }

                        },

						{

                            "mDataProp": function (row, type, val, meta) {

                                return money_format_india(parseFloat(row.brevn_log_dia_val).toFixed(3));

                            }

                        },

						{ "mDataProp": function ( row, type, val, meta ) {

							 id= row.brevn_log_id;

							 edit_target=(access.edit=='0'?"":"#confirm-edit");

							 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/ret_breakeven_logs/delete/'+id : '#' );

							 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

							//  action_content='<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

							var action_content=''

							if(access.edit == '1'){

														

								action_content+=  '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a>' ;

								

								}

								if(access.delete == '1'){

								

								action_content+='<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

								}

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

	$("#ed_acc_status").on('switchChange.bootstrapSwitch', function (event, state) {

		var x=$(this).data('on-text');

		var y=$(this).data('off-text');

		if($("#ed_acc_status").is(':checked') && x=='YES')

		{

					$("#ed_account_status").val(1);

		} else if(y=='NO')

		{

		  $("#ed_account_status").val(0);

		}

	});

}

 $("#update_brevnlog").on('click',function(){

	var id=$("#edit-id").val();

	 if($("#ed_gold_value").val() == '')

	{

    		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'please select Enter Gold Value.'});

	}

	 if($("#ed_silver_value").val() == '')

	{

    		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'please select Enter Slver Value..'});

	}

	 if($("#ed_dimond_value").val() == '')

	{

    		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'please select Enter Dimond Name..'});

	}

	else

	{

	    $("#update_brevnlog").prop('disabled',true);

	    update_brlog(id);

	}

});

 function update_brlog(id)

{

    //var device_type = $("input[name='ed_device_type']:checked").val();

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		data:{"goldvalue":$("#ed_gold_value").val(),"silvervalue":$('#ed_silver_value').val(),"dimondvalue":$('#ed_dimond_value').val(),"dimondvalue":$('#ed_dimond_value').val()},

		url: base_url+"index.php/admin_ret_catalog/ret_breakeven_logs/Update/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		cache: false,

		success:function(){

			$("div.overlay").css("display", "none");

			location.reload(true);

		}

	});

}

$("#acc_status").on('switchChange.bootstrapSwitch', function (event, state) {

	var x=$(this).data('on-text');

	var y=$(this).data('off-text');

	if($("#acc_status").is(':checked') && x=='YES')

	{

				$("#account_status").val(1);

	} else if(y=='NO')

	{

	  $("#account_status").val(0);

	}

});

/* $("#ed_acc_status").on('switchChange.bootstrapSwitch', function (event, state) {

	var x=$(this).data('on-text');

	var y=$(this).data('off-text');

	if($("#ed_acc_status").is(':checked') && x=='YES')

	{

				$("#ed_account_status").val(1);

	} else if(y=='NO')

	{

	  $("#ed_account_status").val(0);

	}

}); */

$(document).on('click', "#breakeven_list a.btn-edit", function(e) {

		$("#ed_gold_value").val('');

		$("#ed_silver_value").val('');

		$("#ed_dimond_value").val('');

		e.preventDefault();

		id=$(this).data('id');

	    get_brlogv(id);

	    $("#edit-id").val(id);

});

function get_brlogv(id)

{

   my_Date = new Date();

	$.ajax({

		type:"GET",

		url: base_url+"index.php/admin_ret_catalog/ret_breakeven_logs/Edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		cache:false,

		dataType:"JSON",

		success:function(data){

            $('#ed_gold_value').val(data.brevn_log_gold_val);

			$('#ed_silver_value').val(data.brevn_log_silver_val);

			$('#ed_dimond_value').val(data.brevn_log_dia_val);

			$('#brevn_log_id').val(data.brevn_log_id);

           // $("input[name='ed_device_type'][value="+data.device_type+"]").prop('checked', true);

		}

	});

}

//Breakeven Logs End

// Stack Type Starts

$("#add_stak").on('click',function(){

	if($("#stock_name").val() == '')

	{

    		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'please select Enter Stack Name..'});

	}

	else{

	    $("#add_stak").prop('disabled',true);

	    add_stak();

	}

});

function add_stak()

{

    var stock_type = $("input[name='stock_type']:checked").val();

	my_Date = new Date();

	$.ajax({

		data:{"stock_name":$("#stock_name").val(),"stock_status":$('#stock_status').val(),'stock_type':stock_type},

		url: base_url+"index.php/admin_ret_catalog/stock_issue_type/Add?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		dataType : "JSON",

		cache:false,

		success:function(data){

			if(data.status){

				$('#stock_name').val('');

				window.location.reload(true);

			}

		}

	});

}

function get_stock_issue()

{

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		url: base_url+"index.php/admin_ret_catalog/stock_issue_type/ajax?nocache=" + my_Date.getUTCSeconds(),

		type:"GET",

		dataType: 'json',

		cache:false,

		success:function(data){

		   		var list 	= data.list;

				var access	    = data.access;

				if(access.add == '0')

				{

					$('#add_size').attr('disabled','disabled');

				}

			 var oTable = $('#stock_list').DataTable();

			 oTable.clear().draw();

			 if (list!= null && list.length > 0)

			 {

				oTable = $('#stock_list').dataTable({

						"bDestroy": true,

						"bInfo": true,

						"bFilter": true,

						"bSort": true,

						"order": [[ 0, "desc" ]],

						"aaData"  : list,

						"aoColumns": [

						{ "mDataProp": "id_stock_issue_type" },

						{ "mDataProp": "name" },

						{ "mDataProp": function ( row, type, val, meta ){

                        	/* active_url =base_url+"index.php/admin_ret_catalog/ret_pay_device/update_status/"+row.device_type+"/"+(row.status==0?1:0); */

                        	return (row.is_remove_from_stock ==1? "Remove From Stock":"No")

                        	} },

						{ "mDataProp": function ( row, type, val, meta ){

                        	active_url =(access.edit=='1')?base_url+"index.php/admin_ret_catalog/stock_issue_type/update_status/"+row.id_stock_issue_type+"/"+(row.status==0?1:0):"#";

                        	return "<a href='"+active_url+"'><i class='fa "+(row.status==1?'fa-check':'fa-remove')+"' style='color:"+(row.status==1?'green':'red')+"'></i></a>"

                        	}

                        },

						{ "mDataProp": function ( row, type, val, meta ) {

							 id= row.id_stock_issue_type;

							 edit_target=(access.edit=='0'?"":"#confirm-edit");

							 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/stock_issue_type/delete/'+id : '#' );

							 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

							//  action_content='<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

							var action_content=''

							if(access.edit == '1'){

														

								action_content+=  '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a>' ;

								

								}

								if(access.delete == '1'){

								

								action_content+='<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

								}

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

	$("#ed_stock_sts").on('switchChange.bootstrapSwitch', function (event, state) {

		var x=$(this).data('on-text');

		var y=$(this).data('off-text');

		if($("#ed_stock_sts").is(':checked') && x=='YES')

		{

					$("#ed_stock_status").val(1);

		} else if(y=='NO')

		{

		  $("#ed_stock_status").val(0);

		}

	});

}

$("#stock_sts").on('switchChange.bootstrapSwitch', function (event, state) {

	var x=$(this).data('on-text');

	var y=$(this).data('off-text');

	if($("#stock_sts").is(':checked') && x=='YES')

	{

				$("#stock_status").val(1);

	} else if(y=='NO')

	{

	  $("#stock_status").val(0);

	}

});

$(document).on('click', "#stock_list a.btn-edit", function(e) {

		$("#ed_name").val('');

		e.preventDefault();

		id=$(this).data('id');

	    get_stock(id);

	    $("#edit-id").val(id);

});

function get_stock(id)

{

   my_Date = new Date();

	$.ajax({

		type:"GET",

		url: base_url+"index.php/admin_ret_catalog/stock_issue_type/Edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		cache:false,

		dataType:"JSON",

		success:function(data){

            $('#ed_stock_name').val(data.name);

            $("input[name='ed_stock_type'][value="+data.is_remove_from_stock+"]").prop('checked', true);

            var ed_stock_status = data.status;

			if(ed_stock_status == 1)

			{

			$('#ed_stock_sts').bootstrapSwitch('state', true);

			}

			else

			{

			$('#ed_stock_sts').bootstrapSwitch('state', false);

			}

			$("#ed_stock_status").val(ed_stock_status);

		}

	});

}

 $("#update_stock").on('click',function(){

	var id=$("#edit-id").val();

	 if($("#ed_stock_name").val() == '')

	{

    		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'please select Enter Stock Name..'});

	}

	else

	{

	    $("#update_stock").prop('disabled',true);

	    update_stock(id);

	}

});

 function update_stock(id)

{

    var stock_type = $("input[name='ed_stock_type']:checked").val();

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		data:{"stock_name":$("#ed_stock_name").val(),"stock_status":$('#ed_stock_status').val(),'stock_type':stock_type},

		url: base_url+"index.php/admin_ret_catalog/stock_issue_type/Update/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		cache: false,

		success:function(){

			$("div.overlay").css("display", "none");

			location.reload(true);

		}

	});

}

// Stack Type End

function get_product_RetailSections(){

    my_Date = new Date();

    $.ajax({

        type: 'GET',

        url: base_url+"index.php/admin_ret_catalog/get_section?nocache=" + my_Date.getUTCSeconds(),

        dataType:'json',

        success:function(data){

            section_details = data;

            var ids = [];

            $(".id_section").each(function() {

                ids = ids.concat($(this).val().split(','));

            });

            $("#section_sel option").remove();

            $.each(data, function(key, item){

                var selected = ids.indexOf(item.id_section.toString()) !== -1;

                $("#section_sel").append(

                    $("<option></option>")

                    .attr("value", item.id_section)

                    .prop("selected", selected)

                    .text(item.section_name)

                );

            });

            $("#section_sel").select2(); // Initialize the select2 plugin

            $(".overlay").css("display","none");

        }

    });

}

//Section Mapping

function getRetailSections(){

    my_Date = new Date();

    $.ajax({

        type: 'GET',

        url: base_url+"index.php/admin_ret_catalog/get_section?nocache=" + my_Date.getUTCSeconds(),

        dataType:'json',

        success:function(data){

			section_details = data;

            var id=$("#id_section").val();

            $("#section_sel option").remove();

            $.each(data,function(key, item){

                $("#section_sel").append(

                    $("<option></option>")

                    .attr("value",item.id_section)

                    .text(item.section_name)

                );

            });

            $("#section_sel").select2("val",(id!='' && id>0?id:''));

            $(".overlay").css("display","none");

        }

    })

}

function getActiveSections(){

    my_Date = new Date();

    $.ajax({

        type: 'GET',

        url: base_url+"index.php/admin_ret_catalog/get_section?nocache=" + my_Date.getUTCSeconds(),

        dataType:'json',

        success:function(data){

            var id=$("#product_section_select").val();

            $("#product_section_select option").remove();

            $.each(data,function(key, item){

                $("#product_section_select").append(

                    $("<option></option>")

                    .attr("value",item.id_section)

                    .text(item.section_name)

                );

            });

            if($("#product_section_select").length>0)

            {

                $("#product_section_select").select2("val",(id!='' && id>0?id:''));

            }

            $(".overlay").css("display","none");

        }

    })

}

$('#select_all').click(function(event) {

	  $("tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));

      event.stopPropagation();

});

$('#product_section_update').on('click',function(){

    $("div.overlay").css("display","block");

    // alert($('#product_section_select').val());

    if($('#product_section_select').val()!='' && $('#product_section_select').val()!=null)

    {

        if($("input[name='pro_id[]']:checked").val())

        {

            var selected=[];

            $("#product_list tbody tr").each(function(index, value){

                if($(value).find("input[name='pro_id[]']:checked").is(":checked"))

                {

                    transData = {

                    'pro_id': $(value).find(".pro_id").val(),

                    'product_section_select':$('#product_section_select').val(),

                    }

                    console.log(transData);

                    selected.push(transData);

                }

            });

            console.log(selected);

            update_product_section(selected);

        }

        else

        {

            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'please select Product..'});

            $("div.overlay").css("display", "none");

        }

    }

    else{

        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'please select Section..'});

        $("div.overlay").css("display", "none");

    }

});

function update_product_section(req_data)

{

    my_Date=new Date();

    $('div.overlay').css("display","block");

    $.ajax({

        url:base_url+"index.php/admin_ret_catalog/update_product_section?nocache" + my_Date.getUTCSeconds(),

        data:  {'req_data':req_data},

        type:"POST",

        async:false,

            success:function(data){

                window.location.reload();

                $("div.overlay").css("display", "none");

            },

            error:function(error)

            {

                $("div.overlay").css("display", "none");

            }

    });

}

//Section Mapping

//Feedback Master

function get_feedback_details()

{

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		url: base_url+"index.php/admin_ret_catalog/feedback/ajax?nocache=" + my_Date.getUTCSeconds(),

		type:"GET",

		dataType: 'json',

		cache:false,

		success:function(data){

		   		var list 	= data.list;

				var access	    = data.access;

			 var oTable = $('#feedback_list').DataTable();

			 oTable.clear().draw();

			 if (list!= null && list.length > 0)

			 {

				oTable = $('#feedback_list').dataTable({

						"bDestroy": true,

						"bInfo": true,

						"bFilter": true,

						"bSort": true,

						"order": [[ 0, "desc" ]],

						"aaData"  : list,

						"aoColumns": [

						{ "mDataProp": "id_feedback" },

						{ "mDataProp": "name" },

						{ "mDataProp": function ( row, type, val, meta ) {

							 id= row.id_feedback;

							//  edit_target=(access.edit=='0'?"":"#confirm-edit");

							edit_target="#confirm-edit";

							//  delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/feedback/delete/'+id : '#' );

							 delete_url= base_url+'index.php/admin_ret_catalog/feedback/delete/'+id ;

							//  delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

							 delete_confirm= "#confirm-delete";

							 action_content='<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

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

$("#add_feedback").on('click',function(){

	if($("#feedback").val() == '')

	{

    	msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Feedback.</div>';

    	$('#error-msg').html(msg);

    	return false;

	}else{

	    add_feedback();

	}

});

function add_feedback()

{

	my_Date = new Date();

	$.ajax({

		data:{"name":$("#feedback").val()},

		url: base_url+"index.php/admin_ret_catalog/feedback/add?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		cache:false,

		success:function(data){

			$('#feedback').val('');

			window.location.reload(true);

		}

	});

}

$(document).on('click', "#feedback_list a.btn-edit", function(e) {

	e.preventDefault();

	id=$(this).data('id');

	get_feedback(id);

	$("#edit-id").val(id);

});

function get_feedback(id)

{

my_Date = new Date();

$.ajax({

	type:"GET",

	url: base_url+"index.php/admin_ret_catalog/feedback/edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),

	cache:false,

	dataType:"JSON",

	success:function(data){

		$('#ed_feedback').val(data.name);

	}

});

}

$("#update_feedback").on('click',function(){

	var id=$("#edit-id").val();

	if($("#ed_feedback").val() != '')

	{

	    update_feedback(id);

	}

	else

	{

		 msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Please Enter Feedback Name</strong></div>';

		 $('#error').html(msg);

		 return false;

	}

});

 function update_feedback(id)

{

	 $("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		data:{"name":$("#ed_feedback").val()},

		url: base_url+"index.php/admin_ret_catalog/feedback/update/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		cache: false,

		success:function(){

			$("div.overlay").css("display", "none");

			location.reload(true);

		}

	});

}

//Feedback Master

/**

*	Master :: Charges

*	Starts

*/

$("#tag_display_switch").bootstrapSwitch('state', false);

$("#tag_display_switch").on('switchChange.bootstrapSwitch', function (event, state) {

	var x=$(this).data('on-text');

	var y=$(this).data('off-text');

			if($("#tag_display_switch").is(':checked') && x=='YES')

			{

				$("#tag_display").val(1);

	} else if(y=='NO')

	{

		$("#tag_display").val(0);

	}

});

$("#ed_tag_display").on('switchChange.bootstrapSwitch', function (event, state) {

	var x=$(this).data('on-text');

	var y=$(this).data('off-text');

			if($("#ed_tag_display").is(':checked') && x=='YES')

			{

				$("#ed_tag_display").val(1);

	} else if(y=='NO')

	{

		$("#ed_tag_display").val(0);

	}

});

function add_charges()

{

   var charge_name = document.getElementById("charge_name").value;

   var charge_code = document.getElementById("charge_code").value;

   var value_charge = document.getElementById("value_charge").value;

   var charge_description = document.getElementById("charge_description").value;

   var tag_display = document.getElementById("tag_display").value;

    var charge_tax = document.getElementById("charge_tax").value;

   console.log("tag_display", tag_display);

   my_Date = new Date();

        $.ajax({

            url:base_url+ "index.php/admin_ret_catalog/charges/Add?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),

            data:{'charge_name':charge_name,'charge_code':charge_code,'charge_description':charge_description,'value_charge':value_charge,'tag_display':tag_display, 'charge_tax' : charge_tax},

            dataType:"JSON",

            type:"POST",

            success:function(data)

            {

                 msg='<div class = "alert alert-success"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Success!</strong>Charge Added successfully.</div>';

                 $('#chit_alert').html(msg);

			     get_charges_list();

            },

            error:function(error)

            {

               $("div.overlay").css("display", "none");

            },

            complete: function(data)

            {

           //  window.location.href = base_url+"index.php/admin_ret_catalog/charges";

            }

       });

}

function add_charges_save_and_new()

{

   var charge_name_save_and_new = document.getElementById("charge_name").value;

   var charges_code_save_and_new = document.getElementById("charge_code").value;

   var charges_value_save_and_new = document.getElementById("value_charge").value;

   var charges_description_save_and_new = document.getElementById("charge_description").value;

   var tag_display_save_and_new = document.getElementById("tag_display").value;

    var charge_tax = document.getElementById("charge_tax").value;

   my_Date = new Date();

        $.ajax({

            url:base_url+ "index.php/admin_ret_catalog/charges/Add?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),

            data:{'charge_name':charge_name_save_and_new,'charge_code':charges_code_save_and_new,'charge_description':charges_description_save_and_new,'value_charge':charges_value_save_and_new,'tag_display':tag_display_save_and_new, 'charge_tax' : charge_tax},

            dataType:"JSON",

            type:"POST",

            success:function(data)

            {

				$('#charge_name').val('');

				$('#charge_code').val('');

				$('#charge_description').val('');

				$('#error-msg').html('');

              msg='<div class = "alert alert-success"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Success!</strong>Charge added successfully.</div>';

            $('#chit_alert').html(msg);

			get_charges_list();

			},

            error:function(error)

            {

               $("div.overlay").css("display", "none");

            },

            complete: function(data)

            {

            }

        });

}

function get_active_charges()

{

    my_Date = new Date();

    $.ajax({

        data: ( {'from_date':'','to_date':''}),

			  url:base_url+ "index.php/admin_ret_catalog/charges?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),

			  dataType:"JSON",

			  type:"POST",

			  success:function(data){

			         charges_list = data.charges;

					  },

					  error:function(error)

					  {

						 //$("div.overlay").css("display", "none");

					  }

		  });

}

function get_charges_list(from_date="", to_date="")

{

	my_Date = new Date();

	$("div.overlay").css("display", "block");

	$.ajax({

        data: ( {'from_date':from_date,'to_date':to_date}),

			  url:base_url+ "index.php/admin_ret_catalog/charges?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),

			  dataType:"JSON",

			  type:"POST",

			  success:function(data){

				         set_charges_list(data);

			   			$("div.overlay").css("display", "none");

					  },

					  error:function(error)

					  {

						 $("div.overlay").css("display", "none");

					  }

		  });

}

function set_charges_list(data)

{

	 var access	= data.access;

	 var Charges = data.charges;

	 var oTable = $('#charges_list').DataTable();

	     oTable.clear().draw();

			  	 if (Charges!= null && Charges.length > 0)

			  	  {

					  	oTable = $('#charges_list').DataTable({

				                "bDestroy": true,

				                "bInfo": true,

				                "bFilter": true,

				                "bSort": true,

				                "order": [[ 0, "desc" ]],

				                "order": [[ 0, "desc" ]],

				                "aaData": Charges,

				                "aoColumns": [

                          { "mDataProp": function ( row, type, val, meta ) {

                           return row.id_charge !=null && row.id_charge !="" ?row.id_charge:'-';

                         }},

						 { "mDataProp": function ( row, type, val, meta ) {

                      		  return row.name_charge !=null?row.name_charge:'-';

                          }},

						  { "mDataProp": function ( row, type, val, meta ) {

							return row.value_charge !=null?row.value_charge:'-';

					  	  }},

                          { "mDataProp": function ( row, type, val, meta ) {

                      		  return row.code_charge !=null?row.code_charge:'-';

                          }},

                         { "mDataProp": function ( row, type, val, meta ) {

                                            id= row.id_charge;

											edit_target=(access.edit=='0'?"":"#edit_charges");

							                // action_content='<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a> <a href="#" class="btn btn-danger btn-del" onClick="delete_confirm_charges('+id+')" data-toggle="modal" ><i class="fa fa-trash"></i></a>'

											

											var action_content=''

											if(access.edit == '1'){

																		

												action_content+=  '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a>' ;

												

												}

												if(access.delete == '1'){

												

												action_content+='<a href="#" class="btn btn-danger btn-del" onClick="delete_confirm_charges('+id+')" data-toggle="modal" ><i class="fa fa-trash"></i></a>'

												}

												return action_content;

                                            }

                                         }

						 ]

				            });

					  	 }

}

function edit_charges(charges_id_edit)

{

	charges_id = charges_id_edit;

	$.ajax({

        data: ( {'id_charge':charges_id}),

			  url:base_url+ "index.php/admin_ret_catalog/charges/edit?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),

			  dataType:"JSON",

			  type:"POST",

			  success:function(data){

				 document.getElementById("charge_name_edit").value = data[0].name_charge;

                 document.getElementById("charge_code_edit").value = data[0].code_charge;

				 document.getElementById("charge_value_edit").value = data[0].value_charge;

                 document.getElementById("charge_description_edit").value = data[0].description_charge;

                 document.getElementById("charge_tax_edit").value = data[0].charge_tax;

				 var tag_display=data.tag_display;

				 if(tag_display==1)

				 {

					 $("#ed_tag_display_switch").bootstrapSwitch('state', true);

				 }

				 else

				 {

					 $("#ed_tag_display_switch").bootstrapSwitch('state', false);

				 }

			  },

			  error:function(error)

				{

					$("div.overlay").css("display", "none");

				}

		  });

}

function update_charges()

{

   var charge_name_update = document.getElementById("charge_name_edit").value;

   var charge_code_update = document.getElementById("charge_code_edit").value;

   var charge_description_update = document.getElementById("charge_description_edit").value;

   var charge_value_update = document.getElementById("charge_value_edit").value;

   var tag_display_update = document.getElementById("ed_tag_display").value;

   var charge_tax_update = document.getElementById("charge_tax_edit").value;

   my_Date = new Date();

        $.ajax({

            url:base_url+ "index.php/admin_ret_catalog/charges/update?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),

            data:{'name_charge':charge_name_update,'charge_code':charge_code_update,'description_charge':charge_description_update,'value_charge':charge_value_update,'tag_display':tag_display_update,'id_charge':charges_id, 'charge_tax' : charge_tax_update},

            dataType:"JSON",

            type:"POST",

            success:function(data)

            {

                 msg='<div class = "alert alert-success"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Success!</strong>Record Updated successfully.</div>';

				$('#chit_alert').html(msg);

				$('#error').html('');

			get_charges_list();

			},

            error:function(error)

            {

               $("div.overlay").css("display", "none");

            },

            complete: function(data)

            {

         //    window.location.href = base_url+"index.php/admin_ret_catalog/charges";

            }

        });

}

function delete_confirm_charges(id_delete_charge)

{

	id_charge_delete = id_delete_charge;

    $('#delete_charges').modal('show');

}

function delete_charge(id)

{

	my_Date = new Date();

	$("div.overlay").css("display", "block");

	$.ajax({

        data: ( {'id_charge':id_charge_delete}),

			  url:base_url+ "index.php/admin_ret_catalog/charges/delete?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),

			  dataType:"JSON",

			  type:"POST",

			  success:function(data){

				       $('#delete_charges').modal('toggle');

					  msg='<div class = "alert alert-success"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Success!</strong>Charge Deleted successfully.</div>';

                      $('#chit_alert').html(msg);

			   		  get_charges_list();

			  },

			  error:function(error)

              {

                $("div.overlay").css("display", "none");

              },

              complete: function(data)

              {

        //      window.location.href = base_url+"index.php/admin_ret_catalog/charges";

              }

		});

}

//Sub Design and Design Mapping

	//master sub design

	 $("#sd_status").on('switchChange.bootstrapSwitch', function (event, state) {

		var x = $(this).data('on-text');

		var y = $(this).data('off-text');

		if($("#sd_status").is(':checked') && x=='YES')

		{

			$("#sub_des_status").val(1);

		}

		else if(y == 'NO')

		{

			$("#sub_des_status").val(0);

		}

	});

	$("#ed_sub_status").on('switchChange.bootstrapSwitch', function (event, state) {

		var x = $(this).data('on-text');

		var y = $(this).data('off-text');

		if($("#ed_sub_status").is(':checked') && x=='YES')

		{

		$("#ed_sd_status").val(1);

		}

		else if(y == 'NO')

		{

		$("#ed_sd_status").val(0);

		}

	});

	//master sub design

function get_ActiveSubDesigns()

{

	$.ajax({

	type: 'GET',

	url: base_url+'index.php/admin_ret_catalog/ret_sub_design/active_list',

	dataType:'json',

	success:function(data){

    		var id =  $("#sub_design").val();

    		$.each(data, function (key, item) {

    		    $("#sub_design_sel,#sub_design_filter").append(

    		    $("<option></option>")

    		    .attr("value", item.id_sub_design)

    		    .text(item.sub_design_name)

    		    );

    		});

    		$("#sub_design_sel,#sub_design_filter").select2(

    		{

    			placeholder:"Select Sub Design",

    			allowClear: true

    		});

    		if($('#sub_design_sel').length)

    		{

    		     $("#sub_design_sel").select2('val',"");

    		}

    		if($('#sub_design_filter').length)

    		{

    		    $("#sub_design_filter").select2('val',"");

    		}

		}

	});

}

function get_SubDesigns()

{

	$.ajax({

	type: 'GET',

	url: base_url+'index.php/admin_ret_catalog/ret_sub_design/active_list',

	dataType:'json',

	success:function(data){

    		var id =  $("#sub_design").val();

    		$.each(data, function (key, item) {

    		    $("#sub_design_sel,#sub_design_filter").append(

    		    $("<option></option>")

    		    .attr("value", item.id_sub_design)

    		    .text(item.sub_design_name)

    		    );

    		});

    		$("#sub_design_sel,#sub_design_filter").select2(

    		{

    			placeholder:"Select Sub Design",

    			allowClear: true

    		});

    		if($('#sub_design_sel').length)

    		{

    		     $("#sub_design_sel").select2('val',"");

    		}

    		if($('#sub_design_filter').length)

    		{

    		    $("#sub_design_filter").select2('val',"");

    		}

		}

	});

}

//Sub Design Master

$("#subdesign_submit").on('click',function(){

	if($("#sub_design_name").val() == '')

	{

    	$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Sub Design..'});

	}

	else{

	    $('#sub_design_form').submit();

	}

});

function get_ActiveDesign()

{

    $.ajax({

	type: 'GET',

	url: base_url+'index.php/admin_ret_catalog/get_ActiveDesign',

	dataType:'json',

	success:function(data){

		var id =  $("#select_design").val();

		$.each(data, function (key, item) {

		   $("#select_design,#select_design_fitler").append(

		   $("<option></option>")

		   .attr("value", item.design_no)

		   .text(item.design_name)

		   );

		});

		$("#select_design,#select_design_fitler").select2(

		{

			placeholder:"Select Design",

			allowClear: true

		});

		$("#select_design").select2("val",(id!='' && id>0?id:''));

		if($("#select_design_fitler").length)

		{

		    $("#select_design_fitler").select2("val",'');

		}

		$(".overlay").css("display", "none");

	}

	});

}

function get_sub_design_details()

{

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		url: base_url+"index.php/admin_ret_catalog/ret_sub_design?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		data:{'id_design':$('#select_design_fitler').val(),'id_sub_design':$('#sub_design_filter').val()},

		dataType: 'json',

		cache:false,

		success:function(data){

		   		var list 	= data.list;

				var access	    = data.access;

				if(access.add == '0')

				{

					$('#add_size').attr('disabled','disabled');

				}

			 var oTable = $('#subdesign_list').DataTable();

			 oTable.clear().draw();

			 if (list!= null && list.length > 0)

			 {

				oTable = $('#subdesign_list').dataTable({

						"bDestroy": true,

						"bInfo": true,

						"bFilter": true,

						"bSort": true,

						"dom": 'lBfrtip',

						"buttons" : ['excel','print'],

    			        "lengthMenu": [ [ 100, 250, 500, -1], [100, 250, 500, "All"] ],

						"order": [[ 0, "desc" ]],

						"aaData"  : list,

						"aoColumns": [

						{ "mDataProp": "id_sub_design" },

					    /*{ "mDataProp": function ( row, type, val, meta ){

					        if(row.default_image!='' && row.default_image!=null)

					        {

					            return '<img src='+base_url+'assets/img/sub_design/'+row.id_sub_design+'/'+row.default_image+' width="40" height="35">';

					        }else{

					            return '<img src='+base_url+'assets/img/no_image.png'+' width="40" height="35">';

					        }

						}

						},*/

						{ "mDataProp": "sub_design_name" },

						{ "mDataProp": "sub_design_code" },

						{ "mDataProp": function ( row, type, val, meta ){

						active_url =(access.edit ==1 )? base_url+"index.php/admin_ret_catalog/ret_sub_design/update_status/"+row.id_sub_design+'/'+(row.status==0?1:0):'#';

						return "<a href='"+active_url+"'><i class='fa "+(row.status==1?'fa-check':'fa-remove')+"' style='color:"+(row.status==1?'green':'red')+"'></i></a>"

						}

						},

						{ "mDataProp": function ( row, type, val, meta ) {

							 id= row.id_sub_design;

							 edit_target=(base_url+'index.php/admin_ret_catalog/ret_sub_design/edit/'+id);

							 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/ret_sub_design/delete/'+id : '#' );

							 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

							//  action_content='<a href='+edit_target+' class="btn btn-primary btn-edit" id="edit" role="button"><i class="fa fa-edit" ></i></a><a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

							 var action_content='';

							if(access.edit == '1'){

														

								action_content+=  '<a href='+edit_target+' class="btn btn-primary btn-edit" id="edit" role="button"><i class="fa fa-edit" ></i></a>' ;

								

								}

								if(access.delete == '1'){

								 

								 action_content+='<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

								}

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

//Sub Design Master

//Product Mapping

$('#search_design_maping').on('click',function(){

    get_product_mapping_details();

});

function get_product_mapping_details()

{

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		url: base_url+"index.php/admin_ret_catalog/ret_products_mapping?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		data:{'id_product':$('#prod_filter').val(),'id_design':$('#select_design_fitler').val()},

		dataType: 'json',

		cache:false,

		success:function(data){

		   		var list 	= data.product;

				var access	    = data.access;

				if(access.delete=='0')

				 {

				     $('#delete_row').css("display","none");

				 }

				if(access.add == '0')

				{

					$('#add_size').attr('disabled','disabled');

				}

			 var oTable = $('#subdesign_list').DataTable();

			 oTable.clear().draw();

			 if (list!= null && list.length > 0)

			 {

				oTable = $('#subdesign_list').dataTable({

						"bDestroy": true,

						"bInfo": true,

						"bFilter": true,

						"bSort": true,

						"order": [[ 0, "desc" ]],

						"dom": 'lBfrtip',

						"buttons" : ['excel','print'],

    			        "lengthMenu": [ [ 100, 250, 500, -1], [100, 250, 500, "All"] ],

						"aaData"  : list,

						"aoColumns": [

					    { "mDataProp": function ( row, type, val, meta ){

	                	chekbox='<input type="checkbox" class="mapping_id" name="mapping_id[]" value="'+row.mapping_id+'"/>'

	                	return chekbox+" "+row.mapping_id;

		                }},

						{ "mDataProp": "product_name" },

						{ "mDataProp": "design_name" },

						{ "mDataProp": function ( row, type, val, meta ) {

							 id= row.mapping_id;

							 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/ret_products_mapping/delete/'+id : '#' );

							 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

							 action_content=(access.delete == '1' )? '<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>' :''

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

$('#update_product_design_mapping').on('click',function(){

	if($('#select_product').val()=='' || $('#select_product').val()==null)

	{

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Product..'});

		$("#update_product_design_mapping").prop('disabled',false);

	}else if($('#select_design').val()=='' || $('#select_design').val()==null)

	{

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Design..'});

		$("#update_product_design_mapping").prop('disabled',false);

	}

	else

	{

		update_product_mapping();

	}

});

function update_product_mapping()

{

	my_Date = new Date();

	$.ajax({

			 url:base_url+ "index.php/admin_ret_catalog/update_product_design_mapping?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),

			 data:  {'id_product':$('#select_product').val(),'id_design':$('#select_design').val()},

			 type:"POST",

			 dataType: "json",

			 async:false,

			 	  success:function(data){

			 	  	if(data.status)

			 	  	{

		 					$.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>"+data.msg});

			 	    		get_product_mapping_details();

			 	  	}

			 	  	else

			 	  	{

			 	  		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.msg});

			 	  	}

			 	  	$('#select_design').select2("val","");

			 	  	$('#select_product').select2("val","");

			 	  	$('#update_design').prop('disabled',false);

				  },

				  error:function(error)

				  {

					 $("div.overlay").css("display", "none");

				  }

		  });

}

     $("#delete_product_mapping").on('click',function(){

        if($("input[name='mapping_id[]']:checked").val())

        {

                var selected = [];

                var approve=false;

                $("#subdesign_list tbody tr").each(function(index, value)

                {

                if($(value).find("input[name='mapping_id[]']:checked").is(":checked"))

                {

                transData = {

                    'mapping_id'   : $(value).find(".mapping_id").val(),

                }

                selected.push(transData);

                }

                })

                req_data = selected;

                delete_product_mapping(req_data);

        }

        else

        {

            alert('Please Mapping Details..');

        }

    });

    function delete_product_mapping(data="")

    {

        my_Date = new Date();

        $("div.overlay").css("display", "block");

        $.ajax({

        url:base_url+ "index.php/admin_ret_catalog/delete_product_design_mapping?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),

        data:  {'req_data':data},

        type:"POST",

        async:false,

        dataType:'json',

        success:function(data){

            if(data.status)

            {

                $.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>"+data.msg});

            }

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

//Product Mapping

//sub design mapping

function get_ProductDesign()

{

    $('#select_design option').remove();

    $.ajax({

	type: 'POST',

	url: base_url+'index.php/admin_ret_catalog/get_ProductDesign',

	dataType:'json',

	data:{'id_product':$('#select_product').val()},

	success:function(data){

		var id =  $("#select_design").val();

		$.each(data, function (key, item) {

		   $("#select_design").append(

		   $("<option></option>")

		   .attr("value", item.design_no)

		   .text(item.design_name)

		   );

		});

		$("#select_design").select2(

		{

			placeholder:"Select Design",

			allowClear: true

		});

		$("#select_design").select2("val",(id!='' && id>0?id:''));

		$(".overlay").css("display", "none");

	}

	});

}

$('#search_sub_design_maping').on('click',function(){

    get_sub_design_mapping_details();

});

function get_sub_design_mapping_details()

{

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		url: base_url+"index.php/admin_ret_catalog/ret_subdesign_mapping?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		data:{'id_product':$('#prod_filter').val(),'id_design':$('#select_design_fitler').val(),'id_sub_design':$('#sub_design_filter').val()},

		dataType: 'json',

		cache:false,

		success:function(data){

		   		var list 	= data.product;

				var access	    = data.access;

				if(access.add == '0')

				{

					$('#add_size').attr('disabled','disabled');

				}

				if(access.delete=='0')

				 {

				     $('#delete_row').css("display","none");

				 }

			 var oTable = $('#subdesign_list').DataTable();

			 oTable.clear().draw();

			 if (list!= null && list.length > 0)

			 {

				oTable = $('#subdesign_list').dataTable({

						"bDestroy": true,

						"bInfo": true,

						"bFilter": true,

						"bSort": true,

						"order": [[ 0, "desc" ]],

						"aaData"  : list,

						"dom": 'lBfrtip',

						"buttons" : ['excel','print'],

    			        "lengthMenu": [ [ 100, 250, 500, -1], [100, 250, 500, "All"] ],

						"aoColumns": [

						 { "mDataProp": function ( row, type, val, meta ){

	                	chekbox='<input type="checkbox" class="id_sub_design_mapping" name="id_sub_design_mapping[]" value="'+row.id_sub_design_mapping+'"/>'

	                	return chekbox+" "+row.id_sub_design_mapping;

		                }},

						{ "mDataProp": "product_name" },

						{ "mDataProp": "design_name" },

						{ "mDataProp": "sub_design_name" },

						{ "mDataProp": function ( row, type, val, meta ){

					        if(row.default_image!='' && row.default_image!=null)

					        {

					            return '<a href='+base_url+'assets/img/sub_design/'+row.id_sub_design_mapping+'/'+row.default_image+' target="_blank"><img src='+base_url+'assets/img/sub_design/'+row.id_sub_design_mapping+'/'+row.default_image+' width="40" height="35"></a>';

					        }else{

					            return '<img src='+base_url+'assets/img/no_image.png'+' width="40" height="35">';

					        }

						}

						},

                        { "mDataProp": function ( row, type, val, meta ){

                            id= row.id_sub_design_mapping;

                            edit_target=("#imageModal_new");

                            content='<a href="#" class="btn btn-success btn-sm" id = "img_upload_order"  data-id='+id+'  onclick="subdesign_images_update('+id+')"><i class="fa fa-plus"></i></a>';

    	                    return content;

		                }},

		                { "mDataProp": function ( row, type, val, meta ){

                            id= row.id_sub_design_mapping;

                            content='<a href="#" class="btn btn-primary btn-sm" value=0 data-toggle="modal" onclick="get_karigar_products('+id+')"><i class="fa fa-plus"></i></a>';

                            return content;

		                }},

		                { "mDataProp": function ( row, type, val, meta ){

                            id= row.id_sub_design_mapping;

                            content='<a href="#" class="btn btn-default btn-sm" value=0 data-toggle="modal" onclick="subdesign_description_update('+id+')"><i class="fa fa-plus"></i></a>';

                            return content;

		                }},

		                {

                        "mDataProp": null,

                        "sClass": "control center",

                        "sDefaultContent":  '<span class="drill-val"><i class="fa fa-chevron-circle-down text-teal"></i></span>'

                        },

						{ "mDataProp": function ( row, type, val, meta ) {

							 id= row.id_sub_design_mapping;

							 if(access.detele==1)

							 {

							     $('#delete_sub_design_mapping').css("display","none");

							 }

							 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/ret_subdesign_mapping/delete/'+id : '#' );

							 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

							 action_content='<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

							 return action_content;

							 }

						}

					    ]

					});

					var anOpen =[];

            		$(document).on('click',"#subdesign_list .control", function(){

            		   var nTr = this.parentNode;

            		   var i = $.inArray( nTr, anOpen );

            		   if ( i === -1 ) {

            				$('.drill-val', this).html('<i class="fa fa-chevron-circle-up text-teal"></i>');

            				oTable.fnOpen( nTr, fnFormatRowSubDesignImages(oTable, nTr), 'details' );

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

$("#order_des_new").on("hidden.bs.modal", function(){

 CKEDITOR.instances.description_new.destroy();

});

function fnFormatRowSubDesignImages( oTable, nTr )

{

  var oData = oTable.fnGetData( nTr );

  var rowDetail = '';

  var prodTable =

     '<div class="innerDetails">'+

      '<table class="table table-responsive table-bordered text-center table-sm">'+

        '<tr class="bg-teal">'+

        '<th>S.No</th>'+

        '<th>Image</th>'+

        '</tr>';

    var karigarTable =

    '<div class="innerDetails">'+

    '<table class="table table-responsive table-bordered text-center table-sm">'+

    '<tr class="bg-teal">'+

    '<th>S.No</th>'+

    '<th>Karigar Name</th>'+

    '<th>Karigar Mobile</th>'+

    '<th>Karigar Code</th>'+

    '</tr>';

  var img_details = oData.img_details;

  var karigar_details = oData.karigar_details;

  $.each(img_details, function (idx, val) {

  	prodTable +=

        '<tr class="prod_det_btn">'+

        '<td>'+parseFloat(idx+1)+'</td>'+

        '<td><img src='+base_url+'assets/img/sub_design/'+val.id_sub_design_mapping+'/'+val.image_name+' width="40" height="35"></td>'+

        '</tr>';

  });

  $.each(karigar_details, function (idx, val) {

  	karigarTable +=

        '<tr class="prod_det_btn">'+

        '<td>'+parseFloat(idx+1)+'</td>'+

        '<td>'+val.karigar_name+'</td>'+

        '<td>'+val.mobile+'</td>'+

        '<td>'+val.code+'</td>'+

        '</tr>';

  });

  rowDetail = prodTable+'</table></div>'+karigarTable;

  return rowDetail;

}

function subdesign_images_update(id_sub_design_mapping)

{

    $('#imageModal_new').modal('show');

    $('#id_sub_design_mapping').val(id_sub_design_mapping);

    getsub_design_images(id_sub_design_mapping);

}

$('#close_img_modal').on('click',function(){

    window.location.reload();

});

$('#subdesignimg_submit').on('click', function(e) {

    //$('#imageModal_new'),modal('toggle');

    $("div.overlay").css("display", "block");

    var form_data=$('#subdesign_img_form').serialize();

    var url=base_url+ "index.php/admin_ret_catalog/update_sub_design_image?nocache=" + my_Date.getUTCSeconds();

        $.ajax({

            url:url,

            data: form_data,

            type:"POST",

            dataType:"JSON",

            success:function(data){

    			if(data.status)

    			{

    			    $.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>"+data.msg});

    			}else{

    			    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.msg});

    			}

    			window.location.reload();

    			$("div.overlay").css("display", "none");

            },

            error:function(error)

            {

                $("div.overlay").css("display", "none");

            }

        });

});

function subdesign_description_update(id_sub_design_mapping)

{

    $.ajax({

    data: ( {'id_sub_design_mapping':id_sub_design_mapping}),

		  url:base_url+ "index.php/admin_ret_catalog/ret_sub_design/sub_design_description?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),

		  dataType:"JSON",

		  type:"POST",

		  success:function(data){

		      $('#order_des_new').modal('show');

		      $('#id').val(id_sub_design_mapping);

			  description = data['description'];

			  CKEDITOR.replace('description_new');

	          CKEDITOR.instances.description_new.setData(description);

		  },

		  error:function(error)

			{

				$("div.overlay").css("display", "none");

			}

	  });

}

$('#close_des_modal').on('click',function(){

    window.location.reload();

});

$("#subdesigndes_submit").on('click',function()

{

	$('#order_des_new').modal('toggle');

	description=  CKEDITOR.instances.description_new.getData();

	my_Date = new Date();

    var form_data = new FormData();

    form_data.append('description', description);

	form_data.append('id_sub_design_mapping', id);

	$.ajax({

		data:form_data,

		url: base_url+"index.php/admin_ret_catalog/update_subdesign_des?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

        dataType:'json',

        cache : false,

		enctype: 'multipart/form-data',

		contentType : false,

		processData : false,

		success:function(data){

		    if(data.status)

			{

			  $.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>"+data.message});

			}else{

			  $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message});

			}

			window.location.reload();

        }

	});

});

$('#update_sup_design_mapping').on('click',function(){

	if($('#select_product').val()=='' || $('#select_product').val()==null)

	{

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Product..'});

		$("#update_sup_design_mapping").prop('disabled',false);

	}else if($('#select_design').val()=='' || $('#select_design').val()==null)

	{

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Design..'});

		$("#update_sup_design_mapping").prop('disabled',false);

	}

	else if($('#sub_design_sel').val()=='' || $('#sub_design_sel').val()==null)

	{

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Sub Design..'});

		$("#update_sup_design_mapping").prop('disabled',false);

	}

	else

	{

		update_sup_design_mapping();

	}

});

function update_sup_design_mapping()

{

	my_Date = new Date();

	$.ajax({

			 url:base_url+ "index.php/admin_ret_catalog/update_subdesign_mapping?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),

			 data:  {'id_product':$('#select_product').val(),'id_design':$('#select_design').val(),'id_sub_design':$('#sub_design_sel').val(),'images':JSON.stringify($('#sub_design_images').val())},

			 type:"POST",

			 dataType: "json",

			 async:false,

			 	  success:function(data){

			 	  	if(data.status)

			 	  	{

		 					$.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>"+data.msg});

			 	    		get_sub_design_mapping_details();

			 	  	}

			 	  	else

			 	  	{

			 	  		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.msg});

			 	  	}

			 	  	$('#select_design').select2("val","");

			 	  	$('#select_product').select2("val","");

			 	  	$('#sub_design_sel').select2("val","");

			 	  	$('#update_sup_design_mapping').prop('disabled',false);

				  },

				  error:function(error)

				  {

					 $("div.overlay").css("display", "none");

				  }

		  });

}

function get_karigar_products(id_sub_design_mapping)

{

    $.ajax({

    data: ( {'id_sub_design_mapping':id_sub_design_mapping}),

		  url:base_url+ "index.php/admin_ret_catalog/ret_sub_design/get_karigar_products?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),

		  dataType:"JSON",

		  type:"POST",

		  success:function(data){

                $('#karigar_modal').modal('show');

                $('#id_sub_des').val(id_sub_design_mapping);

                $('#karigar').val(data.id_karigar);

                $('#karigar_sel').select2("val",data.id_karigar);

                if($('#karigar').data('id_karigar'))

    			{

    			   var ar = $('#karigar').data('id_karigar');

                   $("#pkarigar_sel").select2('val',ar);

    			}

		  },

		  error:function(error)

			{

				$("div.overlay").css("display", "none");

			}

	  });

}

$("#karigar_prod_submit").on('click',function()

{

	$('#karigar_modal').modal('toggle');

	$.ajax({

		url: base_url+"index.php/admin_ret_catalog/update_karigar_products?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

        dataType:'json',

        data:{'id_karigar':$('#karigar_sel').val(),'id_sub_design_mapping':$('#id_sub_des').val()},

		success:function(data){

		    if(data.status)

			{

			  $.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>"+data.message});

			}else{

			  $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message});

			}

			window.location.reload();

        }

	});

});

 $("#delete_sub_design_mapping").on('click',function(){

        if($("input[name='id_sub_design_mapping[]']:checked").val())

        {

                var selected = [];

                var approve=false;

                $("#subdesign_list tbody tr").each(function(index, value)

                {

                if($(value).find("input[name='id_sub_design_mapping[]']:checked").is(":checked"))

                {

                transData = {

                    'id_sub_design_mapping'   : $(value).find(".id_sub_design_mapping").val(),

                }

                selected.push(transData);

                }

                })

                req_data = selected;

                delete_sub_design_mapping(req_data);

        }

        else

        {

            alert('Please Mapping Details..');

        }

    });

    function delete_sub_design_mapping(data="")

    {

        my_Date = new Date();

        $("div.overlay").css("display", "block");

        $.ajax({

        url:base_url+ "index.php/admin_ret_catalog/delete_sub_design_mapping?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),

        data:  {'req_data':data},

        type:"POST",

        async:false,

        dataType: "json",

        success:function(data){

            if(data.status)

            {

                $.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>"+data.msg});

            }

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

//sub design mapping

//Image Compression

$('#sub_design_images').on('change',function(){

	validateSubDesignImages();

});

function validateSubDesignImages()

{

    $("div.overlay").css("display", "block");

        $('#sub_design_preview').html('');

		var preview = $('#sub_design_preview');

		var files   = event.target.files;

		 for (var i = 0; i < files.length; i++)

		 {

			    const compress           = new Compress();

			    const product_images     = [files[i]];

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

								  img_resource.push({"src":output.prefix +output.data,'name':output.alt,'is_default':"0"});

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

			resource     = img_resource;

		    set_image_preview();

		  },500);

}

function getsub_design_images(id_sub_design_mapping)

{

    $("div.overlay").css("display", "block");

    my_Date = new Date();

	$.ajax({

	 url:base_url+ "index.php/admin_ret_catalog/ret_sub_design/sub_design_images?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),

	 data:  {'id_sub_design_mapping':id_sub_design_mapping},

	 type:"POST",

	 dataType: "json",

	 async:false,

	 	  success:function(data){

	 	    if(data.length>0)

	 	    {

	 	        $.each(data,function(key,items){

                    convertImgToBase64(base_url+'assets/img/sub_design/'+items['id_sub_design_mapping']+'/'+items['image_name'], function(base64Img){

                        img_resource.push({"src":base64Img,'name':items['image_name'],'is_default':items['is_default']});

                    });

	 	        });

	 	         setTimeout(function(){

        		        set_image_preview();

        		 },3000);

	 	       $("div.overlay").css("display", "none");

	 	    }

		  },

		  error:function(error)

		  {

			 $("div.overlay").css("display", "none");

		  }

    });

}

function convertImgToBase64(url, callback, outputFormat){

	var canvas = document.createElement('CANVAS');

	var ctx = canvas.getContext('2d');

	var img = new Image;

	img.crossOrigin = 'Anonymous';

	img.onload = function(){

		canvas.height = img.height;

		canvas.width = img.width;

	  	ctx.drawImage(img,0,0);

	  	var dataURL = canvas.toDataURL(outputFormat || 'image/png');

	  	callback.call(this, dataURL);

        // Clean up

	  	canvas = null;

	};

	img.src = url;

}

function remove_order_images(param)

{

	localStorage.removeItem("img_details");

	$('#order_img_'+param.key).remove();

	console.log(param);

	img_resource.splice(param.key,1);

	localStorage.setItem('img_details',JSON.stringify(img_resource));

	console.log(localStorage);

	console.log(img_resource);

}

function set_image_preview()

{

    $("div.overlay").css("display", "block");

    var trHtml='';

    $.each(img_resource,function(key,item){

        trHtml+='<tr class="'+key+'">'

                    +'<td><input type="checkbox" class="is_default_img" '+(item.is_default==1 ? 'checked' :'')+' >'+parseInt(key+1)+'</td>'

                    +'<td><img class="thumbnail" src="'+item.src+'" style="width:100px;height:100px;"/></td>'

                    +'<td><a href="#" onClick="remove_row($(this).closest(\'tr\'),'+key+')" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'

                +'</tr>';

    });

    $('#design_img_preview > tbody').html(trHtml);

    $('#subdesign_images').val(JSON.stringify(img_resource));

    $("div.overlay").css("display", "none");

}

function remove_row(curRow,curRowkey)

{

     $("div.overlay").css("display", "block");

     curRow.remove();

      $.each(img_resource,function(key,item){

          if(key==curRowkey)

          {

              	img_resource.splice(curRowkey,1);

          }

      });

     set_image_preview();

}

$(document).on('change','.is_default_img',function(e){

    var imgsrc=$(this).closest('tr').attr('class');

    $.each(img_resource,function(key,item){

          if(key==imgsrc)

          {

              img_resource[key].is_default=1

          }

    });

     $('#subdesign_images').val(JSON.stringify(img_resource));

    $('#design_img_preview > tbody tr').each(function(idx, row){

        curRow = $(this);

        if(imgsrc==idx)

        {

            curRow.find('.is_default_img').prop('checked',true);

        }else{

            curRow.find('.is_default_img').prop('checked',false);

        }

    });

});

//Image Compression

$('#sub_design_name').on('keyup',function(){

    var sub_design_name = $("#sub_design_name").val();

     if(this.value.length>4)

     {

         getSubDesignName(sub_design_name);

     }

});

function getSubDesignName(sub_design_name){

	my_Date = new Date();

	$.ajax({

        url: base_url+'index.php/admin_ret_catalog/getSubDesignName/?nocache=' + my_Date.getUTCSeconds(),

        dataType: "json",

        method: "POST",

        data: {'sub_design_name': sub_design_name},

        success: function (data) {

			$("#sub_design_name").autocomplete(

			{

				source: data,

				select: function(e, i)

				{

					e.preventDefault();

					$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Sub Design Already Exists..'});

					$('#sub_design_name').val('');

				},

				change: function (event, ui) {

					if (ui.item === null) {

					}

			    },

				response: function(e, i) {

		            // ui.content is the array that's about to be sent to the response callback.

		            if(sub_design_name != ""){

						if (i.content.length === 0) {

						}

						else{

						}

					}else{

					}

		        },

				 minLength: 1,

			});

        }

     });

}

$('#design_name').on('keyup',function(){

    var design_name = $("#design_name").val();

     if(this.value.length>4)

     {

         getDesignName(design_name);

     }

});

function getDesignName(design_name){

	my_Date = new Date();

	$.ajax({

        url: base_url+'index.php/admin_ret_catalog/getDesignName/?nocache=' + my_Date.getUTCSeconds(),

        dataType: "json",

        method: "POST",

        data: {'design_name': design_name},

        success: function (data) {

			$("#design_name").autocomplete(

			{

				source: data,

				select: function(e, i)

				{

					e.preventDefault();

					$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Design Already Exists..'});

					$('#design_name').val('');

				},

				change: function (event, ui) {

					if (ui.item === null) {

					}

			    },

				response: function(e, i) {

		            // ui.content is the array that's about to be sent to the response callback.

		            if(design_name != ""){

						if (i.content.length === 0) {

						}

						else{

						}

					}else{

					}

		        },

				 minLength: 1,

			});

        }

     });

}

$('#prod_filter').on('change',function(){

    get_ProductDesigns();

});

function get_ProductDesigns(){

    $('#select_design_fitler option').remove();

	my_Date = new Date();

	$.ajax({

        url: base_url+'index.php/admin_ret_catalog/get_ProductDesign/?nocache=' + my_Date.getUTCSeconds(),

        dataType: "json",

        method: "POST",

        data: {'id_product': $('#prod_filter').val()},

        success: function (data) {

			$.each(data, function (key, item) {

    		   $("#select_design_fitler").append(

    		   $("<option></option>")

    		   .attr("value", item.design_no)

    		   .text(item.design_name)

    		   );

    		});

    		$("#select_design_fitler").select2(

    		{

    			placeholder:"Select Design",

    			allowClear: true

    		});

    		if($("#select_design_fitler").length)

    		{

    		    $("#select_design_fitler").select2("val",'');

    		}

        }

     });

}

//Sub Design and Design Mapping

$("input[name='design[wastage_type]']").change(function () {

        $('#wc_detail tbody').empty();

        var value = $("input[name='design[wastage_type]']:checked").val();

        if(value == 1){

            $(".flexiable_type").css("display", "none");

            $(".fixed_type").css("display", "block");

        }else{

            $(".flexiable_type").css("display", "block");

            $(".fixed_type").css("display", "none");

        }

});

 $("#add_wc_weight_info").on('click',function(){

    var a= $('#wc_detail tbody tr').length;

	var html = "";

	var i = ++a;

	html+="<tr id='wc"+i+"'><td>"+i+"</td><td><input name='wcrange["+i+"][from_wt]' step='.001' id='from_wt"+i+"' class='form-control from_wt' placeholder='From weight' type='number'></td><td><input name='wcrange["+i+"][to_wt]'  step='.001' id='to_wt"+i+"' class='form-control to_wt' placeholder='To weight' type='number'></td><td style='width: 90px;'><input name='wcrange["+i+"][wc_method]' id='wc_method_pc"+i+"' class='wc_method_pc' type='radio' checked> <label for='wc_method_pc"+i+"'>Pc</label> &nbsp; <input name='wcrange["+i+"][wc_method]' id='wc_method_wt"+i+"' class='wc_method_wt' type='radio'><label for='wc_method_wt"+i+"'>Wt</label></td><td><input name='wcrange["+i+"][wc_percent]'  step='.001' id='wc_percent"+i+"' class='form-control wc_percent' placeholder='VA(%)' type='number'></td><td><input name='wcrange["+i+"][wc_wt]'  step='.001' id='wc_wt"+i+"' class='form-control wc_wt' placeholder='VA(Wt)' type='number' readonly></td><td><input name='wcrange["+i+"][wc_min]'  step='.001' id='wc_min"+i+"' class='form-control wc_min' placeholder='Min VA' type='number'></td><td><input name='wcrange["+i+"][wc_min_wt]'  step='.001' id='wc_min_wt"+i+"' class='form-control wc_min_wt' placeholder='Min VA(Wt)' type='number' readonly></td><td style='display:none'><input name='wcrange["+i+"][wc_max]' step='.001' id='wc_max"+i+"' class='form-control wc_max' placeholder='Max VA' type='number'></td><td><input name='wcrange["+i+"][mc_percent]'  step='.001' id='mc_percent"+i+"' class='form-control mc_percent' placeholder='MC(%)' type='number'></td><td><input name='wcrange["+i+"][mcrg_min]'  step='.001' id='mcrg_min"+i+"' class='form-control mcrg_min' placeholder='MC Min' type='number'></td><td style='display:none' ><input name='wcrange["+i+"][mcrg_max]' step='.001' id='mcrg_max"+i+"' class='form-control mcrg_max' placeholder='MC Max' type='number'></td><td><a href='#' onClick='remove_row($(this).closest(\'tr\'));' class='btn btn-danger btn-del'><i class='fa fa-trash'></i></a></td>";

	/*html+="<tr id='wc"+i+"'><td>"+i+"</td><td><input name='wcrange["+i+"][from_wt]' step='.001' id='from_wt"+i+"' class='form-control from_wt' placeholder='From weight' type='number'></td><td><input name='wcrange["+i+"][to_wt]'  step='.001' id='to_wt"+i+"' class='form-control to_wt' placeholder='To weight' type='number'></td><td><input name='wcrange["+i+"][wc_percent]'  step='.001' id='wc_percent"+i+"' class='form-control wc_percent' placeholder='VA(%)' type='number'></td><td><input name='wcrange["+i+"][wc_min]'  step='.001' id='wc_min"+i+"' class='form-control wc_min' placeholder='Min VA' type='number'></td><td style='display:none'><input name='wcrange["+i+"][wc_max]' step='.001' id='wc_max"+i+"' class='form-control wc_max' placeholder='Max VA' type='number'></td><td><input name='wcrange["+i+"][mc_percent]'  step='.001' id='mc_percent"+i+"' class='form-control mc_percent' placeholder='MC(%)' type='number'></td><td><input name='wcrange["+i+"][mcrg_min]'  step='.001' id='mcrg_min"+i+"' class='form-control mcrg_min' placeholder='MC Min' type='number'></td><td style='display:none' ><input name='wcrange["+i+"][mcrg_max]' step='.001' id='mcrg_max"+i+"' class='form-control mcrg_max' placeholder='MC Max' type='number'></td><td><a href='#' onClick='remove_row($(this).closest(\'tr\'));' class='btn btn-danger btn-del'><i class='fa fa-trash'></i></a></td>";*/

  	$('#wc_detail').append(html);

});

$(document).on('click',"#edit_mas_design_list tbody tr a.btn-wc-view", function(){

	id=$(this).data('id');

	 wastage_view(id);

 }) ;

 $(document).on('click',"#edit_mas_design_list tbody tr a.btn-attribute-view", function(){

	id=$(this).data('id');

	 attribute_view(id);

 }) ;

function wastage_view(id)

{

    my_Date = new Date();

    $.ajax({

        type:"GET",

        url: base_url+"index.php/admin_ret_catalog/get_wastage_details/"+id+"?nocache=" + my_Date.getUTCSeconds(),

        cache:false,

        dataType:"JSON",

        success:function(data)

        {

            var wastage = data;

            var oTable = $('#wcdetail').DataTable();

            oTable.clear().draw();

            oTable = $('#wcdetail').dataTable({

            "bDestroy": true,

            "bInfo": true,

            "order": [[ 0, "desc" ]],

            "bFilter": true,

            "bSort": true,

            "aaData": wastage,

            "aoColumns": [

            { "mDataProp" : "id_wc" },

            { "mDataProp" : "wc_from_weight" },

            { "mDataProp" : "wc_to_weight" },

            { "mDataProp" : "wc_percent"},

            { "mDataProp" : "wc_min"},

			{ "mDataProp" : "wc_max"},

			{ "mDataProp" : "mc"},

			{ "mDataProp" : "mcrg_min"},

			{ "mDataProp" : "mcrg_max"},

			{ "mDataProp": function ( row, type, val, meta ) {

				let des_wc_id = row.id_wc;

				delete_url = base_url+'index.php/admin_ret_catalog/delete_design_weight_range/'+des_wc_id;

				action_content='<a href="#" class="btn btn-danger btn-del delete_design_weight_range" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" data-parent="'+id+'" ><i class="fa fa-trash"></i></a>'

				return action_content;

				}

		   	}

            ]

            });

        }

    });

}

function attribute_view(id)

{

	$('#des_attribute_detail tbody').empty();

    my_Date = new Date();

    $.ajax({

        type:"GET",

        url: base_url+"index.php/admin_ret_catalog/get_design_attr_values/"+id+"?nocache=" + my_Date.getUTCSeconds(),

        cache:false,

        dataType:"JSON",

        success:function(data)

        {

            var attribute = data;

            var oTable = $('#des_attribute_detail').DataTable();

            oTable.clear().draw();

            oTable = $('#des_attribute_detail').dataTable({

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

				let des_attr_id = row.attr_des_id;

				delete_url = base_url+'index.php/admin_ret_catalog/delete_design_attribute/'+des_attr_id;

				action_content='<a href="#" class="btn btn-danger btn-del delete_design_attribute" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" data-parent="'+id+'" ><i class="fa fa-trash"></i></a>'

				return action_content;

				}

		   	}

            ]

            });

        }

    });

}

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

/** Attribute Master Starts */

$("#chk_attr_status").bootstrapSwitch();

$("#chk_attr_status").on('switchChange.bootstrapSwitch', function (event, state) {

	var x=$(this).data('on-text');

	var y=$(this).data('off-text');

	if($("#chk_attr_status").is(':checked') && x=='ACTIVE')  {

		$("#attr_status").val(1);

	} else if(y=='INACTIVE') {

		$("#attr_status").val(0);

	}

});

function set_attribute_table(from_date = "", to_date = "") {

	my_Date = new Date();

	$.ajax({

			url:base_url+ "index.php/admin_ret_catalog/attribute?nocache=" + my_Date.getUTCSeconds(),

			dataType:"JSON",

			data: ( {'from_date':from_date,'to_date':to_date}),

			type:"POST",

			success:function(data) {

			var attribute 	= data.attribute;

			var access		= data.access;

			$('#total_count').text(attribute.length);

			if(access.add == '0') {

				$('#attribute_add').attr('disabled','disabled');

			}

			var oTable = $('#attribute_list').DataTable();

			oTable.clear().draw();

			if (attribute!= null && attribute.length > 0) {

				oTable = $('#attribute_list').dataTable({

					"bDestroy": true,

					"bInfo": true,

					"order": [[ 0, "desc" ]],

					"bFilter": true,

					"scrollX":'100%',

					"bSort": true,

					"dom": 'lBfrtip',

					"buttons" : ['excel','print'],

					"tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },

					"aaData": attribute,

					"aoColumns": [	{ "mDataProp": "attr_id" },

									{ "mDataProp": "attr_name" },

									{ "mDataProp": function ( row, type, val, meta ){

										status_url =(access.edit =='1')? base_url+"index.php/admin_ret_catalog/attribute/update_status/"+row.attr_id+"/"+(row.attr_status==1?0:1):'#';

										return "<a href='"+status_url+"'><i class='fa "+(row.attr_status==1?'fa-check':'fa-remove')+"'style='color:"+(row.attr_status==1?'green':'red')+"'></i></a>"

									}

									},

									{ "mDataProp": function ( row, type, val, meta ) {

										id= row.attr_id;

										edit_url=(access.edit=='1' ? base_url+'index.php/admin_ret_catalog/attribute/edit/'+id : '#' );

										delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/attribute/delete/'+id : '#' );

										delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

										// action_content='<a href="'+edit_url+'" class="btn btn-primary btn-edit"><i class="fa fa-edit" ></i></a><a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

										

										var action_content = "";

                    if (access.edit == "1") {

                      action_content +=

                        '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id=' +

                        id +

                        "  data-target=" +

                        edit_target +

                        '><i class="fa fa-edit" ></i></a>';

                    }

                    if (access.delete == "1") {

                      action_content +=

                        '<a href="#" class="btn btn-danger btn-del" data-href=' +

                        delete_url +

                        ' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>';

                    }

										return action_content;

										}

									}]

				});

			}

		},

		error:function(error)

		{

			$("div.overlay").css("display", "none");

		}

	});

}

function add_attribute()

{

	if(validate_attributes()) {

		let _html_add = '<div class="row attributes">'+

							'<div class="col-md-3">'+

								'<div class="form-group">'+

									'<input class="form-control attr_val" name="attr[attr_val][]"  type="text" placeholder="Attribute Value" />'+

								'</div>'+

							'</div>'+

							'<div class="col-md-2 attribute_buttons">'+

								'<div class="form-group">'+

								'</div>'+

							'</div>'+

						'</div>';

		$(".attribute-values").append(_html_add);

		add_attribute_buttons();

	} else {

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill The Required Fields..'});

	}

}

function remove_attribute(itemObj) {

	$(itemObj).parent().parent().parent().remove();

	add_attribute_buttons();

}

function validate_attributes() {

	let attributes_validated = true;

	$('.attribute-values').children('.attributes').each(function () {

		let attr_val =  $.trim($(this).find('.attr_val').val());

		if(attr_val == "") {

			attributes_validated = false;

			return false;

		}

	});

	return attributes_validated;

	return true;

}

function add_attribute_buttons() {

	$('.attribute-values .attributes .add_attribute').remove();

	$('.attribute-values .attributes .remove_attribute').remove();

	let total_attributes = $('.attribute-values .attributes').length;

	if(total_attributes == 1) {

		$('.attribute-values .attributes:last-child .attribute_buttons > div').prepend('<button type="button" class="btn btn-success add_attribute"><i class="fa fa-plus"></i></button>');

	} else {

		$('.attribute-values .attributes:last-child .attribute_buttons > div').prepend('<button type="button" class="btn btn-success add_attribute"><i class="fa fa-plus"></i></button>');

		$('.attribute-values .attributes .attribute_buttons > div').append('<button type="button" class="btn btn-danger remove_attribute"><i class="fa fa-trash"></i></button>');

	}

}

function get_activeAttributes(itemObj)

{

	let _parent = $(itemObj).closest('tr');

	$(_parent).find(".des_upd_attr_name").empty();

	let attr_names_href = base_url+'index.php/admin_ret_catalog/attribute/active_list';

	let attr_names_data = {}

	_ajaxCallPost(attr_names_href, attr_names_data)

	.then((data) => {

		$.each(data, function (key, item) {

			$(_parent).find(".des_upd_attr_name").append(

				$("<option></option>")

				.attr("value", item.attr_id)

				.text(item.attr_name)

			);

		});

		$(_parent).find(".des_upd_attr_name").select2({

			placeholder:"Select Attribute",

			allowClear: true

		});

		$(_parent).find(".des_upd_attr_name").select2("val",'');

	})

	.catch((error) => {

		console.log("Error On get_activeAttributes ",error);

		$.toaster({ priority : 'danger', title : 'Warning!', message : "Error occured in retriving data."});

	});

}

function get_attributeValues(itemObj)

{

	let _parent = $(itemObj).closest('tr');

	let attr_id = $(_parent).find(".des_upd_attr_name").val();

	$(_parent).find(".des_upd_attr_value").empty();

	let attr_values_href = base_url+'index.php/admin_ret_catalog/attribute/activeValues_list';

	let attr_values_data = {

		"attr_id" : attr_id

	}

	_ajaxCallPost(attr_values_href, attr_values_data)

	.then((data) => {

		$.each(data, function (key, item) {

			$(_parent).find(".des_upd_attr_value").append(

				$("<option></option>")

				.attr("value", item.attr_val_id)

				.text(item.attr_val)

			);

		});

		$(_parent).find(".des_upd_attr_value").select2({

			placeholder:"Select Attribute Value",

			allowClear: true

		});

		$(_parent).find(".des_upd_attr_value").select2("val",'');

	})

	.catch((error) => {

		console.log("Error On get_activeAttributes ",error);

		$.toaster({ priority : 'danger', title : 'Warning!', message : "Error occured in retriving data."});

	});

}

$('#add_newattribute').on('click',function() {

	if($.trim($('#attr_name').val()) == '' || $.trim($('#attr_status').val()) == '')

	{

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill The Required Fields..'});

	}

	else if(!validate_attributes())

	{

	  	$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Attributes Not Added.Please Fill The Required Fields..'});

	}

	else

	{

		$('#attribute_form').submit();

	}

});

$('#attribute_date').daterangepicker({

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

		set_attribute_table(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));

		$('#user1').text(start.format('YYYY-MM-DD'));

		$('#user2').text(end.format('YYYY-MM-DD'));

	}

);

/** Attribute Master Ends */

/** MC / VA / Attribute Settings */

function add_des_attribute()

{

	if(validate_desAttr_row()) {

		let attr_row_last = $("#update_attribute_detail tbody tr:last-child");

		let sno = (attr_row_last.length > 0 ? parseInt(attr_row_last.find('.sno').text()) : 0)+1;

		let _html_add = '<tr class="attr_row">'+

							'<td width="10%" class="sno">'+

								sno+

							'</td>'+

							'<td width="40%">'+

								'<select class="form-control des_upd_attr_name" name="desAttr[attr_name][]" placeholder="Attribute Name"></select>'+

							'</td>'+

							'<td width="40%">'+

								'<select class="form-control des_upd_attr_value" name="desAttr[attr_value][]" placeholder="Attribute Value"></select>'+

							'</td>'+

							'<td width="10%" class="attribute_row_buttons">'+

								'<button type="button" class="btn btn-danger remove_des_attribute"><i class="fa fa-trash"></i></button>'+

							'</td>'+

						'</tr>';

		$("#update_attribute_detail tbody").append(_html_add);

		$('#update_attribute_detail .attr_row:last-child').find('.des_upd_attr_name').select2();

		$('#update_attribute_detail .attr_row:last-child').find('.des_upd_attr_value').select2();

		get_activeAttributes($('#update_attribute_detail .attr_row:last-child').find('.des_upd_attr_name'));

	} else {

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill The Required Fields..'});

	}

}

function remove_des_attribute(itemObj)

{

	$(itemObj).closest('tr').remove();

}

function validate_desAttr_row()

{

	let row_validated = true;

	let des_attr_rows = $("#update_attribute_detail tbody tr");

	$.each(des_attr_rows, function (attrkey, attritem) {

		let attr_name = $(attritem).find('.des_upd_attr_name').val();

		let attr_value = $(attritem).find('.des_upd_attr_value').val();

		if(!attr_name > 0 || !attr_value > 0) {

			row_validated = false;

			return false;

		}

	});

	return row_validated;

}

function delete_design_settings(href)

{

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

/** Product Charges */

active_charges = [];

function get_ActiveCharges() {

	active_charges = [];

	let charges_href = base_url+"index.php/admin_ret_catalog/charges/getActiveChargesList/?nocache=" + my_Date.getUTCSeconds();

	let charges_data = {};

	_ajaxCallPost(charges_href, charges_data)

	.then((data) => {

		console.log("charges_details", data);

		active_charges = data;

	})

	.catch((error) => {

		console.log("Error On get_ActiveCharges ",error);

		$.toaster({ priority : 'danger', title : 'Warning!', message : "Error occured in retriving charges for product."});

	});

}

function get_ActiveChargesForProduct(itemObj) {

	$(itemObj).empty();

	let charges_href = base_url+"index.php/admin_ret_catalog/charges/getActiveChargesList/?nocache=" + my_Date.getUTCSeconds();

	let charges_data = {};

	$(active_charges).each(function(idx, charges) {

		$(itemObj).append(

			$("<option></option>")

			.attr("value", charges.id_charge)

			.text(charges.name_charge)

		);

	});

	$(itemObj).select2({

		placeholder:"Select Charge",

		allowClear: true

	});

	$(itemObj).select2("val",'');

}

function add_prod_charges_value(row) {

	let prod_charge_name =  $(row).find(".prod_charge_name").val();

	$(active_charges).each(function(idx, charges){

		console.log(" id charge ",charges.id_charge);

		if(charges.id_charge == prod_charge_name) {

			$(row).find(".prod_charge_value").val(charges.value_charge);

		}

	});

}

function add_product_charges()

{

	if(validate_product_charges()) {

		let _html_add = '<div class="row row_prod_charges">'+

							'<div class="col-md-3">'+

								'<div class="form-group">'+

									'<select class="form-control prod_charge_name" name="product[charges][id][]" placeholder="Charge Name"></select>'+

								'</div>'+

							'</div>'+

							'<div class="col-md-2">'+

								'<div class="form-group">'+

									'<input class="form-control prod_charge_value" name="product[charges][value][]"  type="number" placeholder="Charge Value" step="any" />'+

								'</div>'+

							'</div>'+

							'<div class="col-md-1 product_charges_button">'+

								'<div class="form-group">'+

								'</div>'+

							'</div>'+

						'</div>';

		$(".prod_charges").append(_html_add);

		add_product_charges_buttons();

		$('.prod_charges .row_prod_charges:last-child').find('.prod_charge_name').select2();

		get_ActiveChargesForProduct($('.prod_charges .row_prod_charges:last-child').find('.prod_charge_name'));

	} else {

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill The Required Fields For Charges..'});

	}

}

function remove_product_charges(itemObj) {

	$(itemObj).remove();

	add_product_charges_buttons();

}

function validate_product_charges() {

	let charges_validated = true;

	$('.prod_charges').children('.row_prod_charges').each(function () {

		let prod_charge_name =  parseFloat($.trim($(this).find('.prod_charge_name').val()));

		let prod_charge_value =  parseFloat($.trim($(this).find('.prod_charge_value').val()));

		if(!(prod_charge_name > 0) || !(prod_charge_value >= 0)) {

			charges_validated = false;

			return false;

		}

	});

	return charges_validated;

}

function add_product_charges_buttons() {

	$('.prod_charges .row_prod_charges .product_charges_button .addProdCharge').remove();

	$('.prod_charges .row_prod_charges .product_charges_button .removeProdCharge').remove();

	$('.prod_charges .row_prod_charges:last .product_charges_button > div').prepend('<button type="button" class="btn btn-success addProdCharge"><i class="fa fa-plus"></i></button>');

	$('.row_prod_charges .product_charges_button > div').append('<button type="button" class="btn btn-danger removeProdCharge"><i class="fa fa-trash"></i></button>');

}

/** Product Charges */

function get_active_design_products()

{

	$('#des_select option').remove();

	$(".overlay").css('display','block');

	$.ajax({

		type: 'POST',

		url: base_url+'index.php/admin_ret_catalog/get_active_design_products',

		dataType:'json',

		data:{'id_product':$('#product_select').val()},

		success:function(data){

			var id =  $('#des_select').val();

			$.each(data, function (key, item) {

				$('#des_select').append(

				$("<option></option>")

				.attr("value", item.design_no)

				.text(item.design_name)

				);

			});

			$("#des_select,#ed_des_select").select2({

				placeholder: "Design",

				allowClear: true

			});

			$("#des_select").select2("val",(id!='' && id>0?id:''));

			$(".overlay").css("display", "none");

		}

	});

}

//Karigar Product Mapping

/*

$('#prod_filter').on('change',function()

{

    if(this.value!='')

    {

        if(ctrl_page[1]=='ret_karigar_product')

        {

            get_ActiveDesign(this.value);

        }

    }

});

$('#search_karigar_products').on('click',function()

{

    set_karigar_product_list();

});

function get_ActiveDesign(id_product="")

{

    $('#select_design option').remove();

    $('#select_design_fitler option').remove();

    $.ajax({

	type: 'POST',

	url: base_url+'index.php/admin_ret_catalog/get_ActiveDesign',

	dataType:'json',

	data:{'id_product':id_product},

	success:function(data){

		var id =  $("#select_design").val();

		$.each(data, function (key, item) {

		   $("#select_design,#select_design_fitler").append(

		   $("<option></option>")

		   .attr("value", item.design_no)

		   .text(item.design_name)

		   );

		});

		$("#select_design,#select_design_fitler").select2(

		{

			placeholder:"Select Design",

			allowClear: true

		});

		$("#select_design").select2("val",(id!='' && id>0?id:''));

		if($("#select_design_fitler").length)

		{

		    $("#select_design_fitler").select2("val",'');

		}

		$(".overlay").css("display", "none");

	}

	});

}

function set_karigar_product_list()

{

    $("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		url: base_url+"index.php/admin_ret_catalog/ret_karigar_product?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		data:{'id_product':$('#prod_filter').val(),'id_design':$('#select_design_fitler').val(),'id_karigar':$('#select_karigar_filter').val()},

		dataType: 'json',

		cache:false,

		success:function(data){

		   		var list 	= data.list;

				var access	    = data.access;

				if(access.add == '0')

				{

					$('#add_size').attr('disabled','disabled');

				}

			 var oTable = $('#subdesign_list').DataTable();

			 oTable.clear().draw();

			 if (list!= null && list.length > 0)

			 {

				oTable = $('#subdesign_list').dataTable({

						"bDestroy": true,

						"bInfo": true,

						"bFilter": true,

						"bSort": true,

						"dom": 'lBfrtip',

						"buttons" : ['excel','print'],

    			        "lengthMenu": [ [ 100, 250, 500, -1], [100, 250, 500, "All"] ],

						"order": [[ 0, "desc" ]],

						"aaData"  : list,

						"aoColumns": [

					    { "mDataProp": function ( row, type, val, meta ){

	                	chekbox='<input type="checkbox" class="id_karigar_product" name="id_karigar_product[]" value="'+row.id_karigar_product+'"/>'

	                	return chekbox+" "+row.id_karigar_product;

		                }},

						{ "mDataProp": "karigar_name" },

						{ "mDataProp": "product_name" },

						{ "mDataProp": "design_name" },

						{ "mDataProp": function ( row, type, val, meta ){

						active_url =base_url+"index.php/admin_ret_catalog/ret_karigar_product/update_status/"+row.id_karigar_product+'/'+(row.status==0?1:0);

						return "<a href='"+active_url+"'><i class='fa "+(row.status==1?'fa-check':'fa-remove')+"' style='color:"+(row.status==1?'green':'red')+"'></i></a>"

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

$('#update_product_mapping').on('click',function(){

	if($('#select_product').val()=='' || $('#select_product').val()==null)

	{

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Product..'});

		$("#update_product_mapping").prop('disabled',false);

	}else if($('#select_design').val()=='' || $('#select_design').val()==null)

	{

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Design..'});

		$("#update_product_mapping").prop('disabled',false);

	}

	else if($('#karigar_sel').val()=='' || $('#karigar_sel').val()==null)

	{

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Karigar..'});

		$("#update_product_mapping").prop('disabled',false);

	}

	else

	{

		update_product_mapping();

	}

});

function update_product_mapping()

{

	my_Date = new Date();

	$.ajax({

			 url:base_url+ "index.php/admin_ret_catalog/update_product_mapping?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),

			 data:  {'id_product':$('#select_product').val(),'id_design':$('#select_design').val(),'id_karigar':$('#karigar_sel').val()},

			 type:"POST",

			 dataType: "json",

			 async:false,

			 	  success:function(data){

			 	  	if(data.status)

			 	  	{

		 					$.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>"+data.msg});

			 	    		set_karigar_product_list();

			 	  	}

			 	  	else

			 	  	{

			 	  		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.msg});

			 	  	}

			 	  	$('#select_design').select2("val","");

			 	  	$('#select_product').select2("val","");

			 	  	$('#karigar_sel').select2("val","");

			 	  	$('#update_design').prop('disabled',false);

				  },

				  error:function(error)

				  {

					 $("div.overlay").css("display", "none");

				  }

		  });

}

  $("#delete_product_mapping").on('click',function(){

        if($("input[name='id_karigar_product[]']:checked").val())

        {

                var selected = [];

                var approve=false;

                $("#subdesign_list tbody tr").each(function(index, value)

                {

                if($(value).find("input[name='id_karigar_product[]']:checked").is(":checked"))

                {

                transData = {

                    'id_karigar_product'   : $(value).find(".id_karigar_product").val(),

                }

                selected.push(transData);

                }

                })

                req_data = selected;

                delete_product_mapping(req_data);

        }

        else

        {

            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Product."});

        }

    });

    function delete_product_mapping(data="")

    {

        my_Date = new Date();

        $("div.overlay").css("display", "block");

        $.ajax({

        url:base_url+ "index.php/admin_ret_catalog/delete_product_mapping?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),

        data:  {'req_data':data},

        type:"POST",

        async:false,

        dataType:'json',

        success:function(data){

            if(data.status)

            {

                $.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>"+data.msg});

            }

            else

            {

                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.msg});

            }

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

//Karigar Product Mapping*/

//web devices

function get_ActiveCounter()

{

    $('#counter_select option').remove();

	$(".overlay").css('display','block');

	$.ajax({

		type: 'POST',

		url: base_url+'index.php/admin_ret_catalog/get_ActiveCounter',

		dataType:'json',

		data:{'id_branch':$('#branch_select').val(),'id_floor':$('#counter_flr_sel').val()},

		success:function(data){

		   var id =  $('#id_counter').val();

		   $.each(data, function (key, item) {

			   		$('#counter_select').append(

						$("<option></option>")

						.attr("value", item.counter_id)

						  .text(item.counter_name )

					);

			});

			$("#counter_select").select2({

			    placeholder: "Select Counter",

			    allowClear: true

		    });

		 $("#counter_select").select2("val",(id!='' && id>0?id:''));

    			 $(".overlay").css("display", "none");

    		}

	});

}

function get_ActiveBranchFloor()

{

    $('#counter_flr_sel option').remove();

	$(".overlay").css('display','block');

	$.ajax({

		type: 'POST',

		url: base_url+'index.php/admin_ret_catalog/get_ActiveBranchFloor',

		dataType:'json',

		data:{'id_branch':$('#branch_select').val()},

		success:function(data){

		   var id =  $('#id_floor').val();

		   $.each(data, function (key, item) {

			   		$('#counter_flr_sel').append(

						$("<option></option>")

						.attr("value", item.floor_id)

						  .text(item.floor_name )

					);

			});

			$("#counter_flr_sel").select2({

			    placeholder: "Select Floor",

			    allowClear: true

		    });

		 $("#counter_flr_sel").select2("val",(id!='' && id>0?id:''));

    			 $(".overlay").css("display", "none");

    		}

	});

}

function get_web_device_list(from_date,to_date)

{

	my_Date = new Date();

	$("div.overlay").css("display", "block");

	$.ajax({

		 url:base_url+"index.php/admin_ret_catalog/web_devices/ajax?nocache=" + my_Date.getUTCSeconds(),

		 dataType:"JSON",

		 type:"POST",

		 success:function(data){

   			 set_device_list(data);

   			 $("div.overlay").css("display", "none");

		  },

		  error:function(error)

		  {

			 $("div.overlay").css("display", "none");

		  }

	});

}

function set_device_list(data)

{

   $("div.overlay").css("display", "none");

   var list = data.list;

   var access = data.access;

   var oTable = $('#device_list').DataTable();

    if(access.add == '0')

	 {

		$('#add_device').attr('disabled','disabled');

	 }

	 oTable.clear().draw();

   	 if (list!= null && list.length > 0)

	 {

	 	oTable = $('#device_list').dataTable({

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

			            { "mDataProp": "id_device" },

						{ "mDataProp": "device_name" },

						{ "mDataProp": "token_id" },

						{ "mDataProp": "branch_name" },

						{ "mDataProp": function ( row, type, val, meta ) {

							 id= row.id_device

							 edit_url=(access.edit=='1' ? base_url+'index.php/admin_ret_catalog/web_devices/edit/'+id : '#' );

							 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/web_devices/delete/'+id : '#' );

							 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

							 action_content=(access.edit =='1' ? '<a href="'+edit_url+'" class="btn btn-primary btn-edit"><i class="fa fa-edit" ></i></a>':"")+(access.delete == '1' ? '<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>':"");

							return action_content;

						}

					}]

		});

	}

}

//web devices

 //collection

$("#add_newcollection").on('click',function(){

	if($("#add_collection_name").val() != '')

	{

	    $("#add_collection").prop('disabled',true);

	    $("#add_newcollection").prop('disabled',true);

		add_collection(1);

		$("#add_collection_name").val('');

	}

	else if($("#add_collection_name").val() == '')

	{

		$.toaster({ priority : 'danger', title : 'Warning!', message : "Please Ener The Collection Name.."});

	    return false;

	}

});

$("#add_collection").on('click',function(){

	if($("#add_collection_name").val() != '')

	{

	    $("#add_collection").prop('disabled',true);

	    $("#add_newcollection").prop('disabled',true);

		add_collection(0);

		$("#add_collection_name").val('');

	}

	else if($("#add_collection_name").val() == '')

	{

		$.toaster({ priority : 'danger', title : 'Warning!', message : "Please Ener The Collection Name.."});

	    return false;

	}

});

function add_collection(type)

{

	my_Date = new Date();

	$.ajax({

		data:{"collection_name":$('#add_collection_name').val(),"status":$('#collection_status').val()},

		url: base_url+"index.php/admin_ret_catalog/ret_collection/save?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		dataType:"JSON",

		cache:false,

		success:function(data){

		    if(data.status)

		    {

		        $("#add_collection").prop('disabled',false);

	            $("#add_newcollection").prop('disabled',false);

		        $.toaster({ priority : 'success', title : 'Warning!', message :data.message});

		        set_collection_list();

		    }else

		    {

		        $("#add_collection").prop('disabled',false);

	            $("#add_newcollection").prop('disabled',false);

		        $.toaster({ priority : 'danger', title : 'Warning!', message : data.message});

		        set_collection_list();

		    }

		    if(type==0)

		    {

		        $('#confirm-add').modal('toggle');

		    }

		}

	});

}

function set_collection_list()

{

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		url: base_url+"index.php/admin_ret_catalog/ret_collection/ajax?nocache=" + my_Date.getUTCSeconds(),

		type:"GET",

		dataType: 'json',

		cache:false,

		success:function(data){

		   		var list 	= data.list;

				var access	    = data.access;

				if(access.add == '0')

				{

					$('#add_collc').attr('disabled','disabled');

				}

			 var oTable = $('#collection_list').DataTable();

			 oTable.clear().draw();

			 if (list!= null && list.length > 0)

			 {

				oTable = $('#collection_list').dataTable({

						"bDestroy": true,

						"bInfo": true,

						"bFilter": true,

						"bSort": true,

						"order": [[ 0, "desc" ]],

						"aaData"  : list,

						"aoColumns": [

						{ "mDataProp": "id_collection" },

						{ "mDataProp": "collection_name" },

						{ "mDataProp": function ( row, type, val, meta ){

						active_url =(access.edit =='1' )? base_url+"index.php/admin_ret_catalog/ret_collection/update_status/"+row.id_collection+"/"+(row.status==0?1:0):'#';

						return "<a href='"+active_url+"'><i class='fa "+(row.status==1?'fa-check':'fa-remove')+"' style='color:"+(row.status==1?'green':'red')+"'></i></a>"

						}

						},

						{ "mDataProp": function ( row, type, val, meta ) {

							 id= row.id_collection;

							 edit_target=(access.edit=='0'?"":"#confirm-edit");

							 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/ret_collection/Delete/'+id : '#' );

							 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

							// action_content='<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

							var action_content='';

							if(access.edit == '1'){

														

							action_content+=  '<a href='+edit_target+' class="btn btn-primary btn-edit" id="edit" role="button"><i class="fa fa-edit" ></i></a>' ;

								

							}

							if(access.delete == '1'){

								 

							action_content+='<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

								

						    }

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

$(document).on('click', "#collection_list a.btn-edit", function(e) {

	e.preventDefault();

	id=$(this).data('id');

    get_collection(id);

    $("#edit-id").val(id);

});

function get_collection(id)

{

   my_Date = new Date();

	$.ajax({

		type:"GET",

		url: base_url+"index.php/admin_ret_catalog/ret_collection/edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		cache:false,

		dataType:"JSON",

		success:function(data){

			//console.log(data);

			id=data.id_collection;

			$('#ed_collection_name').val(data.collection_name);

		}

	});

}

$("#update_collection").on('click',function(){

	if($("#ed_collection_name").val() != '')

	{

	    $("#update_collection").prop('disabled',true);

		update_collection();

		$("#ed_collection_name").val('');

	}

	else if($("#ed_collection_name").val() == '')

	{

		$.toaster({ priority : 'danger', title : 'Warning!', message : "Please Ener The Collection Name.."});

	    return false;

	}

});

function update_collection()

{

	my_Date = new Date();

	$.ajax({

		data:{"collection_name":$('#ed_collection_name').val()},

		url: base_url+"index.php/admin_ret_catalog/ret_collection/update/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		dataType:"JSON",

		cache:false,

		success:function(data){

		    if(data.status)

		    {

		        $("#update_collection").prop('disabled',false);

		        $.toaster({ priority : 'success', title : 'Warning!', message :data.message});

		        $('#confirm-edit').modal('toggle');

		        set_collection_list();

		    }else

		    {

		        $("#update_collection").prop('disabled',false);

		        $.toaster({ priority : 'danger', title : 'Warning!', message : data.message});

		        set_collection_list();

		    }

		}

	});

}

//collection

//repair master

function get_repair_list()

{

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		url: base_url+"index.php/admin_ret_catalog/repair_master/ajax?nocache=" + my_Date.getUTCSeconds(),

		type:"GET",

		dataType: 'json',

		cache:false,

		success:function(data){

		   		var list 	= data.list;

				var access	    = data.access;

				if(access.add == '0')

				{

					$('#add_collc').attr('disabled','disabled');

				}

			 var oTable = $('#collection_list').DataTable();

			 oTable.clear().drawret_repair_list

			 if (list!= null && list.length > 0)

			 {

				oTable = $('#ret_repair_list').dataTable({

						"bDestroy": true,

						"bInfo": true,

						"bFilter": true,

						"bSort": true,

						"order": [[ 0, "desc" ]],

						"aaData"  : list,

						"aoColumns": [

						{ "mDataProp": "id_repair_master" },

						{ "mDataProp": "name" },

						{ "mDataProp": function ( row, type, val, meta ){

						active_url =(access.edit=='1')?base_url+"index.php/admin_ret_catalog/repair_master/update_status/"+row.id_repair_master+"/"+(row.status==0?1:0):'#';

						return "<a href='"+active_url+"'><i class='fa "+(row.status==1?'fa-check':'fa-remove')+"' style='color:"+(row.status==1?'green':'red')+"'></i></a>"

						}

						},

						{ "mDataProp": function ( row, type, val, meta ) {

							 id= row.id_repair_master;

							 edit_target=(access.edit=='0'?"":"#confirm-edit");

							 delete_url=(access.delete=='1'? base_url+'index.php/admin_ret_catalog/repair_master/Delete/'+id : '#' );

							 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

							//  action_content='<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+' data-target='+edit_target+'><i class="fa fa-edit" ></i></a><a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete"><i class="fa fa-trash"></i></a>'

							

							var action_content = "";

              if (access.edit == "1") {

                action_content +=

                  '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+' data-target='+edit_target+'><i class="fa fa-edit" ></i></a>';

              }

              if (access.delete == "1") {

                action_content +=

                  '<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete"><i class="fa fa-trash"></i></a>';

              }

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

$('#repair_name_save_and_new,#repair_name_save_and_close').on('click',function(){

    if($('#repair_name').val()=='')

    {

        $.toaster({ priority : 'danger', title : 'Warning!', message : "Please Enter The Name.."});

    }else

    {

        add_repair_damage_item(1);

    }

});

function add_repair_damage_item(add_type)

{

    my_Date = new Date();

	$.ajax({

		data:{"name":$('#repair_name').val()},

		url: base_url+"index.php/admin_ret_catalog/repair_master/save?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		dataType:"JSON",

		cache:false,

		success:function(data){

		    if(data.status)

		    {

		        $("#repair_name_save_and_new").prop('disabled',false);

	            $("#repair_name_save_and_close").prop('disabled',false);

		        $.toaster({ priority : 'success', title : 'Warning!', message :data.message});

		        get_repair_list();

		    }else

		    {

		        $("#add_collection").prop('disabled',false);

	            $("#add_newcollection").prop('disabled',false);

		        $.toaster({ priority : 'danger', title : 'Warning!', message : data.message});

		        get_repair_list();

		    }

		    if(add_type==0)

		    {

		        $('#confirm-add').modal('toggle');

		    }

		}

	});

}

$(document).on('click', "#ret_repair_list a.btn-edit", function(e) {

	e.preventDefault();

	id=$(this).data('id');

    get_repair_item_list(id);

    $("#edit_id").val(id);

});

function get_repair_item_list(id)

{

   my_Date = new Date();

	$.ajax({

		type:"GET",

		url: base_url+"index.php/admin_ret_catalog/repair_master/edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		cache:false,

		dataType:"JSON",

		success:function(data){

			//console.log(data);

			id=data.id_repair_master;

			$('#repair_name_edit').val(data.name);

		}

	});

}

$("#update_repair_name").on('click',function(){

	if($("#repair_name_edit").val() != '')

	{

	    $("#update_repair_name").prop('disabled',true);

		update_repair_master_item();

		$("#repair_name_edit").val('');

	}

	else if($("#repair_name_edit").val() == '')

	{

		$.toaster({ priority : 'danger', title : 'Warning!', message : "Please Ener The Collection Name.."});

	    return false;

	}

});

function update_repair_master_item()

{

    var id=$("#edit_id").val();

    my_Date = new Date();

	$.ajax({

		data:{"name":$('#repair_name_edit').val()},

		url: base_url+"index.php/admin_ret_catalog/repair_master/update/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		dataType:"JSON",

		cache:false,

		success:function(data){

		    if(data.status)

		    {

		        $("#update_repair_name").prop('disabled',false);

		        $.toaster({ priority : 'success', title : 'Warning!', message :data.message});

		        get_repair_list();

		    }else

		    {

		        $("#update_repair_name").prop('disabled',false);

		        $.toaster({ priority : 'danger', title : 'Warning!', message : data.message});

		        get_repair_list();

		    }

		    $('#confirm-edit').modal('toggle');

		    get_repair_list();

		}

	});

}

//repair master

/** Product Division */

function set_product_division_table() {

	my_Date = new Date();

	$.ajax({

			url:base_url+ "index.php/product_division/ajax?nocache=" + my_Date.getUTCSeconds(),

			dataType:"JSON",

			type:"POST",

			success:function(data) {

				console.log(data);

				var product_division 	= data.product_division;

				var access		= data.access;

				$('#total_product_division').text(product_division.length);

				if(access.add == '0') {

					$('#add_product_division').attr('disabled','disabled');

				}

				var oTable = $('#product_division_list').DataTable();

				oTable.clear().draw();

				if (product_division!= null && product_division.length > 0) {

				oTable = $('#product_division_list').dataTable({

						"bDestroy": true,

						"bInfo": true,

						"bFilter": true,

						"bSort": true,

						"order": [[ 0, "desc" ]],

						"aaData": product_division,

						"aoColumns": [

										{ "mDataProp": "id_pro_division" },

										{ "mDataProp": "div_value" },

										{ "mDataProp": function ( row, type, val, meta ){

											status_url = (access.edit =='1')?base_url+"index.php/admin_ret_catalog/product_division_status/"+(row.status==1?0:1)+"/"+row.id_pro_division:'#';

											return "<a href='"+status_url+"'><i class='fa "+(row.status==1?'fa-check':'fa-remove')+"' style='color:"+(row.status==1?'green':'red')+"'></i></a>"

											}

										},

										{ "mDataProp": function ( row, type, val, meta ) {

											id= row.id_pro_division;

											edit_target=(access.edit=='0'?"":"#confirm-edit");

											delete_url=(access.delete=='1' ? base_url+'index.php/product_division/delete/'+id : '#' );

											delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

											// action_content='<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i> Edit</a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> Delete</a>'

											var action_content=''

											if(access.edit == '1'){

																	   

											   action_content+=  '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i> Edit</a> ' ;

											   

											   }

											   if(access.delete == '1'){

												

												action_content+=' <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> Delete</a>'

											   }

											return action_content;

										}

									}]

					});

				}

			},

			error:function(error)  {

				$("div.overlay").css("display", "none");

			}

	});

}

$(document).on('click', "#product_division_list a.btn-edit", function(e) {

	$("#ed_div_value").val('');

	e.preventDefault();

	id=$(this).data('id');

	get_product_division(id);

	$("#edit-id").val(id);

});

$("#add_newprod_division").on('click',function() {

	if($('#div_value').val() != '') {

		add_product_division($('#div_value').val());

		$('#div_value').val('');

	}

	else if($("#div_value").val() == '') {

		msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Product Division.</div>';

		$("div.overlay").css("display", "none");

		$('#error-msg').html(msg);

		return false;

	}

});

$("#update_prod_division").on('click',function(){

	var div_value=($("#ed_div_value").val());

	var id=$("#edit-id").val();

	if($("#ed_div_value").val() != '') {

		update_product_division(div_value,id);

	}

	else if($("#ed_div_value").val() == ''){

		msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Product Division.</div>';

		$("div.overlay").css("display", "none");

		$('#error').html(msg);

		return false;

	}

});

function add_product_division(div_value) {

	my_Date = new Date();

	$.ajax({

		data:{"div_value":div_value},

		url: base_url+"index.php/product_division/add?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		cache:false,

		success:function(data){

			$('#div_value').val('');

			 msg='<div class = "alert alert-success"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Success!</strong>Product division added successfully.</div>';

			$('#chit_alert').html(msg);

			location.reload(true);

		}

	});

}

function update_product_division(div_value,id) {

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		data:{"div_value":div_value},

		url: base_url+"index.php/product_division/update/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		cache: false,

		success:function(){

			$("div.overlay").css("display", "none");

			window.location.reload(true);

		}

	});

}

function get_product_division(id) {

   my_Date = new Date();

	$.ajax({

		type:"GET",

		url: base_url+"index.php/product_division/edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		cache:false,

		dataType:"JSON",

		success:function(data){

			let div_value=data.div_value;

		    $('#ed_div_value').val(div_value);

		}

	});

}

/** Product Division */

//Purity Rate Master

$('#closebtn').on('click', function () {

	$('#SelectMetal').select2("val", "");

	$('#SelectPurityid').select2("val", "");

	$('#RatePurityid').select2("val", "");

});

$('.close').on('click', function () {

	$('#SelectMetal').select2("val", "");

	$('#SelectPurityid').select2("val", "");

	$('#RatePurityid').select2("val", "");

});

function get_MetalName() {

	$.ajax({

		type: 'POST',

		url: base_url + 'index.php/admin_ret_catalog/MetalSelect',

		dataType: 'json',

		cache: false,

		success: function (data) {

			var id_metal = $('#Metal_input').val();

			var ed_metal = $('#ed_Metal_input').val();

			$.each(data, function (key, item) {

				$('#SelectMetal,#ed_SelectMetal').append(

					$("<option></option>")

						.attr("value", item.id_metal)

						.text(item.metal)

				);

			});

			$("#SelectMetal,#ed_SelectMetal").select2({

				placeholder: "Metal_select",

				allowClear: true

			});

			$("#SelectMetal").select2("val", (id_metal != '' && id_metal > 0 ? id_metal : ''));

			$("#ed_SelectMetal").select2("val", (ed_metal != '' && ed_metal > 0 ? ed_metal : ''));

		}

	});

}

// To select Purity in catalo_master>Metal Rate Purity>Add Modal

function SelectPurity($id_metal) {

	$('#Purity_input option').remove();

	$(".overlay").css('display', 'block');

	$.ajax({

		type: 'POST',

		url: base_url + 'index.php/admin_ret_catalog/PuritySelect',

		data: { 'id_metal': $id_metal },

		cache: false,

		dataType: 'json',

		success: function (data) {

			var id_purity_value = $('#Purity_input').val();

			var id_purity = $('#ed_Purity_input').val();

			// console.log(id_purity);

			$.each(data, function (key, item) {

				$('#SelectPurityid,#ed_SelectPurityid').append(

					$("<option></option>")

						.attr("value", item.id_purity)

						.text(item.purity)

				);

			});

			$("#SelectPurityid,#ed_SelectPurityid").select2({

				placeholder: "Purity Select",

				allowClear: true

			});

			$("#SelectPurityid").select2("val", (id_purity_value != '' && id_purity_value > 0 ? id_purity_value : ''));

			$("#ed_SelectPurityid").select2("val", (id_purity != '' && id_purity > 0 ? id_purity : ''));

		}

	});

}

// To select Metal Rate in catalo_master>Metal Rate Purity>Add Modal

function Metalrate() {

	$('#Rate_input option').remove();

	$(".overlay").css("display", "block");

	$.ajax({

		type: 'GET',

		url: base_url + 'index.php/admin_ret_catalog/MetalRates',

		dataType: 'json',

		cache: false,

		success: function (data) {

			// alert("in js"+rate_field);

			var COLUMN_NAME = $('#Rate_input').val();

			var ed_columnname = $('#ed_Rate_input').val();

			console.log(ed_columnname);

			$.each(data, function (key, item) {

				$('#RatePurityid,#ed_RatePurityid').append(

					$('<option></option>')

						.attr("values", item.COLUMN_NAME)

						.text(item.COLUMN_NAME)

				);

			});

			$("#RatePurityid,#ed_RatePurityid").select2({

				placeholder: "Rate Select",

				allowClear: false

			});

			$("#RatePurityid").select2("val", (COLUMN_NAME != '' ? COLUMN_NAME : ''));

			$("#ed_RatePurityid").select2("val", (ed_columnname != '' ? ed_columnname : ''));

			$(".overlay").css("display", "none");

		}

	});

}

//ADD

//Onchange function to Metal purity rate to get input from dropdown

//Metal Select

$('#SelectMetal,#ed_SelectMetal').on('change', function (e) {

	if (this.value != '') {

		$('#Metal_input,#ed_Metal_input').val(this.value);

		$('#SelectPurityid option,#ed_SelectPurityid option').remove();

		$('#RatePurityid option,#ed_RatePurityid option').remove();

		$('#SelectPurityid option,#ed_SelectPurityid option').remove();

		$('#RatePurityid option,#ed_RatePurityid option').remove();

		Metalrate();

		SelectPurity(this.value);

	}

});

//Purity Select

$("#SelectPurityid,#ed_SelectPurityid").on('change', function (e) {

	if (this.value != '') {

		$("#Purity_input,#ed_Purity_input").val(this.value);

	}

	else {

		$('#Purity_input,#ed_Purity_input').val();

	}

})

//Rate Select

$("#RatePurityid,#ed_RatePurityid").on('change', function (e) {

	if (this.value !== '') {

		$('#Rate_input,#ed_Rate_input').val(this.value);

	}

	else {

		$('#Rate_input,#ed_Rate_input').val();

	}

})

//Metal Rate Purity validation and Flashmessage

$("#Metalratepurity").on('click', function () {

	if ($("#Metal_input").val() != '' && $("#Purity_input").val() != '' && $("#Rate_input").val() != '') {

		SavePurityrate($('#Metal_input').val(), $('#Purity_input').val(), $('#Rate_input').val());

		$("#Metal_input").val('');

		$("#Purity_input").val('');

		$("#Rate_input").val('');

	}

	else if ($("#Metal_input").val() == '') {

		msg = '<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Select  Metal .</div>';

		$("div.overlay").css("display", "none");

		$('#error-msg').html(msg);

		return false;

	}

	else if ($("#Purity_input").val() == '') {

		msg = '<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Select Purity .</div>';

		$("div.overlay").css("display", "none");

		$('#error-msg').html(msg);

		return false;

	}

	else if ($("#Rate_input").val() == '') {

		msg = '<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Select Rate.</div>';

		$("div.overlay").css("display", "none");

		$('#error-msg').html(msg);

		return false;

	}

});

//add

//Save Purity_rate >

function SavePurityrate(Metal_input, Purity_input, Rate_input) {

	$.ajax({

		data: { "Metal_input": Metal_input, "Purity_input": Purity_input, "Rate_input": Rate_input },

		url: base_url + "index.php/admin_ret_catalog/ret_metalpurity/add?nocache=",

		type: "POST",

		cache: false,

		dataType: "json",

		success: function (data) {

			msg = '<div class ="alert alert-' + data.class + '"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>   ' + data.title + '   </strong> ' + data.message + ' </div>';

			$('#chit_alert').html(msg);

			window.location.reload();

		}

	});

}

//Get_details to edit Modal

$(document).on('click', "#PurityTable a.btn-edit", function (e) {

	e.preventDefault();

	id = $(this).data('id');

	get_purityratemetals(id);

});

// To append edit modal and append the data's

function get_purityratemetals(id) {

	$.ajax({

		type: "POST",

		url: base_url + "index.php/admin_ret_catalog/ret_metalpurity/edit/" + id + "?nocache",

		cache: false,

		dataType: "json",

		success: function (data) {

			$("#ed_SelectMetal").select2("val", (data.id_metal != '' && data.id_metal > 0 ? data.id_metal : ''));

			$('#ed_Metal_input').val(data.id_metal);

			//get_MetalName();

			$('#ed_Purity_input').val(data.id_purity);

			//SelectPurity(data.id_metal);

			$('#ed_Rate_input').val(data.rate_field);

			//Metalrate();

		}

	});

}

//To update the data From ui to Database From Edit Modal

$('#update_PurityRate').on('click', function () {

    if($('#ed_SelectMetal').val()=='' || $('#ed_SelectMetal').val()==null)

    {

        $.toaster({ priority : 'danger', title : 'Warning!', message : "Please select Metal"});

    }

    else if($('#ed_SelectPurityid').val()=='' || $('#ed_SelectPurityid').val()==null)

    {

        $.toaster({ priority : 'danger', title : 'Warning!', message : "Please select Purity"});

    }

    else if($('#ed_RatePurityid').val()=='' || $('#ed_RatePurityid').val()==null)

    {

        $.toaster({ priority : 'danger', title : 'Warning!', message : "Please select Rate Field"});

    }

    else

    {

        var Metal = $('#ed_Metal_input').val();

    	var Purity = $('#ed_Purity_input').val();

    	var Rate = $('#ed_RatePurityid').val();

    	update_purity_rate(Metal, Purity, Rate);

    }

});

function update_purity_rate(Metal, Purity, Rate) {

	$.ajax({

		type: 'POST',

		dataType: 'json',

		data: { "Metal_input": Metal, "Purity_input": Purity, "Rate_input": Rate },

		cache: false,

		url: base_url + "index.php/admin_ret_catalog/ret_metalpurity/update/" + id + "?nocache=",

		success: function (data) {

			msg = '<div class ="alert alert-' + data.class + '"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>   ' + data.title + '   </strong> ' + data.message + ' </div>';

			$('#chit_alert').html(msg);

			window.location.reload();

		}

	});

}

function Metal_purity_table() {

	$.ajax({

		type: "POST",

		url: base_url + 'index.php/admin_ret_catalog/ret_metalpurity?nocache',

		dataType: 'json',

		cache: false,

		success: function (data) {

			console.log(data);

			var purity = data.Purity;

			var access = data.access;

			$('#PurityTable').DataTable({

				"order": [[0, "desc"]],

				data: purity,

				columns: [

					{ 'data': 'id_metal_purity_rate' },

					{ 'data': 'metalname' },

					{ 'data': 'PurityName' },

					{ 'data': 'rate_field' },

					{ 'data': 'market_rate_field' },

					{

						'data': function (row) {

							id = row.id_metal_purity_rate;

							edit_target = (access.edit == '0' ? "" : "#confirm-edit");

							delete_confirm = (access.delete == '1' ? '#confirm-delete' : '');

							delete_url = (access.delete == '1' ? base_url + 'index.php/admin_ret_catalog/ret_metalpurity/delete/' + id : '#');

							// action_content = '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id=' + id + '  data-target=' + edit_target + '><i class="fa fa-edit" ></i> </a><a href="#" class="btn btn-danger btn-del" data-href=' + delete_url + ' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> </a>'

							var action_content=''

							if(access.edit == '1'){

													   

							   action_content+=  '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a>' ;

							   

							   }

							   if(access.delete == '1'){

								

								action_content+='<a href="#" class="btn btn-danger btn-del" data-href=' + delete_url + ' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> </a>'

							   }

							return action_content;

						}

					}

				]

			});

		}

	});

}

//Purity Rate Master

function set_bnk_deposit_table() {

	my_Date = new Date();

	$("div.overlay").css("display", "block");

	var oTable = $('#bank_deposit_list').DataTable();

	$.ajax({

			type: 'GET',

			url:  base_url+'index.php/admin_ret_catalog/bank_deposit/ajax_list?nocache='+ my_Date.getUTCSeconds(),

			dataType: 'json',

			success: function(data) {

				var access=data.access;

				console.log(data);

				$('#total_bank_deposit').text(data.deposits.length);

				if(access.add == '0') {

					$('#bank_deposit_list').attr('disabled','disabled');

				}

				var max_id = data.max_id;

				oTable = $('#bank_deposit_list').dataTable({

						"bDestroy": true,

						"bInfo": true,

						"bFilter": true,

						"bSort": true,

						"aaSorting": [[ 0, "desc" ]],

						"aaData": data.deposits,

						"aoColumns": [

										{ "mDataProp": "dep_id" },

										{ "mDataProp": "branch_name" },

										{ "mDataProp": "bank_name" },

										{ "mDataProp": "cash_type" },

										{ "mDataProp": "dep_amount_formatted" },

										{ "mDataProp": "deposit_type" },

										{ "mDataProp": "dep_ref_id" },

										{ "mDataProp": "entry_date" },

										{ "mDataProp": "deposited_date" },

										{ "mDataProp": "cash_in_hand_date" },

										{ "mDataProp": "mode_name" },

										{ "mDataProp": "dep_narration" },

										{ "mDataProp": "deposited_by" },

										{ "mDataProp": function ( row, type, val, meta ) {

											id         = row.dep_id;

											edit_url   = (access.edit=='1'?base_url+'index.php/deposit/edit/'+id:"#");

											delete_url   = (access.delete=='1'?base_url+'index.php/deposit/delete/'+id:"#");

											action_content='<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button><ul class="dropdown-menu">'+

											'<li><a href="'+edit_url+'" class="btn-edit"><i class="fa fa-edit" ></i> Edit</a></li><li><a href="#" class="btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> Delete</a></li></ul></div>';

											return (id==max_id?action_content:'-');

										}

										}

									],

									"footerCallback": function (row, data, start, end, display) {

										if (data.length > 0) {

											var api = this.api(), data;

											for (var i = 0; i <= data.length - 1; i++) {

												var intVal = function (i) {

													return typeof i === 'string' ?

														i.replace(/[\$,]/g, '') * 1 :

														typeof i === 'number' ?

															i : 0;

												};

												amount = api

													.column(4)

													.data()

													.reduce(function (a, b) {

														return intVal(a) + intVal(b);

													}, 0);

												$(api.column(0).footer()).html('Total');

												$(api.column(4).footer()).html(money_format_india(parseFloat(amount).toFixed(2)));

											}

										} else {

											var api = this.api(), data;

											$(api.column(0).footer()).html('Total');

											$(api.column(4).footer()).html('');

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

$(document).on("change", "#cash_type", function() {

	let type = $(this).val();

	if(type == 1) {

		$(".curr_bal_retail").css("display", "block");

		$(".curr_bal_chit").css("display", "none");

	} else if(type == 2) {

		$(".curr_bal_retail").css("display", "none");

		$(".curr_bal_chit").css("display", "block");

	}

});

function getDepositBranch(branch = '')

{

	$('#dep_branch option').remove();

	$.ajax({

		type: 'GET',

		url: base_url+'index.php/branch/branchname_list',

		dataType:'json',

		success:function(data) {

    		$.each(data.branch, function (key, item) {

				$("#dep_branch").append(

					$("<option></option>")

					.attr("value", item.id_branch)

					.text(item.name)

				);

    		});

    		$("#dep_branch").val(branch!='' && branch > 0 ? branch : '');

	    }

	});

}

$(document).on("change", "#dep_branch, #cash_type", function() {

	let branch = $("#dep_branch").val();

	let cash_type = $("#cash_type").val();

	get_cash_in_hand(branch, cash_type);

});

function get_cash_in_hand(branch, cash_type) {

	$("div.overlay").css("display", "block");

	$.ajax({

    	type: 'POST',

		url: base_url+'index.php/admin_ret_catalog/bank_deposit/get_cash_in_hand',

		data :{'id_branch' : branch, 'cash_type' : cash_type},

		dataType:'json',

		success:function(data) {

			$("div.overlay").css("display", "none");

			console.log(data);

			if(data.status) {

				$(".curr_balance").html(data.cash_in_hand);

			} else {

				$.toaster({ priority : 'danger', title : 'Warning!', message : data.message});

			}

		}

	});

}

/*Karigar Calculation Type*/

$('#karigar_for').on('change',function(e)

{

	if(this.value==1 || this.value==2 || this.value==5 || this.value==6)

	{

		$('.kar_calc').css('display','block');

	}

	else

	{

		$('.kar_calc').css('display','none');

		$('#karigar_calc_type').val('');

	}

});

/*Karigar Calculation Type*/

/*Karigar Approval List*/

$('#stone_select_all').click(function(event) {

	$("tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));

	event.stopPropagation();

});

$("input[name='app[approval_for]']:radio").on('change',function(){

	$('.wastages').css('display','block');

	$('.stones').css('display','none');

	$('.charges').css('display','none');

	if(this.value==0)

	{

	    get_KarigarWastages();

		$('.wastages').css('display','block');

		$('.stones').css('display','none');

		$('.charges').css('display','none');

	}else if(this.value==1)

	{

	     get_KarigarStones();

		$('.wastages').css('display','none');

		$('.stones').css('display','block');

		$('.charges').css('display','none');

	}

	else

	{

		$('.wastages').css('display','none');

		$('.stones').css('display','none');

		$('.charges').css('display','block');

	}

	get_WastApprvKarigars();

});

$('#approval_search').on('click',function()

{

	var approval_type = $("input[name='app[approval_for]']:checked").val();

	console.log(approval_type);

	if(approval_type==0)

	{

		get_KarigarWastages();

	}

	else if(approval_type==1)

	{

		get_KarigarStones();

	}

	else

	{

		get_KarigarCharges();

	}

});

function get_KarigarWastages()

{

    $(".overlay").css("display", "block");

	$('#karigar_wastage_list > tbody').empty();

	my_Date = new Date();

	$.ajax({

		url:base_url+ "index.php/admin_ret_catalog/karigar_approval/wastages_list/?nocache=" + my_Date.getUTCSeconds(),

		data: {'id_karigar':$('#karigar_sel').val(),'status':(ctrl_page[3]==0 ? 0 :ctrl_page[3]==3 ? 3: "")},

		dataType:"JSON",

		type:"POST",

		success:function(data)

		{

			var list = data.list;

			if (list!= null && list.length > 0)

			{

				var html="";

				var length = 1;

				$.each(list,function(key,val)

                {

                    var wast_img_name = '';

                    if(val.image_name!=null)

                    {

                        wast_img_name = base_url+'assets/img/karigar/product_wastage/'+val.id_karikar_wast+'/'+val.image_name;

                    }

                    else

                    {

                        wast_img_name = base_url+'assets/img/no_image.png';

                    }

					var uom_list = "<option value=''>-Select UOM-</option>";

					$.each(uom_details,function(key,itm)

					{

						var uom_selected = '';

						if(val.uom_id == itm.uom_id)

						{

							uom_selected = "selected='selected'";

						}

						uom_list += "<option value='"+itm.uom_id+"' "+uom_selected+">"+itm.uom_name+"</option>";

					})

					var quality_list = "<option value=''>-Select Quality-</option>";

					$.each(quality_code,function(key,itm)

					{

						var quality_selected = '';

						if(val.quality_id == itm.quality_id)

						{

							quality_selected = "selected='selected'";

						}

						quality_list += "<option value='"+itm.quality_id+"' "+quality_selected+">"+itm.code+"</option>";

					})

                    length=length+1;

					html+='<tr class="'+length+'">'+

					'<td><input type="checkbox" class="kar_wast" name="id_karikar_wast[]"/>'+val.id_karikar_wast+'<input type="hidden" class="kar_wast_id"  value='+val.id_karikar_wast+'><input type="hidden" class="id_karigar" name="id_karigar[]" value='+val.id_karikar+'><input type="hidden" class="kar_mobile"  value='+val.mobile+'></td>'+

					'<td class="wast_karigar">'+val.karigar+'</td>'+

					'<td class="wast_karigar">'+val.karigar_company+'</td>'+

					'<td class="wast_product">'+val.cat_name+'<input type="hidden" class="cat_id" name="cat_id[]" value='+val.id_category+'></td>'+

					'<td class="wast_product">'+val.product_name+'<input type="hidden" class="pro_id" name="pro_id[]" value='+val.id_product+'><input type="hidden" class="stone_type" name="stone_type[]" value='+val.stone_type+'></td>'+

					'<td class="wast_design">'+val.design_name+'<input type="hidden" class="des_id" name="des_id[]" value="'+val.id_design+'"></td>'+

					'<td class="wast_sub_design">'+val.sub_design_name+'<input type="hidden" class="sub_des_id" name="sub_des_id[]" value="'+val.id_sub_design+'"></td>'+

					'<td class="wast_purity">'+val.purity+'<input type="hidden" class="id_purity" name="id_purity[]" value="'+val.id_purity+'"></td>'+

					"<td><select class='appr_kar_calc_type' class='form-control' style='width: 100px;' "+(val.stone_type==0 ? "":"disabled")+"><option value='1' "+(val.karigar_calc_type==1 ? 'selected' :'')+">Pure Weight x Rate</option><option value='2' "+(val.karigar_calc_type==2 ? 'selected' :'')+">Purchase Touch</option><option value='3' "+(val.karigar_calc_type==3 ? 'selected' :'')+">Pure Weight x Wastage %</option></select></td>"+

					'<td class="pur_touch"><input type="number" step="any" class="form-control appr_pur_touch" placeholder="Touch"  style="width: 100px;" value="'+val.pur_touch+'" '+(val.stone_type==0 ? '' : 'readonly')+'/></td>'+

					"<td><select class='appr_calc_type' class='form-control'  style='width: 100px;' "+(val.stone_type==0 ? '' : 'disabled')+"><option value='0' "+(val.calc_type==0 ? 'selected' :'')+">Mc & Wast On Gross</option><option value='1' "+(val.calc_type==1 ? 'selected' :'')+">Mc & Wast On Net</option><option value='2' "+(val.calc_type==2 ? 'selected' :'')+"> Mc on Gross,Wast On Net</option></select></td>"+

					"<td><select class='appr_va_type' class='form-control' style='width: 100px;' "+(val.karigar_calc_type==2 || val.stone_type!=0 ? 'disabled' :'')+"><option value='1' "+(val.wastage_type==1 ? 'selected' :'')+">Percentage</option><option value='2' "+(val.wastage_type==2 ? 'selected' :'')+">Weight</option></select></td>"+

					'<td class="wast_per"><input type="number" step="any" class="form-control appr_wast_percent" placeholder="wastages"  style="width: 100px;" value="'+val.wastage_per+'" '+((val.wastage_type==2 || val.karigar_calc_type==2 || val.stone_type!=0) ? 'disabled' :'')+'/><input type="hidden" class="act_wast_per" value="'+val.wastage_per+'"></td>'+

					'<td class="wastage_wt"><input type="number" step="any" class="form-control appr_wast_wgt" placeholder="wastages"  style="width: 100px;" value="'+val.wastage_wt+'" '+((val.wastage_type==1 || val.karigar_calc_type==2 || val.stone_type!=0) ? 'disabled' :'')+'/><input type="hidden" class="act_wast_wt" value="'+val.wastage_wt+'"></td>'+

					"<td><select class='appr_mc_type' class='form-control' style='width: 100px;' "+(val.karigar_calc_type==2 || val.stone_type!=0 ? 'disabled' :'')+"><option value='1' "+(val.mc_type==1 ? 'selected' :'')+">Gram</option><option value='2' "+(val.mc_type==2 ? 'selected' :'')+">Piece</option></select></td>"+

					'<td class="mc_value"><input type="number" step="any" class="form-control appr_mc" placeholder="MC"  style="width: 100px;" value="'+val.mc_value+'" '+(val.karigar_calc_type==2 || val.stone_type!=0 ? 'disabled' :'')+'/></td>'+

					'<td><img src="'+wast_img_name+'"width="50" height="55"><a class="btn btn-secondary wastages_images" data-toggle="modal" data-id='+val.id_karikar_wast+' id="edit" ><i class="fa fa-eye" ></i></a></td>'+

					"<td><a href='#' onClick='add_kar_product_charges($(this).closest(\"tr\"));' class='btn btn-default btn-sm'><i class='fa fa-plus'></i></a><input type='hidden' class='charges_details'  value='"+(val.charges_details.length > 0 ? JSON.stringify(val.charges_details) :'')+"'></td>"+

					"<td><select class='appr_stn_calc_type' class='form-control' style='width: 100px;' "+(val.stone_type==0 ? 'disabled' :'')+"><option value='1' "+(val.stone_cal_type==1 ? 'selected' :'')+">Wt</option><option value='2' "+(val.stone_cal_type==2 ? 'selected' :'')+">PCS</option></select></td>"+

					"<td><select class='appr_uom_id' class='form-control' style='width: 100px;' "+(val.stone_type==0 ? 'disabled' :'')+">"+uom_list+"</select></td>"+

					"<td><select class='appr_quality_id' class='form-control' style='width: 100px;' "+(val.stone_type==2 ? '' :'disabled')+">"+quality_list+"</select></td>"+

					'<td class="from_wt"><input type="number" step="any" class="form-control appr_from_wt" placeholder="From Cent"  style="width: 100px;" value="'+val.from_wt+'" '+(val.stone_type==2  ? '' :'readonly')+'/></td>'+

					'<td class="to_wt"><input type="number" step="any" class="form-control appr_to_wt" placeholder="To Cent"  style="width: 100px;" value="'+val.to_wt+'" '+(val.stone_type==2 ? '' :'readonly')+'/></td>'+

					'<td class="rate_per_grm"><input type="number" step="any" class="form-control appr_rate_per_grm" placeholder="Rate Per gram"  style="width: 100px;" value="'+val.rate_per_gram+'" '+(val.stone_type==0 ? 'readonly' :'')+'/></td>'+

					'<td>'+(val.status==0 ? '<span class="badge bg-green">'+val.wastage_status+'</span>':'<span class="badge bg-yellow">'+val.wastage_status+'</span>')+'</td>'

					'</tr>';

				});

				if($('#karigar_wastage_list > tbody  > tr').length>0)

				{

					$('#karigar_wastage_list > tbody > tr:first').before(html);

				}

				else

				{

					$('#karigar_wastage_list tbody').append(html);

				}

				$('#karigar_wastage_list > tbody').find('.appr_kar_calc_type').select2();

				$('#karigar_wastage_list > tbody').find('.appr_kar_calc_type').select2({

				    placeholder: "Karigar Calc Type",

				    allowClear: true

				});

				$('#karigar_wastage_list > tbody').find('.appr_calc_type').select2();

				$('#karigar_wastage_list > tbody').find('.appr_calc_type').select2({

				    placeholder: "Calc Type",

				    allowClear: true

				});

				$('#karigar_wastage_list > tbody').find('.appr_va_type').select2();

				$('#karigar_wastage_list > tbody').find('.appr_va_type').select2({

				    placeholder: "V.A Type",

				    allowClear: true

				});

				$('#karigar_wastage_list > tbody').find('.appr_mc_type').select2();

				$('#karigar_wastage_list > tbody').find('.appr_mc_type').select2({

				    placeholder: "MC Type",

				    allowClear: true

				});

				$('#karigar_wastage_list > tbody').find('.appr_stn_calc_type').select2();

				$('#karigar_wastage_list > tbody').find('.appr_stn_calc_type').select2({

				    placeholder: "Stone Calc Type",

				    allowClear: true

				});

				$('#karigar_wastage_list > tbody').find('.appr_uom_id').select2();

				$('#karigar_wastage_list > tbody').find('.appr_uom_id').select2({

				    placeholder: "UOM",

				    allowClear: true

				});

				$('#karigar_wastage_list > tbody').find('.appr_quality_id').select2();

				$('#karigar_wastage_list > tbody').find('.appr_quality_id').select2({

				    placeholder: "Quality Code",

				    allowClear: true

				});

				$("div.overlay").css("display", "none");

			}

			else

            {

                $(".overlay").css("display", "none");

                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Record Found..'});

            }

		},

		error:function(error)

		{

		$("div.overlay").css("display", "none");

		}

	})

}

$(document).on('change','.appr_kar_calc_type',function()

{

	var row = $(this).closest('tr');

	row.find('.appr_va_type').prop("disabled",false);

	row.find('.appr_wast_percent').prop("disabled",false);

	row.find('.appr_wast_wgt').prop("disabled",false);

	row.find('.appr_mc_type').prop("disabled",false);

	row.find('.appr_mc').prop("disabled",false);

	if(this.value==2)

	{

		row.find('.appr_va_type').prop("disabled",true);

		row.find('.appr_wast_percent').prop("disabled",true);

		row.find('.appr_wast_wgt').prop("disabled",true);

		row.find('.appr_mc_type').prop("disabled",true);

		row.find('.appr_mc').prop("disabled",true);

	}

});

$(document).on('change','.appr_va_type ',function()

{

	var row = $(this).closest('tr');

	if(this.value==2)

	{

		row.find('.appr_wast_percent').val(0);

		row.find('.appr_wast_wgt').val(row.find('.act_wast_wt').val());

		row.find('.appr_wast_percent').prop('disabled',true);

		row.find('.appr_wast_wgt').prop('disabled',false);

	}

	else

	{

		row.find('.appr_wast_wgt').val(0);

		row.find('.appr_wast_percent').val(row.find('.act_wast_per').val());

		row.find('.appr_wast_wgt').prop('disabled',true);

		row.find('.appr_wast_percent').prop('disabled',false);

	}

});

function get_KarigarStones()

{

	$('#karigar_stones_list > tbody').empty();

	my_Date = new Date();

	$.ajax({

		url:base_url+ "index.php/admin_ret_catalog/karigar_approval/stones_list/?nocache=" + my_Date.getUTCSeconds(),

		data: {'id_karigar':$('#karigar_sel').val()},

		dataType:"JSON",

		type:"POST",

		success:function(data)

		{

			$("div.overlay").css("display", "none");

			var list = data.list;

			console.log(list);

			console.log((list!= null && list.length > 0));

			if (list!= null && list.length > 0)

			{

				var html="";

				$.each(list,function(key,val)

                {

					html+='<tr>'+

					'<td><input type="checkbox" class="kar_stn" name="id_karigar_stone[]"/>'+val.id_karigar_stone+'<input type="hidden" class="kar_stn_id"  value='+val.id_karigar_stone+'><input type="hidden" class="id_karigar" name="id_karigar[]" value='+val.id_karigar+'></td>'+

					'<td class="stn_karigar">'+val.karigar+'</td>'+

					'<td class="stn_karigar_cmpy">'+val.company+'</td>'+

					'<td class="stn_type">'+val.stone_type+'<input type="hidden" class="stone_type_id" name="stone_type_id[]" value='+val.stone_type_id+'></td>'+

					'<td class="stn_name">'+val.stone_name+'<input type="hidden" class="stone_id" name="stone_id[]" value="'+val.stone_id+'"></td>'+

					'<td class="stn_uom">'+val.uom_name+'<input type="hidden" class="uom_id" name="uom_id[]" value="'+val.uom_id+'"></td>'+

					'<td class="stn_calc_type">'+val.stn_cal_type+'</td>'+

					'<td class="stn_rate">'+val.stn_rate+'</td>'+

					'<td class="stn_from_wt">'+val.from_wt+'</td>'+

					'<td class="stn_to_wt">'+val.to_wt+'</td>'+

					'<td class="stn_quality_code">'+val.quality_code+'<input type="hidden" class="quality_id" name="quality_id[]" value='+val.quality_id+'></td>'+

					'<td class="stn_clarity">'+val.clarity+'</td>'+

					'<td class="stn_color">'+val.color+'</td>'+

					'<td class="stn_cut+">'+val.cut+'</td>'+

					'<td class="stn_shape">'+val.shape+'</td>'+

					'<td>'+(val.status==0 ? '<span class="badge bg-green">'+val.stone_status+'</span>':'<span class="badge bg-yellow">'+val.stone_status+'</span>')+'</td>'

					'</tr>';

				});

				if($('#karigar_stones_list > tbody  > tr').length>0)

				{

					$('#karigar_stones_list > tbody > tr:first').before(html);

				}

				else

				{

					$('#karigar_stones_list tbody').append(html);

				}

			}

			else

            {

                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Record Found..'});

            }

            $(".overlay").css("display", "none");

		},

		error:function(error)

		{

		$("div.overlay").css("display", "none");

		}

	})

}

function get_KarigarCharges()

{

	$('#karigar_charges_list > tbody').empty();

	my_Date = new Date();

	$.ajax({

		url:base_url+ "index.php/admin_ret_catalog/karigar_approval/charges_list/?nocache=" + my_Date.getUTCSeconds(),

		data: {'id_karigar':$('#karigar_sel').val()},

		dataType:"JSON",

		type:"POST",

		success:function(data)

		{

			$("div.overlay").css("display", "none");

			var list = data.list;

			console.log(list);

			console.log((list!= null && list.length > 0));

			if (list!= null && list.length > 0)

			{

				var html="";

				$.each(list,function(key,val)

                {

					html+='<tr>'+

					'<td><input type="checkbox" class="kar_ch" name="id_karigar_charges[]"/>'+val.id_karigar_charges+'<input type="hidden" class="kar_ch_id"  value='+val.id_karigar_charges+'><input type="hidden" class="id_karigar" name="id_karigar[]" value='+val.id_karigar+'></td>'+

					'<td class="ch_karigar">'+val.karigar+'</td>'+

					'<td class="ch_name">'+val.charge_name+'<input type="hidden" class="charge_id" name="charge_id[]" value='+val.charge_id+'></td>'+

					'<td class="charge_value">'+val.charge_value+'</td>'+

					'</tr>';

				});

				if($('#karigar_charges_list > tbody  > tr').length>0)

				{

					$('#karigar_charges_list > tbody > tr:first').before(html);

				}

				else

				{

					$('#karigar_charges_list tbody').append(html);

				}

			}

			else

            {

                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Record Found..'});

            }

            $(".overlay").css("display", "none");

		},

		error:function(error)

		{

		$("div.overlay").css("display", "none");

		}

	})

}

$('#status_submit').on('click',function()

{

	var approval_type = $("input[name='app[approval_for]']:checked").val();

	var ApprovedList = [];

	var kar_mobile="";

	if(approval_type==0)

	{

		$('#karigar_wastage_list tbody tr').each(function (index,value)

		{

			var row=$(this).closest('tr');

			if(row.find("input[name='id_karikar_wast[]']:checked").is(":checked"))

			{

				 kar_mobile = row.find('.kar_mobile').val();

				ApprovedList.push({

				    "id_karikar_wast"   :row.find('.kar_wast_id').val(),

				    "id_karigar"        :row.find('.id_karigar').val(),

				    "cat_id"            :row.find('.cat_id').val(),

					"id_purity"         :row.find('.id_purity').val(),

				    "pro_id"            :row.find('.pro_id').val(),

				    "des_id"            :row.find('.des_id').val(),

				    "sub_des_id"        :row.find('.sub_des_id').val(),

					"stone_type"        :row.find('.stone_type').val(),

				    "calc_type"         :row.find('.appr_calc_type').val(),

				    "wastage_type"      :row.find('.appr_va_type').val(),

				    "wastage_per"       :row.find('.appr_wast_percent').val(),

				    "wastage_wt"        :row.find('.appr_wast_wgt').val(),

				    "mc_type"           :row.find('.appr_mc_type').val(),

				    "mc_value"          :row.find('.appr_mc').val(),

				    "pur_touch"         :row.find('.appr_pur_touch').val(),

				    "kar_calc_type"     :row.find('.appr_kar_calc_type').val(),

				    "charges_details"   :row.find('.charges_details').val(),

					"stone_cal_type"   :row.find('.appr_stn_calc_type').val(),

					"uom_id"            :row.find('.appr_uom_id ').val(),

					"quality_id"        :row.find('.appr_quality_id ').val(),

					"from_wt"           :row.find('.appr_from_wt').val(),

					"to_wt"             :row.find('.appr_to_wt').val(),

					"rate_per_gram"     :row.find('.appr_rate_per_grm').val(),

				});

			}

		});

	}

	else if(approval_type==1)

	{

		$('#karigar_stones_list tbody tr').each(function(index,value)

		{

			var row=$(this).closest('tr');

			if(row.find("input[name='id_karigar_stone[]']:checked").is(":checked"))

			{

				ApprovedList.push({"id_karigar_stone":row.find('.kar_stn_id').val(),"id_karigar":row.find('.id_karigar').val(),"stone_type_id":row.find('.stone_type_id').val(),"stone_id":row.find('.stone_id').val(),'quality_id':row.find('.quality_id').val(),'from_wt':row.find('.stn_from_wt').html(),'to_wt':row.find('.stn_to_wt').val()});

			}

		})

	}

	else

	{

		$('#karigar_charges_list tbody tr').each(function(index,value)

		{

			var row=$(this).closest('tr');

			if(row.find("input[name='id_karigar_charges[]']:checked").is(":checked"))

			{

				ApprovedList.push({"id_karigar_charges":row.find('.kar_ch_id').val(),"id_karigar":row.find('.id_karigar').val(),"charge_id":row.find('.charge_id').val()});

			}

		})

	}

	if(ApprovedList.length>0)

	{

		console.log(ApprovedList);

		  if ($('#otp_required').val() == 1 && $('#is_otp_verfied').val() != 1) {

			$('#vendor_otp_modal').modal('show');

			vendor_at_send_otp(kar_mobile);

		 }

		else{

			karigarApprovedList(ApprovedList);

		}

	}

	else

	{

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please select Any Id to proceed..'});

	}

});

//Order delievery otp starts

$('.submit_vendor_approval').on('click', function () {

	if ($('#otp_required').val() == 1 && $('#is_otp_verfied').val() != 1) {

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>OTP is Not Verified Yet.." });

		return false;

	} else {

		$('#status_submit').trigger('click');

	}

});

function vendor_at_send_otp(kar_mobile){

	my_Date = new Date();

	var send_resend = $('#send_resend').val();

	$.ajax({

		url: base_url + "index.php/admin_ret_catalog/vendor_sendotp?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),

		type: "POST",

		data: { 'mobile': kar_mobile, 'send_resend': send_resend },

		dataType: "json",

		async: false,

		success: function (data) {

			$('#resend_mobile').val(kar_mobile);

			if (data.status) {

				$("#vendor_otp_modal").modal({

					backdrop: 'static',

					keyboard: false

				});

				var fewSeconds = 3;

				$("#resend_vendor_otp").prop('disabled', true);

				timer = setTimeout(function () {

					$("#resend_vendor_otp").prop('disabled', false);

				}, fewSeconds * 1000);

			}

			else {

				$("#resend_vendor_otp").modal({

					backdrop: 'static',

					keyboard: false

				});

				var fewSeconds = 3;

				$("#resend_vendor_otp").prop('disabled', true);

				timer = setTimeout(function () {

					$("#resend_vendor_otp").prop('disabled', false);

				}, fewSeconds * 1000);

			}

		},

	});

}

$(document).on('input', '#vendor_trns_otp', function (e) {

	if (this.value.length == 6) {

		$('#verify_vendor_otp').prop('disabled', false);

		$('#vendor_trns_otp').val(this.value);

	}

	else {

		$('#verify_vendor_otp').prop('disabled', true);

	}

});

$('#verify_vendor_otp').on('click', function () {

	vendor_verify_otp();

});

function vendor_verify_otp() {

	var transfered_amount_Details= [];

	my_Date = new Date();

	$.ajax({

		url: base_url + "index.php/admin_ret_catalog/vendor_verify_otp?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),

		data: { "otp": $("#vendor_trns_otp").val()},

		type: "POST",

		dataType: "json",

		async: false,

		success: function (data) {

			$('.otp_alert').css('display', 'block');

			if (data.status) {

				// alert("1");

				$('#vendor_trns_otp').prop('disabled', true);

				$('#verify_vendor_otp').prop('disabled', true);

				$('.submit_vendor_approval').prop('disabled', false);

				$(".otp_alert").append('<p style="color:green">' + data.msg + '</p>');

				$('#resend_vendor_otp').prop('disabled', true);

				$('#is_otp_verfied').val(1);

				// $('#adv_trans_otp').val($('#order_trns_otp').val());

				setTimeout(function () {

					$('.otp_alert').css('display', 'none');

					$(".otp_alert").empty();

				}, 3000);

			}

			else {

				$('#verify_vendor_otp').prop('disabled', false);

				$('.submit_vendor_approval').prop('disabled', true);

				$(".otp_alert").append('<p style="color:red">' + data.msg + '</p>');

				$('#is_otp_verfied').val(0);

				setTimeout(function () {

					$('.otp_alert').css('display', 'none');

					$(".otp_alert").empty();

					$('#vendor_trns_otp').prop('disabled', false);

					$("#resend_vendor_otp").prop('disabled', false);

				}, 3000);

				$('#resend_vendor_otp').prop('disabled', false);

			}

		},

		error: function (error) {

			$(".overlay").css('display', "none");

		}

	});

}

$('#resend_vendor_otp').on('click', function () {

	$('#send_resend').val(1);

	$("#vendor_trns_otp").val('');

	$('.submit_vendor_approval').prop('disabled', true);

	var kar_mobile =$("#resend_mobile").val();

	 vendor_at_send_otp(kar_mobile);

});

$('#close').on('click', function () {

	clearTimeout(timer); //clears the previous timer.

	$("#vendor_trns_otp").val('');

	$("#resend_vendor_otp").attr("disabled", true);

	$(".submit_vendor_approval").attr("disabled", true);

	$('#is_otp_verfied').val(0);

});

//Order delievery otp ends

function karigarApprovedList(approved_data)

{

	$(".overlay").css("display", "block");

	var postData = {};

	var status = $('#select_status').val();

	var approval_for = $("input[name='app[approval_for]']:checked").val();

	postData={'approved_data':approved_data,'status':status,'approval_for':approval_for};

	$.ajax({

		type:'POST',

        url : base_url + 'index.php/admin_ret_catalog/karigar_approval/save',

		dataType : 'json',

		data : postData,

		success : function(data)

        {

            console.log(data.status);

            if(data.status)

            {

                $.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>"+data.message});

            }

            else

            {

                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message});

            }

            //window.location.href = base_url+"index.php/admin_ret_catalog/karigar/list";

			window.location.reload();

        }

	})

}

$('.add_wastage').on('click',function()

{

	var validateRow = true;

	$('#product_item_list > tbody  > tr').each(function(index, tr)

	{

		var row = $(this);

		if(row.find('.category').val()==0)

		{

			validateRow = false;

		}

	});

	if(validateRow)

	{

		if(validateOrderDetailRow())

		{

			create_new_empty_karigar_product_detail();

		}else

		{

			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill the required fields..'});

		}

	}

	else

	{

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'All Categories Selected'});

	}

});

function add_Wastage_row()

{

	var Wastagevalidate = true;

	$('#product_item_list > tbody  > tr').each(function(index, tr)

	{

		if($(this).find('category').val()==0)

		{

			Wastagevalidate = false;

		}

	});

	if(Wastagevalidate)

	{

		if(validateOrderDetailRow())

		{

			create_new_empty_karigar_product_detail();

		}else

		{

			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill the required fields..'});

		}

	}

	else

	{

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'All Categories Selected'});

	}

}

function validateOrderDetailRow()

{

    var validate = true;

	$('#product_item_list > tbody  > tr').each(function(index, tr)

	{

		if($(this).find('.product').val() == "" || $(this).find('.design').val() == "" || $(this).find('.design').val() == null || $(this).find('.sub_design').val() == "" || $(this).find('.sub_design').val() == null)

		{

			validate = false;

		}

		if($(this).find('.product option:selected').attr('stone_type')==0)  // For Ornamnets Products

		{

			if($(this).find('.purity').val()=="" || $(this).find('.purity').val()== null || $(this).find('.kar_calc_type').val()=="" || $(this).find('.kar_calc_type').val()==null)

			{

				validate = false;

			}

			else if($(this).find('.kar_calc_type').val()==2 && $(this).find('.pur_touch').val()=='')

			{

				validate = false;

			}

			else if($(this).find('.kar_calc_type').val()!=2 && ($(this).find('.wast_percent').val()=='' || $(this).find('.mc').val()==''))

			{

				validate = false;

			}

			else if(($(this).find('.kar_calc_type').val()!=2 && $(this).find('.va_type ').val()==1) && ($(this).find('.wast_percent').val()=='' || $(this).find('.mc').val()==''))

			{

				validate = false;

			}

			else if(($(this).find('.kar_calc_type').val()!=2 && $(this).find('.va_type ').val()==2) && ($(this).find('.wast_wgt').val()=='' || $(this).find('.mc').val()==''))

			{

				validate = false;

			}

		}

		else if($(this).find('.product option:selected').attr('stone_type')==2) // For Diamond Products

		{

			validate_wt = validate_diamondProd_wt_range($(this));

			if($(this).find('.prod_uom_id').val() == null || $(this).find('.stn_calc_type').val() == "" || $(this).find('.stone_rate').val() == "" || ! validate_wt )

			{

				validate = false;

				if(validate_wt)

				{

					$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill the required fields..'});

				}

			}

		}

		else if($(this).find('.product option:selected').attr('stone_type')==1) // For  Stone Products

		{

			if($(this).find('.prod_uom_id').val() == null || $(this).find('.stn_calc_type').val() == "" || $(this).find('.stone_rate').val() == "")

			{

				validate = false;

			}

		}

	});

	return validate;

}

$(document).on('change',".is_item_select",function(){

	$(this).closest('tr').find('.category').prop('disabled',true);

    $(this).closest('tr').find('.product').prop('disabled',true);

    $(this).closest('tr').find('.design').prop('disabled',true);

    $(this).closest('tr').find('.sub_design').prop('disabled',true);

	$(this).closest('tr').find('.purity').prop('disabled',true);

    $(this).closest('tr').find('.mc_type').prop('disabled',true);

    $(this).closest('tr').find('.mc').prop('disabled',true);

    $(this).closest('tr').find('.pur_touch').prop('disabled',true);

    $(this).closest('tr').find('.wast_percent').prop('disabled',true);

    $(this).closest('tr').find('.calc_type').prop('disabled',true);

    $(this).closest('tr').find('.wast_wgt').prop('disabled',true);

    $(this).closest('tr').find('.va_type').prop('disabled',true);

	$(this).closest('tr').find('.kar_calc_type').prop('disabled',true);

    if($(this).is(":checked"))

    {

        $(this).closest('tr').find('.is_item_select').val(1);

        $(this).closest('tr').find('.product').prop('disabled',false);

        $(this).closest('tr').find('.design').prop('disabled',false);

        $(this).closest('tr').find('.sub_design').prop('disabled',false);

        $(this).closest('tr').find('.mc').prop('disabled',false);

        $(this).closest('tr').find('.mc_type').prop('disabled',false);

        $(this).closest('tr').find('.pur_touch').prop('disabled',false);

        $(this).closest('tr').find('.wast_percent').prop('disabled',false);

        $(this).closest('tr').find('.calc_type').prop('disabled',false);

        $(this).closest('tr').find('.wast_wgt').prop('disabled',false);

        $(this).closest('tr').find('.va_type').prop('disabled',false);

        $(this).closest('tr').find('.category').prop('disabled',false);

        $(this).closest('tr').find('.purity').prop('disabled',false);

        $(this).closest('tr').find('.kar_calc_type').prop('disabled',false);

    }else{

        $(this).closest('tr').find('.is_item_select').val(0);

    }

    if($(this).closest('tr').find('.kar_calc_type ').val()==2)

    {

        $(this).closest('tr').find('.mc_type').prop('disabled',true);

        $(this).closest('tr').find('.mc').prop('disabled',true);

        $(this).closest('tr').find('.mc_type').prop('disabled',true);

        $(this).closest('tr').find('.wast_percent').prop('disabled',true);

        $(this).closest('tr').find('.wast_wgt').prop('disabled',true);

        $(this).closest('tr').find('.va_type').prop('disabled',true);

    }

});

function create_new_empty_karigar_product_detail()

{

    	var html = "";

    	var item_length = $('#product_item_list tbody > tr').length;

		var a = item_length;

		var i = ++a;

		$("#i_increment").val(i);

			html+="<tr id='detail"+i+"' class='"+i+"'>"+

				"<td><input type='checkbox' class='is_item_select' name='o_item["+i+"][is_item_select]' value='1' checked></td>"+

				"<td><select class='form-control category' name='o_item["+i+"][category]' id='category"+i+"' required='true' style='width: 100px !important;' placeholder='Search Category'/><input type='hidden' name='o_item["+i+"][id_category]' id='id_category"+i+"' required='true'/></td>"+

				"<td><select  class='form-control purity' name='o_item["+i+"][purity]' id='purity"+i+"' required='true' style='width: 100px !important;' /><input type='hidden' name='o_item["+i+"][id_purity]' id='id_purity"+i+"' required='true'/></td>"+

				"<td><select  class='form-control product' name='o_item["+i+"][product]' id='product"+i+"' required='true' style='width: 100px !important;' /><input type='hidden' name='o_item["+i+"][id_product]' id='id_product"+i+"' required='true'/></td>"+

				"<td><select  class='form-control design' name='o_item["+i+"][design]' id='design"+i+"' required='true' style='width: 100px !important;' /><input type='hidden' name='o_item["+i+"][id_design]' id='id_design"+i+"' required='true'/></td>"+

				"<td><select  class='form-control sub_design' name='o_item["+i+"][sub_design]' id='sub_design"+i+"' required='true' style='width: 100px !important;' /><input type='hidden' name='o_item["+i+"][id_sub_design]' id='id_sub_design"+i+"' required='true'/></td>"+

				"<td><select  class='form-control kar_calc_type' name='o_item["+i+"][kar_calc_type]' style='width: 100px !important;'><option value=''>Select Type</option><option value='1'  >Pure Weight x Rate</option><option value='2' >Purchase Touch</option><option value='3' >Pure Weight x Wastage %</option></select></td>"+

				"<td><input type='number' step='any' class='form-control pur_touch' placeholder='Touch' name='o_item["+i+"][pur_touch]' style='width: 100px;'/></td>"+

				"<td><select  class='form-control calc_type' name='o_item["+i+"][calc_type]' style='width: 100px !important;;'><option value=''>Select Type</option><option value='0'  >Mc & Wast On Gross</option><option value='1' >Mc & Wast On Net</option><option value='2' >Mc on Gross,Wast On Net</option></select></td>"+

				"<td><select ' class='form-control va_type' name='o_item["+i+"][va_type]' style='width: 100px !important;'><option value='1' selected>Percentage</option><option value='2'>Weight</option></select></td>"+

				"<td class='va_per'><input type='number' class='form-control wast_percent' placeholder='Wast %' name='o_item["+i+"][wast_percent]' required='true' style='width: 100px !important;'/></td>"+

				"<td class='va_wt' style='display:none'><input type='number' class='form-control wast_wgt' placeholder='Wast Wgt' name='o_item["+i+"][wast_wgt]' required='true' style='width: 100px !important;'/></td>"+

				"<td><select  class='form-control mc_type' name='o_item["+i+"][id_mc_type]' style='width: 100px !important;'><option value='1'>Gram</option><option value='2' selected>Piece</option></select></td>"+

				"<td><input type='number' step='any' class='form-control mc' placeholder='MC Amount' name='o_item["+i+"][mc]' style='width: 100px !important;'/></td>"+

				'<td><a href="#" onClick="add_kar_product_charges($(this).closest(\'tr\'));" class="btn btn-default btn-sm "><i class="fa fa-plus"></i></a><input type="hidden" class="charges_details" name="o_item['+i+'][charges_details]""></td>'+

				"<td><select  class='form-control stn_calc_type' name='o_item["+i+"][stn_calc_type]' style='width: 100px !important;' disabled><option value=''>Select Type</option><option value='1' >Wt</option><option value='2' >PCS</option></select></td>"+

				"<td><select class='prod_uom_id' class='form-control' id='prod_uom_id"+i+"' name='o_item["+i+"][uom_id]' style='width: 100px !important;' disabled></td>"+

				"<td><select class='prod_stn_quality' class='form-control' id='prod_stn_quality"+i+"' name='o_item["+i+"][quality_id]' style='width: 100px !important;' disabled></select></td>"+

				"<td><input type='number' step='any' class='form-control prod_from_wt' placeholder='From' name='o_item["+i+"][from_wt]' readonly/style='width: 100px !important;'></td>"+

				"<td><input type='number' step='any' class='form-control prod_to_wt' placeholder='To' name='o_item["+i+"][to_wt]' style='width: 100px !important;' readonly/></td>"+

				"<td style='display: flex;flex-direction: row;' ><input type='number' step='any' class='form-control stone_rate' placeholder='Stone rate' name='o_item["+i+"][stone_rate]' style='width: 100px !important;' readonly/></td>"+

				'<td><img src="'+base_url+'assets/img/no_image.png"  class="img-thumbnail" id="wast_image_pre" style="width:100px;" onClick="update_karWast_image_upload($(this).closest(\'tr\'));" alt="classfication image"><input type="hidden" class="order_img" name="o_item['+i+'][order_img]""></td>'+

				'<td><button type="button" class="btn btn-success addKarigarWastages" onClick="add_Wastage_row()"><i class="fa fa-plus"></i></button><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'

			"</tr>";

			$('#product_item_list tbody').append(html);

			$('#product_item_list tr:not(:last-child) .addKarigarWastages').remove();

			$('#category'+i).append(

				$("<option></option>")

				.attr("value",'')

				.text('--Choose--')

			);

			$('#category'+i).append(

				$("<option></option>")

				.attr("value",0)

				.text('All')

			);

			$.each(cat_details,function(key,item)

			{

				$('#category'+i).append(

					$("<option></option>")

					.attr("value", item.id_ret_category)

					.text(item.name)

				);

			});

			$('#prod_uom_id'+i).append(

				$("<option></option>")

				.attr("value", "")

				.text('-Choose-')

			);

			$('#prod_stn_quality'+i).append(

				$("<option></option>")

				.attr("value", "")

				.text('-Choose-')

			);

			$.each(uom_details, function (key, item)

			{

                $('#prod_uom_id'+i).append(

                $("<option></option>")

                .attr("value", item.uom_id)

                .text(item.code)

                );

            });

			$.each(quality_code, function (key, item)

			{

                $('#prod_stn_quality'+i).append(

                $("<option></option>")

                .attr("value", item.quality_id)

                .text(item.code)

                );

            });

			$('#product_item_list > tbody').find('.category').select2();

			$('#product_item_list > tbody').find('.category').select2({

				placeholder: "Category",

				allowClear: true

			});

			$('#product_item_list > tbody').find('.product').select2();

            $('#product_item_list > tbody').find('.product').select2({

                placeholder: "Product",

                allowClear: true

            });

			$('#product_item_list > tbody').find('.design').select2();

            $('#product_item_list > tbody').find('.design').select2({

                placeholder: "Design",

                allowClear: true

            });

			$('#product_item_list > tbody').find('.sub_design').select2();

            $('#product_item_list > tbody').find('.sub_design').select2({

                placeholder: "Sub Design",

                allowClear: true

            });

			$('#product_item_list > tbody').find('.purity').select2();

            $('#product_item_list > tbody').find('.purity').select2({

                placeholder: "Purity",

                allowClear: true

            });

            $('#product_item_list > tbody').find('.mc_type').select2();

            $('#product_item_list > tbody').find('.mc_type').select2({

                placeholder: "MC Type",

                allowClear: true

            });

            $('#product_item_list > tbody').find('.calc_type').select2();

			$('#product_item_list > tbody').find('.calc_type').select2({

				placeholder: "Calc Type",

				allowClear: true

			});

			$('#product_item_list > tbody').find('.va_type').select2();

            $('#product_item_list > tbody').find('.va_type').select2({

            	placeholder: "VA Type",

            	allowClear: true

            });

			$('#product_item_list > tbody').find('.kar_calc_type').select2();

            $('#product_item_list > tbody').find('.kar_calc_type').select2({

            	placeholder: "Karigar Calc Type",

            	allowClear: true

            });

			$('#product_item_list > tbody').find('.stn_calc_type').select2();

            $('#product_item_list > tbody').find('.stn_calc_type').select2({

            	placeholder: "Stone Calc Type",

            	allowClear: true

            });

			$('#product_item_list > tbody').find('.prod_uom_id').select2();

            $('#product_item_list > tbody').find('.prod_uom_id').select2({

            	placeholder: "Select UOM",

            	allowClear: true

            });

			$('#product_item_list > tbody').find('.prod_stn_quality').select2();

            $('#product_item_list > tbody').find('.prod_stn_quality').select2({

            	placeholder: "Select Quality Code",

            	allowClear: true

            });

}

$(document).on('change','.va_type',function()

{

	var curRow=$(this).closest('tr');

	if(this.value==2)

	{

		curRow.find('.va_wt').css('display','block');;

		$('.va_wgt').css('display','block');;

		curRow.find('.va_per').css('display','none');

		$('.va_perc').css('display','none');

	}

	else

	{

		curRow.find('.va_wt').css('display','none');

		$('.va_wgt').css('display','none');

		curRow.find('.va_per').css('display','block');

		$('.va_perc').css('display','block');

	}

});

function get_karigar_product_details(id){

	my_Date = new Date();

	$.ajax({

        url: base_url+'index.php/admin_ret_catalog/karigar/product_details/'+id+'?nocache=' + my_Date.getUTCSeconds(),

        dataType: "json",

        method: "POST",

        success: function (data) {

			console.log(data);

        	$("#product_item_list tbody tr").empty();

        	$("#product_item_list tbody tr").empty();

        	$("#i_increment").val(0);

			var a = 0;

	        $.each(data.karigar_wastages, function (key, item) {

	        	var html_1 = "";

				var wast_img_resource = [];

				var img_name ='';

				if(item.karigar_wastage_images.length>0)

				{

				    $.each(item.karigar_wastage_images,function(key,items)

    				{

    				if(items.is_default==1)

    					{

    						img_name = base_url+'assets/img/karigar/product_wastage/'+items['id_karikar_wast']+'/'+items['image_name'];

    					}

    					else

    					{

    						img_name = base_url+'assets/img/no_image.png';

    					}

                        wast_img_resource.push({"src":items['img_src'],'name':items['image_name'],'is_default':items['is_default']});

    				});

				}else{

				    img_name = base_url+'assets/img/no_image.png';

				}

				console.log('wast_img',wast_img_resource);

				var i = ++a;

				$("#i_increment").val(i);

				html_1+="<tr id='detail"+i+"' class='"+i+"'>"+

				"<td><input type='checkbox' class='is_item_select' name='o_item["+i+"][is_item_select]' ></td>"+

				"<td><select class='form-control category' placeholder='Category Name' name='o_item["+i+"][category]' required='true' value='"+item.id_category+"' id='category"+i+"'  style=width:150px/></td>"+

                "<td><select class='purity form-control' placeholder='Purity Name' name='o_item["+i+"][purity]' required='true' value='"+item.id_purity+"' id='purity"+i+"'  style=width:150px/><input type='hidden' class='id_purity' name='o_item["+i+"][id_purity]' id='id_purity"+i+"' required='true' value='"+item.id_purity+"'/></td>"+

				"<td><select class='product form-control' placeholder='product Name' name='o_item["+i+"][product]' required='true' value='"+item.id_product+"' id='product"+i+"'  style=width:150px/><input type='hidden' name='o_item["+i+"][id_product]' id='id_product"+i+"' class='id_product' required='true' value='"+item.id_product+"'/><input type='hidden' name='o_item["+i+"][id_karikar_wast]' value="+item.id_karikar_wast+" ></td>"+

				"<td><select class='design form-control' placeholder='design Name' name='o_item["+i+"][design]' required='true' value='"+item.id_design+"' id='design"+i+"'   style=width:150px/><input type='hidden' class='id_design' name='o_item["+i+"][id_design]' id='id_design"+i+"' required='true' value='"+item.id_design+"'/></td>"+

				"<td><select class='sub_design form-control' placeholder='sub_design Name' name='o_item["+i+"][sub_design]' required='true' value='"+item.id_sub_design+"' id='sub_design"+i+"'  style=width:150px/><input type='hidden' name='o_item["+i+"][id_sub_design]' class='id_sub_design' id='id_sub_design"+i+"' required='true' value='"+item.id_sub_design+"'/></td>"+

				"<td><select class='kar_calc_type' class='form-control' name='o_item["+i+"][kar_calc_type]' style='width: 100px;'><option value='1' "+(item.karigar_calc_type==1 ? 'selected' :'')+">Pure Weight x Rate</option><option value='2' "+(item.karigar_calc_type==2 ? 'selected' :'')+">Purchase Touch</option><option value='3' "+(item.karigar_calc_type==3 ? 'selected' :'')+">Pure Weight x Wastage %</option></select></td>"+

				"<td><input type='number' step='any' class='form-control pur_touch' placeholder='Touch' name='o_item["+i+"][pur_touch]'  value='"+item.pur_touch+"' style='width:100px;'/></td>"+

                "<td><select class='calc_type' class='form-control' name='o_item["+i+"][calc_type]' style='width: 100px;' "+(item.karigar_calc_type==2 ? 'disabled' :'')+"  ><option value='0' "+(item.calc_type==0 ? 'selected' :'')+">Mc & Wast On Gross</option><option value='1' "+(item.calc_type==1 ? 'selected' :'')+">Mc & Wast On Net</option><option value='2' "+(item.calc_type==2 ? 'selected' :'')+"> Mc on Gross,Wast On Net</option></select></td>"+

				"<td><select class='va_type' class='form-control' name='o_item["+i+"][va_type]' style='width: 100px;'  "+(item.karigar_calc_type==2 ? 'disabled' :'')+"  ><option value='1' "+(item.wastage_type==1 ? 'selected' :'')+">Percentage</option><option value='2' "+(item.wastage_type==2 ? 'selected' :'')+">Weight</option></select></td>"+

                "<td class='va_per' style='"+(item.wastage_type==2 ? "display:none;":"display:block;")+"'><input type='number' class='form-control wast_percent' placeholder='wastages ' name='o_item["+i+"][wast_percent]' required='true' value='"+item.wastage_per+"' id='wastage"+i+"' style='width:100px;' "+(item.karigar_calc_type==2 ? 'disabled' :'')+"  /></td>"+

                "<td class='va_wt' style='"+(item.wastage_type==1 ? "display:none;":"display:block;")+"'><input type='number' class='form-control wast_wgt' placeholder='wastages ' name='o_item["+i+"][wast_wgt]' required='true' value='"+item.wastage_wt+"' id='wast_wgt"+i+"' style='width:100px;' "+(item.karigar_calc_type==2 ? 'disabled' :'')+"  /></td>"+

				"<td><select class='mc_type' class='form-control' name='o_item["+i+"][id_mc_type]' style='width: 100px;'  "+(item.karigar_calc_type==2 ? '' :'')+"  ><option value='1' "+(item.mc_type==1 ? 'selected' :'')+">Gram</option><option value='2' "+(item.mc_type==2 ? 'selected' :'')+">Piece</option></select></td>"+

				"<td><input class='mc form-control' placeholder='mc ' name='o_item["+i+"][mc]' required='true' value='"+item.mc_value+"'  "+(item.karigar_calc_type==2 ? '' :'')+"  id='mc"+i+"' style='width:100px;'/></td>"+

				"<td><a href='#' onClick='add_kar_product_charges($(this).closest(\"tr\"));' class='btn btn-default btn-sm'><i class='fa fa-plus'></i></a><input type='hidden' class='charges_details' name='o_item["+i+"][charges_details]'  value='"+(item.karigar_charges.length > 0 ? JSON.stringify(item.karigar_charges) :'')+"'></td>"+

				"<td><select  class='form-control stn_calc_type' name='o_item["+i+"][stn_calc_type]' style='width: 100px !important;' disabled><option value=''>Select Type</option><option value='1' "+(item.stone_cal_type==1 ? 'selected' :'')+">Wt</option><option value='2' "+(item.stone_cal_type==2 ? 'selected' :'')+">PCS</option></select></td>"+

				"<td><select class='prod_uom_id' class='form-control' id='prod_uom_id"+i+"' name='o_item["+i+"][uom_id]' style='width: 100px !important;' disabled></td>"+

				"<td><select class='prod_stn_quality' class='form-control' id='prod_stn_quality"+i+"' name='o_item["+i+"][quality_id]' style='width: 100px !important;' disabled></select></td>"+

				"<td><input type='number' step='any' class='form-control prod_from_wt' placeholder='From' name='o_item["+i+"][from_wt]' readonly/style='width: 100px !important;' value='"+item.from_wt+"'></td>"+

				"<td><input type='number' step='any' class='form-control prod_to_wt' placeholder='To' name='o_item["+i+"][to_wt]' style='width: 100px !important;' value='"+item.to_wt+"' readonly/></td>"+

				"<td style='display: flex;flex-direction: row;' ><input type='number' step='any' class='form-control stone_rate' placeholder='Stone rate' name='o_item["+i+"][stone_rate]' style='width: 100px !important;'  value='"+item.rate_per_gram+"' readonly/></td>"+

				"<td><img src='"+img_name+"'  class='img-thumbnail' id='wast_image_pre' style='width:200px;' onClick='update_karWast_image_upload($(this).closest(\"tr\"));' alt='classfication image'><input type='hidden' class='order_img' name='o_item["+i+"][order_img]' value='"+(item.karigar_wastage_images.length > 0 ? JSON.stringify(wast_img_resource) :'')+"' ></td>"+

				'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'

				"</tr>";

				$('#product_item_list tbody').append(html_1);

				$('#category'+i).append(

					$("<option></option>")

					.attr("value", 0)

					.text('All')

				);

			    $.each(data.categories,function(key,val)

				{

					$('#category'+i).append(

				    	$("<option></option>").attr("value", val.id_ret_category).text(val.name)

				    );

				});

				$('#category'+i).val(item.id_category);

				$('#prod_uom_id'+i).append(

					$("<option></option>")

					.attr("value", 0)

					.text('---')

				);

				$.each(data.uom,function(key,val)

				{

					$('#prod_uom_id'+i).append(

				    	$("<option></option>").attr("value", val.uom_id).text(val.code)

				    );

				});

				$('#prod_uom_id'+i).val(item.uom_id);

				$('#prod_stn_quality'+i).append(

					$("<option></option>")

					.attr("value", 0)

					.text('---')

				);

				$.each(data.quality_code,function(key,val)

				{

					$('#prod_stn_quality'+i).append(

				    	$("<option></option>").attr("value", val.quality_id).text(val.code)

				    );

				});

				$('#prod_stn_quality'+i).val(item.quality_id);

				let row_lastObj = $('#product_item_list tbody #detail'+i);

				get_KarigarProducts(row_lastObj,item.id_category);

				get_ActiveDesigns(row_lastObj,item.id_product);

				get_ActiveSubDesigns(row_lastObj,item.id_design);

				get_KarigarWastPurity(row_lastObj,item.id_category);

				$('#product_item_list > tbody').find('.category').select2();

				$('#product_item_list > tbody').find('.category').select2({

				    placeholder: "Category",

				    allowClear: true

				});

				$('#product_item_list > tbody').find('.product').select2();

				$('#product_item_list > tbody').find('.product').select2({

				    placeholder: "product",

				    allowClear: true

				});

				$('#product_item_list > tbody').find('.purity').select2();

				$('#product_item_list > tbody').find('.purity').select2({

				    placeholder: "purity",

				    allowClear: true

				});

				$('#product_item_list > tbody').find('.design').select2();

				$('#product_item_list > tbody').find('.design').select2({

				    placeholder: "design",

				    allowClear: true

				});

				$('#product_item_list > tbody').find('.sub_design').select2();

				$('#product_item_list > tbody').find('.sub_design').select2({

				    placeholder: "sub_design",

				    allowClear: true

				});

				$('#product_item_list > tbody').find('.mc_type').select2();

				$('#product_item_list > tbody').find('.mc_type').select2({

				    placeholder: "MC Type",

				    allowClear: true

				});

				$('#product_item_list > tbody').find('.kar_calc_type').select2();

				$('#product_item_list > tbody').find('.kar_calc_type').select2({

				    placeholder: "Karigar Calc Type",

				    allowClear: true

				});

				$('#product_item_list > tbody').find('.calc_type').select2();

				$('#product_item_list > tbody').find('.calc_type').select2({

				    placeholder: "Calc Type",

				    allowClear: true

				});

				$('#product_item_list > tbody').find('.va_type').select2();

                $('#product_item_list > tbody').find('.va_type').select2({

                	placeholder: "V.A Type",

                	allowClear: true

                });

				$('#product_item_list > tbody').find('.stn_calc_type').select2();

				$('#product_item_list > tbody').find('.stn_calc_type').select2({

					placeholder: "Stone Calc Type",

					allowClear: true

				});

				$('#product_item_list > tbody').find('.prod_uom_id').select2();

				$('#product_item_list > tbody').find('.prod_uom_id').select2({

					placeholder: "Select UOM",

					allowClear: true

				});

				$('#product_item_list > tbody').find('.prod_stn_quality').select2();

				$('#product_item_list > tbody').find('.prod_stn_quality').select2({

					placeholder: "Select Quality Code",

					allowClear: true

				});

	 		});

        }

     });

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

function add_kar_product_charges(curRow,id)

{

    $('#cus_chargeModal .modal-body').find('#table_charges tbody').empty();

    if(curRow!=undefined)

	{

		$('#charge_active_row').val(curRow.closest('tr').attr('class'));

	}

    var selData = curRow.find('.charges_details').val();

        if(selData!='' && selData!='[]')

        {

            var charges = JSON.parse(selData);

            console.log(charges);

            let options = '';

            $(charges).each(function(key,val)

            {

                $(charges_list).each(function(idx, charges){

                    options += "<option value='"+charges.id_charge+"' "+(charges ? (charges.id_charge == val.charge_id ? 'selected' : '') : '')+">"+charges.name_charge+"</option>";

                });

                let row_cls = $('#table_charges tbody tr').length;

                let _row_last = $('#table_charges tbody tr:last');

                let sno = (_row_last.length > 0 ? parseInt(_row_last.find('.sno').text()) : 0)+1;

                let new_row = "<tr class='ch_"+row_cls+"'><td class='sno'>"+sno+"</td><td><select class='form-control chargesType'><option value=''>--Select--</option>"+options+"</select></td><td><select class='form-control calc_type'><option value='1' "+(val.calc_type==1 ? 'selected' :'')+">Per Item</option><option value='2' "+(val.calc_type==2 ? 'selected' :'')+" >Per Piece</option></select></td><td><input type='text' value='"+(charges ? (val.charge_value == undefined ? 0 : val.charge_value) : '')+"' class='form-control chargesValue' /></td><td class='chargeModal_buttons'></td></tr>";

                $('#cus_chargeModal .modal-body').find('#table_charges tbody').append(new_row);

                $('#cus_chargeModal .modal-body').find('#table_charges tbody ch_'+row_cls+ '.chargesType').focus();

            });

        }

        else

        {

            let options = '';

            console.log(charges_list);

            $(charges_list).each(function(idx, charges){

                options += "<option value='"+charges.id_charge+"' "+(selData ? (charges.id_charge == selData.charge_id ? 'selected' : '') : '')+">"+charges.name_charge+"</option>";

            });

            let row_cls = $('#table_charges tbody tr').length;

            let _row_last = $('#table_charges tbody tr:last');

            let sno = (_row_last.length > 0 ? parseInt(_row_last.find('.sno').text()) : 0)+1;

            let new_row = "<tr class='ch_"+row_cls+"'><td class='sno'>"+sno+"</td><td><select class='form-control chargesType'><option value=''>--Select--</option>"+options+"</select></td><td><select class='form-control calc_type'><option value='1'>Per Item</option><option value='2'>Per Piece</option></select></td><td><input type='text' value='"+(selData ? (selData.charge_value == undefined ? 0 : selData.charge_value) : '')+"' class='form-control chargesValue' /></td><td class='chargeModal_buttons'></td></tr>";

            $('#cus_chargeModal .modal-body').find('#table_charges tbody').append(new_row);

            $('#cus_chargeModal .modal-body').find('#table_charges tbody ch_'+row_cls+ '.chargesType').focus();

        }

		addChargeModal_buttons();

        $('#cus_chargeModal').modal('show');

}

function addChargeModal_buttons()

{

	let charge_rows = $("#table_charges tbody");

	$(charge_rows).find('.create_charge_item_details').remove();

	$(charge_rows).find('.remove_charge_item_details').remove();

	$(charge_rows).find(".chargeModal_buttons:last").prepend('<button type="button" class="btn btn-success btn-xs create_charge_item_details"><i class="fa fa-plus"></i></button>');

	$(charge_rows).find(".chargeModal_buttons").append('<button type="button" class="btn btn-danger btn-xs remove_charge_item_details" onclick="remove_charge(this)"><i class="fa fa-trash"></i></button>');

}

$(document).on('click', '#table_charges tbody tr .create_charge_item_details, .add_charges', function(){

    if(validate_charges_row()){

		create_new_charges_row();

	}else{

		alert("Please fill required charge fields");

	}

});

function create_new_charges_row()

{

    if(validate_charges_row())

    {

        let options = '';

        $(charges_list).each(function(idx, charges){

            options += "<option value='"+charges.id_charge+"'>"+charges.name_charge+"</option>";

        });

        let row_cls = $('#table_charges tbody tr').length;

        let _row_last = $('#table_charges tbody tr:last');

        let sno = (_row_last.length > 0 ? parseInt(_row_last.find('.sno').text()) : 0)+1;

        let new_row = "<tr class='ch_"+row_cls+"'><td class='sno'>"+sno+"</td><td><select class='form-control chargesType'><option value=''>--Select--</option>"+options+"</select></td><td><select class='form-control calc_type'><option value='1'>Per Item</option><option value='2'>Per Piece</option></select></td><td><input type='text'  class='form-control chargesValue' /></td><td class='chargeModal_buttons'></td></tr>";

        $('#cus_chargeModal .modal-body').find('#table_charges tbody').append(new_row);

        $('#cus_chargeModal .modal-body').find('#table_charges tbody ch_'+row_cls+ '.chargesType').focus();

        addChargeModal_buttons();

    }

}

function remove_charge(obj)

{

	$(obj).closest('tr').remove();

	addChargeModal_buttons();

}

$('#cus_chargeModal  #update_charge_details').on('click', function(){

	if(validate_charges_row())

    {

		var charge_details=[];

		$('#cus_chargeModal .modal-body #table_charges> tbody  > tr').each(function(index, tr) {

			charge_details.push({

						'charge_value'       : $(this).find('.chargesValue').val(),

						'charge_id'          : $(this).find('.chargesType').val(),

						'calc_type'          : $(this).find('.calc_type').val(),

						'name_charge'        : $(this).find('.chargesType :selected').text(),

						});

		});

		var catRow=$('#charge_active_row').val();

		$('.'+catRow).find('.charges_details').val(JSON.stringify(charge_details));

        $('#cus_chargeModal .modal-body').find('#table_charges tbody').empty();

        $('#cus_chargeModal').modal('hide');

    }

    else

    {

    	alert('Please Fill The Required charge Details');

    }

});

$(document).on('change','.category',function()

{

	var row = $(this).closest('tr');

	var row_len = row.closest('tr').attr('class');

	console.log((row_len > 1));

	if(CategoryWastageRestrictions(this.value))

	{

		get_KarigarProducts(row,this.value);

		get_KarigarWastPurity(row,this.value);

	}

	else

	{

		row.find('.category').select2("val","");

	}

});

$(document).on('change','.purity',function()

{

	var row = $(this).closest('tr');

	var cat_id = row.find('.category').val();

	var trid = $(this).closest('tr').attr('id');

	var stone_type = row.find('.product option:selected').attr('stone_type'); // 0 - Ornaments , 1 - stone 2 - Diamond

	if((stone_type==0 ? PurityWastageRestrictions(cat_id,this.value,trid) : true))

	{

		row.find('.purity').val(this.value);

	}

	else

	{

		row.find('.purity').select2("val","");

	}

})

function get_KarigarProducts(curRow,id_catgeory)

{

	$(".overlay").css('display','block');

    $.ajax({

        type: 'POST',

        url: base_url+"index.php/admin_ret_catalog/ret_product/active_list/?nocache=" + my_Date.getUTCSeconds(),

	    data :{'id_category':id_catgeory},

        dataType:'json',

		success:function(data)

		{

			curRow.find(".product option").remove();

			var pro_id = curRow.find('.id_product').val();

			console.log(pro_id);

			$('.product').append(

				$("<option></option>")

				.attr("value",0)

				.text('All')

			);

           	$.each(data, function (key, item) {

                curRow.find(".product").append(

                    $("<option></option>")

                    .attr("value", item.pro_id)

					.attr("stone_type", item.stone_type)

                    .text(item.product_name)

                );

            });

			$(".product").select2({

                placeholder: "Product",

                allowClear: true

            });

			curRow.find('.product').select2("val",(pro_id!='' ? pro_id :""));

            $(".overlay").css("display", "none");

		}

	});

}

function get_KarigarWastPurity(curRow,id_catgeory)

{

	$(".overlay").css('display','block');

    $.ajax({

        type: 'POST',

        url: base_url+"index.php/admin_ret_catalog/get_KarigarWastPurity/?nocache=" + my_Date.getUTCSeconds(),

	    data :{'id_category':id_catgeory},

        dataType:'json',

		success:function(data)

		{

			curRow.find(".purity option").remove();

			var id_purity = curRow.find('.id_purity').val();

			$.each(data, function (key, item) {

				curRow.find(".purity").append(

					$("<option></option>")

					.attr("value", item.id_purity)

					.text(item.purity)

				);

			});

            curRow.find(".purity").select2({

                placeholder: "Purity",

                allowClear: true

            });

			curRow.find('.purity').select2("val",(id_purity!=''&& id_purity>0 ? id_purity :""));

            $(".overlay").css("display", "none");

		}

	});

}

$(document).on('change',".product",function()

{

	var row = $(this).closest('tr');

	var trid = $(this).closest('tr').attr('id');

	var cat_id = row.find('.category').val();

	var purity_id = row.find('.purity').val();

	var row_len = row.closest('tr').attr('class');

	row.find('.design option').remove();

	var stone_type = row.find('.product option:selected').attr('stone_type'); // 0 - Ornaments , 1 - stone 2 - Diamond

	if((stone_type==0 ? ProductWasatageRestrictions(cat_id,purity_id,this.value,trid) : true))

	{

		if(this.value!='')

		{

			get_ActiveDesigns(row,this.value);

			ProductStoneValidation(row,stone_type);

		}

	}

	else

	{

		row.find('.product').select2("val","");

	}

});

function get_ActiveDesigns(curRow)

{

    $(".overlay").css('display','block');

    $.ajax({

        type: 'POST',

        url: base_url+'index.php/admin_ret_catalog/get_active_design_products',

        dataType:'json',

        data:{'id_product':curRow.find('.product').val()},

        success:function(data){

            var des_id = curRow.find('.id_design').val();

            curRow.find(".design option").remove();

			curRow.find('.design').append(

                $("<option></option>")

                .attr("value",0)

                .text('All')

            );

           $.each(data, function (key, item) {

                curRow.find(".design").append(

                    $("<option></option>")

                    .attr("value", item.design_no)

                    .text(item.design_name)

                );

            });

            $(".design").select2({

                placeholder: "Design",

                allowClear: true

            });

            curRow.find('.design').select2("val",(des_id!='' ? des_id :""));

            $(".overlay").css("display", "none");

        }

    });

}

$(document).on('change',".design",function(){

    var row = $(this).closest('tr');

    var trid = $(this).closest('tr').attr('id');

    var cat_id = row.find('.category').val();

	var purity_id = row.find('.purity').val();

    var pro_id = row.find('.product').val();

    row.find('.sub_design option').remove();

	var stone_type = row.find('.product option:selected').attr('stone_type'); // 0 - Ornaments , 1 - stone 2 - Diamond

	if((stone_type==0 ? DesignWastageRestrictions(cat_id,purity_id,pro_id,this.value,trid) : true))

	{

		if(this.value!='')

		{

			get_ActiveSubDesigns(row,this.value);

		}

	}

	else

	{

		row.find('.design').select2("val","");

	}

});

$(document).on('change','.sub_design',function()

{

	var row = $(this).closest('tr');

    var trid = $(this).closest('tr').attr('id');

	var cat_id = row.find('.category').val();

	var purity_id = row.find('.purity').val();

	var pro_id = row.find('.product').val();

	var des_id = row.find('.design').val();

	var stone_type = row.find('.product option:selected').attr('stone_type'); // 0 - Ornaments , 1 - stone 2 - Diamond

	if((stone_type == 0 ? subDesignRestriction(cat_id,purity_id,pro_id,des_id,this.value,trid) : true))

	{

		row.find('.sub_design').val(this.value);

	}

	else

	{

		row.find('.sub_design').select2("val","");

	}

});

function get_ActiveSubDesigns(curRow,id_design)

{

    $(".overlay").css('display','block');

    $.ajax({

        type: 'POST',

        url: base_url+'index.php/admin_ret_catalog/get_ActiveSubDesigns',

        dataType:'json',

        data:{'id_product':curRow.find('.product').val(),'design_no':curRow.find('.design').val()},

        success:function(data){

            var id_sub_des = curRow.find('.id_sub_design').val();

            curRow.find(".sub_design option").remove();

			curRow.find('.sub_design').append(

                $("<option></option>")

                .attr("value", 0)

                .text('All')

            );

           $.each(data, function (key, item) {

                curRow.find(".sub_design").append(

                    $("<option></option>")

                    .attr("value", item.id_sub_design)

                    .text(item.sub_design_name)

                );

            });

            $(".sub_design").select2({

                placeholder: "Sub Design",

                allowClear: true

            });

            curRow.find('.sub_design').select2("val",(id_sub_des!='' ? id_sub_des :""));

            $(".overlay").css("display", "none");

        }

    });

}

function get_WastApprvKarigars()

{

	$('#karigar_sel option').remove();

	$.ajax({

		type: 'POST',

		url: base_url+'index.php/admin_ret_catalog/get_WastApprvKarigars',

		data:{'approval_for':$('.aprroval_for:checked').val()},

		dataType:'json',

		success:function(data){

			var id =  $("#karigar").val();

			$.each(data, function (key, item) {

				$("#karigar_sel").append(

				$("<option></option>")

				.attr("value", item.id_karigar)

				.text(item.karigar)

				);

			});

			$("#karigar_sel").select2(

			{

				placeholder:"Select Karigar",

				allowClear:true,

				//closeOnSelect: false

			});

			if($('#karigar').data('karigar'))

			{

				   var ar = $('#karigar').data('karigar');

				   $("#karigar_sel").select2('val',ar);

			}

			else

			{

				  $("#karigar_sel").select2('val','');

			}

		}

	});

}

function CategoryWastageRestrictions(cat_id)

{

	var Wastagevalidate = true;

	var t_len = $('#product_item_list tbody tr').length;

	if((cat_id!='' && cat_id!=undefined) && t_len>1)

	{

		$('#product_item_list > tbody  > tr').each(function(index, tr)

		{

			var row = $(this);

			if(row.find('.category').val()==0)

			{

				$.toaster({ priority : 'danger', title : 'Warning!', message : "All Category Selected"});

				Wastagevalidate = false;

			}

			/*else if((row.find('.category').val() == cat_id) && row.find('.product').val()==0)

			{

				$.toaster({ priority : 'danger', title : 'Warning!', message : "Category Already Exists"});

				Wastagevalidate = false;

			}*/

		});

	}

	return Wastagevalidate;

}

function PurityWastageRestrictions(cat_id,purity_id,trid)

{

	var Wastagevalidate = true;

	var t_len = $('#product_item_list tbody tr').length;

	if((purity_id!='' && purity_id!=undefined) && t_len>1)

	{

		$('#product_item_list > tbody  > tr').each(function(index, tr)

		{

			var row = $(this);

			var table_id = $(this).attr('id');

			if(trid!=table_id)  //condition to check for current row values

			{

				if((row.find('.category').val() == cat_id) && ((row.find('.purity').val() == purity_id)) && row.find('.product').val() == 0)

				{

					$.toaster({ priority : 'danger', title : 'Warning!', message : "Cannot Select this Purity"});

					Wastagevalidate = false;

				}

			}

		});

	}

	return Wastagevalidate;

}

function ProductWasatageRestrictions(cat_id,purity_id,pro_id,trid)

{

	var Wastagevalidate = true;

	var t_len = $('#product_item_list tbody tr').length;

	if((pro_id!='' && pro_id!=undefined) && t_len>1)

	{

		$('#product_item_list > tbody  > tr').each(function(index, tr)

		{

			var row = $(this);

			var table_id = $(this).attr('id');

			if(trid!=table_id)  //condition to check for current row values

			{

				if((row.find('.category').val() == cat_id) && (row.find('.purity').val() == purity_id) && (pro_id==0))

				{

					$.toaster({ priority : 'danger', title : 'Warning!', message : "Cannot Select All Products"});

					Wastagevalidate = false;

				}

				else if((row.find('.category').val() == cat_id) && (row.find('.purity').val() == purity_id) && ((row.find('.product').val() == pro_id)) && row.find('.design').val() == 0)

				{

					$.toaster({ priority : 'danger', title : 'Warning!', message : "Product Already Exists"});

					Wastagevalidate = false;

				}

			}

		});

	}

	return Wastagevalidate;

}

function DesignWastageRestrictions(cat_id,purity_id,pro_id,des_id,trid)

{

	var Wastagevalidate = true;

	var t_len = $('#product_item_list tbody tr').length;

	console.log(t_len);

	if((des_id!='' && des_id!=undefined) && t_len>1)

	{

		$('#product_item_list > tbody  > tr').each(function(index, tr)

		{

			var row = $(this);

			var table_id = $(this).attr('id');

			if(trid!=table_id) //condition to check for current row values

			{

				if(((row.find('.category').val() == cat_id) && (row.find('.purity').val() == purity_id) && (row.find('.product').val() == pro_id) && des_id == 0))

				{

					$.toaster({ priority : 'danger', title : 'Warning!', message : "Cannot Select All Designs"});

					Wastagevalidate = false;

				}

				else if((row.find('.category').val() == cat_id) && (row.find('.purity').val() == purity_id) && (row.find('.product').val() == pro_id) &&  (row.find('.design').val() == des_id ) && row.find('.sub_design').val() == 0)

				{

					$.toaster({ priority : 'danger', title : 'Warning!', message : "Design Already Exists"});

					Wastagevalidate = false;

				}

			}

		});

	}

	return Wastagevalidate;

}

function subDesignRestriction(cat_id,purity_id,pro_id,des_id,sub_des_id,trid)

{

	var Wastagevalidate = true;

	var t_len = $('#product_item_list tbody tr').length;

	if((sub_des_id!='' && sub_des_id !=undefined) && t_len>1)

	{

		$('#product_item_list > tbody  > tr').each(function(index, tr)

		{

			var row = $(this);

			var table_id = $(this).attr('id');

			if(trid!=table_id) //condition to check for current row values

			{

				if((row.find('.category').val() == cat_id) && (row.find('.purity').val() == purity_id) && (row.find('.product').val() == pro_id) && (row.find('.design').val()==des_id) && sub_des_id==0)

				{

					$.toaster({ priority : 'danger', title : 'Warning!', message : "Cannot Select All Sub Designs"});

					Wastagevalidate = false;

				}

				else if((row.find('.category').val() == cat_id) && (row.find('.purity').val() == purity_id) && (row.find('.product').val() == pro_id) && (row.find('.design').val()==des_id) && sub_des_id==row.find('.sub_design').val())

				{

					$.toaster({ priority : 'danger', title : 'Warning!', message : "Sub Design Already Exists"});

					Wastagevalidate = false;

				}

			}

		});

	}

	return Wastagevalidate;

}

$(document).on('change','.prod_to_wt,.prod_from_wt',function()

{

    if($(this).val() != '')

    {

		var curRow=$(this).closest('tr');

		if(ctrl_page[1]=='ret_stone_rate_settings')

		{

			validateLooseProduct_wt_range(curRow);

		}

		else

		{

			validate_diamondProd_wt_range(curRow);

		}

    }

});

function validate_diamondProd_wt_range(curRow)

{

    var row_id = curRow.attr('id');

	var from_wt = parseFloat (curRow.find('.prod_from_wt').val());

	var to_wt = parseFloat(curRow.find('.prod_to_wt').val());

	var category = curRow.find('.category').val();

	var product = curRow.find('.product').val();

	var design = curRow.find('.design').val();

	var sub_design = curRow.find('.sub_design ').val();

	var stn_quality  = curRow.find('.stn_quality').val();

    var validate = true;

    if((category!='' && category!=null) && (product!='' && product!=null) && (design!='' && design!=null) && (sub_design!='' && sub_design!=null)  && curRow.find('.prod_from_wt').val() != '' && curRow.find('.prod_to_wt').val() != '' && curRow.find('.stn_quality').val() != '' && curRow.find('.stn_quality').val() != null  && from_wt != to_wt)

	{

    	$('#product_item_list > tbody  > tr').each(function(index, tr) {

			if($(this).find('.category ').val() == category && $(this).find('.product').val() == product &&  $(this).find('.design ').val() == design &&  $(this).find('.sub_design ').val() == sub_design &&  $(this).find('.stn_quality').val() == stn_quality ){

				if( ( parseFloat ($(this).find('.prod_from_wt').val())  <= from_wt &&  parseFloat ($(this).find('.prod_to_wt').val())  >= from_wt ||  parseFloat ($(this).find('.prod_from_wt').val())  <= to_wt &&  parseFloat ($(this).find('.prod_to_wt').val())  >= to_wt) && row_id != $(this).attr('id') ){

					validate = false;

					$.toaster({ priority : 'danger', title : 'Warning!', message : "Invalid From and To Cent "});

					return validate;

				}

			}

    	});

    }

	else if(from_wt == to_wt)

	{

    	validate = false;

		$.toaster({ priority : 'danger', title : 'Warning!', message : "Invalid From and To Cent "});

    }

	else

	{

        validate = false;

        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill the required fields....'});

    }

    return validate;

}

//Image Upload

function update_image_upload(curRow,id)

{

	$('#uploadArea_p_stn').empty();

	if(curRow!=undefined)

	{

		var preview = 'uploadArea_p_stn';

		var order_img=curRow.find('.order_img').val();

		if(order_img!='')

		{

			var img_details=JSON.parse(order_img);

			$.each(img_details,function(key,item){

			   if(item)

			   {

			   		var div = document.createElement("div");

					div.setAttribute('class','col-md-4');

					div.setAttribute('id',+key);

					param = {"key":key,"preview":preview,"stone_type":"order_images"};

					div.innerHTML+= "<a onclick='remove_stn_img("+JSON.stringify(param)+")'><i class='fa fa-trash'></i>Delete</a><img class='thumbnail' src='" + item.src + "'" +

					"style='width: 100px;height: 100px;'/>";

					$('#'+preview).append(div);

			   }

			   $('#lot_img_upload').css('display','');

			});

		}

		$('#cus_i_increment').val(curRow.closest('tr').attr('class'));

	}

	$('#imageModal').modal('show');

}

$("#order_images").on('change',function(){

		if(this.value!='')

		{

			validateCertifImg(this.id);

		}

	});

function validateCertifImg(type)

 {

 	if(type == 'order_images'){

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

					if(type == 'order_images')

					{

						pre_img_resource.push({'src':event.target.result,'name':fileName});

						pre_img_files.push(file);

					}

				}

				if (file)

				{

					reader.readAsDataURL(file);

				}

			}

	 	}

    }

	setTimeout(function(){

		var resource = [];

		$('#'+preview+' div').remove();

		if(type == 'order_images'){

			resource = pre_img_resource;

		}

			console.log(resource);

		$.each(resource,function(key,item){

		   if(item)

		   {

		   		var div = document.createElement("div");

				div.setAttribute('class','col-md-4');

				div.setAttribute('id',+key);

				param = {"key":key,"preview":preview,"stone_type":type};

				div.innerHTML+= "<a onclick='remove_stn_img("+JSON.stringify(param)+")'><i class='fa fa-trash'></i>Delete</a><img class='thumbnail' src='" + item.src + "'" +

				"style='width: 100px;height: 100px;'/>";

				$('#'+preview).append(div);

		   }

		   $('#lot_img_upload').css('display','');

		});

	},1000);

	pre_img_resource=[];

}

 function remove_stn_img(param)

 {

 		console.log(param);

 		$('#'+param.preview+' #'+param.key).remove();

 		if(param.stone_type == 'order_images')

 		{

			pre_img_resource.splice(param.key,1);

			console.log(pre_img_resource);

		}

 }

 $('#imageModal  #update_img').on('click', function(){

	$('#imageModal').modal('toggle');

	var curRow=$("#i_increment").val();

	$('.'+curRow).find('.order_img').val(JSON.stringify(pre_img_resource));

});

/*Karigar Approval List*/

/*Karigar Wastage Product Images*/

function update_karWast_image_upload(curRow,id)

{

	$('#Kar_wast_imageModal .modal-body').find('#wast_img_preview tbody').empty();

	$('#wast_image_pre').empty();

	$('#wastImg_active_row').val(curRow.closest('tr').attr('class'));

	if(curRow!=undefined)

	{

		var order_img=curRow.find('.order_img').val();

		if(order_img!='')

		{

			var img_details=JSON.parse(order_img);

			var trHtml='';

			$.each(img_details,function(key,item){

				if(item)

				{

					trHtml+='<tr class="'+key+'">'

						+'<td><input type="checkbox" class="is_default_wastimg" '+(item.is_default==1 ? 'checked' :'')+' >'+parseInt(key+1)+'</td>'

						+'<td><img class="thumbnail" src="'+item.src+'" style="width:100px;height:100px;"/></td>'

						+'<td><a href="#" onClick="remove_row($(this).closest(\'tr\'),'+key+')" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'

					+'</tr>';

				}

			});

			$('#wast_img_preview > tbody').html(trHtml);

		}

		else

		{

			$('#wast_pro_image').val('');

			img_resource=[];

		}

	}

	$('#Kar_wast_imageModal').modal('show');

}

$('#wast_pro_images').on('change',function()

{

	validateWastProImages();

});

$('#snap_shots').on('click',function()

{

	Webcam.snap( function(data_uri) {

		$(".image-tag").val(data_uri);

		img_resource.push({'src':data_uri,'name':(Math.floor(100000 + Math.random() * 900000))+'jpg','is_default':"0"});

		 pre_img_files.push(data_uri);

		 alert("Your Webcam Images Take Snap Shot Successfullys.");

	 } );

	 setTimeout(function(){

		var resource = [];

		resource     = img_resource;

		var row = $(this).closest('tr');

		set_wastproimage_preview(row);

	},500);

})

function validateWastProImages()

{

	$('div.overlay').css("display","block");

	//$('#wast_img_preview').html('');

	var preview = $('#wast_image_pre');

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

				img_resource.push({"src":output.prefix +output.data,'name':output.alt,'is_default':"0"});

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

		resource     = img_resource;

		set_wastproimage_preview();

	},500);

}

function set_wastproimage_preview()

{

	console.log(img_resource);

	$("div.overlay").css("display", "block");

	var trHtml='';

	$.each(img_resource,function(key,item){

		trHtml+='<tr class="'+key+'">'

			+'<td><input type="checkbox" class="is_default_wastimg" '+(item.is_default==1 ? 'checked' :'')+' >'+parseInt(key+1)+'</td>'

			+'<td><img class="thumbnail" src="'+item.src+'" style="width:100px;height:100px;"/></td>'

			+'<td><a href="#" onClick="remove_row($(this).closest(\'tr\'),'+key+')" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'

		+'</tr>';

	});

	$('#wast_img_preview > tbody').html(trHtml);

	$('#wast_pro_image').val(JSON.stringify(img_resource));

    $("div.overlay").css("display", "none");

}

$(document).on('change','.is_default_wastimg',function(e)

{

    var imgsrc=$(this).closest('tr').attr('class');

	$.each(img_resource,function(key,item){

		if(key==imgsrc)

		{

			img_resource[key].is_default=1

		}

        else

        {

        	img_resource[key].is_default=0

        }

  	});

	$('#wast_pro_image').val(JSON.stringify(img_resource));

	$('#wast_img_preview > tbody tr').each(function(idx, row){

		curRow = $(this);

		if(imgsrc==idx)

		{

			curRow.find('.is_default_wastimg').prop('checked',true);

		}else{

			curRow.find('.is_default_wastimg').prop('checked',false);

		}

	});

})

$('#Kar_wast_imageModal  #wastproimg_submit').on('click', function()

{

	$('#Kar_wast_imageModal').modal('toggle');

	var curRow=$("#wastImg_active_row").val();

	$('.'+curRow).find('.order_img').val(JSON.stringify(img_resource));

	var preview = $('.'+curRow).find('#wast_image_pre');

	$.each(img_resource,function(key,item)

	{

		if(item.is_default==1)

		{

			preview.prop('src',item.src);

		}

	});

});

$('#Kar_wast_imageModal #close_imgModal').on('click',function()

{

	$('#Kar_wast_imageModal').modal('toggle');

});

/*Karigar Wastage Product Images*/

function get_branches()

{

    $('#ed_branch_select option').remove();

    $('#branch_select option').remove();

	$.ajax({

		type: 'GET',

		url: base_url+'index.php/branch/branchname_list',

		dataType:'json',

		success:function(data)

		{

			var id_branch =  $('#id_branch').val();

			var branch =  $('#branch_select').val();

			$.each(data.branch, function (key, item)

			{

				$("#ed_branch_select").append(

				$("<option></option>")

				.attr("value", item.id_branch)

				.text(item.name )

			);

			});

			$("#ed_branch_select").select2({

				placeholder: "Select Branch",

				allowClear: true,

				width: '100%'

			});

			$("#ed_branch_select").select2("val",(id_branch!='' && id_branch>0?id_branch:''));

			$.each(data.branch, function (key, item)

			{

			$("#branch_select").append(

			$("<option></option>")

			.attr("value", item.id_branch)

			.text(item.name )

			);

			});

			$("#branch_select").select2({

			placeholder: "Select Branch",

			allowClear: true,

			width: '100%'

			});

			if($('#branch_select').length)

			{

				$("#branch_select").select2("val",(branch!='' && branch>0?branch:''));

			}

		}

	});

}

/*Karigar Stone Details*/

$('.add_stone').on('click',function(){

    if(validateStoneDetailRow())

    {

        create_new_empty_karigar_stone_detail();

    }else

    {

        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill the required fields..'});

    }

});

function validate_dmt_wt_range(curRow)

{

    var row_id = curRow.attr('id');

    var from_wt = parseFloat (curRow.find('.from_wt').val());

    var to_wt = parseFloat(curRow.find('.to_wt').val());

    var stn_name  = curRow.find('.stn_name ').val();

    var stn_quality  = curRow.find('.stn_quality').val();

    var validate = true;

    if(curRow.find('.from_wt ').val() != '' && curRow.find('.to_wt ').val() != '' && curRow.find('.stn_quality  ').val() != '' && curRow.find('.stn_quality  ').val() != null && curRow.find('.stn_type ').val() ==1 && from_wt != to_wt){

    $('#stone_item_list > tbody  > tr').each(function(index, tr) {

        if($(this).find('.stn_type ').val() ==1 &&  $(this).find('.stn_name ').val() == stn_name &&  $(this).find('.stn_quality').val() == stn_quality ){

            if( ( parseFloat ($(this).find('.from_wt').val())  <= from_wt &&  parseFloat ($(this).find('.to_wt').val())  >= from_wt ||  parseFloat ($(this).find('.from_wt').val())  <= to_wt &&  parseFloat ($(this).find('.to_wt').val())  >= to_wt) && row_id != $(this).attr('id') ){

                validate = false;

                $.toaster({ priority : 'danger', title : 'Warning!', message : "Invalid From and To Weight "});

                return validate;

            }

        }

    });

    }else if(curRow.find('.stn_type ').val() !=1){

        validate = true;

    }else if(from_wt == to_wt){

    validate = false;

    $.toaster({ priority : 'danger', title : 'Warning!', message : "Invalid From and To Weight "});

    }else{

        validate = false;

        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill the required fields....'});

    }

    return validate;

}

$(document).on('change','.to_wt,.from_wt',function()

{

    if($(this).val() != '')

    {

		var curRow=$(this).closest('tr');

		if(ctrl_page[1]=='ret_stone_rate_settings')

		{

			validate_Diamondrate_wt_range(curRow);

		}

		else

		{

			validate_dmt_wt_range(curRow);

		}

    }

});

function validateStoneDetailRow()

{

    var validate = true;

	$('#stone_item_list > tbody  > tr').each(function(index, tr) {

		validate_wt = validate_dmt_wt_range($(this));

		if($(this).find('.stn_type').val() == "" || $(this).find('.stn_name').val() == "" || $(this).find('.uom_id').val() == null || $(this).find('.stn_calc_type').val() == "" || $(this).find('.stone_rate').val() == "" || ! validate_wt )

		{

			validate = false;

			if(validate_wt){

				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill the required fields..'});

			}

			return validate;

		}

	});

	return validate;

}

$(document).on('change',".is_stn_select",function(){

    $(this).closest('tr').find('.stn_type').prop('disabled',true);

    $(this).closest('tr').find('.stn_name').prop('disabled',true);

    $(this).closest('tr').find('.uom_id').prop('disabled',true);

    $(this).closest('tr').find('.stn_calc_type').prop('disabled',true);

    $(this).closest('tr').find('.stone_rate').prop('disabled',true);

    if($(this).is(":checked"))

    {

        $(this).closest('tr').find('.is_stn_select').val(1);

        $(this).closest('tr').find('.stn_type').prop('disabled',false);

        $(this).closest('tr').find('.stn_name').prop('disabled',false);

        $(this).closest('tr').find('.uom_id').prop('disabled',false);

        $(this).closest('tr').find('.stn_calc_type').prop('disabled',false);

        $(this).closest('tr').find('.stone_rate').prop('disabled',false);

    }else{

        $(this).closest('tr').find('.is_stn_select').val(0);

    }

});

function create_new_copy_karigar_stone_detail(cuRrow)

{

	if(validateStoneDetailRow()){

    	var html = "";

    	var html = "";

		var a = $("#i_increment_stn").val();

		var i = ++a;

		var stn_type = cuRrow.find('.stn_type').val();

		var stn_name = cuRrow.find('.stn_name').val();

		var uom_id = cuRrow.find('.uom_id').val();

		var stn_calc_type = cuRrow.find('.stn_calc_type').val();

		var stn_quality = cuRrow.find('.stn_quality').val();

		$("#i_increment_stn").val(i);

			html+="<tr id='detail"+i+"' class='"+i+"'>"+

			"<td><input type='checkbox' class='is_stn_select' name='stn["+i+"][is_stn_select]' value='1' checked></td>"+

			"<td><select class='stn_type' id='stn_type"+i+"' class='form-control' name='stn["+i+"][stn_type]' style='width: 150px;'></td>"+

			"<td><select class='stn_name' class='form-control' id='stn_name"+i+"' name='stn["+i+"][stn_name]' style='width: 150px;' >  <input type ='hidden' class ='stn_id' value ="+stn_name+"></td>"+

			"<td><select class='uom_id' class='form-control' id='uom_id"+i+"' name='stn["+i+"][uom_id]' style='width: 150px;' ></td>"+

			"<td><select class='stn_calc_type' class='form-control' id='stn_calc_type"+i+"' name='stn["+i+"][stn_calc_type]' style='width: 150px;'><option value='1'>Wt</option><option value='2'>Pcs</option></select></td>"+

			"<td><select class='stn_quality' class='form-control' id='id_stn_quality"+i+"' name='stn["+i+"][quality_id]' '></select></td>"+

			"<td><input type='number' step='any' class='form-control from_wt' placeholder='From' name='stn["+i+"][from_wt]' readonly/></td>"+

			"<td><input type='number' step='any' class='form-control to_wt' placeholder='To' name='stn["+i+"][to_wt]' readonly/></td>"+

			"<td style='display: flex;flex-direction: row;' ><input type='number' step='any' class='form-control stone_rate' placeholder='Stone rate' name='stn["+i+"][stone_rate]' /> <a href='#' onClick='create_new_copy_karigar_stone_detail($(this).closest(\"tr\"));'  class='btn btn-primary add_stone' style='display:none;' ><i class='fa fa-plus'></i></a></td>"+

			'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'

            "</tr>";

			$('#stone_item_list tbody').append(html);

			$('#stn_type'+i).append(

				$("<option></option>")

				.attr("value", "")

				.text('-Choose-')

			);

			$('#uom_id'+i).append(

				$("<option></option>")

				.attr("value", "")

				.text('-Choose-')

			);

			$('#stn_calc_type'+i).append(

				$("<option></option>")

				.attr("value", "")

				.text('-Choose-')

			);

        	$.each(stone_types, function (key, item) {

                $('#stn_type'+i).append(

                $("<option></option>")

                .attr("value", item.id_stone_type)

                .text(item.stone_type)

                );

            });

			$.each(uom_details, function (key, item) {

                $('#uom_id'+i).append(

                $("<option></option>")

                .attr("value", item.uom_id)

                .text(item.code)

                );

            });

			$.each(quality_code, function (key, item) {

                $('#id_stn_quality'+i).append(

                $("<option></option>")

                .attr("value", item.quality_id)

                .text(item.code)

                );

            });

            $('#stone_item_list > tbody').find('.stn_type').select2();

            $('#stone_item_list > tbody').find('.stn_name').select2();

            $('#stone_item_list > tbody').find('.uom_id').select2();

			$('#stone_item_list > tbody').find('.stn_calc_type').select2();

            $('#stone_item_list > tbody').find('.stn_type').select2({

                placeholder: "Stone Type",

                allowClear: true

            });

            $('#stone_item_list > tbody').find('.stn_name').select2({

                placeholder: "Stone Name",

                allowClear: true

            });

            $('#stone_item_list > tbody').find('.uom_id').select2({

                placeholder: "UOM",

                allowClear: true

            });

			$('#stone_item_list > tbody').find('.stn_calc_type').select2({

                placeholder: "Calc Type",

                allowClear: true

            });

			$('#stone_item_list > tbody').find('.stn_quality').select2({

                placeholder: "Quality",

                allowClear: true

            });

			var last_row =$('#stone_item_list > tbody > tr').last();

			last_row.find('.stn_type').select2("val",(stn_type!='' && stn_type>0?stn_type:''));

			last_row.find('.stn_name').select2("val",(stn_name!='' && stn_name>0?stn_name:''));

			last_row.find('.uom_id').select2("val",(uom_id!='' && uom_id>0?uom_id:''));

			last_row.find('.stn_calc_type').select2("val",(stn_calc_type!='' && stn_calc_type>0?stn_calc_type:''));

			last_row.find('.stn_quality').select2("val",(stn_quality!='' && stn_quality>0?stn_quality:''));

		}

}

function create_new_empty_karigar_stone_detail()

{

    	var html = "";

    	var html = "";

		var a = $("#i_increment_stn").val();

		var i = ++a;

		$("#i_increment_stn").val(i);

			html+="<tr id='detail"+i+"' class='"+i+"'>"+

			"<td><input type='checkbox' class='is_stn_select' name='stn["+i+"][is_stn_select]' value='1' checked></td>"+

			"<td><select class='stn_type' id='stn_type"+i+"' class='form-control' name='stn["+i+"][stn_type]' style='width: 150px;'></td>"+

			"<td><select class='stn_name' class='form-control' id='stn_name"+i+"' name='stn["+i+"][stn_name]' style='width: 150px;' ></td>"+

			"<td><select class='uom_id' class='form-control' id='uom_id"+i+"' name='stn["+i+"][uom_id]' style='width: 150px;' ></td>"+

			"<td><select class='stn_calc_type' class='form-control' id='stn_calc_type"+i+"' name='stn["+i+"][stn_calc_type]' style='width: 150px;'><option value='1'>Wt</option><option value='2'>Pcs</option></select></td>"+

			"<td><select class='stn_quality' class='form-control' id='id_stn_quality"+i+"' name='stn["+i+"][quality_id]' disabled></select></td>"+

			"<td><input type='number' step='any' class='form-control from_wt' placeholder='From' name='stn["+i+"][from_wt]' readonly /></td>"+

			"<td><input type='number' step='any' class='form-control to_wt' placeholder='To' name='stn["+i+"][to_wt]' readonly /></td>"+

			"<td style='display: flex;flex-direction: row;' ><input type='number' step='any' class='form-control stone_rate' placeholder='Stone rate' name='stn["+i+"][stone_rate]' /> <a href='#' onClick='create_new_copy_karigar_stone_detail($(this).closest(\"tr\"))' class='btn btn-primary add_stone' style ='display:none;'><i class='fa fa-plus'></i></a></td>"+

			'<td  ><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'

            "</tr>";

			$('#stone_item_list tbody').append(html);

			$('#stn_type'+i).append(

				$("<option></option>")

				.attr("value", "")

				.text('-Choose-')

			);

			$('#uom_id'+i).append(

				$("<option></option>")

				.attr("value", "")

				.text('-Choose-')

			);

			$('#stn_calc_type'+i).append(

				$("<option></option>")

				.attr("value", "")

				.text('-Choose-')

			);

			$('#id_stn_quality'+i).append(

				$("<option></option>")

				.attr("value", "")

				.text('-Choose-')

			);

        	$.each(stone_types, function (key, item) {

                $('#stn_type'+i).append(

                $("<option></option>")

                .attr("value", item.id_stone_type)

                .text(item.stone_type)

                );

            });

			$.each(uom_details, function (key, item) {

                $('#uom_id'+i).append(

                $("<option></option>")

                .attr("value", item.uom_id)

                .text(item.code)

                );

            });

            $.each(quality_code, function (key, item) {

                $('#id_stn_quality'+i).append(

                $("<option></option>")

                .attr("value", item.quality_id)

                .text(item.code)

                );

            });

            $('#stone_item_list > tbody').find('.stn_type').select2();

            $('#stone_item_list > tbody').find('.stn_name').select2();

            $('#stone_item_list > tbody').find('.uom_id').select2();

			$('#stone_item_list > tbody').find('.stn_calc_type').select2();

            $('#stone_item_list > tbody').find('.stn_type').select2({

                placeholder: "Stone Type",

                allowClear: true

            });

            $('#stone_item_list > tbody').find('.stn_name').select2({

                placeholder: "Stone Name",

                allowClear: true

            });

            $('#stone_item_list > tbody').find('.uom_id').select2({

                placeholder: "UOM",

                allowClear: true

            });

			$('#stone_item_list > tbody').find('.stn_calc_type').select2({

                placeholder: "Calc Type",

                allowClear: true

            });

            $('#stone_item_list > tbody').find('.stn_quality').select2({

                placeholder: "Quality",

                allowClear: true

            });

}

function get_stone_details(id)

{

	my_Date = new Date();

	$.ajax({

        url: base_url+'index.php/admin_ret_catalog/karigar/stone_details/'+id+'?nocache=' + my_Date.getUTCSeconds(),

        dataType: "json",

        method: "POST",

		success: function (data)

		{

			console.log('stones',data);

			$("#stone_item_list tbody tr").empty();

        	$("#stone_item_list tbody tr").empty();

        	$("#i_increment_stn").val(0);

			var a = 0;

			$.each(data.karigar_stones, function (key, val)

			{

				var html_1 = "";

				var i = ++a;

				$("#i_increment_stn").val(i);

				html_1+="<tr id='detail"+i+"' class='"+i+"'>"+

				"<td><input type='checkbox' class='is_stn_select' name='stn["+i+"][is_stn_select]'></td>"+

				"<td><select class='stn_type' id='stn_type"+i+"' class='form-control' name='stn["+i+"][stn_type]' style='width: 150px;'></td>"+

				"<td><select class='stn_name' class='form-control' id='stn_name"+i+"' name='stn["+i+"][stn_name]' style='width: 150px;'></td>"+

				"<td><select class='uom_id' class='form-control' id='uom_id"+i+"' name='stn["+i+"][uom_id]' style='width: 150px;' ></td>"+

				"<td><select class='stn_calc_type' class='form-control' id='stn_calc_type"+i+"' name='stn["+i+"][stn_calc_type]' style='width: 150px;' ><option value='1'>Wt</option><option value='2'>Pcs</option></select></td>"+

				"<td><select class='stn_quality' class='form-control' id='id_stn_quality"+i+"' name='stn["+i+"][quality_id]' "+(val.quality_id=='' || val.quality_id==null ? 'disabled' :'')+" ></select></td>"+

				"<td><input type='number' step='any' class='form-control from_wt' placeholder='From'  value='"+val.from_wt+"' name='stn["+i+"][from_wt]' '"+(val.from_wt=='' || val.from_wt==null ? 'readonly' :'')+"' /></td>"+

				"<td><input type='number' step='any' class='form-control to_wt' placeholder='To' value='"+val.to_wt+"'  name='stn["+i+"][to_wt]' '"+(val.to_wt=='' || val.to_wt==null ? 'readonly' :'')+"' /></td>"+

				"<td style='display: flex;flex-direction: row;' ><input type='number' step='any' class='form-control stone_rate' placeholder='Stone rate' name='stn["+i+"][stone_rate]' value='"+val.rate_per_gram+"' /> <a href='#' onClick='create_new_copy_karigar_stone_detail($(this).closest(\"tr\"));'  class='btn btn-primary add_stone' style='display:none;' ><i class='fa fa-plus'></i></a></td>"+

				'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'

				"</tr>";

				$('#stone_item_list tbody').append(html_1);

				$.each(data.stone_type,function(key,item){

					$('#stn_type'+i).append(

						$("<option></option>").attr("value", item.id_stone_type).text(item.stone_type)

					);

				});

				$('#stn_type'+i).val(val.stone_type);

				$.each(data.uom,function(key,item){

					$('#uom_id'+i).append(

						$("<option></option>").attr("value", item.uom_id).text(item.code)

					);

				});

				$('#uom_id'+i).val(val.uom_id);

				$.each(data.stones,function(key,item){

                    $('#stn_name'+i).append(

				    	$("<option></option>").attr("value", item.stone_id).text(item.stone_name)

				    );

                });

				$.each(data.quality_code, function (key, item) {

					$('#id_stn_quality'+i).append(

					$("<option></option>")

					.attr("value", item.quality_id)

					.text(item.code)

					);

				});

				$('#stone_item_list > tbody').find('.stn_quality').select2({

					placeholder: "Quality",

					allowClear: true

				});

				$('#id_stn_quality'+i).val(val.quality_id);

				$('#stn_name'+i).val(val.stone_id);

				$('#stone_item_list > tbody').find('.stn_type').select2();

				$('#stone_item_list > tbody').find('.stn_name').select2();

				$('#stone_item_list > tbody').find('.uom_id').select2();

				$('#stone_item_list > tbody').find('.stn_calc_type').select2();

				$('#stone_item_list > tbody').find('.stn_type').select2({

					placeholder: "Stone Type",

					allowClear: true

				});

				$('#stone_item_list > tbody').find('.stn_name').select2({

					placeholder: "Stone Name",

					allowClear: true

				});

				$('#stone_item_list > tbody').find('.uom_id').select2({

					placeholder: "UOM",

					allowClear: true

				});

				$('#stone_item_list > tbody').find('.stn_calc_type').select2({

					placeholder: "Calc type",

					allowClear: true

				});

			});

		}

	});

}

function get_ActiveStoneType()

{

	$.ajax({

		type:'GET',

		url: base_url+'index.php/admin_ret_tagging/getStoneTypes',

		dataType:'json',

		success:function(data)

		{

			stone_types=data;

		}

	});

}

$(document).on('change','.stn_type',function()

    {

        var curRow=$(this).closest('tr');

        var id_stn_type=curRow.find('.stn_type').val();

        getActiveStones(curRow,id_stn_type);

        if($(this).val() == 1)

		{

			if(ctrl_page[1]=='ret_stone_rate_settings')

			{

				curRow.find('.from_wt').attr('readonly',false);

				curRow.find('.to_wt').attr('readonly',false);

				curRow.find('.stn_quality ').select2("enable", true);

			}

			else

			{

				curRow.find('.from_wt').attr('readonly',false);

				curRow.find('.to_wt').attr('readonly',false);

				curRow.find('.stn_quality ').select2("enable", true);

				curRow.find('.add_stone').show();

				curRow.find('.stn_quality').select2("enable", true);

			}

        }else{

			if(ctrl_page[1]=='ret_stone_rate_settings')

			{

				curRow.find('.from_wt').val('');

				curRow.find('.to_wt').val('');

				curRow.find('.from_wt').attr('readonly',true);

				curRow.find('.to_wt').attr('readonly',true);

				curRow.find('.stn_quality ').select2("enable", true);

			}

			else

			{

				curRow.find('.from_wt').val('');

				curRow.find('.to_wt').val('');

				curRow.find('.stn_quality').select2('val','');

				curRow.find('.stn_quality ').select2("enable", false);

				curRow.find('.from_wt').attr('readonly',true);

				curRow.find('.to_wt').attr('readonly',true);

				curRow.find('.add_stone').hide();

				curRow.find('.stn_quality').select2("enable", false);

			}

        }

    });

function getActiveStones(curRow,id_stn_type)

{

	curRow.find(".stn_name option").remove();

	$.ajax({

		type:'POST',

		data: {'stone_type' : id_stn_type},

		url: base_url+'index.php/admin_ret_catalog/stone/ActiveStones',

		dataType:'json',

		success:function(data)

		{

			curRow.find(".stn_name option").remove();

			$('.stn_name').append(

                $("<option></option>")

                .attr("value", "")

                .text('-Choose-')

            );

			$.each(data, function (key, item) {

                curRow.find(".stn_name").append(

                    $("<option></option>")

                    .attr("value", item.stone_id)

					.attr("data-uom_id", item.uom_id)

                    .text(item.stone_name)

                );

            });

			$(".stn_name").select2({

                placeholder: "Stone name",

                allowClear: true

            });

			var stn_id = curRow.find('.stn_id').val();

			if( stn_id != '' && stn_id != undefined){

				curRow.find(".stn_name").select2("val",stn_id);

			}

		}

	});

}

/*Karigar Stone Details*/

/*Karigar KYC registration*/

function get_ActiveKYC()

{

	$.ajax({

		type:'GET',

		url: base_url+'index.php/admin_ret_catalog/get_ActiveKYC',

		dataType:'json',

		success:function(data)

		{

			kyc_details=data;

		}

	});

}

$('.add_kyc').on('click',function()

{

	if(validateKYCDetailRow())

    {

    	create_new_empty_karigar_kyc_detail();

	}

	else

	{

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill the required fields..'});

	}

});

function validateKYCDetailRow()

{

    var validate = true;

	$('#kyc_list > tbody  > tr').each(function(index, tr) {

		if($(this).find('.proof_name').val() == "")

		{

			validate = false;

		}

		else if($(this).find('.reg_exp').val()!='' && $(this).find('.id_reg_exp').val()=='')

		{

			validate = false;

		}

	});

	return validate;

}

function create_new_empty_karigar_kyc_detail()

{

	var html = "";

	var a = $("#i_increment").val();

	var i = ++a;

	$("#i_increment").val(i);

	html+="<tr id='detail"+i+"' class='"+i+"'>"+

		"<td><select class='proof_name'  class='form-control' name='kyc_det["+i+"][proof_name]' id='proof_name"+i+"' required='true'/></td>"+

		"<td><input type='text' class='form-control id_reg_exp' placeholder='ID' name='kyc_det["+i+"][id_reg_exp]'value='' style='text-transform:uppercase' required='true'/><input type='hidden' name='kyc_det["+i+"][reg_exp]' class='reg_exp' id='reg_exp"+i+"'/></td></td>"+

		'<td><a href="#" onClick="update_fr_image_upload($(this).closest(\'tr\'));" class="btn btn-default btn-sm fr_img"><i class="fa fa-plus fr_img" ></i>Front</a><a href="#" onClick="update_bk_image_upload($(this).closest(\'tr\'));" class="btn btn-default btn-sm bk_img"><i class="fa fa-plus bk_img"></i>BACK</a><input type="hidden" class="order_img_fr" name="kyc_det['+i+'][order_img_fr]""><input type="hidden" class="order_img_bk" name="kyc_det['+i+'][order_img_bk]""></td><input type="hidden" name="kyc_det['+i+'][img_type]" class="img_type" id="img_type'+i+'"/>'+

		'<td><a href="#"  onClick="update_documents($(this).closest(\'tr\'));" class="btn btn-default btn-sm"><i class="fa fa-plus"></i></a><input type="hidden" class="order_document"  name="kyc_det['+i+'][order_document]"><input type="hidden" class="document_file"  name="kyc_det['+i+'][document_file]"><input type="hidden" name="kyc_det['+i+'][doc_type]" class="doc_type" id="doc_type'+i+'"/></td>'+

		'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'

	"</tr>";

		$('#kyc_list tbody').append(html);

		$('#proof_name'+i).append(

			$("<option></option>")

			.attr("value", "")

			.text('-Choose-')

		);

		$.each(kyc_details, function (key, item) {

			$('#proof_name'+i).append(

			$("<option></option>")

			.attr("value", item.id_kyc_master)

			.text(item.kyc_name)

			);

		});

		$('#kyc_list > tbody').find('.proof_name').select2();

		$('#kyc_list > tbody').find('.proof_name').select2({

			placeholder: "Select Proof Name",

			allowClear: true

		});

}

function get_kyc_details(id)

{

	my_Date = new Date();

	$.ajax({

        url: base_url+'index.php/admin_ret_catalog/karigar/kyc_details/'+id+'?nocache=' + my_Date.getUTCSeconds(),

        dataType: "json",

        method: "POST",

		success: function (data)

		{

			console.log(data);

			console.log('kyc',data.karigar_kyc_det);

			$("#kyc_list tbody tr").empty();

        	$("#kyc_list tbody tr").empty();

        	$("#i_increment").val(0);

			var a = 0;

			$.each(data.karigar_kyc_det, function (key, val)

			{

				var fr_img_resource = [];

				var bk_img_resource = [];

				var doc_resource = [];

				if(val['fr_img_src']!='')

				{

					fr_img_resource.push({'src':val['fr_img_src'],'name':val['front_images']});

				}

				else

				{

					fr_img_resource = [];

				}

				if(val['bk_img_src']!='')

				{

					bk_img_resource.push({'src':val['bk_img_src'],'name':val['back_images']});

				}

				else

				{

					bk_img_resource = [];

				}

				if(val['document']!='')

				{

					doc_resource.push({'src':val['doc_src'],'name':val['document']});

				}

				else

				{

					doc_resource = [];

				}

				var html_1 = "";

				var i = ++a;

				$("#i_increment").val(i);

				html_1+="<tr id='detail"+i+"' class='"+i+"'>"+

				"<td><select class='proof_name'  class='form-control' name='kyc_det["+i+"][proof_name]' id='proof_name"+i+"' value='"+val.id_kyc+"' required='true'/><input type='hidden' class='id_karigar_kyc' value='"+val.id_karigar_kyc+"'></td>"+

				"<td><input type='text' class='form-control id_reg_exp' placeholder='ID' name='kyc_det["+i+"][id_reg_exp]' value='"+val.kyc_number+"' required='true' style='"+(val.is_id_req==1 ? "display:block;text-transform:uppercase;" : "display:none;")+"'/><input type='hidden' name='kyc_det["+i+"][reg_exp]' class='reg_exp' id='reg_exp"+i+"' value='"+val.regular_expression+"'/></td></td>"+

				"<td><a href='#' onClick='update_fr_image_upload($(this).closest(\"tr\"));' class='btn btn-default btn-sm fr_img'  name='kyc_det["+i+"][fr_img]'><i class='fa fa-plus fr_img'></i>Front</a><a href='#' onClick='update_bk_image_upload($(this).closest(\"tr\"));' class='btn btn-default btn-sm bk_img' name='kyc_det["+i+"][bk_img]' style='"+(val.image==1 ? "display:none;":"display:block;")+"'><i class='fa fa-plus'></i>BACK</a><input type='hidden' class='order_img_fr' name='kyc_det["+i+"][order_img_fr]' value='"+(JSON.stringify(fr_img_resource))+"'><input type='hidden' class='order_img_bk' name='kyc_det["+i+"][order_img_bk]' value='"+(JSON.stringify(bk_img_resource))+"'></td><input type='hidden' name='kyc_det["+i+"][img_type]' class='img_type' id='img_type"+i+"' value='"+val.image_type+"'/>"+

				"<td><a href='#' onClick='update_documents($(this).closest(\"tr\"));' class='btn btn-default btn-sm'><i class='fa fa-plus'></i></a><input type='hidden' class='order_document' name='kyc_det["+i+"][order_document]' value='"+(JSON.stringify(doc_resource))+"'><input type='hidden' class='document_file'  name='kyc_det["+i+"][document_file]' value='"+val.document+"'><input type='hidden' name='kyc_det["+i+"][doc_type]' class='doc_type' id='doc_type"+i+"'  value='"+val.doc_type+"'/></td>"+

				"<td><a href='#' onClick='$(this).closest(\"tr\").remove();' class='btn btn-danger btn-del'><i class='fa fa-trash'></i></a></td>"+

				"</tr>";

				$('#kyc_list tbody').append(html_1);

				$.each(data.kyc,function(key,item){

					$('#proof_name'+i).append(

						$("<option></option>").attr("value", item.id_kyc_master).text(item.kyc_name)

					);

				});

				$('#proof_name'+i).val(val.id_kyc);

				$('#kyc_list > tbody').find('.proof_name').select2();

				$('#kyc_list > tbody').find('.proof_name').select2({

					placeholder: "Select Kyc",

					allowClear: true

				});

			})

		}

	});

}

$(document).on('change','.proof_name',function()

{

	var curRow=$(this).closest('tr');

	var id_kyc=curRow.find('.proof_name').val();

	console.log(id_kyc);

	getKycDetails(curRow,id_kyc);

    curRow.find('.reg_exp').val('');

	curRow.find('.id_reg_exp').val('');

});

function getKycDetails(curRow,id_kyc)

{

	var id_kyc = curRow.find('.proof_name').val();

	$.ajax({

		type:'POST',

		url: base_url+'index.php/admin_ret_catalog/getKycDetails',

		dataType:'json',

		data:{'id_kyc':id_kyc},

		success:function(data)

		{

			console.log(data);

			if(data[0].is_id_req==1)

			{

				curRow.find('.id_reg_exp').css('display','block');

				curRow.find('.reg_exp').val(data[0].regular_expression);

			}

			else

			{

				curRow.find('.id_reg_exp').css('display','none');

			}

			if(data[0].image==1)

			{

				curRow.find('.fr_img').css('display','block');

				curRow.find('.bk_img').css('display','none');

			}

			else

			{

				curRow.find('.fr_img').css('display','block');

				curRow.find('.bk_img').css('display','block');

			}

			curRow.find('.doc_type').val(data[0].doc_type);

			curRow.find('.img_type').val(data[0].image_type);

		}

	})

}

$(document).on('change','.id_reg_exp',function()

{

	var row = $(this).closest('tr');

	if(row.find('.proof_name').val()==1)

	{

		pan_validate(this.value,row);


	} else if (row.find('.proof_name').val()==5){

		GST_validate(this.value,row)

	}

	else

	{

		aadhar_validate(this.value,row);

	}

})

function pan_validate(reg_exp,curRow)

{



    var regexp = curRow.find('.id_reg_exp').val();

    var regex = new RegExp(/^[a-zA-Z]{5}\d{4}[a-zA-Z]{1}$/);

	var id_karigar = $('#id_karigar').val();

    if(!regex.test(regexp))

	{

		curRow.find('.id_reg_exp').val('');

		alert("Not a valid PAN No.");

		curRow.find('.id_reg_exp').focus();

	}

	else

	{

		curRow.find('.id_reg_exp').val(regexp);

		checkPANAvail(regexp, id_karigar).then(function(response) {
			if (response.status === false) {
				curRow.find('.id_reg_exp').val('');
				$.toaster({ 
					priority: 'danger', 
					title: 'Warning!', 
					message: '</br>' + response.message, 
					settings: { 'timeout': 5000 } 
				});
			}
		}).catch(function(error) {
			console.error("AJAX Error:", error);
		});

	}



}



function aadhar_validate(reg_exp,curRow)

{

        var value = reg_exp;

        aadhar_no = value.replace(/\D/g, "").split(/(?:([\d]{4}))/g).filter(s => s.length > 0).join(" ");

        curRow.find('.id_reg_exp').val(aadhar_no);

		var id_karigar = $('#id_karigar').val();

        var regexp = curRow.find('.reg_exp').val();

        var regex = new RegExp(regexp);

        if(!regex.test(aadhar_no))

    	{

    		curRow.find('.id_reg_exp').val('');

    		alert("Not a valid AADHAR No.");

    		curRow.find('.id_reg_exp').focus();

    	}

    	else

    	{

    		curRow.find('.id_reg_exp').val(reg_exp);

			checkAADHARAvail(reg_exp, id_karigar).then(function(response) {
				if (response.status === false) {
					curRow.find('.id_reg_exp').val('');
					$.toaster({ 
						priority: 'danger', 
						title: 'Warning!', 
						message: '</br>' + response.message, 
						settings: { 'timeout': 5000 } 
					});
				}
			}).catch(function(error) {
				console.error("AJAX Error:", error);
			});

    	}

}

function update_fr_image_upload(curRow,id)

{

	pre_img_resource=[];

	$('#image_type').val(curRow.find('.img_type').val());

	$('#frimg_active_row').val(curRow.closest('tr').attr('class'));

	$('#uploadArea_front').empty();

	if(curRow!=undefined)

	{

		var preview = 'uploadArea_front';

		var order_img=curRow.find('.order_img_fr').val();

		if(order_img!='')

		{

			var img_details=JSON.parse(order_img);

			$.each(img_details,function(key,item){

			   if(item)

			   {

					pre_img_resource.push({'src':item.src,'name':item.name});

			   		var div = document.createElement("div");

					div.setAttribute('class','col-md-4');

					div.setAttribute('id',+key);

					param = {"key":key,"preview":preview,"stone_type":"order_images_front"};

					div.innerHTML+= "<div class='form-group'><div class='image-input-wrapper'><img class='thumbnail' src='" + item.src + "'" +

					"style='width: 200px;height: 200px;'/><a onclick='remove_stn_img("+JSON.stringify(param)+")' class='btn btn-danger btn-del'><i class='fa fa-trash'></i>Delete</a>&nbsp;&nbsp;<a href="+item.src+" download="+item.name+" class='btn btn-success '><i class='fa fa-download'></i>Download</a></div></div>";

					$('#'+preview).append(div);

			   }

			   $('#lot_img_upload').css('display','');

			});

		}

		$('#cus_i_increment').val(curRow.closest('tr').attr('class'));

	}

	$('#front_imageModal').modal('show');

}

$("#order_images_front").on('change',function(){

	if(this.value!='')

	{

		validateCertifImgFront(this.id);

	}

});

function validateCertifImgFront(type)

{

	var img_type = $('#image_type').val();

 	console.log('img_type',img_type);

	console.log('type',type);

 	if(type =='order_images_front')

	{

		console.log('uploadArea_front');

		preview = 'uploadArea_front';

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

			if(img_type==1) // Only JPG IMAGES

			{

				if(ext != "jpg")

				{

					alert("Upload JPG Images only");

					files[i] = "";

				}

				else

				{

					var reader = new FileReader();

					reader.onload = function (event) {

						if(type =='order_images_front')

						{

							pre_img_resource.push({'src':event.target.result,'name':fileName});

							pre_img_files.push(file);

						}

					}

					if (file)

					{

						reader.readAsDataURL(file);

					}

				}

			}

			else if(img_type==2) // Only JPEG IMAGES

			{

				if(ext != "jpeg")

				{

					alert("Upload JPEG Images only");

					files[i] = "";

				}

				else

				{

					var reader = new FileReader();

					reader.onload = function (event) {

						if(type =='order_images_front')

						{

							pre_img_resource.push({'src':event.target.result,'name':fileName});

							pre_img_files.push(file);

						}

					}

					if (file)

					{

						reader.readAsDataURL(file);

					}

				}

			}

			else if(img_type==3) // Only png IMAGES

			{

				if(ext != "png")

				{

					alert("Upload PNG Images only");

					files[i] = "";

				}

				else

				{

					var reader = new FileReader();

					reader.onload = function (event) {

						if(type =='order_images_front')

						{

							pre_img_resource.push({'src':event.target.result,'name':fileName});

							pre_img_files.push(file);

						}

					}

					if (file)

					{

						reader.readAsDataURL(file);

					}

				}

			}

			else   // JPG , JPEG , PNG Files

			{

				if(ext != "jpg" && ext != "png" && ext != "jpeg")

				{

					alert("Upload JPG or PNG Images only");

					files[i] = "";

				}

				else

				{

					var reader = new FileReader();

					reader.onload = function (event) {

						if(type =='order_images_front')

						{

							pre_img_resource.push({'src':event.target.result,'name':fileName});

							pre_img_files.push(file);

						}

					}

					if (file)

					{

						reader.readAsDataURL(file);

					}

				}

			}

	 	}

    }

	setTimeout(function(){

		var resource = [];

		$('#'+preview+' div').remove();

		if(type =='order_images_front')

		{

			resource = pre_img_resource;

		}

			console.log(resource);

		$.each(resource,function(key,item){

		   if(item)

		   {

		   		var div = document.createElement("div");

				div.setAttribute('class','col-md-4');

				div.setAttribute('id',+key);

				param = {"key":key,"preview":preview,"stone_type":type};

				div.innerHTML+= "<div class='form-group'><div class='image-input-wrapper'><img class='thumbnail' src='" + item.src + "'" +

				"style='width: 200px;height: 200px;'/><a onclick='remove_stn_img("+JSON.stringify(param)+")' class='btn btn-danger btn-del'><i class='fa fa-trash'></i>Delete</a>&nbsp;&nbsp;<a href="+item.src+" download="+item.name+" class='btn btn-success '><i class='fa fa-download'></i>Download</a></div></div>";

				$('#'+preview).append(div);

		   }

		   $('#lot_img_upload').css('display','');

		});

	},1000);

	pre_img_resource=[];

}

$('#front_imageModal  #update_img_front').on('click', function()

{

	$('#front_imageModal').modal('toggle');

	var curRow=$("#frimg_active_row").val();

	console.log('curRow',curRow);

	$('.'+curRow).find('.order_img_fr').val(JSON.stringify(pre_img_resource));

	console.log(pre_img_resource);

});

function update_bk_image_upload(curRow,id)

{

	pre_img_resource=[];

    $('#bkimg_active_row').val(curRow.closest('tr').attr('class'));

	$('#uploadArea_back').empty();

	if(curRow!=undefined)

	{

		var preview = 'uploadArea_back';

		var order_img=curRow.find('.order_img_bk').val();

		if(order_img!='')

		{

			var img_details=JSON.parse(order_img);

			$.each(img_details,function(key,item){

			   if(item)

			   {

			   		var div = document.createElement("div");

					div.setAttribute('class','col-md-4');

					div.setAttribute('id',+key);

					param = {"key":key,"preview":preview,"stone_type":"order_images_bk"};

					div.innerHTML+= "<div class='form-group'><div class='image-input-wrapper'><img class='thumbnail' src='" + item.src + "'" +

					"style='width: 200px;height: 200px;'/><a onclick='remove_stn_img("+JSON.stringify(param)+")' class='btn btn-danger btn-del'><i class='fa fa-trash'></i>Delete</a>&nbsp;&nbsp;<a href='"+item.src+"' download="+item.name+" class='btn btn-success '><i class='fa fa-download'></i>Download</a></div></div>";

					$('#'+preview).append(div);

			   }

			   $('#lot_img_upload').css('display','');

			});

		}

		$('#back_imageModal').modal('show');

		$('#cus_i_increment').val(curRow.closest('tr').attr('class'));

	}

}

$("#order_images_back").on('change',function(){

	if(this.value!='')

	{

		validateCertifImgBack(this.id);

	}

});

function validateCertifImgBack(type)

{

	var img_type = $('#image_type').val();

 	console.log('img_type',img_type);

	console.log('type',type);

 	if(type =='order_images_back')

	{

		console.log('uploadArea_back');

		preview = 'uploadArea_back';

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

			if(img_type==1) // Only JPG IMAGES

			{

				if(ext != "jpg")

				{

					alert("Upload JPG Images only");

					files[i] = "";

				}

				else

				{

					var reader = new FileReader();

					reader.onload = function (event) {

						if(type =='order_images_back')

						{

							pre_img_resource.push({'src':event.target.result,'name':fileName});

							pre_img_files.push(file);

						}

					}

					if (file)

					{

						reader.readAsDataURL(file);

					}

				}

			}

			else if(img_type==2)  // Only JPEG IMAGES

			{

				if(ext != "jpeg")

				{

					alert("Upload JPEG Images only");

					files[i] = "";

				}

				else

				{

					var reader = new FileReader();

					reader.onload = function (event) {

						if(type =='order_images_back')

						{

							pre_img_resource.push({'src':event.target.result,'name':fileName});

							pre_img_files.push(file);

						}

					}

					if (file)

					{

						reader.readAsDataURL(file);

					}

				}

			}

			else if(img_type==3)  // Only PNG IMAGES

			{

				if(ext != "png")

				{

					alert("Upload PNG Images only");

					files[i] = "";

				}

				else

				{

					var reader = new FileReader();

					reader.onload = function (event) {

						if(type =='order_images_back')

						{

							pre_img_resource.push({'src':event.target.result,'name':fileName});

							pre_img_files.push(file);

						}

					}

					if (file)

					{

						reader.readAsDataURL(file);

					}

				}

			}

			else // JPG , JPEG , PNG

			{

				if(ext != "jpg" && ext != "png" && ext != "jpeg")

				{

					alert("Upload JPG or PNG Images only");

					files[i] = "";

				}

				else

				{

					var reader = new FileReader();

					reader.onload = function (event) {

						if(type =='order_images_back')

						{

							pre_img_resource.push({'src':event.target.result,'name':fileName});

							pre_img_files.push(file);

						}

					}

					if (file)

					{

						reader.readAsDataURL(file);

					}

				}

			}

	 	}

    }

	setTimeout(function(){

		var resource = [];

		$('#'+preview+' div').remove();

		if(type =='order_images_back'){

			resource = pre_img_resource;

		}

			console.log(resource);

		$.each(resource,function(key,item){

		   if(item)

		   {

		   		var div = document.createElement("div");

				div.setAttribute('class','col-md-4');

				div.setAttribute('id',+key);

				param = {"key":key,"preview":preview,"stone_type":type};

				div.innerHTML+= "<div class='form-group'><div class='image-input-wrapper'><img class='thumbnail' src='" + item.src + "'" +

				"style='width: 200px;height: 200px;'/><a onclick='remove_stn_img("+JSON.stringify(param)+")' class='btn btn-danger btn-del'><i class='fa fa-trash'></i>Delete</a>&nbsp;&nbsp;<a href='"+item.src+"' download="+item.name+" class='btn btn-success '><i class='fa fa-download'></i>Download</a></div></div>";

				$('#'+preview).append(div);

		   }

		   $('#lot_img_upload').css('display','');

		});

	},1000);

	pre_img_resource=[];

}

$('#back_imageModal  #update_img_back').on('click', function(){

	$('#back_imageModal').modal('toggle');

	var curRow=$("#bkimg_active_row").val();

	$('.'+curRow).find('.order_img_bk').val(JSON.stringify(pre_img_resource));

});

$(document).on('click', "#karigar_wastage_list a.wastages_images", function(e) {

	   e.preventDefault();

	   id=$(this).data('id');

	   $("#edit-id").val(id);

	   view_wastage_imgs(id);

});

function view_wastage_imgs(id_kar_wast)

{

	$('#order_images').empty();

     update_wast_img_id1 = id_kar_wast;

      data = [];

     var wastage_image1 =  base_url+'assets/img/karigar/product_wastage/'+id_kar_wast;

     $('#imageModal_wastage').modal('show');

     $(".overlay").css('display',"none");

     $.ajax({

        data: ( {'id_karigar_wast':id_kar_wast}),

              url:base_url+ "index.php/admin_ret_catalog/get_kar_img_by_id?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),

              dataType:"JSON",

              type:"POST",

              success:function(data){

                  retrive_img = data;

                  console.log(retrive_img);

                  console.log(data.length);

                  for (i = 0; i < data.length; i++)

                  {

                    img_src = data[i].image_name;

                    var preview = $('#order_images');

                    var img = wastage_image1 + '/' + img_src;

					console.log(img);

                    if (img_src) {

					div = document.createElement("div");

                    div.setAttribute('class', 'col-md-3 images');

                    div.setAttribute('id', 'order_img_edit_' + [i]);

                    $('.images').css('margin-right','25px');

                    key = [i];

                    param = img_src;

                    console.log(param);

                    div.innerHTML += "<div class='form-group'><div class='image-input image-input-outline' id='kt_image_'><div class='image-input-wrapper'><img class='thumbnail' src='" + img + "'" + "style='width: 300px;height: 250px;'/><a href="+img+" download="+img_src+"><i class='fa fa-download btn btn-success'>Download</i></div></div></a>";

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

function update_documents(curRow,id)

{

	$('#docModal .modal-body').find('#kyc_document_pre tbody').empty();

	$('#doc_active_row').val(curRow.closest('tr').attr('class'));

	$('#document_type').val(curRow.find('.doc_type').val());

	if(curRow!=undefined)

	{

		var order_doc=curRow.find('.order_document').val();

		$('#kyc_doc_file').val(order_doc);

		if(order_doc!='')

		{

			var doc_details=JSON.parse(order_doc);

			var trHtml ='';

			$.each(doc_details,function(key,item){

			   if(item)

			   {

				trHtml+='<tr class="'+key+'">'

				+'<td>'+item.name+'</td>'

				+'<td><a href="#" onClick="remove_docrow($(this).closest(\'tr\'),'+key+')" class="btn btn-danger btn-del"><i class="fa fa-trash"></i>Delete</a>&nbsp;&nbsp;<a href="'+item.src+'" id="download_pdf" download="'+item.name+'" class="btn btn-success "><i class="fa fa-download"></i>Download</a></td>'

				'</tr>';

			   }

			   $('#lot_img_upload').css('display','');

			});

			$('#kyc_document_pre > tbody').html(trHtml);

		}

		else

		{

			$('#kyc_doc_file').val('');

			document_resource = [];

		}

	}

	$('#docModal').modal('show');

}

$("#doc_file").on('change',function(){

	if(this.value!='')

	{

		validateDocument(this.id);

	}

});

function validateDocument(type)

{

	var doc_type = $('#document_type').val();

	if(type =='doc_file')

	{

		preview = 'uploadArea_doc';

	}

	var files = event.target.files;

	for (var i = 0; i < files.length; i++)

	{

		var file = files[i];

		var fileName =file.name;

		var ext = fileName.substring(fileName.lastIndexOf('.') + 1);

		ext = ext.toLowerCase();

		if(doc_type==1 || doc_type=='')

		{

			if(ext != "pdf")

			{

				alert("Upload PDF documents only");

				files[i] = "";

			}

			else

			{

				var reader = new FileReader();

				reader.onload = function (event) {

					if(type =='doc_file')

					{

						document_resource.push({'src':event.target.result,'name':fileName});

						document_files.push(file);

					}

				}

				if (file)

				{

					reader.readAsDataURL(file);

				}

			}

		}

		else if(doc_type==2)

		{

			if(ext != "docx")

			{

				alert("Upload Word documents only");

				files[i] = "";

			}

			else

			{

				var reader = new FileReader();

				reader.onload = function (event) {

					if(type =='doc_file')

					{

						document_resource.push({'src':event.target.result,'name':fileName});

						document_files.push(file);

					}

				}

				if (file)

				{

					reader.readAsDataURL(file);

				}

			}

		}

	}

	setTimeout(function(){

		var resource = [];

		$('#'+preview+' div').remove();

		if(type =='doc_file')

		{

			resource = document_resource;

		}

		set_kyc_document_preview();

	},1000);

}

function set_kyc_document_preview()

{

	$("div.overlay").css("display", "block");

	var trHtml='';

	$.each(document_resource,function(key,item)

	{

		console.log(item);

		trHtml+='<tr class="'+key+'">'

			+'<td>'+item.name+'</td>'

			+'<td><a href="#" onClick="remove_docrow($(this).closest(\'tr\'),'+key+')" class="btn btn-danger btn-del"><i class="fa fa-trash"></i>Delete</a>&nbsp;&nbsp;<a href="'+item.src+'" id="download_pdf" download="'+item.name+'" class="btn btn-success "><i class="fa fa-download"></i>Download</a></td>'

		+'</tr>';

	});

	$('#kyc_document_pre > tbody').html(trHtml);

	$('#kyc_doc_file').val(JSON.stringify(document_resource));

    $("div.overlay").css("display", "none");

}

$(document).on('click','#download_pdf', function(e)

{

	var row = $(this);

	row.href = URL.createObjectURL(

	  new Blob([document.documentElement.outerHTML] , {type:'application/pdf'})

	);

})

function remove_docrow(curRow,curRowkey)

{

	$("div.overlay").css("display", "block");

	curRow.remove();

	$.each(document_resource,function(key,item){

		if(key==curRowkey)

		{

			document_resource.splice(curRowkey,1);

		}

	});

	set_kyc_document_preview();

}

$('#docModal  #update_doc').on('click', function()

{

	$('#docModal').modal('toggle');

	var curRow=$("#doc_active_row").val();

	$('.'+curRow).find('.order_document').val(JSON.stringify(document_resource));

	do_upload();

});

function do_upload()

{

	var doc_file = $("#doc_file")[0].files[0];

	console.log('doc_file',doc_file);

	var formData = new FormData();

	formData.append('doc_file', doc_file);

	$.ajax({

		data:formData,

		url: base_url+'index.php/admin_ret_catalog/do_upload',

		type: 'POST',

		dataType:"JSON",

		processData: false,

        contentType: false,

		success:function(data)

		{

			if(data.status==true)

			{

				var curRow=$("#doc_active_row").val();

				$('.'+curRow).find('.document_file').val(data.response.file_name);

				$('#docModal').modal('toggle');

			}

			else

			{

				$.toaster({ priority : 'danger', title : 'Warning!', message : "This FileType Not Allowed"});

			}

		}

	});

}

/*function update_documents(curRow,id)

{

	$('#docModal').modal('show');

}

$('#demo').FancyFileUpload({

	params : {

		action : 'fileuploader'

	  },

	  edit : false,

	  maxfilesize : 1000000,

	  added : function(e, data) {

		// It is okay to simulate clicking the start upload button.

		//this.find('.ff_fileupload_actions button.ff_fileupload_start_upload').click();

		console.log(e);

		console.log(data);

	},

});

$('#docModal  #update_doc').on('click', function()

{

	console.log('document save');

	$('#docModal').modal('toggle');

	var curRow=$("#i_increment").val();

	$('.'+curRow).find('.order_document').val(JSON.stringify(pre_img_resource));

});*/

/*Karigar KYC registration*/

function set_quality_table()

{

    my_Date = new Date();

    $.ajax({

		     url: base_url+"index.php/admin_ret_catalog/diamond?nocache=" + my_Date.getUTCSeconds(),

             dataType:"JSON",

             type:"POST",

             success:function(data){

				 console.log(data);

                 var quality    = data.quality;

                 var access     = data.access;

                 $('#total_quality').text(quality.length);

				 if(access.add == '0')

				{

					 $('#add_quality').attr('disabled','disabled');

				}

             var oTable = $('#quality_list').DataTable();

                 oTable.clear().draw();

                         if (quality!= null && quality.length > 0)

                            {

                            oTable = $('#quality_list').dataTable({

                                "bDestroy": true,

                                "bInfo": true,

                                "bFilter": true,

                                "order": [[ 0, "desc" ]],

                                "scrollX":'100%',

                                "bSort": true,

                                "order": [[ 0, "desc" ]],

                                "dom": 'lBfrtip',

                                "buttons" : ['excel','print'],

                                "tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },

                                "aaData": quality,

                                "aoColumns": [  { "mDataProp": "quality_id" },

                                                { "mDataProp": "code" },

												{ "mDataProp": "clarity" },

												{ "mDataProp": "color" },

												{ "mDataProp": "cut" },

												{ "mDataProp": "name" },

                                                { "mDataProp": function ( row, type, val, meta ){

                                                status_url = (access.edit=='1')?base_url+"index.php/admin_ret_catalog/diamond/update_status/"+row.quality_id+"/"+(row.status==1?0:1):'#';

                                                return "<a href='"+status_url+"'><i class='fa "+(row.status==1?'fa-check':'fa-remove')+"'style='color:"+(row.status==1?'green':'red')+"'></i></a>"

                                                    }

                                                },

												{ "mDataProp": function ( row, type, val, meta ) {

													id= row.quality_id;

													edit_url=(base_url+'index.php/admin_ret_catalog/diamond/edit/'+id);

													delete_confirm=(access.delete=='1' ?'#confirm-delete':'');

													delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/diamond/delete/'+id : '#' );

													// action_content='<a href='+edit_url+' class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'><i class="fa fa-edit" ></i></a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

													var action_content=''

													if(access.edit == '1'){

																				

														action_content+=  '<a href='+edit_url+' class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'><i class="fa fa-edit" ></i></a>' ;

														

														}

														if(access.delete == '1'){

														

														action_content+='<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

														}

													return action_content;

													}

                                                }]

                                });

                            }

                      },

                      error:function(error)

                      {

                         $("div.overlay").css("display", "none");

                      }

                  });

}

//diamond clarity

function get_Activeclarity(){

	$.ajax({

	type: 'GET',

	url: base_url+'index.php/admin_ret_catalog/diamond/active_diamondclarity',

	dataType:'json',

	success:function(data){

		var id =  $("#clarity_id").val();

		$.each(data, function (key, item) {

		   $("#clarity_sel").append(

		   $("<option></option>")

		   .attr("value", item.id_clarity)

		   .text(item.clarity)

		   );

		});

		$("#clarity_sel").select2(

		{

		placeholder:"Select Clarity",

		allowClear: true

		});

		   $("#clarity_sel").select2("val",(id!='' && id>0?id:''));

		   $(".overlay").css("display", "none");

		}

	});

}

$("#clarity_sel").on('change',function(e){

	if(this.value!='')

	{

		//alert(this.value);

	$("#clarity_id").val(this.value);

	}

	else

	{

	$("#clarity_id").val('');

	}

	});

//diamond color

function get_Activecolor(){

	$.ajax({

	type: 'GET',

	url: base_url+'index.php/admin_ret_catalog/diamond/active_diamondcolor',

	dataType:'json',

	success:function(data){

		var id =  $("#color_id").val();

		$.each(data, function (key, item) {

		   $("#color_sel").append(

		   $("<option></option>")

		   .attr("value", item.id_color)

		   .text(item.color)

		   );

		});

		$("#color_sel").select2(

		{

		placeholder:"Select Color",

		allowClear: true

		});

		   $("#color_sel").select2("val",(id!='' && id>0?id:''));

		   $(".overlay").css("display", "none");

		}

	});

}

$("#color_sel").on('change',function(e){

	if(this.value!='')

	{

	$("#color_id").val(this.value);

	}

	else

	{

	$("#color_id").val('');

	}

	});

//diamond color

function get_Activecut(){

	$.ajax({

	type: 'GET',

	url: base_url+'index.php/admin_ret_catalog/diamond/active_diamondcut',

	dataType:'json',

	success:function(data){

		var id =  $("#cut_id").val();

		$.each(data, function (key, item) {

		   $("#cut_sel").append(

		   $("<option></option>")

		   .attr("value", item.id_cut)

		   .text(item.cut)

		   );

		});

		$("#cut_sel").select2(

		{

		placeholder:"Select Cut",

		allowClear: true

		});

		   $("#cut_sel").select2("val",(id!='' && id>0?id:''));

		   $(".overlay").css("display", "none");

		}

	});

}

$("#cut_sel").on('change',function(e){

	if(this.value!='')

	{

	$("#cut_id").val(this.value);

	}

	else

	{

	$("#cut_id").val('');

	}

	});

//diamond shape

function get_Activeshape(){

	$.ajax({

	type: 'GET',

	url: base_url+'index.php/admin_ret_catalog/diamond/active_diamondshape',

	dataType:'json',

	success:function(data){

		var id =  $("#shape_id").val();

		$.each(data, function (key, item) {

		   $("#shape_sel").append(

		   $("<option></option>")

		   .attr("value", item.shape_id)

		   .text(item.name)

		   );

		});

		$("#shape_sel").select2(

		{

		placeholder:"Select Shape",

		allowClear: true

		});

		   $("#shape_sel").select2("val",(id!='' && id>0?id:''));

		   $(".overlay").css("display", "none");

		}

	});

}

$("#shape_sel").on('change',function(e){

	if(this.value!='')

	{

	$("#shape_id").val(this.value);

	}

	else

	{

	$("#shape_id").val('');

	}

	});

$("#status").on('switchChange.bootstrapSwitch', function (event, state) {

		var x = $(this).data('on-text');

		var y = $(this).data('off-text');

		  if($("#status").is(':checked') && x=='YES')

		  {

			  $("#ad_quality_status").val(1);

		  }

		  else if(y == 'NO')

		  {

			  $("#ad_quality_status").val(0);

		  }

  });

    function checkcode(code)

    {

        $("div.overlay").css("display", "block");

        $.ajax({

            type: 'GET',

            url:  base_url+'index.php/admin_ret_catalog/check_quality_code/'+code,

            dataType: 'json',

            success: function(avail) {

            if(avail==1)

            {

            $('#code').val('');

            $.toaster({ priority : 'danger', title : 'Warning!', message : "Quality code name already exists"});

            $("div.overlay").css("display", "none");

            }

            },

            error:function(error)

            {

            $("div.overlay").css("display", "none");

            }

        });

    }

     if($('#code').length>0)

     {

            $('#code').on('blur onchange',function(){

        	if(this.value.length >=1)

        	{

        		checkcode(this.value);

            }

            else

            {

        	  $(this).val('');

        	  $(this).attr('placeholder', 'Enter valid quality code')

            }

           });

     }

  $("#qua_submit").on('click',function()

  {

    	if($("#code").val()==null || $("#code").val()=="" || $("#code").val() == 0)

    	{

    		$.toaster({ priority : 'danger', title : 'Warning!', message : "Enter the proper quality code name."});

    	}

    	else if($("#clarity_id").val()==null || $("#clarity_id").val()=="" || $("#clarity_id").val() == 0)

    	{

    		$.toaster({ priority : 'danger', title : 'Warning!', message : "Select the Proper Clarity."});

    	}

    	else if($("#color_id").val()==null || $("#color_id").val()=="" || $("#color_id").val() == 0)

    	{

    		$.toaster({ priority : 'danger', title : 'Warning!', message : "Select the Proper Color."});

    	}

    	else if($("#cut_id").val()==null || $("#cut_id").val()=="" || $("#cut_id").val() == 0)

    	{

    		$.toaster({ priority : 'danger', title : 'Warning!', message : "Select the Proper Cut."});

    	}

    	else if($("#shape_id").val()==null || $("#shape_id").val()=="" || $("#shape_id").val() == 0)

    	{

    		$.toaster({ priority : 'danger', title : 'Warning!', message : "Select the Proper Shape."});

    	}else

    	{

    	    $('#diamond_master').submit();

    	}

  });

    function create_diamond_rate_table_row()

	{

		var trHtml = '';

		var row_count = $('#diamond_rate_table >tbody > tr').length + 1;

		trHtml+='<tr id="diamond_rate_'+row_count+'" >'

					+'<td><input class ="from_cent" type="number" name="rate_list[from_cent][]"></td>'

					+'<td><input class ="to_cent" type="number" name="rate_list[to_cent][]"></td>'

					+'<td><input class ="rate" type="number" name="rate_list[rate][]"></td>'

					+"<td><div><button class='add_new_dia_rate btn btn-success' name='add'type='button'><i class='fa fa-plus'></i></button>"

					+"<button class='delete btn btn-danger' name='delete'type='button'><i class='fa fa-trash'></i></button></div></td>"

					+'</tr>';

		$('#diamond_rate_table >tbody').append(trHtml);

  }

    function validate_diamond_rate_table_row(curRow)

    {

        var row_id = curRow.attr('id');

        var from_wt = parseFloat (curRow.find('.from_cent').val());

        var to_wt = parseFloat(curRow.find('.to_cent').val());

        var validate = true;

        if(curRow.find('.from_cent ').val() != '' && curRow.find('.to_cent ').val() != '' && from_wt <= to_wt){

        $('#diamond_rate_table > tbody  > tr').each(function(index, tr) {

                if( ( parseFloat ($(this).find('.from_cent').val())  <= from_wt &&  parseFloat ($(this).find('.to_cent').val())  >= from_wt ||  parseFloat ($(this).find('.from_cent').val())  <= to_wt &&  parseFloat ($(this).find('.to_cent').val())  >= to_wt) && row_id != $(this).attr('id') ){

                    validate = false;

                    $.toaster({ priority : 'danger', title : 'Warning!', message : "Invalid From and To Weight "});

                    return validate;

                }

        });

        } else if( from_wt >= to_wt ){

        validate = false;

        $.toaster({ priority : 'danger', title : 'Warning!', message : "Invalid From and To Weight "});

        }else{

            validate = false;

            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill the required fields....'});

        }

        return validate;

    }

    $(document).on('change','.from_cent,.to_cent',function()

    {

        if ($(this).val() != ''){

    		var curRow=$(this).closest('tr');

    		validate_diamond_rate_table_row(curRow);

        }

    });

	$("#diamond_rate_table").on('click','.delete',function(){

		   $(this).closest('tr').remove();

	});

	$("#diamond_rate_table").on('click','.add_new_dia_rate',function(){

    	if(validate_diamond_rate_table())

    	{

    	    create_diamond_rate_table_row();

    	}

    });

	$("#add_diamond_rate").on("click",function(){

    	if(validate_diamond_rate_table())

    	{

    		create_diamond_rate_table_row();

    	}

    });

    function validate_diamond_rate_table()

    {

        var validate= true;

        $('#diamond_rate_table > tbody  > tr').each(function(index, row) {

        var tr=$(this);

        if(tr.find('.from_cent ').val() == '' || tr.find('.to_cent ').val() == ''  || tr.find('.rate').val() == '' || ! validate_diamond_rate_table_row(tr) ){

        validate= false;

        return validate;

        }

        });

        return validate;

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

                $(".overlay").css("display", "none");

            }

        });

    }

    $(document).on('change','.stn_name ',function()

    {

        if ($(this).val() != '')

        {

        	var curRow=$(this).closest('tr');

        	var stn_name=$(this).val();

        	var uom_id = $('option:selected', this).attr('data-uom_id');

        	curRow.find('.uom_id ').select2("val",(uom_id!='' && uom_id>0?uom_id:''));

        }

    });

	function get_Activequality()

	{

		$.ajax({

		type: 'GET',

		url: base_url+'index.php/admin_ret_catalog/diamond_rate/active_diamondquality',

		dataType:'json',

		success:function(data){

			var id =  $("#quality_code_id").val();

			$.each(data, function (key, item) {

			   $("#quality_sel").append(

			   $("<option></option>")

			   .attr("value", item.quality_id)

			   .text(item.code)

			   );

			});

			$("#quality_sel").select2(

			{

			placeholder:"Select Diamond Quality code",

			allowClear: true

			});

			   $("#quality_sel").select2("val",(id!='' && id>0?id:''));

			   $(".overlay").css("display", "none");

			}

		});

	}

	if($('#effective_date').length>0)

	{

		$('#effective_date').datepicker({

				   format: 'dd/mm/yyyy'

				})

				.on('changeDate', function(ev){

					 $(this).datepicker('hide');

			});

	}

    function set_diamond_rate_table()

	{

		//alert();

		my_Date = new Date();

		$.ajax({

				 url:base_url+ "index.php/admin_ret_catalog/diamond_rate?nocache=" + my_Date.getUTCSeconds(),

				 data:{ 'quality_code_id':$('#quality_sel').val()},

				 dataType:"JSON",

				 type:"POST",

				 success:function(data){

					var rate 	    = data.rate;

					var access		= data.access;

					$('#total_rate').text(rate.length);

					if(access.add == '0')

					{

						$('#add_rate').attr('disabled','disabled');

					}

				 var oTable = $('#rate_list').DataTable();

				 oTable.clear().draw();

				 if (rate!= null && rate.length > 0)

				 {

					oTable = $('#rate_list').dataTable({

							"bDestroy": true,

							"bInfo": true,

							"bFilter": true,

							"bSort": true,

							"dom": 'lBfrtip',

							"order": [[ 0, "desc" ]],

							"buttons" : ['excel','print'],

							"tableTools": { "buttons": [ { "sExtends": "xls",

							"oSelectorOpts": { page: 'current'

							} },

							{ "sExtends": "pdf",

							"oSelectorOpts": { page: 'current'

							} } ] },

							"aaData"  : rate,

							"aoColumns": [	{ "mDataProp": "rate_id"},

											{ "mDataProp": "code"},

											{ "mDataProp": "effective_date"},

											{ "mDataProp": function ( row, type, val, meta ){

                                                status_url = base_url+"index.php/admin_ret_catalog/diamond_rate/update_status/"+row.rate_id+"/"+(row.rate_status==1?0:1)+"/"+row.quality_code_id;

                                                return "<a href='"+status_url+"'><i class='fa "+(row.rate_status==1?'fa-check':'fa-remove')+"'style='color:"+(row.rate_status==1?'green':'red')+"'></i></a>"

											   }

                                                },

											{ "mDataProp": function ( row, type, val, meta ) {

												id= row.rate_id;

												edit_url=(base_url+'index.php/admin_ret_catalog/diamond_rate/edit/'+id);

												delete_confirm=(access.delete=='1' ?'#confirm-delete':'');

												delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/diamond_rate/delete/'+id : '#' );

												action_content=(access.edit =='1' ? '<a href='+edit_url+' class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'><i class="fa fa-edit" ></i></a>':"") +(access.delete == '1' ? '<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>':'')

												return action_content;

												}

											}]

						});

					}

			  },

			  error:function(error)

			  {

				 $("div.overlay").css("display", "none");

			  }

		});

	}

	$('#diamond_rate_submit').on('click',function(){

	    var quality_code_id=$('#quality_code_id').val();

	    var effective_date=$('#effective_date').val();

		var allow_submit=true;

		if($("#quality_sel").val()==null || $("#quality_sel").val()=="" || $("#quality_sel").val() == 0)

     	{

			$.toaster({ priority : 'danger', title : 'Warning!', message : "Enter the proper quality code name."});

			allow_submit=false;

	   }

	   else if($("#effective_date").val()==null || $("#effective_date").val()=="" || $("#effective_date").val() == 0)

	  {

			$.toaster({ priority : 'danger', title : 'Warning!', message : "Select the Proper effective time."});

			allow_submit=false;

	  }

	  else if($(".from_cent").val()==null || $(".from_cent").val()=="" || $(".from_cent").val() == 0)

	  {

			$.toaster({ priority : 'danger', title : 'Warning!', message : "Enter the proper from cent."});

			allow_submit=false;

	  }

	  if(allow_submit)

		{

			my_Date = new Date();

		    //$("div.overlay").css("display", "block");

		    var form_data=$('#diamond_rate_list').serialize();

		   if(ctrl_page[2]=='add')

		    {

		     var url = base_url+ "index.php/admin_ret_catalog/diamond_rate/save/?nocache=" + my_Date.getUTCSeconds();

		    }

		   else{

			var url = base_url+ "index.php/admin_ret_catalog/diamond_rate/update/"+ctrl_page[3]+"?nocache=" + my_Date.getUTCSeconds();

		   }

		    $.ajax({

		        url:url,

		        data: form_data,

		        type:"POST",

		        dataType:"JSON",

		        success:function(data){

					if(data.length)

					{

						window.location.href = base_url+'index.php/admin_ret_catalog/diamond_rate/list';

						$.toaster({ priority : 'success', title : 'Warning!', message :data.message});

						$("div.overlay").css("display", "none");

					}else

					{

						$.toaster({ priority : 'success', title : 'Warning!', message :data.message});

					    window.location.href= base_url+'index.php/admin_ret_catalog/diamond_rate/list';

					}

					$("div.overlay").css("display", "none");

		        },

		        error:function(error)

		        {

		        $("div.overlay").css("display", "none");

		        }

		    });

		}

		else{

			// $('#diamond_rate_submit').prop('disabled',false);

			$("div.overlay").css("display", "none");

		}

	});

	$('#quality_sel').on('change',function(){

		set_diamond_rate_table();

	});

//v.a and m.c fixing

$("input[name='settings[wastage_type]']").change(function () {

	change_wastage_type();

});

function create_new_empty_weight_range_row()

{

    var a= $('#wc_detail tbody tr').length;

	var html = "";

	var i = ++a;

	html+="<tr id='wc"+i+"'>"

	+"<td>"+i+"</td><td><input name='wcrange["+i+"][from_weight]' step='.001' id='from_weight"+i+"' class='form-control from_weight' placeholder='From weight' type='number'></td>"

	+"<td><input name='wcrange["+i+"][to_weight]'  step='.001' id='to_weight"+i+"' class='form-control to_weight' placeholder='To weight' type='number'></td>"

	+"<td style='width: 90px;'><input name='wcrange["+i+"][wc_method]' id='wc_method_pc"+i+"' class='wc_method_pc' type='radio' checked value='1'> <label for='wc_method_pc"+i+"'>Pc</label> &nbsp; <input name='wcrange["+i+"][wc_method]' id='wc_method_wt"+i+"' class='wc_method_wt' type='radio' value='2'><label for='wc_method_wt"+i+"'>Wt</label></td>"

	+"<td><input name='wcrange["+i+"][wc_min]'  step='.001' id='wc_min"+i+"' class='form-control wc_min' placeholder='Min VA (%)' type='number'></td>"

	+"<td><input name='wcrange["+i+"][wc_min_wt]'  step='.001' id='wc_min_wt"+i+"' class='form-control wc_min_wt' placeholder='Min VA(Wt)' type='number' readonly></td>"

	+"<td><input name='wcrange["+i+"][wastage]'  step='.001' id='wastage"+i+"' class='form-control wastage' placeholder='Max V.A (%)' type='number'></td>"

	+"<td><input name='wcrange["+i+"][wc_wt]'  step='.001' id='wc_wt"+i+"' class='form-control wc_wt' placeholder='Max V.A (Wt)' type='number' readonly></td>"

	

	+"<td><select name='wcrange["+i+"][mc_type]' id='mc_type"+i+"' class='form-control mc_type' style='width: 100px;'><option value=2>Per Gram</option><option value=1 selected>Per Pcs</option></select></td>"

	+"<td><input name='wcrange["+i+"][mcrg_min]'  step='.001' id='mcrg_min"+i+"' class='form-control mcrg_min' placeholder='Min MC' type='number'></td>"

	+"<td><input name='wcrange["+i+"][making_charge]' step='.001' id='making_charge"+i+"' class='form-control making_charge' placeholder='Max MC' type='number'></td>"

	+"<td><a href='#' onClick='add_new_range();' class='btn btn-success btn-plus'><i class='fa fa-plus'></i></a><a href='#' onClick='remove_row($(this).closest(\"tr\"));' class='btn btn-danger btn-del'><i class='fa fa-trash'></i></a></td>"

	+"</tr>";

  	$('#wc_detail').append(html);

  	$('.from_weight').focus();

}

function validate_weight_range_row_06_09_2024()

{

    let Rowvalidate = true;

	if($('#wc_detail > tbody >tr').length > 0 ){

		$('#wc_detail > tbody >tr').each(function(index, tr) {

			let checked_val = $(this).find(".wc_method_pc").is(":checked") ? 1 : 2;

			if($.trim($(this).find('.from_weight').val()) == "" || $.trim($(this).find('.to_weight').val()) == "" || (checked_val == 1 ? $.trim($(this).find('.wc_min').val()) == "" : $.trim($(this).find('.wc_min_wt').val()) == "") || $.trim($(this).find('.mcrg_min').val()) == "" )

			{

				Rowvalidate = false;

			}

		});

	}else{

		Rowvalidate = false;

	}

	return Rowvalidate;

}

// function validate_weight_range_row() {

// 	let Rowvalidate = 0;

// 	if ($('#wc_detail > tbody >tr').length > 0) {

// 		$('#wc_detail > tbody >tr').each(function (index, tr) {

// 			var wt_min_va = parseFloat($.trim($(this).find('.wc_min').val()));

// 			var wt_max_va = parseFloat($.trim($(this).find('.wastage').val()));

// 			var wt_min_va_wt = parseFloat($.trim($(this).find('.wc_min_wt').val()));

// 			var wt_max_va_wt = parseFloat($.trim($(this).find('.wc_wt').val()));

// 			var wt_min_mc = parseFloat($.trim($(this).find('.mcrg_min').val()));

// 			var wt_max_mc = parseFloat($.trim($(this).find('.making_charge').val()));

// 			let checked_val = $(this).find(".wc_method_pc").is(":checked") ? 1 : 2;

// 			if ($.trim($(this).find('.from_weight').val()) == "" || $.trim($(this).find('.to_weight').val()) == "" || (checked_val == 1 ? $.trim($(this).find('.wc_min').val()) == "" : $.trim($(this).find('.wc_min_wt').val()) == "") || $.trim($(this).find('.mcrg_min').val()) == "") {

// 				Rowvalidate = 1;

// 			} else if ($.trim($(this).find('.from_weight').val()) == "" || $.trim($(this).find('.to_weight').val()) == "" || (checked_val == 1 ? wt_max_va < wt_min_va : wt_max_va_wt < wt_min_va_wt) || wt_max_mc < wt_min_mc) {

// 				$.toaster({

// 					priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Max MC and VA values should be greater than Min MC and VA values',

// 					settings: {

// 						timeout: 5 * 1000 // Timeout in milliseconds (e.g., 5000 ms = 5 seconds)

// 					}

// 				});

// 				Rowvalidate = 2;

// 			}

// 		});

// 	} else {

// 		Rowvalidate = 1;

// 	}

// 	return Rowvalidate;

// }

// function add_new_range()

// {

//     if(validate_weight_range_row())

//     {

//         create_new_empty_weight_range_row();

//     }else

//     {

//         $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill The Required Fields..'});

//     }

// }

function validate_weight_range_row() {

    let Rowvalidate = 0;

    let weightRanges = []; // Start with the existing ranges fetched from the database

	  // Clear previous error highlighting

	  $('#wc_detail > tbody >tr').each(function () {

        $(this).find('input').removeClass('error-field');

    });

    if ($('#wc_detail > tbody >tr').length > 0) {

        $('#wc_detail > tbody >tr').each(function (index, tr) {

            var fromWeight = parseFloat($.trim($(this).find('.from_weight').val()));

            var toWeight = parseFloat($.trim($(this).find('.to_weight').val()));

            var wt_min_va = parseFloat($.trim($(this).find('.wc_min').val()));

            var wt_max_va = parseFloat($.trim($(this).find('.wastage').val()));

            var wt_min_va_wt = parseFloat($.trim($(this).find('.wc_min_wt').val()));

            var wt_max_va_wt = parseFloat($.trim($(this).find('.wc_wt').val()));

            var wt_min_mc = parseFloat($.trim($(this).find('.mcrg_min').val()));

            var wt_max_mc = parseFloat($.trim($(this).find('.making_charge').val()));

            let checked_val = $(this).find(".wc_method_pc").is(":checked") ? 1 : 2;

            // Check if any field is empty

            if ($.trim($(this).find('.from_weight').val()) == "" || $.trim($(this).find('.to_weight').val()) == "" || 

                (checked_val == 1 ? $.trim($(this).find('.wc_min').val()) == "" : $.trim($(this).find('.wc_min_wt').val()) == "") || 

                $.trim($(this).find('.mcrg_min').val()) == "") {

					// Highlight empty fields

					if ($.trim($(this).find('.from_weight').val()) == "") $(this).find('.from_weight').addClass('error-field');

					if ($.trim($(this).find('.to_weight').val()) == "") $(this).find('.to_weight').addClass('error-field');

					if (checked_val == 1 && $.trim($(this).find('.wc_min').val()) == "") $(this).find('.wc_min').addClass('error-field');

					if (checked_val == 2 && $.trim($(this).find('.wc_min_wt').val()) == "") $(this).find('.wc_min_wt').addClass('error-field');

					if ($.trim($(this).find('.mcrg_min').val()) == "") $(this).find('.mcrg_min').addClass('error-field');

                

                Rowvalidate = 1;

                return false; // Exit loop early if a validation error is found

            } 

            // Validate Min and Max values

            else if ((checked_val == 1 ? wt_max_va < wt_min_va : wt_max_va_wt < wt_min_va_wt) || wt_max_mc < wt_min_mc) {

				 // Highlight fields with invalid Min/Max values

				 if (checked_val == 1 && wt_max_va < wt_min_va) {

                    $(this).find('.wc_min').addClass('error-field');

                    $(this).find('.wastage').addClass('error-field');

                }

                if (checked_val == 2 && wt_max_va_wt < wt_min_va_wt) {

                    $(this).find('.wc_min_wt').addClass('error-field');

                    $(this).find('.wc_wt').addClass('error-field');

                }

                if (wt_max_mc < wt_min_mc) {

                    $(this).find('.mcrg_min').addClass('error-field');

                    $(this).find('.making_charge').addClass('error-field');

                }

                $.toaster({

                    priority: 'danger', title: 'Warning!', message: 'Max MC and VA values should be greater than Min MC and VA values',

                    settings: { timeout: 5 * 1000 }

                });

                Rowvalidate = 2;

                return false; // Exit loop early if a validation error is found

            }

            // Check for overlapping ranges with both existing and new entries

            for (let range of weightRanges) {

                if (

                    (fromWeight === range.from && toWeight === range.to) ||  // Check for duplicate ranges

                    (fromWeight >= range.from && fromWeight <= range.to) ||   // Check for overlapping ranges

                    (toWeight >= range.from && toWeight <= range.to) || 

                    (fromWeight < range.from && toWeight > range.to)

                ) {

					 // Highlight the overlapping range fields

					 $(this).find('.from_weight').addClass('error-field');

					 $(this).find('.to_weight').addClass('error-field');

                    // $.toaster({

                    //     priority: 'danger', title: 'Warning!', message: 'Weight ranges should not overlap or be duplicated.',

                    //     settings: { timeout: 5 * 1000 }

                    // });

					showWarning('Weight ranges should not overlap or be duplicated');

                    Rowvalidate = 3;

                    return false; // Exit loop early if a validation error is found

                }

            }

            // If no issues, add the current range to the array for further validation

            weightRanges.push({ from: fromWeight, to: toWeight });

        });

    } else {

        Rowvalidate = 1; // No rows available

    }

    return Rowvalidate;

}

function add_new_range() {

	var row_validate = validate_weight_range_row();

 if (row_validate == 0) {

		create_new_empty_weight_range_row();

	}

}

$('#add_wc_weight_info').on('click',function(){

    add_new_range();

});

$('#update_weight_range_settings').on('click',function(){

    var allow_submit=true;

    var wastageType = $("input[name='settings[wastage_type]']:checked").val();

	var is_va_mc_based_on_branch = $("#is_va_mc_based_on_branch").val();

	var fix_min_va = parseFloat($('#min_wastag_valuess').val());

	var fix_max_va = parseFloat($('#wastag_value').val());

	var fix_min_va_wt = parseFloat($('#wastag_min_wt').val());

	var fix_max_va_wt = parseFloat($('#wastag_wt').val());

	var fix_min_mc = parseFloat($('#min_mc_value').val());

	var fix_max_mc = parseFloat($('#mc_value').val());

	if($('#des_prod_name').val()==null){

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Product..'});

		allow_submit=false;

	}else if($('#des_des_name').val()==null){

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select  Design..'});

		allow_submit=false;

	}else if($('#select_sub_design').val()==null){

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Sub Design..'});

		allow_submit=false;

	}

	else if($('#update_mc_type').val()== 0 && wastageType==1){

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Mc Type..'});

		allow_submit=false;

	}

	else if(is_va_mc_based_on_branch == 1 && $('#branch_filter').val() ==null){

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Branch ..'});

		allow_submit=false;

	}

	else if ($('#margin_mrp').val() >100){

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter vaild Margin MRP(%) ..'});

		allow_submit=false;

	}

	if(wastageType==1)

	{

		var wastage_method = $("#wastag_method").val();

		if((wastage_method == 1 ? ($('#wastag_value').val()=='' || $('#min_wastag_valuess').val()=='') : ($('#wastag_wt').val()=='' || $('#wastag_min_wt').val()=='')) || $('#mc_value').val()=='' || $('#min_mc_value').val()=='')

		{

			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill The MC and V.A'});

			allow_submit=false;

		} else if ((wastage_method == 1 ? (fix_max_va < fix_min_va) : (fix_max_va_wt < fix_min_va_wt)) || fix_max_mc < fix_min_mc) {

			$.toaster({

				priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Max MC and VA values should be greater than Min MC and VA values',

				settings: {

					timeout: 5 * 1000 // Timeout in milliseconds (e.g., 5000 ms = 5 seconds)

				}

			});

			allow_submit = false;

		}

	}

	else if(wastageType==2)

	{

		var row_validate = validate_weight_range_row();

		if (row_validate == 1) 

		{

			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill The Required Fields..'});

			allow_submit=false;

		} else if (row_validate == 2) {

			$.toaster({

				priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Max MC and VA values should be greater than Min MC and VA values',

				settings: {

					timeout: 5 * 1000 // Timeout in milliseconds (e.g., 5000 ms = 5 seconds)

				}

			});

			allow_submit = false;

		}else if (row_validate == 3) {

			allow_submit = false;

		}

	}

    if(allow_submit)

    {

        update_weight_range_settings();

    }

});

function update_weight_range_settings()

{

    $("div.overlay").css("display", "block");

    var form_data=$('#selling_settings').serialize();

	$('#update_weight_range_settings').prop('disabled',true);

	if(ctrl_page[2] == 'add'){

		var url=base_url+ "index.php/admin_ret_catalog/wastage_mc_settings/save?nocache=" + my_Date.getUTCSeconds();

	}else{

		var url=base_url+ "index.php/admin_ret_catalog/wastage_mc_settings/update?nocache=" + my_Date.getUTCSeconds();

	}

    $.ajax({

        url:url,

        data: form_data,

        type:"POST",

        dataType:"JSON",

        success:function(data){

			if(data.status)

			{

			    console.log(data.message);

				$("div.overlay").css("display", "none");

			    $.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>"+data.message});

				window.location.href=base_url+ "index.php/admin_ret_catalog/wastage_mc_settings/list";

			}else

			{

			    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message});

			   $("div.overlay").css("display", "none");

			    $('#update_weight_range_settings').prop('disabled',false);

			}

        },

        error:function(error)

        {

        $("div.overlay").css("display", "none");

        }

    });

}

$('#search_weight_range').on('click',function(){

    get_wastage_settings_details();

});

function get_wastage_settings_details()

{

    $("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

			 url:base_url+ "index.php/admin_ret_catalog/wastage_mc_settings/ajax?nocache=" + my_Date.getUTCSeconds(),

			 dataType:"JSON",

			 type:"POST",

			 data:{'id_product':$('#des_prod_name').val(),'id_design':$('#des_des_name').val(),'id_sub_design':$('#select_sub_design').val(),'id_branch':$('#branch_filter').val()},

			 cache:false,

			 success:function(data){

			     var access		= data.access;

				 var list 	    = data.list;

    			 var oTable = $('#item_list').DataTable();

				 var is_va_mc_based_on_branch =$('#is_va_mc_based_on_branch').val();

    			 oTable.clear().draw();

    			 if (list!= null && list.length > 0)

    			 {

    				if(is_va_mc_based_on_branch == 1){

						oTable = $('#item_list').dataTable({

    						"bDestroy": true,

    						"bInfo": true,

    						"bFilter": true,

    						"bSort": true,

    						"order": [[ 0, "desc" ]],

    						"dom": 'lBfrtip',

    						"buttons" : ['excel','print'],

    						"tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },

    						"aaData"  : list,

    						"aoColumns": [

							             	{ "mDataProp": "id_selling_settings" },

    										{ "mDataProp": "product_name" },

    										{ "mDataProp": "design_name" },

    										{ "mDataProp": "sub_design_name" },

											{ "mDataProp": "id_branch" },

    										{ "mDataProp": "type" },

											{ "mDataProp": function ( row, type, val, meta ) {

												return row.wastag_method == 1 ? 'Percentage' : (row.wastag_method == 2 ? 'Weight' : '')

    										}},

											{ "mDataProp": "min_va" },

											{ "mDataProp": "wastag_min_wt" },

											{ "mDataProp": "va" },

											{ "mDataProp": function ( row, type, val, meta ) {

												if(row.wastage_type == 1){

													return row.wastage_wt;

												}else if(row.wastage_type == 2){

													return '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" onclick="wastagesetting_view('+row.id_selling_settings+')" data-id='+row.id_selling_settings+'>View</a>';

												}

    										}},

											{ "mDataProp": "mc_type" },

											{ "mDataProp": "min_mc" },

											{ "mDataProp": "mc" },

											{ "mDataProp": "margin_mrp" },

    										{ "mDataProp": function ( row, type, val, meta ) {

                          id_selling_settings = row.id_selling_settings;

                          edit_target =

                            access.edit == "0"

                              ? ""

                              : base_url +

                                "index.php/admin_ret_catalog/wastage_mc_settings/edit/" +

                                id_selling_settings;

                          delete_url = true

                            ? base_url +

                              "index.php/admin_ret_catalog/wastage_mc_settings/delete/" +

                              id_selling_settings

                            : "#";

                          delete_confirm = true ? "#confirm-delete" : "";

                          action_content='<a href="'+edit_target+'" class="btn btn-primary btn-edit" id="edit" role="button" data-id='+id_selling_settings+'  ><i class="fa fa-edit" ></i></a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

                         var action_content = "";

                          if (access.edit == "1") {

                            action_content +=

                              '<a href="'+edit_target+'" class="btn btn-primary btn-edit" id="edit" role="button" data-id='+id_selling_settings+'  ><i class="fa fa-edit" ></i></a>';

                          }

                          if (access.delete == "1") {

                            action_content +=

                              '<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>';

                          }

                          return action_content;

                        }

											}

    									]

    					});

					}else{

						oTable = $('#item_list').dataTable({

    						"bDestroy": true,

    						"bInfo": true,

    						"bFilter": true,

    						"bSort": true,

    						"order": [[ 0, "desc" ]],

    						"dom": 'lBfrtip',

    						"buttons" : ['excel','print'],

    						"tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },

    						"aaData"  : list,

    						"aoColumns": [

							             	{ "mDataProp": "id_selling_settings" },

    										{ "mDataProp": "product_name" },

    										{ "mDataProp": "design_name" },

    										{ "mDataProp": "sub_design_name" },

    										{ "mDataProp": "type" },

											{ "mDataProp": function ( row, type, val, meta ) {

												return row.wastag_method == 1 ? 'Percentage' : (row.wastag_method == 2 ? 'Weight' : '')

    										}},

											{ "mDataProp": "min_va" },

											{ "mDataProp": "wastag_min_wt" },

											{ "mDataProp": "va" },

											{ "mDataProp": function ( row, type, val, meta ) {

												if(row.wastage_type == 1){

													return row.va;

												}else if(row.wastage_type == 2){

													return '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" onclick="wastagesetting_view('+row.id_selling_settings+')" data-id='+row.id_selling_settings+'>View</a>';

												}

    										}},

											{ "mDataProp": "mc_type" },

											{ "mDataProp": "min_mc" },

											{ "mDataProp": "mc" },

											{ "mDataProp": "margin_mrp" },

    										{ "mDataProp": function ( row, type, val, meta ) {

												id_selling_settings= row.id_selling_settings;

												 edit_target=(access.edit=='0'?"":base_url+'index.php/admin_ret_catalog/wastage_mc_settings/edit/'+id_selling_settings);

												 delete_url=(true ? base_url+'index.php/admin_ret_catalog/wastage_mc_settings/delete/'+id_selling_settings : '#' );

												 delete_confirm= (true ?'#confirm-delete':'');

												 action_content='<a href="'+edit_target+'" class="btn btn-primary btn-edit" id="edit" role="button" data-id='+id_selling_settings+'  ><i class="fa fa-edit" ></i></a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

												 return action_content;

												 }

											}

    									]

    					});

					}

    			}

    		 $("div.overlay").css("display", "none");

		  },

		  error:function(error)

		  {

			 $("div.overlay").css("display", "none");

		  }

	});

}

$(document).on('click',"#item_list tbody tr a.btn-view", function(){

	id=$(this).data('id');

	 wastage_view_(id);

	$("#id_selling_settings").val(id);

});

$(document).on('click',"#wcdetail tbody tr a.btn-edit", function(){

		var row = $(this).closest('tr');

		row.find('.wc_from_weight').attr('readonly',false);

		row.find('.wc_to_weight').attr('readonly',false);

		row.find('.wc_percent').attr('readonly',false);

		row.find('.mc').attr('readonly',false);

});

function wastage_view_(id_selling_settings)

{

    my_Date = new Date();

	$.ajax({

			 url:base_url+ "index.php/admin_ret_catalog/selling_price/weight_details?nocache=" + my_Date.getUTCSeconds(),

			 dataType:"JSON",

			 type:"POST",

			 cache:false,

			 data:{'id_selling_settings':id_selling_settings},

			 success:function(data){

				 var list 	    = data;

    			 var oTable = $('#wcdetail').DataTable();

    			 oTable.clear().draw();

    			 if (list!= null && list.length > 0)

    			 {

    				oTable = $('#wcdetail').dataTable({

    						"bDestroy": true,

    						"bInfo": true,

    						"bFilter": true,

    						"bSort": true,

    						"order": [[ 0, "desc" ]],

    						"aaData"  : list,

    						"aoColumns": [

                    						{ "mDataProp":function ( row, type, val, meta ){

                							  return '<input type="checkbox" name="id_wc[]" id="id_wc" class="id_wc"  value="'+row.id_wc+'"><input type="hidden" name="id_product[]" id="id_product" class="id_product"  value="'+row.id_product+'"><input type="hidden" name="id_design[]" id="id_design" class="id_design"  value="'+row.id_design+'"><input type="hidden" name="id_sub_design[]" id="id_sub_design" class="id_sub_design"  value="'+row.id_sub_design+'">'+row.id_wc;

                							}},

                							{ "mDataProp":function ( row, type, val, meta ){

                							  return '<input type="number" name="wc_from_weight[]" class="form-control wc_from_weight"  value="'+row.wc_from_weight+'" readonly>';

                							}},

                							{ "mDataProp":function ( row, type, val, meta ){

                							  return '<input type="number" name="wc_to_weight[]" class="form-control wc_to_weight"  value="'+row.wc_to_weight+'" readonly>';

                							}},

                							{ "mDataProp":function ( row, type, val, meta ){

                							  return '<input type="number" name="wc_percent[]"  class="form-control wc_percent"  value="'+row.wc_percent+'" readonly>';

                							}},

                							{ "mDataProp":function ( row, type, val, meta ){

                							  return '<input type="number" name="mc[]"  class="form-control mc"  value="'+row.mc+'" readonly>';

                							}},

                							{ "mDataProp": function ( row, type, val, meta ) {

                                                id= row.id_wc;

                                                action_content='<a href="#" class="btn btn-primary btn-edit" id="edit" role="button"><i class="fa fa-edit" ></i></a>'

                                                return action_content;

											}

										    }

    									]

    					});

    					$('#wastage').modal('show');

    				}else

    				{

    				    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>No Records Found."});

    				}

		  },

		  error:function(error)

		  {

			 $("div.overlay").css("display", "none");

		  }

	});

}

$('#delete_weight_range_settings').on('click',function(){

    if($("input[name='id_wc[]']:checked").val())

    {

        var selected = [];

        var approve=false;

        $("#wcdetail tbody tr").each(function(index, value)

        {

        if($(value).find("input[name='id_wc[]']:checked").is(":checked"))

        {

        transData = {

        'id_wc'           : $(value).find(".id_wc").val(),

        }

        selected.push(transData);

        }

        })

        req_data = selected;

        console.log(req_data);

        delete_wastage_weight_settings(req_data);

    }

    else

    {

        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Any one Items."});

    }

});

function get_productWeightDetails()

{

$(".overlay").css("display", "block");

   $('#wc_detail tbody').empty();

   my_Date = new Date();

	$.ajax({

		type:"POST",

		url: base_url+"index.php/admin_ret_catalog/wastage_mc_settings/get_product_weight?nocache=" + my_Date.getUTCSeconds(),

		cache:false,

		dataType:"JSON",

		data:{'id_product':$("#des_prod_name").val(),'id_design':$("#des_des_name").val(),'id_sub_design':$("#select_sub_design").val()},

		success:function(data){

			var html='';

			if(data.length > 0){

				let i = 1;

				$.each(data,function(key,val){

					html+="<tr id='wc"+i+"'>"

						+"<td>"+i+"</td><td><input name='wcrange["+i+"][from_weight]' step='.001' id='from_weight"+i+"' class='form-control from_weight' value='"+val.from_weight+"' placeholder='From weight' type='number'></td>"

						+"<td><input name='wcrange["+i+"][to_weight]'  step='.001' id='to_weight"+i+"' class='form-control to_weight' value='"+val.to_weight+"' placeholder='To weight' type='number'></td>"

						+"<td style='width: 90px;'><input name='wcrange["+i+"][wc_method]' id='wc_method_pc"+i+"' class='wc_method_pc' type='radio' value='1' checked> <label for='wc_method_pc"+i+"'>Pc</label> &nbsp; <input name='wcrange["+i+"][wc_method]' id='wc_method_wt"+i+"' class='wc_method_wt' type='radio' value='2'><label for='wc_method_wt"+i+"'>Wt</label></td>"

						+"<td><input name='wcrange["+i+"][wc_min]'  step='.001' id='wc_min"+i+"' class='form-control wc_min' placeholder='Min VA (%)' type='number'></td>"

						+"<td><input name='wcrange["+i+"][wc_min_wt]'  step='.001' id='wc_min_wt"+i+"' class='form-control wc_min_wt' placeholder='Min VA(Wt)' type='number' readonly></td>"

						+"<td><input name='wcrange["+i+"][wastage]'  step='.001' id='wastage"+i+"' class='form-control wastage' placeholder='Max V.A (%)' type='number'></td>"

						+"<td><input name='wcrange["+i+"][wc_wt]'  step='.001' id='wc_wt"+i+"' class='form-control wc_wt' placeholder='Max V.A (Wt)' type='number' readonly></td>"

						

						+"<td><select name='wcrange["+i+"][mc_type]' id='mc_type"+i+"' class='form-control mc_type' style='width: 100px;'><option value=2>Per Gram</option><option value=1 selected>Per Pcs</option></select></td>"

						+"<td><input name='wcrange["+i+"][mcrg_min]'  step='.001' id='mcrg_min"+i+"' class='form-control mcrg_min' placeholder='Min MC' type='number'></td>"

						+"<td><input name='wcrange["+i+"][making_charge]' step='.001' id='making_charge"+i+"' class='form-control making_charge' placeholder='Max MC' type='number'></td>"

						+"<td><a href='#' onClick='add_new_range();' class='btn btn-success btn-plus'><i class='fa fa-plus'></i></a><a href='#'  onClick='remove_row($(this).closest(\"tr\"));' class='btn btn-danger btn-del'><i class='fa fa-trash'></i></a></td>"

						+"</tr>";

					i++;

			 });

			 $('#wc_detail').append(html);

			}else{

				create_new_empty_weight_range_row();

			}

			$(".overlay").css("display", "none");

		}

	});

}

$("#select_sub_design").on('change',function(){

	if(this.value!='' && ctrl_page[1]=='wastage_mc_settings' && ctrl_page[2]=='add')

	{

		get_productWeightDetails();

	}

	});

function wastagesetting_view(id)

{

    my_Date = new Date();

    $.ajax({

        type:"GET",

        url: base_url+"index.php/admin_ret_catalog/wastage_mc_settings/get_wastage_details/"+id+"?nocache=" + my_Date.getUTCSeconds(),

        cache:false,

        dataType:"JSON",

        success:function(data)

        {

            var wastage = data;

            var oTable = $('#wcdetail').DataTable();

            oTable.clear().draw();

            oTable = $('#wcdetail').dataTable({

            "bDestroy": true,

            "bInfo": true,

            "order": [[ 0, "desc" ]],

            "bFilter": true,

            "bSort": true,

            "aaData": wastage,

            "aoColumns": [

            { "mDataProp" : "id_wc" },

            { "mDataProp" : "wc_from_weight" },

            { "mDataProp" : "wc_to_weight" },

			{ "mDataProp": function ( row, type, val, meta ) {

				return row.wc_method == 1 ? 'Percentage' : (row.wc_method == 2 ? 'Weight' : '')

			}},

            { "mDataProp" : "wc_min"},

			{ "mDataProp" : "wc_min_wt"},

			{ "mDataProp" : "wc_percent"},

			{ "mDataProp" : "wc_wt"},

			{ "mDataProp" : "mcrg_min"},

			{ "mDataProp" : "mc"},

			{ "mDataProp": function ( row, type, val, meta ) {

				let des_wc_id = row.id_wc;

				delete_url = base_url+'index.php/admin_ret_catalog/delete_design_weight_range/'+des_wc_id;

				action_content='<a href="#" class="btn btn-danger btn-del delete_design_weight_range" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" data-parent="'+id+'" ><i class="fa fa-trash"></i></a>'

				return action_content;

				}

		   	}

            ]

            });

			$('#wastage').modal('show');

        }

    });

}

function get_wastage_mc_settings(id)

{

    my_Date = new Date();

    return $.ajax({

        type:"GET",

        url: base_url+"index.php/admin_ret_catalog/wastage_mc_settings/get_wastage_mc_settings/"+id+"?nocache=" + my_Date.getUTCSeconds(),

        cache:false,

		async:true,

        dataType:"JSON",

        success:function(data)

        {

            var wastage = data;

			$('#id_selling_settings').val(id);

			$('#cat_id').val(wastage.id_ret_category);

			$('#sub_des_id').val(wastage.id_sub_design);

			$('#des_id').val(wastage.id_design);

			$('#pro_id').val(wastage.id_product);

			$('#id_branch').val(wastage.id_branch);

			$('#update_mc_type').val(wastage.mc_type);

			$('#margin_mrp').val(wastage.margin_mrp);

			$('#branch_filter').attr('disabled', 'disabled');

			if(wastage.type == 1){

				$("#wastag_method").prop('disabled',false);

				$("#wastag_method").val(wastage.wastag_method);

				$('#wastag_value').val(wastage.wastage_perc);

				$('#wastag_wt').val(wastage.wastage_wt);

				$('#min_wastag_valuess').val(wastage.wastag_min);

				$('#wastag_min_wt').val(wastage.wastag_min_wt);

				$('#mc_value').val(wastage.mc_value);

				$('#min_mc_value').val(wastage.mc_min);

				$('#mc_value').prop('disabled',false);

				$('#min_mc_value').prop('disabled',false);

				$(".flexiable_type").css("display", "none");

				change_wastage_method();

			}else{

				$("#wastag_method").prop('disabled',true);

				$('#wastag_value').prop('disabled',true);

				$('#mc_value').prop('disabled',true);

				$('#wastag_wt').prop('disabled',true);

				$('#min_mc_value').prop('disabled',true);

				$('#min_wastag_valuess').prop('disabled',true);

				$('#wastag_min_wt').prop('disabled',true);

				$(".flexiable_type").css("display", "block");

				$('#wastage_type_flexi').prop('checked', true);

				get_wastage_mc_setting_WeightDetails();

			}

        }

    });

}

function get_wastage_mc_setting_WeightDetails()

{

$(".overlay").css("display", "block");

   $('#wc_detail tbody').empty();

   my_Date = new Date();

	$.ajax({

		type:"GET",

		url: base_url+"index.php/admin_ret_catalog/wastage_mc_settings/get_wastage_mc_setting_WeightDetails/"+ctrl_page[3]+"?nocache=" + my_Date.getUTCSeconds(),

		cache:false,

		dataType:"JSON",

		success:function(data){

			var html='';

			if(data.length > 0){

				var i = 1;

				$.each(data,function(key,val){

					html+="<tr id='wc"+i+"'>"

						+"<td>"+i+"</td><td><input name='wcrange["+i+"][from_weight]' step='.001' id='from_weight"+i+"' class='form-control from_weight' placeholder='From weight' type='number' value='"+val.wc_from_weight+"'></td>"

						+"<td><input name='wcrange["+i+"][to_weight]'  step='.001' id='to_weight"+i+"' class='form-control to_weight' placeholder='To weight' type='number' value='"+val.wc_to_weight+"'></td>"

						+"<td style='width: 90px;'><input name='wcrange["+i+"][wc_method]' id='wc_method_pc"+i+"' class='wc_method_pc' type='radio' "+(val.wc_method == 1 ? 'checked' : '')+" value='1'> <label for='wc_method_pc"+i+"'>Pc</label> &nbsp; <input name='wcrange["+i+"][wc_method]' id='wc_method_wt"+i+"' class='wc_method_wt' type='radio' "+(val.wc_method == 2 ? 'checked' : '')+" value='2'><label for='wc_method_wt"+i+"'>Wt</label></td>"

						+"<td><input name='wcrange["+i+"][wc_min]'  step='.001' id='wc_min"+i+"' class='form-control wc_min' placeholder='Min VA (%)' type='number' value='"+val.wc_min+"' "+(val.wc_method == 2 ? 'readonly' : '')+"></td>"

						+"<td><input name='wcrange["+i+"][wc_min_wt]'  step='.001' id='wc_min_wt"+i+"' class='form-control wc_min_wt' placeholder='Min VA(Wt)' type='number' value='"+val.wc_min_wt+"' "+(val.wc_method == 1 ? 'readonly' : '')+"></td>"

						+"<td><input name='wcrange["+i+"][wastage]'  step='.001' id='wastage"+i+"' class='form-control wastage' placeholder='Max V.A (%)' type='number' value='"+val.wc_percent+"' "+(val.wc_method == 2 ? 'readonly' : '')+"></td>"

						+"<td><input name='wcrange["+i+"][wc_wt]'  step='.001' id='wc_wt"+i+"' class='form-control wc_wt' placeholder='Max V.A (Wt)' type='number' value='"+val.wc_wt+"' "+(val.wc_method == 1 ? 'readonly' : '')+"></td>"

						+"<td><select name='wcrange["+i+"][mc_type]' id='mc_type"+i+"' class='form-control mc_type' style='width: 100px;'><option value=2 "+(val.mc_type == 2 ? 'selected' : '')+">Per Gram</option><option value=1 "+(val.mc_type == 1 ? 'selected' : '')+">Per Pcs</option></select></td>"

						

						+"<td><input name='wcrange["+i+"][mcrg_min]'  step='.001' id='mcrg_min"+i+"' class='form-control mcrg_min' placeholder='Min MC' type='number' value='"+val.mcrg_min+"'></td>"

						+"<td><input name='wcrange["+i+"][making_charge]' step='.001' id='making_charge"+i+"' class='form-control making_charge' placeholder='Max MC' type='number' value='"+val.mc+"'></td>"

						+"<td><a href='#' onClick='add_new_range();' class='btn btn-success btn-plus'><i class='fa fa-plus'></i></a><a href='#' onClick='remove_row($(this).closest(\"tr\"));' class='btn btn-danger btn-del'><i class='fa fa-trash'></i></a></td>"

						+"</tr>";

					i++;

			 });

			 $('#wc_detail').append(html);

			}

			$(".overlay").css("display", "none");

		}

	});

}

//v.a and m.c fixing end

/*Vendor Registration New*/

var totalTabs = $('.nav-pills li').length;

var currentTab = 1;

$('#add_newkar').on('click',function()

{

	if(validateKarigarDetails())

	{

		addNewKarigar(1);

	}

})

$('#add_karNext').on('click',function(e)

{

	if(validateKarigarDetails())

	{

		addNewKarigar(2);

	}

});

function addNewKarigar($type)

{

	var form_data=$('#karigar_general').serialize();

	my_Date = new Date();

	if(ctrl_page[2]=='add')

	{

		var url = base_url+ "index.php/admin_ret_catalog/karigar_general/save/?nocache=" + my_Date.getUTCSeconds();

	}

	else

	{

		var url = base_url+ "index.php/admin_ret_catalog/karigar_general/update/"+ctrl_page[3]+"?nocache=" + my_Date.getUTCSeconds();

	}

	$.ajax({

		url:url,

		data: form_data,

		type:"POST",

		dataType:"JSON",

		success:function(data)

		{

			if(data.status)

			{

				console.log(($type==1));

				if($type==1)

				{

					window.location.href = base_url+"index.php/admin_ret_catalog/karigar/list";

				}

				else

				{

					currentTab +=1;

					var next_tab = $('.nav-pills > .active').next('li').find('a');

					if(next_tab.length > 0)

					{

						next_tab.trigger('click');

					}

					window.scrollTo({

						top: 100,

						left: 100,

						behavior: "smooth",

					  });

					$('.vendor_det').show();

				}

				$.toaster({ priority : 'success', title : 'Success', message : ''+"</br>"+data.message});

				$('#id_karigar').val(data.value);

				$('#add_newkar').prop('disabled',true);

				$('#add_karNext').prop('disabled',true);

			}

			else

			{

				$.toaster({ priority : 'Danger', title : 'Waring!', message : ''+"</br>"+data.msg});

			}

		}

	});

}

function validateKarigarDetails()

{

	var validate = true;

	var user_type = $("input[name='karigar[user_type]']:checked").val();

	if ($('#first_name').val() == '')

	{

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Enter First Name..' });

		validate = false;

	}

	else if(user_type == 1 && ($('#company_karigar').val() == '' ))

	{

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Enter Company Name..' });

		validate = false;

	}

	else if(user_type == 1 && $('#gst_number_karigar').val() == ''){

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Enter GST no..' });

		validate = false;

	}

	else if($('#karigar_mobile').val() == ''){

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Enter Karigar Mobile..' });

		validate = false;

	}

	else if($('#address1').val() == ''){

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Enter Address 1..' });

		validate = false;

	}

	else if($('#id_country').val() == '' || $('#ed_id_country').val() == ''){

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Select Country..' });

		validate = false;

	}

	else if($('#id_state').val() == '' || $('#ed_id_state').val() == ''){

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Select State..' });

		validate = false;

	}

	else if($('#id_city').val() == '' || $('#ed_id_city').val() == ''){

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Select City..' });

		validate = false;

	}

	else if($('#kar_pincode').val() == ''){

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Enter PIN Code..' });

		validate = false;

	}

	return validate;

}

$('#add_NewcontractPrice').on('click',function(e)

{

	if(validateOrderDetailRow())

	{

		add_NewContractPrice(1)

	}

	else

	{

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Wastages Not Added.Please Fill The Required Fields..' });

	}

})

$('#add_contractPriceNext').on('click',function(e)

{

	if(validateOrderDetailRow())

	{

		add_NewContractPrice(2)

	}

	else

	{

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Wastages Not Added.Please Fill The Required Fields..' });

	}

})

function add_NewContractPrice($type)

{

	var form_data=$('#karigar_general').serialize();

	my_Date = new Date();

	if(ctrl_page[2]=='add')

	{

		var url = base_url+ "index.php/admin_ret_catalog/karigar_wastage/save/?nocache=" + my_Date.getUTCSeconds();

	}

	else

	{

		var url = base_url+ "index.php/admin_ret_catalog/karigar_wastage/update/"+ctrl_page[3]+"?nocache=" + my_Date.getUTCSeconds();

	}

	$.ajax({

		url:url,

		data: form_data,

		type:"POST",

		dataType:"JSON",

		success:function(data)

		{

			console.log(data.status);

			if(data.status)

			{

				if($type==1)

				{

					window.location.href = base_url+"index.php/admin_ret_catalog/karigar/list";

				}

				else

				{

					currentTab +=1;

					var next_tab = $('.nav-pills > .active').next('li').find('a');

					if(next_tab.length > 0)

					{

						next_tab.trigger('click');

					}

				}

				$.toaster({ priority : 'success', title : 'Success', message : ''+"</br>"+data.message});

				$('#id_karigar').val(data.value);

				$('#add_NewcontractPrice').prop('disabled',true);

				$('#add_contractPriceNext').prop('disabled',true);

			}

			else

			{

				$.toaster({ priority : 'Danger', title : 'Waring!', message : ''+"</br>"+data.msg});

			}

		}

	});

}

$('#add_NewStone').on('click',function(e)

{

	if(validateStoneDetailRow())

    {

		add_NewKarStones(1);

	}

	else

	{

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Stones Not Added.Please Fill The Required Fields..' });

	}

})

$('#add_StoneNext').on('click',function(e)

{

	if(validateStoneDetailRow())

	{

		add_NewKarStones(2);

	}

	else

	{

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Stones Not Added.Please Fill The Required Fields..' });

	}

});

function add_NewKarStones($type)

{

	var form_data=$('#karigar_general').serialize();

	my_Date = new Date();

	if(ctrl_page[2]=='add')

	{

		var url = base_url+ "index.php/admin_ret_catalog/karigar_stone/save/?nocache=" + my_Date.getUTCSeconds();

	}

	else

	{

		var url = base_url+ "index.php/admin_ret_catalog/karigar_stone/update/"+ctrl_page[3]+"?nocache=" + my_Date.getUTCSeconds();

	}

	$.ajax({

		url:url,

		data: form_data,

		type:"POST",

		dataType:"JSON",

		success:function(data)

		{

			console.log(data.status);

			if(data.status)

			{

				if($type==1)

				{

					window.location.href = base_url+"index.php/admin_ret_catalog/karigar/list";

				}

				else

				{

					currentTab +=1;

					var next_tab = $('.nav-pills > .active').next('li').find('a');

					if(next_tab.length > 0)

					{

						next_tab.trigger('click');

					}

				}

				$.toaster({ priority : 'success', title : 'Success', message : ''+"</br>"+data.message});

				$('#id_karigar').val(data.value);

				$('#add_NewStone').prop('disabled',true);

				$('#add_StoneNext').prop('disabled',true);

			}

			else

			{

				$.toaster({ priority : 'Danger', title : 'Waring!', message : ''+"</br>"+data.msg});

			}

		}

	});

}

$('#add_NewKyc').on('click',function()

{

	if(validateKYCDetailRow())

	{

		add_NewKycDetails()

	}

	else

	{

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Fill The Required Fields..' });

	}

});

function add_NewKycDetails()

{

	var form_data=$('#karigar_general').serialize();

	my_Date = new Date();

	if(ctrl_page[2]=='add')

	{

		var url = base_url+ "index.php/admin_ret_catalog/karigar_kyc/save/?nocache=" + my_Date.getUTCSeconds();

	}

	else

	{

		var url = base_url+ "index.php/admin_ret_catalog/karigar_kyc/update/"+ctrl_page[3]+"?nocache=" + my_Date.getUTCSeconds();

	}

	$.ajax({

		url:url,

		data: form_data,

		type:"POST",

		dataType:"JSON",

		success:function(data)

		{

			if(data.status)

			{

				window.location.href = base_url+"index.php/admin_ret_catalog/karigar/list";

			}

		}

	});

}

/*Vendor Registration New*/

$(document).on('click', "#shape_list a.btn-edit", function(e){

	$("#ed_shape").val('');

	e.preventDefault();

	id=$(this).data('id');

	get_shape(id);

	$("#edit-id").val(id);

});

$("#add_newshape").on('click',function(){

	if($('#shape').val() != '')

	{

		add_shape($('#shape').val(),$('#desc').val());

		$('#shape').val('');

	}

	else if($("#shape").val() == ''){

	msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Shape name.</div>';

	$("div.overlay").css("display", "none");

	$('#error-msg').html(msg);

    return false;

   }

});

//update shape

$("#update_shape").on('click',function(){

	var shape=($("#ed_shape").val());

	var id=$("#edit-id").val();

	var desc=$("#ed_desc").val();

	if($("#ed_shape").val() != '')

	{

	update_shape(shape,id,desc);

	}

	else if($("#ed_shape").val() == ''){

	msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Shape Name.</div>';

	$("div.overlay").css("display", "none");

	$('#error').html(msg);

	return false;

		}

});

//shape table Starts

function set_shape_table()

{

	my_Date = new Date();

	$.ajax({

			 url:base_url+ "index.php/admin_ret_catalog/shape/ajax?nocache=" + my_Date.getUTCSeconds(),

			 dataType:"JSON",

			 type:"POST",

			 success:function(data){

		 	 console.log(data);

				 var shape 	= data.shape;

				 var access = data.access;

				 $('#total_shape').text(shape.length);

				 if(access.add == '0')

				 {

					$('#add_shape').attr('disabled','disabled');

				 }

			 var oTable = $('#shape_list').DataTable();

				 oTable.clear().draw();

						 if (shape!= null && shape.length > 0)

							{

							oTable = $('#shape_list').dataTable({

									"bDestroy": true,

									"bInfo": true,

									"bFilter": true,

									"bSort": true,

									"order": [[ 0, "desc" ]],

									"aaData": shape,

									"aoColumns": [	{ "mDataProp": "shape_id" },

													{ "mDataProp": "name" },

													{ "mDataProp": function ( row, type, val, meta ){

					                	    		status_url =(access.edit =='1')? base_url+"index.php/admin_ret_catalog/shape_status/"+(row.status==1?0:1)+"/"+row.shape_id:'#';

							                		return "<a href='"+status_url+"'><i class='fa "+(row.status==1?'fa-check':'fa-remove')+"' style='color:"+(row.status==1?'green':'red')+"'></i></a>"

						                				}

								              		},

													{ "mDataProp": function ( row, type, val, meta ) {

														 id= row.shape_id;

														 edit_target=(access.edit=='0'?"":"#confirm-edit");

														 delete_url=(access.delete=='1' ? base_url+'index.php/shape/delete/'+id : '#' );

														 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

														//  action_content='<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i>Edit</a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i>Delete</a>'

														var action_content=''

														if(access.edit == '1'){

														

											        	action_content+=  '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i>Edit</a>' ;

														

													    }

														if(access.delete == '1'){

														 

														 action_content+='<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i>Delete</a>'

														

														}

														 return action_content;

														 }

													}]

								});

							}

					  },

					  error:function(error)

					  {

						 $("div.overlay").css("display", "none");

					  }

			      });

}

//to add Shape

function add_shape(shape,desc)

{

	my_Date = new Date();

    var sp=shape;

	$.ajax({

		data:{"name":sp,"description":desc},

		url: base_url+"index.php/admin_ret_catalog/shape/Add?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		cache:false,

		success:function(data){

			console.log(data)

			$('#shape').val('');

			 msg='<div class = "alert alert-success"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Success!</strong>shape added successfully.</div>';

			$('#chit_alert').html(msg);

			location.reload(true);

		}

	});

}

//update clarity

function update_shape(shape,id,desc)

{

	 $("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		data:{"name":shape,"description":desc},

		url: base_url+"index.php/admin_ret_catalog/shape/Update/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		cache: false,

		success:function(data){

		console.log(data);

		  $("div.overlay").css("display", "none");

			window.location.reload(true);

		}

	});

}

//get shape by id

function get_shape(id)

{

   my_Date = new Date();

	$.ajax({

		type:"GET",

		url: base_url+"index.php/admin_ret_catalog/shape/Edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		cache:false,

		dataType:"JSON",

		success:function(data){

			console.log(data);

			sp=data.name;

		    $('#ed_shape').val(sp);

		    $('#ed_desc').val(data.description);

		}

	});

}

//shape table Ends

/* Reorder Settings New */

$('#add_reorderitems_info').on('click',function()

{

	// var isWarningShown = true;

	if($('#product_select_reorder').val()=='' ||$('#product_select_reorder').val()==null)

	{

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Product..'});

	}

	else if($('#des_select_reorder').val()==''|| $('#des_select_reorder').val()==null)

	{

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Design..'});

	}

	else

	{

		if(validateSettingsDetailRow())

		{

			create_new_empty_reorder_row()

		}

	}

});

function add_new_reorder(curRow)

{

	console.log('add_reorder');

	if(validateSettingsDetailRow())

	{

		create_new_empty_reorder_row()

	}

}

function create_new_empty_reorder_row()

{

	var newRow="";

	var a = $("#i_increment").val();

	var i = ++a;

	$("#i_increment").val(i);

	newRow+='<tr id="detail'+i+'" class="'+i+'">'

			  +'<td><select class="form-control reorder_section_select" name="pieces['+i+'][id_section]" id="reorder_section_select" style="width: 100%"></select><input type="hidden" id="id_section" name=""></td>'

			  +'<td><select class="form-control reorder_branch_select" name="pieces['+i+'][id_branch][]" id="reorder_branch_select" style="width: 80%" multiple></select><input type="hidden" id="id_branch" name=""></td>'

				+'<td><select class="form-control reorder_sub_design" name="pieces['+i+'][id_sub_design][]" id="reorder_sub_design" style="width: 100%" multiple></select><input type="hidden" id="id_sub_design" name=""></td>'

				+'<td><select class="form-control reorder_wt_select" id="reorder_wt_select" name="pieces['+i+'][weight_range]" style="width: 100%;"></select><input type="hidden" id="weight_range" name=""></td>'

				+'<td><select class="form-control reorder_size"  name="pieces['+i+'][id_size]" id="reorder_size" style="width: 100%;"></select></td>'

				+'<td><input type="number" class="form-control min_pcs" name="pieces['+i+'][min_pcs]" value="" placeholder="Enter Min Pcs"></td>'

				+'<td><input type="number" class="form-control max_pcs" name="pieces['+i+'][max_pcs]"  value="" placeholder="Enter Max Pcs"></td>'

				// +'<td><button class="add_new_reorder btn btn-success" id="add_new_reorder" name="add" type="button"><i class="fa fa-plus"></i></button><a href="#" onClick="remove_reorder_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'

				// +'<td><a href="#" onClick="add_new_reorder($(this).closest(\'tr\'));" class=" btn btn-success"><i class="fa fa-plus"></i></a><a href="#" onClick="remove_reorder_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'

				+'<td><a href="#" onclick="add_new_reorder($(this).closest(\'tr\'));" class="btn btn-success small-btn"><i class="fa fa-plus"></i></a><a href="#" onclick="remove_reorder_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del small-btn"><i class="fa fa-trash"></i></a></td>'

			+'</tr>';

			$('#total_reorder_items tbody ').append(newRow);

			$('.reorder_branch_select').append(

				$("<option></option>")

				.attr("value", "")

				.text('-Choose-')

			);

			$('.reorder_section_select').append(

				$("<option></option>")

				.attr("value", "")

				.text('-Choose-')

			);

			$('.reorder_sub_design').append(

				$("<option></option>")

				.attr("value", "")

				.text('-Choose-')

			);

			$('.reorder_wt_select').append(

				$("<option></option>")

				.attr("value", "")

				.text('-Choose-')

			);

			$('.reorder_size').append(

				$("<option></option>")

				.attr("value", "")

				.text('-Choose-')

			);

			console.log(reorder_branch);

			$.each(reorder_branch,function(key,item)

			{

				$('.reorder_branch_select').append(

					$("<option></option>")

					.attr("value",item.id_branch)

					.text(item.name)

				);

			});

			$.each(section_details,function(key,item)

			{

				$('.reorder_section_select').append(

					$("<option></option>")

					.attr("value",item.id_section)

					.text(item.section_name)

				);

			});

			$.each(reorderitems_sub_design, function (key, item)

			{

				$('.reorder_sub_design').append(

				$("<option></option>")

				.attr("value", item.id_sub_design)

				.text(item.sub_design_name)

				);

			});

			$.each(reorderitems_weight, function (key, item)

			{

				$('.reorder_wt_select').append(

				$("<option></option>")

			    .attr("value", item.id_weight)

		        .text(item.value)

				);

			});

			$.each(reorder_size, function (key, item) {

				$('.reorder_size').append(

				$("<option></option>")

				.attr("value", item.id_size)

				.text(item.name)

				);

			});

			$('#total_reorder_items > tbody').find('.reorder_branch_select').select2();

			$('#total_reorder_items > tbody').find('.reorder_sub_design').select2();

			$('#total_reorder_items > tbody').find('.reorder_section_select').select2();

			$('#total_reorder_items > tbody').find('.reorder_wt_select').select2();

			$('#total_reorder_items > tbody').find('.reorder_size').select2();

			$('#total_reorder_items > tbody').find('.reorder_branch_select').select2({

				placeholder: "Branch",

				allowClear: true

			});

			$('#total_reorder_items > tbody').find('.reorder_section_select').select2({

				placeholder: "Section",

				allowClear: true

			});

			$('#total_reorder_items > tbody').find('.reorder_sub_design').select2({

				placeholder: "Sub Design",

				allowClear: true

			});

			$('#total_reorder_items > tbody').find('.reorder_wt_select').select2({

				placeholder: "Weight Range",

				allowClear: true

			});

			$('#total_reorder_items > tbody').find('.reorder_size').select2({

				placeholder: "Size",

				allowClear: true

			});

}

var isWarningShown = false;

function validate_isDuplicateRow(curRow) {

    console.log(curRow);

    console.log('duplicate');

    var validate = true;

    var min_pcs = parseFloat(curRow.find('.min_pcs').val());

    var max_pcs = parseFloat(curRow.find('.max_pcs').val());

    var branch = curRow.find('#reorder_branch_select').val();

    var section = curRow.find('.reorder_section_select').val();

    var sub_design = curRow.find('#reorder_sub_design').val();

    var size = curRow.find('.reorder_size').val();

    var weight = curRow.find('.reorder_wt_select').val();

    // Reset isWarningShown to false before checking each row

    isWarningShown = false;

    $('#total_reorder_items > tbody > tr').each(function (key, item) {

        var areFieldsEqual =

            ($(this).find('.reorder_section_select').val() === section &&

                $(this).find('#reorder_branch_select').val().join(',') === branch.join() &&

                $(this).find('#reorder_sub_design').val().join(',') === sub_design.join() &&

                $(this).find('.reorder_size').val() === size &&

                $(this).find('.reorder_wt_select').val() === weight &&

                parseFloat($(this).find('.min_pcs').val()) === min_pcs &&

                parseFloat($(this).find('.max_pcs').val()) === max_pcs);

        console.log(key);

        if ($(this)[0] === curRow[0]) {

            return;

        }

        console.log(areFieldsEqual);

		if (areFieldsEqual) {

			validate = false;

			if (!isWarningShown) {

				var msg = '<div class="alert alert-danger" id="chit_alert"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>Warning!</strong> Fields are equal in multiple rows</div>';

				$('#chit_alert').html(msg).fadeIn();

				setTimeout(function () {

		 			$('#chit_alert').fadeOut('slow', function () {

						$(this).empty(); // Remove the content after fading out

					});

					isWarningShown = true; // Set the global flag to true

				}, 1000); // Adjust the delay as needed

			}

		}

    });

    return validate;

}

function remove_reorder_row(curRow)

{

    curRow.remove();

}

function get_reorder_branch_details()

{

	// $('.reorder_branch_select option').remove();

	$.ajax({

		type: 'GET',

		url: base_url+'index.php/branch/branchname_list',

		dataType:'json',

		success:function(data){

			reorder_branch=data.branch;

			var id_branch =  $('#id_branch').val();

			$.each(data.branch, function (key, item)

			{

				$(".reorder_branch_select").append(

					$("<option></option>")

					.attr("value", item.id_branch)

					.text(item.name )

				);

			});

			$(".reorder_branch_select").select2({

				placeholder: "Select Branch",

				allowClear: true

			});

			if($('.reorder_branch_select').length)

			{

				$(".reorder_branch_select").select2("val",(id_branch!='' && id_branch>0?id_branch:''));

			}

		}

	});

}

function get_ProductforReorder()

{

	// $('#product_select_reorder option').remove();

	my_Date = new Date();

	$.ajax({

		type: 'POST',

		url: base_url+"index.php/admin_ret_catalog/ret_product/active_list/?nocache=" + my_Date.getUTCSeconds(),

		data :{'id_category':''},

		dataType:'json',

		success:function(data)

		{

			reorder_product=data;

			console.log(reorder_product);

			var id =  $("#product_select_reorder").val();

			$.each(data, function (key, item) {

				$("#product_select_reorder").append(

					$("<option></option>")

					.attr("value", item.pro_id)

					.text(item.product_name)

				);

			});

			$("#product_select_reorder").select2(

			{

				placeholder:"Select Product",

				allowClear: true

			});

			$("#product_select_reorder").select2("val",(id!='' && id>0?id:''));

			$(".overlay").css("display", "none");

		}

	});

}

$('#product_select_reorder').on('change',function()

{

	if(this.value!='')

    {

		$.each(reorder_product, function (key, item) {

			$("#reorder_based_on").val(item.reorder_based_on);

			// console.log(item.reorder_based_on);

		});

        get_designforReorder(this.value);

		get_Activesize_forReorder(this.value);

    }

	// $("#total_reorder_items tbody tr").empty();

});

function get_designforReorder(id_product)

{

    // $('#des_select_reorder option').remove();

    $.ajax({

	type: 'POST',

	url: base_url+'index.php/admin_ret_catalog/get_ProductDesign',

	dataType:'json',

	data:{'id_product':id_product},

	success:function(data){

		var id =  $("#des_select_reorder").val();

		$.each(data, function (key, item) {

		   $("#des_select_reorder").append(

		   $("<option></option>")

		   .attr("value", item.design_no)

		   .text(item.design_name)

		   );

		});

		$("#des_select_reorder").select2(

		{

			placeholder:"Select Design",

			allowClear: true

		});

		$("#des_select_reorder").select2("val",(id!='' && id>0?id:''));

		$(".overlay").css("display", "none");

	}

	});

}

function get_Activesize_forReorder(id_product)

{

	$('.reorder_size option').remove();

    $.ajax({

        type: 'POST',

        url: base_url+'index.php/admin_ret_catalog/get_Activesize',

        dataType:'json',

        data:{'id_product':id_product},

        success:function(data){

		reorder_size = data;

        var size = $('#id_size').val();

        $.each(data, function (key, item) {

        $(".reorder_size").append(

        $("<option></option>")

        .attr("value", item.id_size)

        .text(item.name )

        );

        });

        $(".reorder_size").select2({

        placeholder: "Select Size",

        allowClear: true

        });

		if($(".reorder_size").length)

        {

            // $(".reorder_size").select2("val",'');

        }

        }

    });

}

$('#des_select_reorder').on('change',function()

{

	if(this.value!='')

	{

		get_reorder_sub_designs(this.value);

	}

})

function get_reorder_sub_designs()

{

	$('.reorder_sub_design option').remove();

	$.ajax({

    	type: 'POST',

    	url: base_url+'index.php/admin_ret_catalog/get_ActiveSubDesigns',

    	data :{'id_product':$('#product_select_reorder').val(),'design_no':$('#des_select_reorder').val()},

    	dataType:'json',

    	success:function(data){

			reorderitems_sub_design=data;

			var id =  $("#id_sub_design").val();

			$.each(data, function (key, item) {

        		    $(".reorder_sub_design").append(

        		    $("<option></option>")

        		    .attr("value", item.id_sub_design)

        		    .text(item.sub_design_name)

        		    );

        		});

        		$(".reorder_sub_design").select2(

        		{

        			placeholder:"Select Sub Design",

        			allowClear: true

        		});

				$(".reorder_sub_design").select2("val",(id!='' && id>0?id:''));

    		}

    	});

}

// $(document).on('change','.reorder_sub_design',function()

// {

// 	var row = $(this).closest('tr');

// 	if(this.value != '')

// 	{

// 		get_wtrange_forReoreder(row,this.value);

// 	}

// });

$(document).on('change','#product_select_reorder',function()

{

	var row = $(this).closest('tr');

	if(this.value != '')

	{

		get_wtrange_forReoreder(row,this.value);

	}

});

function get_wtrange_forReoreder(curRow,product)

{

	$(".overlay").css('display','block');

    my_Date = new Date();

	$.ajax({

	type: 'POST',

	url: base_url+'index.php/admin_ret_catalog/reorder_settings/weight_range?nocache='+ my_Date.getUTCSeconds(),

	dataType:'json',

	data:{'id_product': product,'id_design':"",'id_sub_design':""},

	success:function(data){

		reorderitems_weight = data;

		var id =  $("#wt_range").val();

		// curRow.find("#reorder_wt_select option").remove();

		$('#reorder_wt_select').append(

			$("<option></option>")

			.attr("value", "")

			.text('-Choose-')

		);

		$.each(data, function (key, item)

		{

			$("#reorder_wt_select").append(

		    $("<option></option>")

		    .attr("value", item.id_weight)

		    .text(item.value)

		    );

		});

		$("#reorder_wt_select").select2(

		{

			placeholder:"Select Weight Range",

			allowClear: true

		});

			// curRow.find("#reorder_wt_select").val(curRow.find("#reorder_wt_select").val());

			$(".overlay").css("display", "none");

			// $(".reorder_wt_select").select2("val",(id!='' && id >0?id:''));

		}

	});

}

 $('#add_reordersettings_new').on('click',function(e){

	if(ctrl_page[1]== 'reorder_settings' && ctrl_page[2]=='edit')

	{

		$('#add_reordersettings_new').prop('disabled',true);

    }

	var wt_range=$('.reorder_wt_select').val();

	var product=$('#product_select_reorder').val();

	var id_design=$('#des_select_reorder').val();

	var id_sub_design=$('.reorder_sub_design').val();

	var size=$('.reorder_size').val();

	var branch=$('.reorder_branch_select').val();

	var section=$('.reorder_section_select').val();

	var reorder=$('#reorder_based_on').val();

    if(branch=='' || branch==null)

	{

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select branch..' });

	}

	else if(product=='' || product==null)

	{

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Product..' });

	}

	else if(id_design=='' || id_design==null)

	{

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Design..' });

	}

	else if(id_sub_design=='' || id_sub_design==null)

	{

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select SubDesign..' });

	}

	else if(section=='' || section==null)

	{

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Section..' });

	}

	else

	{

	    if($('#branch_settings').val()==1)

	    {

	        if(validateSettingsDetailRow())

            {

                add_reordersettings_new();

            }

            // else

            // {

			// 	alert("Please Fill All Required Fields");

            // }

	    }

	}

});

function add_reordersettings_new()

{

	var form_data=$('#add_reorder').serialize();

	if(ctrl_page[2]=='new')

	{

		var url = base_url+ "index.php/admin_ret_catalog/reorder_settings/add/?nocache=" + my_Date.getUTCSeconds();

	}

	else

	{

		var url = base_url+ "index.php/admin_ret_catalog/reorder_settings/update/"+ctrl_page[3]+"?nocache=" + my_Date.getUTCSeconds();

	}

	console.log(form_data);

	my_Date = new Date();

	$.ajax({

		url:url,

	     data: form_data,

		type:"POST",

		dataType: 'json',

		cache:false,

		success:function(data){

			if(data.status)

		    {

		        if($('#branch_settings').val()==1)

		        {

		            $('#total_items > tbody > tr').each(function(index, tr) {

                         $(this).find('.min_pcs').val('');

                         $(this).find('.max_pcs').val('');

                     });

		        }

				location.href=base_url+'index.php/admin_ret_catalog/reorder_settings/list';

				$("div.overlay").css("display", "none");

		    }else

		    {

				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.msg });

		    }

			$('#chit_alert').html(msg);

		}

	});

}

/* Reorder Settings New */

$('#first_name').on('keyup',function(e)

{

	var kar_name = $('#first_name').val();

	if(kar_name.length>1)

	{

		getExistingKarigars(kar_name);

	}

});

function getExistingKarigars(searchTxt)

{

	my_Date = new Date();

	$.ajax({

        url: base_url+'index.php/admin_ret_catalog/getExistingKarigars/?nocache=' + my_Date.getUTCSeconds(),

        dataType: "json",

        method: "POST",

        data: {'searchTxt': searchTxt},

		success: function (data)

		{

			console.log(data);

			$( "#first_name" ).autocomplete(

			{

				source : data,

				open : function(event,i)

				{

					event.preventDefault();

					$("#first_name").val(i.item.label);

				}

			});

		}

	});

}

$('#select_all_stn').click(function(event) {

	$("tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));

	event.stopPropagation();

});

/*Selling Diamond Rate Master*/

$("#add_selling_diamond_rate").on("click",function()

{

	if(validate_selling_diamond_rate_table())

	{

		create_selling_diamond_rate_table_row();

	}

});

function validate_selling_diamond_rate_table()

{

	var validate= true;

	$('#diamond_selling_rate_table > tbody  > tr').each(function(index, row)

	{

		var tr=$(this);

		if(tr.find('.from_cent ').val() == '' || tr.find('.to_cent ').val() == ''  || tr.find('.rate').val() == '' || ! validate_selling_diamond_rate_table_row(tr))

		{

			validate= false;

			return validate;

		}

	});

	return validate;

}

function create_selling_diamond_rate_table_row()

{

	var trHtml = '';

	var row_count = $('#diamond_selling_rate_table >tbody > tr').length + 1;

	trHtml+='<tr id="diamond_selling_rate_'+row_count+'" >'

		+'<td><input class ="from_cent" type="number" name="sel_rate_list[from_cent][]"></td>'

		+'<td><input class ="to_cent" type="number" name="sel_rate_list[to_cent][]"></td>'

		+'<td><input class ="rate" type="number" name="sel_rate_list[rate][]"></td>'

		+"<td><div><button class='add btn btn-success' name='add'type='button'><i class='fa fa-plus'></i></button>"

		+"<button class='delete btn btn-danger' name='delete'type='button'><i class='fa fa-trash'></i></button></div></td>"

	+'</tr>';

	$('#diamond_selling_rate_table >tbody').append(trHtml);

}

function validate_selling_diamond_rate_table_row(curRow)

{

	var row_id = curRow.attr('id');

	var from_wt = parseFloat (curRow.find('.from_cent').val());

	var to_wt = parseFloat(curRow.find('.to_cent').val());

	var validate = true;

	if(curRow.find('.from_cent ').val() != '' && curRow.find('.to_cent ').val() != '' && from_wt <= to_wt)

	{

		$('#diamond_selling_rate_table > tbody  > tr').each(function(index, tr)

		{

			if( ( parseFloat ($(this).find('.from_cent').val())  <= from_wt &&  parseFloat ($(this).find('.to_cent').val())  >= from_wt ||  parseFloat ($(this).find('.from_cent').val())  <= to_wt &&  parseFloat ($(this).find('.to_cent').val())  >= to_wt) && row_id != $(this).attr('id') )

			{

				validate = false;

				$.toaster({ priority : 'danger', title : 'Warning!', message : "Invalid From and To Weight "});

				return validate;

			}

		});

	}

	else if( from_wt >= to_wt )

	{

		validate = false;

		$.toaster({ priority : 'danger', title : 'Warning!', message : "Invalid From and To Weight "});

	}

	else

	{

		validate = false;

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill the required fields....'});

	}

	return validate;

}

$("#diamond_selling_rate_table").on('click','.delete',function()

{

	$(this).closest('tr').remove();

});

$("#diamond_selling_rate_table").on('click','.add',function()

{

	if(validate_selling_diamond_rate_table())

 	{

		create_selling_diamond_rate_table_row();

 	}

});

$('#selling_diamond_rate_submit').on('click',function()

{

	var quality_code_id=$('#quality_code_id').val();

	var effective_date=$('#effective_date').val();

	var allow_submit=true;

	if($("#quality_sel").val()==null || $("#quality_sel").val()=="" || $("#quality_sel").val() == 0)

	{

		$.toaster({ priority : 'danger', title : 'Warning!', message : "Enter the proper quality code name."});

		allow_submit=false;

	}

    else if($("#effective_date").val()==null || $("#effective_date").val()=="" || $("#effective_date").val() == 0)

	{

		$.toaster({ priority : 'danger', title : 'Warning!', message : "Select the Proper effective time."});

		allow_submit=false;

	}

	else if($(".from_cent").val()==null || $(".from_cent").val()=="" || $(".from_cent").val() == 0)

	{

		$.toaster({ priority : 'danger', title : 'Warning!', message : "Enter the proper from cent."});

		allow_submit=false;

	}

  	if(allow_submit)

	{

		my_Date = new Date();

		//$("div.overlay").css("display", "block");

		var form_data=$('#selling_diamond_rate_list').serialize();

	    if(ctrl_page[2]=='add')

		{

			

			var url = base_url+ "index.php/admin_ret_catalog/selling_diamond_rate/save/?nocache=" + my_Date.getUTCSeconds();

		}

	    else

		{

			

			var url = base_url+ "index.php/admin_ret_catalog/selling_diamond_rate/update/"+ctrl_page[3]+"?nocache=" + my_Date.getUTCSeconds();

	   	}

		$.ajax({

			url:url,

			data: form_data,

			type:"POST",

			dataType:"JSON",

			success:function(data)

			{

				if(data.length)

				{

					window.location.href = base_url+'index.php/admin_ret_catalog/selling_diamond_rate/list';

					$.toaster({ priority : 'success', title : 'Warning!', message :data.message});

					$("div.overlay").css("display", "none");

				}

				else

				{

					$.toaster({ priority : 'success', title : 'Warning!', message :data.message});

					window.location.href= base_url+'index.php/admin_ret_catalog/selling_diamond_rate/list';

				}

				$("div.overlay").css("display", "none");

			},

			error:function(error)

			{

				$("div.overlay").css("display", "none");

			}

		});

	}

	else{

		// $('#diamond_rate_submit').prop('disabled',false);

		$("div.overlay").css("display", "none");

	}

});

function set_selling_diamond_rate_table()

{

	//alert();

	my_Date = new Date();

	$.ajax({

		url:base_url+ "index.php/admin_ret_catalog/selling_diamond_rate?nocache=" + my_Date.getUTCSeconds(),

		data:{ 'quality_code_id':$('#quality_sel').val()},

		dataType:"JSON",

		type:"POST",

		success:function(data){

			var rate 	    = data.rate;

			var access		= data.access;

			$('#total_rate').text(rate.length);

			if(access.add == '0')

			{

				$('#add_selling_rate').attr('disabled','disabled');

			}

			var oTable = $('#selling_rate_list').DataTable();

			oTable.clear().draw();

			if (rate!= null && rate.length > 0)

			{

				oTable = $('#selling_rate_list').dataTable({

					"bDestroy": true,

					"bInfo": true,

					"bFilter": true,

					"bSort": true,

					"dom": 'lBfrtip',

					"order": [[ 0, "desc" ]],

					"buttons" : ['excel','print'],

					"tableTools": { "buttons": [ { "sExtends": "xls",

					"oSelectorOpts": { page: 'current'

					} },

					{ "sExtends": "pdf",

					"oSelectorOpts": { page: 'current'

					} } ] },

					"aaData"  : rate,

					"aoColumns": [	{ "mDataProp": "selling_rate_id"},

									{ "mDataProp": "code"},

									{ "mDataProp": "effective_date"},

									{ "mDataProp": function ( row, type, val, meta ){

										status_url =(access.edit =='1')? base_url+"index.php/admin_ret_catalog/selling_diamond_rate/update_status/"+row.selling_rate_id+"/"+(row.rate_status==1?0:1)+"/"+row.quality_code_id:'#';

										return "<a href='"+status_url+"'><i class='fa "+(row.rate_status==1?'fa-check':'fa-remove')+"'style='color:"+(row.rate_status==1?'green':'red')+"'></i></a>"

										}

										},

									{ "mDataProp": function ( row, type, val, meta ) {

										id= row.selling_rate_id;

										edit_url=(base_url+'index.php/admin_ret_catalog/selling_diamond_rate/edit/'+id);

										delete_confirm=(access.delete=='1' ?'#confirm-delete':'');

										delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/selling_diamond_rate/delete/'+id : '#' );

										 action_content=(access.edit =='1' ? '<a href='+edit_url+' class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'><i class="fa fa-edit" ></i></a>':'')+(access.delete =='1' ? '<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>':"")

										// var action_content=''

										// 	if(access.edit == '1'){

																		

										// 		action_content+=  '<a href='+edit_url+' class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'><i class="fa fa-edit" ></i></a> ' ;

												

										// 		}

										// 		if(access.delete == '1'){

												

										// 		action_content+='<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

										// 		}

										return action_content;

										}

									}]

				});

			}

		},

		error:function(error)

		{

			$("div.overlay").css("display", "none");

		}

	});

}

/*Selling Diamond Rate Master*/

function ProductStoneValidation(curRow,stone_type)

{

	console.log('curRow',curRow);

	console.log('stone_type',stone_type);

	curRow.find('.stn_calc_type').prop('disabled',true);

	curRow.find('.prod_uom_id').prop('disabled',true);

	curRow.find('.prod_stn_quality').prop('disabled',true);

	curRow.find('.prod_from_wt').prop('readonly',true);

	curRow.find('.prod_to_wt').prop('readonly',true);

	curRow.find('.stone_rate').prop('readonly',true);

	curRow.find('.kar_calc_type').prop('disabled',false);

	curRow.find('.pur_touch').prop('readonly',false);

	curRow.find('.calc_type').prop('disabled',false);

	curRow.find('.va_type').prop('disabled',false);

	curRow.find('.wast_percent').prop('readonly',false);

	curRow.find('.wast_wgt').prop('readonly',false);

	curRow.find('.mc_type').prop('disabled',false);

	curRow.find('.mc').prop('readonly',false);

	curRow.find('.purity').prop('disabled',false);

	if(stone_type==2) // For Diamond Product

	{

		curRow.find('.stn_calc_type').prop('disabled',false);

		curRow.find('.prod_uom_id').prop('disabled',false);

		curRow.find('.prod_stn_quality').prop('disabled',false);

		curRow.find('.prod_from_wt').prop('readonly',false);

		curRow.find('.prod_to_wt').prop('readonly',false);

		curRow.find('.stone_rate').prop('readonly',false);

		curRow.find('.kar_calc_type').prop('disabled',true);

		curRow.find('.pur_touch').prop('readonly',true);

		curRow.find('.calc_type').prop('disabled',true);

		curRow.find('.va_type').prop('disabled',true);

		curRow.find('.wast_percent').prop('readonly',true);

		curRow.find('.wast_wgt').prop('readonly',true);

		curRow.find('.mc_type').prop('disabled',true);

		curRow.find('.mc').prop('readonly',true);

		curRow.find('.purity').prop('disabled',true);

		curRow.find('.purity').select2("val","");

	}

	else if(stone_type==1)  // For Normal Stone Product

	{

		curRow.find('.stn_calc_type').prop('disabled',false);

		curRow.find('.prod_uom_id').prop('disabled',false);

		curRow.find('.stone_rate').prop('readonly',false);

		curRow.find('.prod_stn_quality').prop('disabled',true);

		curRow.find('.prod_from_wt').prop('readonly',true);

		curRow.find('.prod_to_wt').prop('readonly',true);

		curRow.find('.kar_calc_type').prop('disabled',true);

		curRow.find('.pur_touch').prop('readonly',true);

		curRow.find('.calc_type').prop('disabled',true);

		curRow.find('.va_type').prop('disabled',true);

		curRow.find('.wast_percent').prop('readonly',true);

		curRow.find('.wast_wgt').prop('readonly',true);

		curRow.find('.mc_type').prop('disabled',true);

		curRow.find('.mc').prop('readonly',true);

		curRow.find('.purity').prop('disabled',true);

		curRow.find('.purity').select2("val","");

	}

}

/*Stone Rate Settings*/

$('.add_stonerate_info').on('click',function()

{

	if(validateStoneRateRow())

	{

		Create_NewEmpty_StoneRateRow()

	}

});

function validateStoneRateRow()

{

    var validate = true;

	$('#total_stone_rate_items > tbody  > tr').each(function(index, tr) {

		validate_wt = validate_Diamondrate_wt_range($(this));

		if($(this).find('.branch_select').val() == "" || $(this).find('.stn_type').val() == "" || $(this).find('.stn_name').val() == "" || $(this).find('.uom_id').val() == null || $(this).find('.stn_calc_type').val() == "" || $(this).find('.min_rate').val() == "" || $(this).find('.max_rate').val() == "" || ! validate_wt)

		{

			validate = false;

			if(validate_wt){

				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill the required fields..'});

			}

			return validate;

		}

	});

	return validate;

}

function validate_Diamondrate_wt_range(curRow)

{

	var row_id = curRow.attr('id');

	var from_wt = parseFloat (curRow.find('.from_wt').val());

    var to_wt = parseFloat(curRow.find('.to_wt').val());

    var stn_name  = curRow.find('.stn_name ').val();

    var stn_quality  = curRow.find('.stn_quality').val();

	var id_branch = curRow.find('.branch_select ').val();

    var validate = true;

	if(curRow.find('.branch_select').val()!='' && curRow.find('.from_wt').val() != '' && curRow.find('.to_wt').val() != '' && curRow.find('.stn_quality').val() != '' && curRow.find('.stn_quality').val() != null && curRow.find('.stn_type').val() ==1 && from_wt != to_wt)

	{

		$('#total_stone_rate_items > tbody  > tr').each(function(index, tr)

		{

			if($(this).find('.stn_type ').val() ==1 &&  $(this).find('.stn_name ').val() == stn_name &&  $(this).find('.stn_quality').val() == stn_quality && $(this).find('.branch_select').val() == id_branch)

			{

				if( ( parseFloat ($(this).find('.from_wt').val())  <= from_wt &&  parseFloat ($(this).find('.to_wt').val())  >= from_wt ||  parseFloat ($(this).find('.from_wt').val())  <= to_wt &&  parseFloat ($(this).find('.to_wt').val())  >= to_wt) && row_id != $(this).attr('id') )

				{

					validate = false;

					$.toaster({ priority : 'danger', title : 'Warning!', message : "Invalid From and To Weight "});

					return validate;

				}

			}

		});

	} else if(curRow.find('.stn_type ').val() !=1){

        validate = true;

    }else if(from_wt == to_wt){

		validate = false;

		$.toaster({ priority : 'danger', title : 'Warning!', message : "Invalid From and To Weight "});

	}else{

        validate = false;

        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill the required fields....'});

    }

    return validate;

}

function Create_NewEmpty_StoneRateRow()

{

	var row="";

	var a = $("#i_increment").val();

	var i = ++a;

	$("#i_increment").val(i);

	row+='<tr id="detail'+i+'" class="'+i+'">'

		+'<td><select class="form-control branch_select" name="stn_rate['+i+'][id_branch]" id="branch_select" style="width: 100px"></select><input type="hidden" id="id_branch" name=""></td>'

		+'<td><select class="form-control stn_type " name="stn_rate['+i+'][stn_type]" id="stn_type" style="width: 100px"></select></td>'

		+'<td><select class="form-control stn_name" name="stn_rate['+i+'][stn_name]" id="stn_name" style="width: 100px"></select></td>'

		+'<td><select class="form-control stn_quality " name="stn_rate['+i+'][stn_quality]" id="stn_quality" style="width: 100px"></select></td>'

		+'<td><select class="form-control uom_id" name="stn_rate['+i+'][uom_id]" id="uom_id" style="width: 100px"></select></td>'

		+'<td><select class="form-control stn_calc_type" name="stn_rate['+i+'][stn_calc_type]" id="stn_calc_type" style="width: 100px"><option value="1" >Wt</option><option value="2" >PCS</option></select></td>'

		+'<td><input type="number" class="form-control from_wt" name="stn_rate['+i+'][from_wt]" value="" style="width: 100px"placeholder="Enter From Cent"></td>'

		+'<td><input type="number" class="form-control to_wt" name="stn_rate['+i+'][to_wt]" value="" placeholder="Enter To Cent"  style="width: 100px"></td>'

		+'<td><input type="number" class="form-control min_rate" name="stn_rate['+i+'][min_rate]" value="" placeholder="Enter Min Rate"  style="width: 100px"></td>'

		+'<td><input type="number" class="form-control max_rate" name="stn_rate['+i+'][max_rate]" value="" placeholder="Enter Max Rate"  style="width: 100px"></td>'

		+'<td><button type="button" class="btn btn-success addStoneRateSettings" onClick="add_stone_rate_row()"><i class="fa fa-plus"></i></button><a href="#" onClick="remove_stn_rate_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'

	+'</tr>';

	$('#total_stone_rate_items tbody ').append(row);

	$('#total_stone_rate_items tr:not(:last-child) .addStoneRateSettings').remove();

	$('.branch_select').append(

		$("<option></option>")

		.attr("value", 0)

		.text('-ALL-')

	);

	$.each(reorder_branch,function(key,item)

	{

		$('.branch_select').append(

			$("<option></option>")

			.attr("value",item.id_branch)

			.text(item.name)

		);

	});

	$('.stn_type').append(

		$("<option></option>")

		.attr("value", "")

		.text('-Choose-')

	);

	$.each(stone_types, function (key, item) {

		$('.stn_type').append(

		$("<option></option>")

		.attr("value", item.id_stone_type)

		.text(item.stone_type)

		);

	});

	$('.uom_id').append(

		$("<option></option>")

		.attr("value", "")

		.text('-Choose-')

	);

	$.each(uom_details, function (key, item) {

		$('.uom_id').append(

		$("<option></option>")

		.attr("value", item.uom_id)

		.text(item.code)

		);

	});

	$('.stn_quality').append(

		$("<option></option>")

		.attr("value", "")

		.text('-Choose-')

	);

	$.each(quality_code, function (key, item) {

		$('.stn_quality').append(

		$("<option></option>")

		.attr("value", item.quality_id)

		.text(item.code)

		);

	});

	$('#total_stone_rate_items > tbody').find('.branch_select').select2();

	$('#total_stone_rate_items > tbody').find('.branch_select').select2({

		placeholder: "Branch",

		allowClear: true

	});

	$('#total_stone_rate_items > tbody').find('.stn_type').select2();

	$('#total_stone_rate_items > tbody').find('.stn_type').select2({

		placeholder: "Stone Type",

		allowClear: true

	});

	$('#total_stone_rate_items > tbody').find('.stn_name').select2();

	$('#total_stone_rate_items > tbody').find('.stn_name').select2({

		placeholder: "Stone Name",

		allowClear: true

	});

	$('#total_stone_rate_items > tbody').find('.stn_quality').select2();

	$('#total_stone_rate_items > tbody').find('.stn_quality').select2({

		placeholder: "Quality Code",

		allowClear: true

	});

	$('#total_stone_rate_items > tbody').find('.uom_id').select2();

	$('#total_stone_rate_items > tbody').find('.uom_id').select2({

		placeholder: "UOM Code",

		allowClear: true

	});

	$('#total_stone_rate_items > tbody').find('.stn_calc_type').select2();

	$('#total_stone_rate_items > tbody').find('.stn_calc_type').select2({

		placeholder: "Stone  Calc Type",

		allowClear: true

	});

}

function add_stone_rate_row()

{

	if(validateStoneRateRow())

	{

		Create_NewEmpty_StoneRateRow()

	}

}

function remove_stn_rate_row(curRow)

{

	curRow.remove();

}

$('#add_stn_rate_settings').on('click',function(e)

{

	add_stone_rate_settings();

});

function add_stone_rate_settings()

{

	var form_data=$('#stn_rate_settings').serialize();

	my_Date = new Date();

	if(ctrl_page[2]=='add')

	{

	 var url = base_url+"index.php/admin_ret_catalog/ret_stone_rate_settings/save?nocache=" + my_Date.getUTCSeconds();

	}

   	else

  	{

		var url = base_url+ "index.php/admin_ret_catalog/ret_stone_rate_settings/update/"+ctrl_page[3]+"?nocache=" + my_Date.getUTCSeconds();

   	}

	$.ajax({

		data: form_data,

		url: url,

		type:"POST",

		dataType: 'json',

		cache:false,

		success:function(data)

		{

			if(data.status)

			{

			    console.log(data.msg);

				$("div.overlay").css("display", "none");

			    $.toaster({ priority : 'success', title : 'Warning!', msg : ''+"</br>"+data.msg});

				window.location.href=base_url+ "index.php/admin_ret_catalog/ret_stone_rate_settings/list";

			}else

			{

			    $.toaster({ priority : 'danger', title : 'Warning!', msg : ''+"</br>"+data.msg});

			   	$("div.overlay").css("display", "none");

			    $('#add_stn_rate_settings').prop('disabled',false);

			}

		}

	});

}

$("input[name='stone_rate_type']:radio").on('change',function()

{

	$('.lse_stn').css('display','block');

	$('.lse_pro').css('display','none');

	if(this.value==2)

	{

		$('.lse_stn').css('display','none');

		$('.lse_pro').css('display','block');

	}

	get_stone_rates_settings_details();

});

$('#search_stone_rate').on('click',function()

{

	get_stone_rates_settings_details();

})

function get_stone_rates_settings_details()

{

	$("div.overlay").css("display", "block");

	var type = $("input[name='stone_rate_type']:checked").val();

	my_Date = new Date();

	$.ajax({

		url:base_url+ "index.php/admin_ret_catalog/ret_stone_rate_settings/ajax?nocache=" + my_Date.getUTCSeconds(),

		dataType:"JSON",

		type:"POST",

		data:{'stone_type':$('#rate_stn_type').val(),'stone_id':$('#rate_stn_id').val(),'id_product':$('#rate_stn_pro').val(),'id_design':$('#rate_stn_des').val(),'id_sub_design':$('#rate_stn_sub_des').val(),'quality_code':$('#rate_quality_code').val(),'id_branch':$('#branch_filter').val() ,'type':type},

		cache:false,

		success:function(data)

		{

			console.log(data)

			if(type==1)

			{

				set_loose_stone_rate_settings(data)

			}

			else

			{

				set_loose_product_rate_settings(data);

			}

			$("div.overlay").css("display", "none");

		},

		error:function(error)

		{

		   $("div.overlay").css("display", "none");

		}

	});

}

function set_loose_stone_rate_settings(data)

{

	var access		= data.access;

	var list 	    = data.list;

	var oTable = $('#stone_rate_list').DataTable();

	oTable.clear().draw();

	if (list!= null && list.length > 0)

	{

		oTable = $('#stone_rate_list').dataTable({

			"bDestroy": true,

			"bInfo": true,

			"bFilter": true,

			"bSort": true,

			"order": [[ 0, "desc" ]],

			"dom": 'lBfrtip',

			"buttons" : ['excel','print'],

			"tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },

			"aaData"  : list,

			"aoColumns": [

				{ "mDataProp": "id_stone_rate" },

				{ "mDataProp": "stone_type" },

				{ "mDataProp": "stone_name" },

				{ "mDataProp": "uom_name" },

				{ "mDataProp": "stone_calc" },

				{ "mDataProp": "quality_code" },

				{ "mDataProp": "branch"},

				{ "mDataProp": "from_cent" },

				{ "mDataProp": "to_cent" },

				{ "mDataProp": "min_rate" },

				{ "mDataProp": "max_rate" },

				{ "mDataProp": function ( row, type, val, meta ) {

						id_stone_rate= row.id_stone_rate;

						edit_target=(access.edit=='0'?"":base_url+'index.php/admin_ret_catalog/ret_stone_rate_settings/edit/'+id_stone_rate+'/'+row.type);

						delete_url=(true ? base_url+'index.php/admin_ret_catalog/ret_stone_rate_settings/delete/'+id_stone_rate : '#' );

						delete_confirm= (true ?'#confirm-delete':'');

						//action_content='<a href="'+edit_target+'" class="btn btn-primary btn-edit" id="edit" role="button" data-id='+id_stone_rate+'  ><i class="fa fa-edit" ></i></a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

						var action_content='';

						if(access.edit == '1'){

													

							action_content+=  '<a href="'+edit_target+'" class="btn btn-primary btn-edit" id="edit" role="button" data-id='+id_stone_rate+'  ><i class="fa fa-edit" ></i></a>' ;

							

							}

							if(access.delete == '1'){

							 

							 action_content+='<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

							}

						return action_content;

					}

				}

			]

		});

	}

	$("div.overlay").css("display", "none");

}

function set_loose_product_rate_settings(data)

{

	var access		= data.access;

	var list 	    = data.list;

	var oTable = $('#product_stone_rate_list').DataTable();

	oTable.clear().draw();

	if (list!= null && list.length > 0)

	{

		oTable = $('#product_stone_rate_list').dataTable({

			"bDestroy": true,

			"bInfo": true,

			"bFilter": true,

			"bSort": true,

			"order": [[ 0, "desc" ]],

			"dom": 'lBfrtip',

			"buttons" : ['excel','print'],

			"tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },

			"aaData"  : list,

			"aoColumns": [

				{ "mDataProp": "id_stone_rate" },

				{ "mDataProp": "product_name" },

				{ "mDataProp": "design_name" },

				{ "mDataProp": "sub_design_name"},

				{ "mDataProp": "uom_name" },

				{ "mDataProp": "stone_calc" },

				{ "mDataProp": "quality_code" },

				{ "mDataProp": "branch"},

				{ "mDataProp": "from_cent" },

				{ "mDataProp": "to_cent" },

				{ "mDataProp": "min_rate" },

				{ "mDataProp": "max_rate" },

				{ "mDataProp": function ( row, type, val, meta ) {

						id_stone_rate= row.id_stone_rate;

						edit_target=(access.edit=='0'?"":base_url+'index.php/admin_ret_catalog/ret_stone_rate_settings/edit/'+id_stone_rate+'/'+row.type);

						delete_url=(true ? base_url+'index.php/admin_ret_catalog/ret_stone_rate_settings/delete/'+id_stone_rate : '#' );

						delete_confirm= (true ?'#confirm-delete':'');

						action_content='<a href="'+edit_target+'" class="btn btn-primary btn-edit" id="edit" role="button" data-id='+id_stone_rate+'  ><i class="fa fa-edit" ></i></a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

						return action_content;

					}

				}

			]

		});

	}

	$("div.overlay").css("display", "none");

}

function getStoneRateSettings(id)

{

	my_Date = new Date();

	$.ajax({

        url: base_url+'index.php/admin_ret_catalog/ret_stone_rate_settings/stone_rate_details/'+id+'?nocache=' + my_Date.getUTCSeconds(),

        dataType: "json",

        method: "POST",

        success: function (data)

		{

			console.log(data);

        	$("#total_stone_rate_items tbody tr").empty();

        	$("#total_stone_rate_items tbody tr").empty();

        	$("#i_increment").val(0);

			var a = 0;

			$.each(data.rate_sett,function(key,item)

			{

				var html_1 = "";

				var i = ++a;

				$("#i_increment").val(i);

				html_1+="<tr id='detail"+i+"' class='"+i+"'>"

					+'<td><select class="form-control branch_select" name="stn_rate['+i+'][id_branch]" id="branch_select'+i+'" style="width: 100px"></select><input type="hidden" id="id_branch" name=""><input type="hidden" name=stn_rate['+i+'][id_stn_rate] value='+id+'></td>'

					+'<td><select class="form-control stn_type " name="stn_rate['+i+'][stn_type]" id="stn_type'+i+'" style="width: 100px"></select></td>'

					+'<td><select class="form-control stn_name" name="stn_rate['+i+'][stn_name]" id="stn_name'+i+'" style="width: 100px"></select></td>'

					+'<td><select class="form-control stn_quality " name="stn_rate['+i+'][stn_quality]" id="stn_quality'+i+'" style="width: 100px"></select></td>'

					+'<td><select class="form-control uom_id" name="stn_rate['+i+'][uom_id]" id="uom_id'+i+'" style="width: 100px"></select></td>'

					+'<td><select class="form-control stn_calc_type" name="stn_rate['+i+'][stn_calc_type]" id="stn_calc_type'+i+'" style="width: 100px"><option value="1" '+(item.stone_calc_type==1 ? 'selected':'')+'>Wt</option><option value="2" '+(item.stone_calc_type==2 ? 'selected':'')+'>PCS</option></select></td>'

					+'<td><input type="number" class="form-control from_wt" name="stn_rate['+i+'][from_wt]" value="'+item.from_cent+'" placeholder="Enter From Cent" '+(item.stone_type!=1 ? 'disabled': '')+' style="width: 100px"></td>'

					+'<td><input type="number" class="form-control to_wt" name="stn_rate['+i+'][to_wt]" value="'+item.to_cent+'" placeholder="Enter To Cent" '+(item.stone_type!=1 ? 'disabled': '')+' style="width: 100px"></td>'

					+'<td><input type="number" class="form-control min_rate" name="stn_rate['+i+'][min_rate]" value="'+item.min_rate+'" placeholder="Enter Min Rate" style="width: 100px"></td>'

					+'<td><input type="number" class="form-control max_rate" name="stn_rate['+i+'][max_rate]" value="'+item.max_rate+'" placeholder="Enter Max Rate" style="width: 100px"></td>'

					+'<td></td>'

				"</tr>";

				$('#total_stone_rate_items tbody').append(html_1);

				$('.branch_select').append(

					$("<option></option>")

					.attr("value", "")

					.text('-Choose-')

				);

				$.each(reorder_branch,function(key,item)

				{

					$('.branch_select').append(

						$("<option></option>")

						.attr("value",item.id_branch)

						.text(item.name)

					);

				});

				$('#branch_select'+i).val(item.id_branch);

				$('.stn_type').append(

					$("<option></option>")

					.attr("value", "")

					.text('-Choose-')

				);

				$('#stn_type'+i).val(item.stone_type);

				$.each(data.stone_type, function (key, item) {

					$('.stn_type').append(

					$("<option></option>")

					.attr("value", item.id_stone_type)

					.text(item.stone_type)

					);

				});

				$('#stn_type'+i).val(item.stone_type);

				$('.stn_name').append(

					$("<option></option>")

					.attr("value", "")

					.text('-Choose-')

				);

				$.each(data.stones, function (key, item) {

					$('.stn_name').append(

					$("<option></option>")

					.attr("value", item.stone_id)

					.text(item.stone_name)

					);

				});

				$('#stn_name'+i).val(item.stone_id);

				$('.uom_id').append(

					$("<option></option>")

					.attr("value", "")

					.text('-Choose-')

				);

				$.each(data.uom, function (key, item) {

					$('.uom_id').append(

					$("<option></option>")

					.attr("value", item.uom_id)

					.text(item.code)

					);

				});

				$('#uom_id'+i).val(item.uom_id);

				$('.stn_quality').append(

					$("<option></option>")

					.attr("value", "")

					.text('-Choose-')

				);

				$.each(data.quality_code, function (key, item) {

					$('.stn_quality').append(

					$("<option></option>")

					.attr("value", item.quality_id)

					.text(item.code)

					);

				});

				$('#stn_quality'+i).val(item.quality_id);

				$('#total_stone_rate_items > tbody').find('.branch_select').select2();

				$('#total_stone_rate_items > tbody').find('.branch_select').select2({

					placeholder: "Branch",

					allowClear: true

				});

				$('#total_stone_rate_items > tbody').find('.stn_type').select2();

				$('#total_stone_rate_items > tbody').find('.stn_type').select2({

					placeholder: "Stone Type",

					allowClear: true

				});

				$('#total_stone_rate_items > tbody').find('.stn_name').select2();

				$('#total_stone_rate_items > tbody').find('.stn_name').select2({

					placeholder: "Stone Name",

					allowClear: true

				});

				$('#total_stone_rate_items > tbody').find('.stn_quality').select2();

				$('#total_stone_rate_items > tbody').find('.stn_quality').select2({

					placeholder: "Quality Code",

					allowClear: true

				});

				$('#total_stone_rate_items > tbody').find('.uom_id').select2();

				$('#total_stone_rate_items > tbody').find('.uom_id').select2({

					placeholder: "UOM Code",

					allowClear: true

				});

				$('#total_stone_rate_items > tbody').find('.stn_calc_type').select2();

				$('#total_stone_rate_items > tbody').find('.stn_calc_type').select2({

					placeholder: "Stone  Calc Type",

					allowClear: true

				});

			})

		}

	});

}

function get_ActiveStoneTypeForRate()

{

	$('#rate_stn_type option').remove();

	$("div.overlay").css("display", "block");

	$.ajax({

		type:'GET',

		url: base_url+'index.php/admin_ret_tagging/getStoneTypes',

		dataType:'json',

		success:function(data)

		{

			var id =  $("#rate_stn_type").val();

			$.each(data, function (key, item) {

				$("#rate_stn_type").append(

				$("<option></option>")

				.attr("value", item.id_stone_type)

				.text(item.stone_type)

				);

			});

			$("#rate_stn_type").select2(

			{

				placeholder:"Select Stone Type",

				allowClear: true

			});

			if($("#rate_stn_type").length)

			{

				$("#rate_stn_type").select2("val",(id!='' && id>0?id:''));

			}

		}

	});

}

$('#rate_stn_type').on('change',function()

{

	if(this.value!='')

	{

		getActiveStonesForRate();

	}

});

function getActiveStonesForRate()

{

	$('#rate_stn_id option').remove();

	$.ajax({

		type:'POST',

		data: {'stone_type' : $("#rate_stn_type").val()},

		url: base_url+'index.php/admin_ret_catalog/stone/ActiveStones',

		dataType:'json',

		success:function(data)

		{

			var id =  $("#rate_stn_id").val();

			$.each(data, function (key, item) {

				$("#rate_stn_id").append(

				$("<option></option>")

				.attr("value", item.stone_id)

				.text(item.stone_name)

				);

			});

			$("#rate_stn_id").select2(

			{

				placeholder:"Select Stone Name",

				allowClear: true

			});

			if($("#rate_stn_id").length)

			{

				$("#rate_stn_id").select2("val",(id!='' && id>0?id:''));

			}

		}

	});

}

function getActive_quality_codeForRate()

{

	$('#rate_quality_code option').remove();

	$.ajax({

	type: 'GET',

	url: base_url+'index.php/admin_ret_catalog/get_quality_code',

	dataType:'json',

	success:function(data)

		{

			var id =  $("#rate_quality_code").val();

			$.each(data, function (key, item) {

				$("#rate_quality_code").append(

				$("<option></option>")

				.attr("value", item.quality_id)

				.text(item.code)

				);

			});

			$("#rate_quality_code").select2(

			{

				placeholder:"Select Quality",

				allowClear: true

			});

			if($("#rate_quality_code").length)

			{

				$("#rate_quality_code").select2("val",(id!='' && id>0?id:''));

			}

		}

	});

}

$(document).on('change','.max_rate',function()

{

	var row = $(this).closest('tr');

	var min_rate = row.find('.min_rate').val();

	if(this.value < parseInt(min_rate))

	{

		row.find('.max_rate').val('');

		row.find('.max_rate').focus();

		$.toaster({ priority : 'danger', title : 'Warning!', message : "Invalid max rate"});

	}

});

$(document).on('change','.min_rate',function()

{

	var row = $(this).closest('tr');

	var max_rate = row.find('.max_rate').val();

	if(this.value > parseInt(max_rate))

	{

		row.find('.min_rate').val('');

		row.find('.min_rate').focus();

		$.toaster({ priority : 'danger', title : 'Warning!', message : "Invalid min rate"});

	}

});

/*Stone Rate Settings*/

/*Loose Product Rate Settings*/

$('#rate_stn_pro').on('change',function()

{

	if(this.value!='')

	{

		getActiveDesignForRate();

	}

});

function getActiveDesignForRate()

{

	$(".overlay").css('display','block');

	$.ajax({

        type: 'POST',

        url: base_url+'index.php/admin_ret_catalog/get_active_design_products',

        dataType:'json',

        data:{'id_product':$('#rate_stn_pro').val()},

        success:function(data){

            var des_id = $('#rate_stn_des').val();

            $("#rate_stn_des option").remove();

           $.each(data, function (key, item) {

                $("#rate_stn_des").append(

                    $("<option></option>")

                    .attr("value", item.design_no)

                    .text(item.design_name)

                );

            });

			$("#rate_stn_des").select2({

                placeholder: "Design",

                allowClear: true

            });

            $('#rate_stn_des').select2("val",(des_id!='' ? des_id :""));

            $(".overlay").css("display", "none");

        }

    });

}

$('#rate_stn_des').on('change',function()

{

	if(this.value!='')

	{

		getActive_SubdesignForRate();

	}

});

function getActive_SubdesignForRate()

{

	$(".overlay").css('display','block');

	$.ajax({

        type: 'POST',

		url: base_url+'index.php/admin_ret_catalog/get_ActiveSubDesigns',

        dataType:'json',

        data:{'id_product':$('#rate_stn_pro').val(),'design_no':$('#rate_stn_des').val()},

        success:function(data){

            var id_sub_des = $('#rate_stn_sub_des').val();

            $("#rate_stn_sub_des option").remove();

           $.each(data, function (key, item) {

                $("#rate_stn_sub_des").append(

                    $("<option></option>")

                    .attr("value", item.id_sub_design)

                    .text(item.sub_design_name)

                );

            });

            $("#rate_stn_sub_des").select2({

                placeholder: "Sub Design",

                allowClear: true

            });

            $('#rate_stn_sub_des').select2("val",(id_sub_des!='' ? id_sub_des :""));

            $(".overlay").css("display", "none");

        }

    });

}

$('.add_stoneproduct_info').on('click',function()

{

	if(ValidateLooseProductRateRow())

	{

		Create_NewEmpty_ProductStoneRateRow()

	}

});

function add_product_rate_row()

{

	if(ValidateLooseProductRateRow())

	{

		Create_NewEmpty_ProductStoneRateRow()

	}

}

function ValidateLooseProductRateRow()

{

    var validate = true;

	$('#total_stnproduct_rate_items > tbody  > tr').each(function(index, tr) {

		validate_wt = validateLooseProduct_wt_range($(this));

		if($(this).find('.stn_prod').val() == "" || $(this).find('.branch_select ').val() == "" || $(this).find('.stn_des').val() == "" || $(this).find('.stn_sub_des').val() == "" || $(this).find('.prod_uom_id ').val() == null || $(this).find('.stn_prod_calc_type ').val() == "" || $(this).find('.min_rate').val() == "" || $(this).find('.max_rate').val() == "" || ! validate_wt)

		{

			validate = false;

			if(validate_wt){

				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill the required fields..'});

			}

			return validate;

		}

	});

	return validate;

}

function Create_NewEmpty_ProductStoneRateRow()

{

	var row="";

	var a = $("#i_increment").val();

	var i = ++a;

	$("#i_increment").val(i);

	row+='<tr id="detail'+i+'" class="'+i+'">'

		+'<td><select class="form-control branch_select" name="stn_prod_rate['+i+'][id_branch]" id="branch_select" style="width: 100px"></select><input type="hidden" id="id_branch" name=""></td>'

		+'<td><select class="form-control stn_prod " name="stn_prod_rate['+i+'][id_product]" id="stn_prod" style="width: 100px"></select></td>'

		+'<td><select class="form-control stn_des" name="stn_prod_rate['+i+'][id_design]" id="stn_des" style="width: 100px"></select></td>'

		+'<td><select class="form-control stn_sub_des" name="stn_prod_rate['+i+'][id_sub_design]" id="stn_sub_des" style="width: 100px"></select></td>'

		+'<td><select class="form-control stn_prod_quality " name="stn_prod_rate['+i+'][stn_quality]" id="stn_prod_quality" style="width: 100px"></select></td>'

		+'<td><select class="form-control prod_uom_id" name="stn_prod_rate['+i+'][uom_id]" id="prod_uom_id" style="width: 100px"></select></td>'

		+'<td><select class="form-control stn_prod_calc_type" name="stn_prod_rate['+i+'][stn_calc_type]" id="stn_prod_calc_type" style="width: 100px"><option value="1" >Wt</option><option value="2" >PCS</option></select></td>'

		+'<td><input type="number" class="form-control prod_from_wt" name="stn_prod_rate['+i+'][from_wt]" value="" style="width: 100px"placeholder="Enter From Cent"></td>'

		+'<td><input type="number" class="form-control prod_to_wt" name="stn_prod_rate['+i+'][to_wt]" value="" placeholder="Enter To Cent"  style="width: 100px"></td>'

		+'<td><input type="number" class="form-control prod_min_rate" name="stn_prod_rate['+i+'][min_rate]" value="" placeholder="Enter Min Rate"  style="width: 100px"></td>'

		+'<td><input type="number" class="form-control prod_max_rate" name="stn_prod_rate['+i+'][max_rate]" value="" placeholder="Enter Max Rate"  style="width: 100px"></td>'

		+'<td><button type="button" class="btn btn-success addProductStoneRateSettings" onClick="add_product_rate_row()"><i class="fa fa-plus"></i></button><a href="#" onClick="remove_stn_rate_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'

	+'</tr>';

	$('#total_stnproduct_rate_items tbody ').append(row);

	$('#total_stnproduct_rate_items tr:not(:last-child) .addProductStoneRateSettings').remove();

	$('.branch_select').append(

		$("<option></option>")

		.attr("value", "")

		.text('-Choose-')

	);

	$.each(reorder_branch,function(key,item)

	{

		$('.branch_select').append(

			$("<option></option>")

			.attr("value",item.id_branch)

			.text(item.name)

		);

	});

	$('.stn_prod').append(

		$("<option></option>")

		.attr("value", "")

		.text('-Choose-')

	);

	$.each(loosestone_product, function (key, item) {

		$('.stn_prod').append(

		$("<option></option>")

		.attr("value", item.pro_id)

		.attr("stone_type", item.stone_type)

		.text(item.product_name)

		);

	});

	$('.prod_uom_id').append(

		$("<option></option>")

		.attr("value", "")

		.text('-Choose-')

	);

	$.each(uom_details, function (key, item) {

		$('.prod_uom_id').append(

		$("<option></option>")

		.attr("value", item.uom_id)

		.text(item.code)

		);

	});

	$('.stn_prod_quality').append(

		$("<option></option>")

		.attr("value", "")

		.text('-Choose-')

	);

	$.each(quality_code, function (key, item) {

		$('.stn_prod_quality').append(

		$("<option></option>")

		.attr("value", item.quality_id)

		.text(item.code)

		);

	});

	$('#total_stnproduct_rate_items > tbody').find('.branch_select').select2();

	$('#total_stnproduct_rate_items > tbody').find('.branch_select').select2({

		placeholder: "Branch",

		allowClear: true

	});

	$('#total_stnproduct_rate_items > tbody').find('.stn_prod').select2();

	$('#total_stnproduct_rate_items > tbody').find('.stn_prod').select2({

		placeholder: "product",

		allowClear: true

	});

	$('#total_stnproduct_rate_items > tbody').find('.stn_des').select2();

	$('#total_stnproduct_rate_items > tbody').find('.stn_des').select2({

		placeholder: "Design",

		allowClear: true

	});

	$('#total_stnproduct_rate_items > tbody').find('.stn_sub_des').select2();

	$('#total_stnproduct_rate_items > tbody').find('.stn_sub_des').select2({

		placeholder: "Design",

		allowClear: true

	});

	$('#total_stnproduct_rate_items > tbody').find('.stn_prod_quality').select2();

	$('#total_stnproduct_rate_items > tbody').find('.stn_prod_quality').select2({

		placeholder: "Quality Code",

		allowClear: true

	});

	$('#total_stnproduct_rate_items > tbody').find('.prod_uom_id').select2();

	$('#total_stnproduct_rate_items > tbody').find('.prod_uom_id').select2({

		placeholder: "UOM Code",

		allowClear: true

	});

	$('#total_stnproduct_rate_items > tbody').find('.stn_prod_calc_type').select2();

	$('#total_stnproduct_rate_items > tbody').find('.stn_prod_calc_type').select2({

		placeholder: "Stone  Calc Type",

		allowClear: true

	});

}

function getLooseProductRateSettings(id)

{

	my_Date = new Date();

	$.ajax({

        url: base_url+'index.php/admin_ret_catalog/ret_stone_rate_settings/product_rate_details/'+id+'?nocache=' + my_Date.getUTCSeconds(),

        dataType: "json",

        method: "POST",

        success: function (data)

		{

			console.log(data);

        	$("#total_stnproduct_rate_items tbody tr").empty();

        	$("#total_stnproduct_rate_items tbody tr").empty();

        	$("#i_increment").val(0);

			var a = 0;

			$.each(data.prod_rate_sett,function(key,item)

			{

				var html_1 = "";

				var i = ++a;

				$("#i_increment").val(i);

					html_1+="<tr id='detail"+i+"' class='"+i+"'>"

						+'<td><select class="form-control branch_select" name="stn_prod_rate['+i+'][id_branch]" id="branch_select'+i+'" style="width: 100px"></select><input type="hidden" id="id_branch" name=""></td>'

						+'<td><select class="form-control stn_prod " name="stn_prod_rate['+i+'][id_product]" id="stn_prod'+i+'" style="width: 100px"></select></td>'

						+'<td><select class="form-control stn_des" name="stn_prod_rate['+i+'][id_design]" id="stn_des'+i+'" style="width: 100px"></select></td>'

						+'<td><select class="form-control stn_sub_des" name="stn_prod_rate['+i+'][id_sub_design]" id="stn_sub_des'+i+'" style="width: 100px"></select></td>'

						+'<td><select class="form-control stn_prod_quality " name="stn_prod_rate['+i+'][stn_quality]" id="stn_prod_quality'+i+'" style="width: 100px"></select></td>'

						+'<td><select class="form-control prod_uom_id" name="stn_prod_rate['+i+'][uom_id]" id="prod_uom_id'+i+'" style="width: 100px"></select></td>'

						+'<td><select class="form-control stn_prod_calc_type" name="stn_prod_rate['+i+'][stn_calc_type]" id="stn_prod_calc_type'+i+'" style="width: 100px"><option value="1" '+(item.stone_calc_type==1 ? 'selected':'')+'>Wt</option><option value="2" '+(item.stone_calc_type==2 ? 'selected':'')+'>PCS</option></select></td>'

						+'<td><input type="number" class="form-control prod_from_wt" name="stn_prod_rate['+i+'][from_wt]" value="'+item.from_cent+'" style="width: 100px"placeholder="Enter From Cent" '+(item.stone_type==1 ? 'disabled': '')+'></td>'

						+'<td><input type="number" class="form-control prod_to_wt" name="stn_prod_rate['+i+'][to_wt]" value="'+item.to_cent+'" placeholder="Enter To Cent"  style="width: 100px" '+(item.stone_type==1 ? 'disabled': '')+'></td>'

						+'<td><input type="number" class="form-control prod_min_rate" name="stn_prod_rate['+i+'][min_rate]" value="'+item.min_rate+'" placeholder="Enter Min Rate"  style="width: 100px"></td>'

						+'<td><input type="number" class="form-control prod_max_rate" name="stn_prod_rate['+i+'][max_rate]" value="'+item.max_rate+'" placeholder="Enter Max Rate"  style="width: 100px"></td>'

						+'<td></td>'

					"</tr>";

				$('#total_stnproduct_rate_items tbody').append(html_1);

				$('.branch_select').append(

					$("<option></option>")

					.attr("value", "")

					.text('-Choose-')

				);

				$.each(reorder_branch,function(key,item)

				{

					$('.branch_select').append(

						$("<option></option>")

						.attr("value",item.id_branch)

						.text(item.name)

					);

				});

				$('#branch_select'+i).val(item.id_branch);

				$('.stn_prod').append(

					$("<option></option>")

					.attr("value","")

					.attr('-Choose-')

				);

				$.each(data.products,function(key,item)

				{

					$('.stn_prod').append(

						$("<option></option>")

						.attr("value",item.pro_id)

						.text(item.product_name)

					);

				});

				$('#stn_prod'+i).val(item.id_product);

				$('.stn_des').append(

					$("<option></option>")

					.attr('value',"")

					.attr('-Choose-')

				);

				$.each(data.designs,function(key,item)

				{

					$('.stn_des').append(

						$("<option></option>")

						.attr("value",item.design_no)

						.text(item.design_name)

					);

				});

				$('#stn_des'+i).val(item.id_design);

				$('.stn_sub_des').append(

					$("<option></option>")

					.attr('value',"")

					.attr('-Choose-')

				);

				$.each(data.sub_designs,function(key,item)

				{

					$('.stn_sub_des').append(

						$("<option></option>")

						.attr("value",item.id_sub_design)

						.text(item.sub_design_name)

					);

				});

				$('#stn_sub_des'+i).val(item.id_sub_design);

				$('.prod_uom_id').append(

					$("<option></option>")

					.attr("value", "")

					.text('-Choose-')

				);

				$.each(data.uom, function (key, item) {

					$('.prod_uom_id').append(

					$("<option></option>")

					.attr("value", item.uom_id)

					.text(item.code)

					);

				});

				$('#prod_uom_id'+i).val(item.uom_id);

				$('.stn_prod_quality').append(

					$("<option></option>")

					.attr("value", "")

					.text('-Choose-')

				);

				$.each(data.quality_code, function (key, item) {

					$('.stn_prod_quality').append(

					$("<option></option>")

					.attr("value", item.quality_id)

					.text(item.code)

					);

				});

				$('#stn_prod_quality'+i).val(item.quality_id);

				$('#total_stnproduct_rate_items > tbody').find('.branch_select').select2();

				$('#total_stnproduct_rate_items > tbody').find('.branch_select').select2({

					placeholder: "Branch",

					allowClear: true

				});

				$('#total_stnproduct_rate_items > tbody').find('.stn_prod').select2();

				$('#total_stnproduct_rate_items > tbody').find('.stn_prod').select2({

					placeholder: "Product",

					allowClear: true

				});

				$('#total_stnproduct_rate_items > tbody').find('.stn_des').select2();

				$('#total_stnproduct_rate_items > tbody').find('.stn_des').select2({

					placeholder: "Design",

					allowClear: true

				});

				$('#total_stnproduct_rate_items > tbody').find('.stn_sub_des').select2();

				$('#total_stnproduct_rate_items > tbody').find('.stn_sub_des').select2({

					placeholder: "Sub Design",

					allowClear: true

				});

				$('#total_stnproduct_rate_items > tbody').find('.stn_prod_quality').select2();

				$('#total_stnproduct_rate_items > tbody').find('.stn_prod_quality').select2({

					placeholder: "Quality Code",

					allowClear: true

				});

				$('#total_stnproduct_rate_items > tbody').find('.prod_uom_id').select2();

				$('#total_stnproduct_rate_items > tbody').find('.prod_uom_id').select2({

					placeholder: "UOM Code",

					allowClear: true

				});

				$('#total_stnproduct_rate_items > tbody').find('.stn_prod_calc_type').select2();

				$('#total_stnproduct_rate_items > tbody').find('.stn_prod_calc_type').select2({

					placeholder: "Stone  Calc Type",

					allowClear: true

				});

			})

		}

	});

}

function getLooseStonesProduct()

{

	$.ajax({

		type:'GET',

		url: base_url+'index.php/admin_ret_catalog/getLooseStonesProduct',

		dataType:'json',

		success:function(data)

		{

			loosestone_product=data;

			var id_product = $('#rate_stn_pro').val();

            $("#rate_stn_pro option").remove();

           $.each(data, function (key, item) {

                $("#rate_stn_pro").append(

                    $("<option></option>")

                    .attr("value", item.pro_id)

                    .text(item.product_name)

                );

            });

            $("#rate_stn_pro").select2({

                placeholder: "Select Product",

                allowClear: true

            });

            $("#rate_stn_pro").select2("val",(id_product!='' ? id_product :""));

            $(".overlay").css("display", "none");

		}

	});

}

$(document).on('change','.stn_prod',function()

{

	if(this.value!='')

	{

		var row = $(this).closest('tr');

		var stone_type = row.find('.stn_prod option:selected').attr('stone_type'); // 0 - Ornaments , 1 - stone 2 - Diamond

		row.find('.prod_from_wt').prop('disabled',false);

		row.find('.prod_to_wt').prop('disabled',false);

		if(stone_type==1)

		{

			row.find('.prod_from_wt').prop('disabled',true);

			row.find('.prod_to_wt').prop('disabled',true);

		}

		getLooseStonesDesign(row);

	}

});

function getLooseStonesDesign(curRow)

{

	$(".overlay").css('display','block');

	$.ajax({

        type: 'POST',

        url: base_url+'index.php/admin_ret_catalog/get_active_design_products',

        dataType:'json',

        data:{'id_product':curRow.find('.stn_prod').val()},

        success:function(data){

            var des_id = curRow.find('.stn_des').val();

            curRow.find(".stn_des option").remove();

           $.each(data, function (key, item) {

                curRow.find(".stn_des").append(

                    $("<option></option>")

                    .attr("value", item.design_no)

                    .text(item.design_name)

                );

            });

			curRow.find(".stn_des").select2({

                placeholder: "Design",

                allowClear: true

            });

            curRow.find('.stn_des').select2("val",(des_id!='' ? des_id :""));

            $(".overlay").css("display", "none");

        }

    });

}

$(document).on('change','.stn_des',function()

{

	if(this.value!='')

	{

		var row = $(this).closest('tr');

		getLooseStoneSubDesign(row);

	}

});

function getLooseStoneSubDesign(curRow)

{

	$(".overlay").css('display','block');

	$.ajax({

        type: 'POST',

		url: base_url+'index.php/admin_ret_catalog/get_ActiveSubDesigns',

        dataType:'json',

        data:{'id_product':curRow.find('.stn_prod').val(),'design_no':curRow.find('.stn_des').val()},

        success:function(data){

            var id_sub_des = curRow.find('.stn_sub_des').val();

            curRow.find(".stn_sub_des option").remove();

           $.each(data, function (key, item) {

                curRow.find(".stn_sub_des").append(

                    $("<option></option>")

                    .attr("value", item.id_sub_design)

                    .text(item.sub_design_name)

                );

            });

            curRow.find(".stn_sub_des").select2({

                placeholder: "Sub Design",

                allowClear: true

            });

            curRow.find('.stn_sub_des').select2("val",(id_sub_des!='' ? id_sub_des :""));

            $(".overlay").css("display", "none");

        }

    });

}

function validateLooseProduct_wt_range(curRow)

{

    var row_id = curRow.attr('id');

	var from_wt = parseFloat (curRow.find('.prod_from_wt').val());

	var to_wt = parseFloat(curRow.find('.prod_to_wt').val());

	var product = curRow.find('.stn_prod ').val();

	var design = curRow.find('.stn_des').val();

	var sub_design = curRow.find('.stn_sub_des ').val();

	var stn_quality  = curRow.find('.stn_prod_quality').val();

    var validate = true;

    if((product!='' && product!=null) && (design!='' && design!=null) && (sub_design!='' && sub_design!=null)  && curRow.find('.prod_from_wt').val() != '' && curRow.find('.prod_to_wt').val() != '' && curRow.find('.stn_prod_quality ').val() != '' && curRow.find('.stn_prod_quality ').val() != null  && from_wt != to_wt)

	{

    	$('#total_stnproduct_rate_items > tbody  > tr').each(function(index, tr) {

			if($(this).find('.stn_prod ').val() == product  &&  $(this).find('.stn_des ').val() == design &&  $(this).find('.stn_sub_des ').val() == sub_design &&  $(this).find('.stn_prod_quality').val() == stn_quality )

			{

				if( ( parseFloat ($(this).find('.prod_from_wt').val())  <= from_wt &&  parseFloat ($(this).find('.prod_to_wt').val())  >= from_wt ||  parseFloat ($(this).find('.prod_from_wt').val())  <= to_wt &&  parseFloat ($(this).find('.prod_to_wt').val())  >= to_wt) && row_id != $(this).attr('id') )

				{

					validate = false;

					$.toaster({ priority : 'danger', title : 'Warning!', message : "Invalid From and To Cent "});

					return validate;

				}

			}

    	});

    }

	else if(from_wt == to_wt)

	{

    	validate = false;

		$.toaster({ priority : 'danger', title : 'Warning!', message : "Invalid From and To Cent "});

    }

	else

	{

        validate = false;

        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill the required fields....'});

    }

    return validate;

}

$('#add_stnproduct_rate_settings').on('click',function(e)

{

	add_stone_product_rate_settings();

});

function add_stone_product_rate_settings()

{

	var form_data=$('#stn_rate_settings').serialize();

	my_Date = new Date();

	if(ctrl_page[2]=='add')

	{

	 var url = base_url+"index.php/admin_ret_catalog/ret_stone_rate_settings/product_save?nocache=" + my_Date.getUTCSeconds();

	}

   	else

  	{

		var url = base_url+ "index.php/admin_ret_catalog/ret_stone_rate_settings/product_update/"+ctrl_page[3]+"?nocache=" + my_Date.getUTCSeconds();

   	}

	$.ajax({

		data: form_data,

		url: url,

		type:"POST",

		dataType: 'json',

		cache:false,

		success:function(data)

		{

			if(data.status)

			{

			    console.log(data.msg);

				$("div.overlay").css("display", "none");

			    $.toaster({ priority : 'success', title : 'Warning!', msg : ''+"</br>"+data.msg});

				window.location.href=base_url+ "index.php/admin_ret_catalog/ret_stone_rate_settings/list";

			}else

			{

			    $.toaster({ priority : 'danger', title : 'Warning!', msg : ''+"</br>"+data.msg});

			   	$("div.overlay").css("display", "none");

			    $('#add_stn_rate_settings').prop('disabled',false);

			}

		}

	});

}

$(document).on('change','.prod_max_rate',function()

{

	var row = $(this).closest('tr');

	var min_rate = row.find('.prod_min_rate').val();

	if(this.value < parseInt(min_rate))

	{

		row.find('.prod_max_rate').val('');

		row.find('.prod_max_rate').focus();

		$.toaster({ priority : 'danger', title : 'Warning!', message : "Invalid max rate"});

	}

});

$(document).on('change','.prod_min_rate',function()

{

	var row = $(this).closest('tr');

	var max_rate = row.find('.prod_max_rate').val();

	if(this.value > parseInt(max_rate))

	{

		row.find('.prod_min_rate').val('');

		row.find('.prod_min_rate').focus();

		$.toaster({ priority : 'danger', title : 'Warning!', message : "Invalid min rate"});

	}

});

/*Loose Product Rate Settings*/

$("input[name='product[stone_type]']").change(function () {

	var value = $("input[name='product[stone_type]']:checked").val();

	if(value!=0){

		$('.uom_block').css('display','block');

	}else{

		$('.uom_block').css('display','none');

	}

});

/* day close datatable */

function set_dayclose_table(from_date = "", to_date = "") {

	my_Date = new Date();

	$.ajax({

		url: base_url + "index.php/admin_ret_catalog/day_close/Ajax?nocache=" + my_Date.getUTCSeconds(),

		data: { 'from_date': from_date, 'to_date': to_date },

		dataType: "JSON",

		type: "POST",

		cache: false,

		success: function (data) {

			var list = data.dayclose;

			var access = data.access;

			if (access.add == '0') {

				$('#day_close_dynamic').attr('disabled', 'disabled');

			}

			var oTable = $('#dayclose_list').DataTable();

			oTable.clear().draw();

			if (list != null && list.length > 0) {

				oTable = $('#dayclose_list').dataTable({

					"bDestroy": true,

					"bInfo": true,

					"bFilter": true,

					"bSort": false,

					"order": [[0, "desc"]],

					"tableTools": {

						"buttons": [{

							"sExtends": "xls",

							"oSelectorOpts": {

								page: 'current'

							}

						},

						{

							"sExtends": "pdf",

							"oSelectorOpts": {

								page: 'current'

							}

						}]

					},

					"aaData": list,

					"aoColumns": [

						{

							"mDataProp": function (row, type, val, meta) {

								if (row.is_day_closed == 0) {

									return '<input type="checkbox" class="id_branch" name="id_branch[]" value="' + row.id_branch + '"/>' + row.id_branch;

								} else {

									return row.id_branch;

								}

							}

						},

						{ "mDataProp": "branch_name" },

						{ "mDataProp": "dayclose_type" },

						// { "mDataProp": "day_close_time" },

						{ "mDataProp": "is_day_closed_status" },

						{ "mDataProp": "entry_date" }

					]

				});

			}

		},

		error: function (error) {

			$("div.overlay").css("display", "none");

		}

	});

}

$("#day_close_dynamic").on('click', function () {

	my_Date = new Date();

	if ($("input[name='id_branch[]']:checked").val()) {

		var selected = [];

		$("#dayclose_list tbody tr").each(function (index, value) {

			if ($(value).find("input[name='id_branch[]']:checked").is(":checked")) {

				transData = {

					'id_branch': $(value).find(".id_branch").val(),

				}

				selected.push(transData);

			}

		});

		console.log(selected);

		var answer = window.confirm("Are You Sure want to Day Close For Selected Branch..?");

		if (answer) {

			$.ajax({

				type: 'POST',

				data: { 'branch': selected },

				url: base_url + "index.php/admin_ret_catalog/day_close/update_dayclose?nocache=" + my_Date.getUTCSeconds(),

				dataType: 'json',

				success: function (data) {

					if (data.status) {

						window.location.reload();

					}

				}

			});

		}

	} else {

		alert("Select Any one Branch");

	}

});

/* day close datatable */

$('#wastag_method').on('change',function(){

	change_wastage_method();

});

$(document).on('click', ".wc_method_pc", function() {

	$(this).closest('tr').find('.wastage').prop("readonly",false);

	$(this).closest('tr').find('.wc_min').prop("readonly",false);

	$(this).closest('tr').find('.wc_wt').val("");

	$(this).closest('tr').find('.wc_min_wt').val("");

	$(this).closest('tr').find('.wc_wt').prop("readonly",true);

	$(this).closest('tr').find('.wc_min_wt').prop("readonly",true);

});

$(document).on('click', ".wc_method_wt", function() {

	$(this).closest('tr').find('.wc_wt').prop("readonly",false);

	$(this).closest('tr').find('.wc_min_wt').prop("readonly",false);

	$(this).closest('tr').find('.wastage').val("");

	$(this).closest('tr').find('.wc_min').val("");

	$(this).closest('tr').find('.wastage').prop("readonly",true);

	$(this).closest('tr').find('.wc_min').prop("readonly",true);

});

function change_wastage_type() {

	$('#wc_detail tbody').empty();

	var value = $("input[name='settings[wastage_type]']:checked").val();

	if(value == 1){

		$("#wastag_method").prop('disabled',false);

		$('#wastag_value').prop('disabled',false);

		$('#wastag_wt').prop('disabled',false);

		$('#mc_value').prop('disabled',false);

		$('#min_mc_value').prop('disabled',false);

		$('#min_wastag_valuess').prop('disabled',false);

		$('#wastag_min_wt').prop('disabled',false);

		$(".flexiable_type").css("display", "none");

		change_wastage_method();

	} else {

		$("#wastag_method").prop('disabled',true);

		$('#wastag_value').prop('disabled',true);

		$('#mc_value').prop('disabled',true);

		$('#wastag_wt').prop('disabled',true);

		$('#min_mc_value').prop('disabled',true);

		$('#min_wastag_valuess').prop('disabled',true);

		$('#wastag_min_wt').prop('disabled',true);

		$(".flexiable_type").css("display", "block");

		if(($('#select_sub_design').val()!=null && $('#des_des_name').val()!=null && $('#des_prod_name').val()!=null))

		{

			get_productWeightDetails();

		}

    }

}

function change_wastage_method() {

	let wastag_method = $("#wastag_method").val();

	if(wastag_method == 1) {

		$("#wastag_wt").val("");

		$("#wastag_min_wt").val("");

		$("#wastag_wt").prop("disabled", true);

		$("#wastag_min_wt").prop("disabled", true);

		$("#wastag_value").prop("disabled", false);

		$("#min_wastag_valuess").prop("disabled", false);

	} else if(wastag_method == 2) {

		$("#wastag_value").val("");

		$("#min_wastag_valuess").val("");

		$("#wastag_value").prop("disabled", true);

		$("#min_wastag_valuess").prop("disabled", true);

		$("#wastag_wt").prop("disabled", false);

		$("#wastag_min_wt").prop("disabled", false);

	}

}

// COVER UP

function get_cover_up_list()

{

	my_Date = new Date();

	$("div.overlay").css("display", "block");

	var from_date = $("#rpt_from_date").text();

	var to_date = $("#rpt_to_date").text();

	$.ajax({

        data: ( {'from_date':from_date,'to_date':to_date}),

			  url:base_url+ "index.php/admin_ret_catalog/cover_up?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),

			  dataType:"JSON",

			  type:"POST",

			  success:function(data){

				         set_cover_list(data);

			   			$("div.overlay").css("display", "none");

					  },

					  error:function(error)

					  {

						 $("div.overlay").css("display", "none");

					  }

		  });

}

function set_cover_list(data)

{

	 var access	= data.access;

	 var Charges = data.list;

	 var oTable = $('#coverup_list').DataTable();

	     oTable.clear().draw();

			  	 if (Charges!= null && Charges.length > 0)

			  	  {

					  	oTable = $('#coverup_list').DataTable({

				                "bDestroy": true,

				                "bInfo": true,

				                "bFilter": true,

				                "bSort": true,

				                "order": [[ 0, "desc" ]],

				                "order": [[ 0, "desc" ]],

				                "aaData": Charges,

				                "aoColumns": [

                          { "mDataProp": function ( row, type, val, meta ) {

                           return row.id_coverup !=null && row.id_coverup !="" ?row.id_coverup:'-';

                         }},

						   { "mDataProp": function ( row, type, val, meta ) {

                      		  return row.created_on !=null?row.created_on:'-';

                          }},

						 { "mDataProp": function ( row, type, val, meta ) {

                      		  return row.metal !=null?row.metal:'-';

                          }},

						  { "mDataProp": function ( row, type, val, meta ) {

							return row.weight !=null?row.weight:'-';

					  	  }},

                         /*{ "mDataProp": function ( row, type, val, meta ) {

                                            id= row.id_coverup;

											edit_target=(access.edit=='0'?"":"#edit_coverup");

							                action_content='<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a> <a href="#" class="btn btn-danger btn-del" onClick="delete_confirm_charges('+id+')" data-toggle="modal" ><i class="fa fa-trash"></i></a>'

                                            return action_content;

                                            }

                                         }*/

						 ]

				            });

					  	 }

}

$("#Cover_Up").on('click',function()

{

	$('#coverup_add').modal('show');

});

$("#cover_save").on('click',function()

{

	cover_up();

});

function cover_up()

{

   var weight = $("#weight").val();

   var metal = $("#metal_sel").val();

   var id_user = $("#id_user").val();

   my_Date = new Date();

        $.ajax({

            url:base_url+ "index.php/admin_app_api/add_cover_up?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),

            data:JSON.stringify({'weight':weight,'metal':metal,'created_by':id_user}),

            dataType:"JSON",

			contentType: "application/json; charset=utf-8",

            type:"POST",

            success:function(data)

            {

				if(data.status){

					$.toaster({ priority : 'success', title : 'Success!', message : data.message });

					window.location.reload();

				}else{

					$.toaster({ priority : 'danger', title : 'Warning!', message : data.message });

				}

            },

            error:function(error)

            {

               $("div.overlay").css("display", "none");

            },

            complete: function(data)

            {

           //  window.location.href = base_url+"index.php/admin_ret_catalog/charges";

            }

       });

}

function get_cancel_reason_details()

{

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		url: base_url+"index.php/admin_ret_catalog/ret_qc_cancel_reason/ajax?nocache=" + my_Date.getUTCSeconds(),

		type:"GET",

		dataType: 'json',

		cache:false,

		success:function(data){

		   		var list 	= data.list;

				var access	    = data.access;

				

			 var oTable = $('#cancel_list').DataTable();

			 oTable.clear().draw();

			 if (list!= null && list.length > 0)

			 {

				oTable = $('#cancel_list').dataTable({

						"bDestroy": true,

						"bInfo": true,

						"bFilter": true,

						"bSort": true,

						"order": [[ 0, "desc" ]],

						"aaData"  : list,

						"aoColumns": [

						{ "mDataProp": "id_cancel" },

						{ "mDataProp": "cancel_reason" },

						{ "mDataProp": function ( row, type, val, meta ){

                        	active_url =base_url+"index.php/admin_ret_catalog/ret_qc_cancel_reason/update_status/"+row.id_cancel+"/"+(row.status==0?1:0);

                        	return "<a href='"+active_url+"'><i class='fa "+(row.status==1?'fa-check':'fa-remove')+"' style='color:"+(row.status==1?'green':'red')+"'></i></a>"

                        	}

                        },

						{ "mDataProp": function ( row, type, val, meta ) {

							 id= row.id_cancel;

							 edit_target=(access.edit=='0'?"":"#confirm-edit");

							 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/ret_qc_cancel_reason/delete/'+id : '#' );

							 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

							 action_content=(access.edit == '1' ? '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a>':"")+ (access.delete == '1' ? '<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>':'')

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

$(document).on('click', "#cancel_list a.btn-edit", function(e) {

	$("#ed_reason_name").val('');

	e.preventDefault();

	id=$(this).data('id');

	get_cancel_reason(id);

	$("#edit-reason-id").val(id);

});

function get_cancel_reason(id)

{

my_Date = new Date();

$.ajax({

	type:"GET",

	url: base_url+"index.php/admin_ret_catalog/ret_qc_cancel_reason/Edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),

	cache:false,

	dataType:"JSON",

	success:function(data){

		$('#ed_reason_name').val(data.cancel_reason);

		

		

		var status = data.status;

		if(status == 1)

		{

		$('#ed_reason_status').bootstrapSwitch('state', true);

		}

		else

		{

		$('#ed_reason_status').bootstrapSwitch('state', false);

		}

		$("#ed_reason_status_value").val(status);

	}

});

}

$("#add_new_section").on('click',function(){

    if($("#section_name").val() == '')

	{

      $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'please select Enter Section Name..'});

	}

	else if($('#section_ShortCode').val()=='')

	{

	    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'please Section Short Code..'});

	}

	else if($('#id_branch').val()=='')

	{

	    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select branch..'});

	}

	else{

	    $("#add_new_section").prop('disabled',true);

	    add_new_section();

	}

});

$("#add_reason").on('click',function(){

	if($("#reason_name").val() == '')

	{

    		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'please Enter Reason...'});

	}

	else{

	    $("#add_reason").prop('disabled',true);

	    add_reason();

	}

});

function add_reason()

{

   

	my_Date = new Date();

	$.ajax({

		url: base_url+"index.php/admin_ret_catalog/ret_qc_cancel_reason/Add?nocache=" + my_Date.getUTCSeconds(),

		data:{"reason_name":$("#reason_name").val(),"status":$('#reason_status_value').val()},

		type:"POST",

		cache:false,

		success:function(data){

			$('#reason_name').val('');

			window.location.reload(true);

		}

	});

}

$("#update_reason").on('click',function(){

	var id=$("#edit-reason-id").val();

	 if($("#ed_reason_name").val() == '')

	{

    		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'please Enter Reason..'});

	}

	else

	{

	    $("#update_reason").prop('disabled',true);

	    update_reason(id);

	}

});

 function update_reason(id)

{

    $("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		data:{"cancel_reason":$("#ed_reason_name").val(),"status":$('#ed_reason_status_value').val()},

		url: base_url+"index.php/admin_ret_catalog/ret_qc_cancel_reason/Update/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		cache: false,

		success:function(){

			$("div.overlay").css("display", "none");

			location.reload(true);

		}

	});

}

$("#ed_reason_status").on('switchChange.bootstrapSwitch', function (event, state) {

	var x=$(this).data('on-text');

	var y=$(this).data('off-text');

	if($("#ed_reason_status").is(':checked') && x=='YES')

	{

				$("#ed_reason_status_value").val(1);

	} else if(y=='NO')

	{

	  $("#ed_reason_status_value").val(0);

	}

});

$("#reason_status").on('switchChange.bootstrapSwitch', function (event, state) {

	var x=$(this).data('on-text');

	var y=$(this).data('off-text');

	if($("#reason_status").is(':checked') && x=='YES')

	{

				$("#reason_status_value").val(1);

	} else if(y=='NO')

	{

	  $("#reason_status_value").val(0);

	}

});

// product group

$("#ad_pro_grp_status").on('switchChange.bootstrapSwitch', function (event, state) {

	var x = $(this).data('on-text');

	var y = $(this).data('off-text');

	  if($("#ad_pro_grp_status").is(':checked') && x=='YES')

	  {

		  $("#add_pro_grp_status").val(1);

	  } 

	  else if(y=='NO')

	  {

		  $("#add_pro_grp_status").val(0);

	  }

});

$("#ed_pro_grp_status").on('switchChange.bootstrapSwitch', function (event, state) {

	var x = $(this).data('on-text');

	var y = $(this).data('off-text');

	  if($("#ed_pro_grp_status").is(':checked') && x=='YES')

	  {

		  $("#edit_pro_grp_status").val(1);

	  } 

	  else if(y == 'NO')

	  {

		  $("#edit_pro_grp_status").val(0);

	  }

});	

$("#metal_prodgrp,#metal_prodgrp1").on('change',function(e){

    if(this.value!='')

    

    {

    

    $("#id_metal_prodgrp,#id_metal_prodgrp1").val(this.value);

    

    }

    

    else

    

    {

    

    $("#id_metal_prodgrp,#id_metal_prodgrp1").val('');

    

    }

    

});

$("#add_pro_groupnew").on('click',function()

  {

	  if($("#product_grpname").val() !='')

	  {

		  add_pro_grpname($("#product_grpname").val(),$('#add_pro_grp_status').val(),$("#id_metal_prodgrp").val());

	  }

	  else

	  {

		msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Group Name .</div>';

		$("div.overlay").css("display", "none");

		$('#error-msg').html(msg);

		return false;

	  }

 });

function add_pro_grpname(product_grpname,prodstatus,prodgrp_metal)

 {

	my_Date = new Date();

	var file =file;

	var data = new FormData();  

	var form_data = new FormData();  

	form_data.append('product_grpname', product_grpname);

	form_data.append('prodstatus',prodstatus);

	form_data.append('prodgrp_metal',prodgrp_metal);

	

	$.ajax({

		data:form_data,

		url: base_url+"index.php/admin_ret_catalog/product_grouping/add?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		dataType:"JSON",

		cache:false,

		enctype: 'multipart/form-data',

		contentType : false,

		processData : false,

		success:function(data){

			console.log(data);

			msg='<div class ="alert alert-'+data.class+'"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>   '+data.title+'   </strong> '+data.message+' </div>';

			$('#chit_alert').html(msg);

			set_product_grp_table();

			$("div.overlay").css("display", "none"); 

			window.location.reload(true);

		}

	});

 }

function set_product_grp_table()

{

my_Date = new Date();

$.ajax({

url:base_url+ "index.php/admin_ret_catalog/product_grouping?nocache=" + my_Date.getUTCSeconds(),

dataType:"JSON",

type:"POST",

cache:false,

success:function(data)

{ 

	var prod_grp= data.prod_grp;

	var access		= data.access;	

	$('#total_prodgrp').text(prod_grp.length);

		if(access.add == '0')

				{ 	

					$('#add_productgrp').attr('disabled','disabled');

				}

var oTable = $('#product_group_list').DataTable();

oTable.clear().draw();

if (prod_grp!= null && prod_grp.length > 0)

{

	oTable = $('#product_group_list').dataTable({

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

	

		"aaData"  : prod_grp,

	

		"aoColumns": [	{ "mDataProp": "id_prod_group" }, 

						{ "mDataProp": "metal" },

	

						{ "mDataProp": "group_name" },

	

						{ "mDataProp": function ( row, type, val, meta ){

	

							status_url = base_url+"index.php/admin_ret_catalog/product_grouping/update_status/"+row.id_prod_group+"/"+(row.status==1?0:1); 

	

							return "<a href='"+status_url+"'><i class='fa "+(row.status==1?'fa-check':'fa-remove')+"' style='color:"+(row.status==1?'green':'red')+"'></i></a>"

	

						}

	

						},

	

						{ "mDataProp": function ( row, type, val, meta ) {

	

							id= row.id_prod_group;

	

							edit_target=(access.edit=='0'?"":"#confirm-edit");

	

							delete_confirm=(access.delete=='1' ?'#confirm-delete':'');

	

							delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/product_grouping/delete/'+id : '#' );

	

							action_content='<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i> </a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> </a>'

	

							return action_content;

	

							}

	

							

	

						}]

	

	});			  	 	

	

}

},

error:function(error)  

{

$("div.overlay").css("display", "none"); 

}	 

});

}

$(document).on('click', "#product_group_list a.btn-edit", function(e) {

	e.preventDefault();

	id = $(this).data('id');

	get_product_grp(id);

	$("#edit-id").val(id);  

});

function get_product_grp(id)

{

   my_Date = new Date();

	$.ajax({

		type:"GET",

		url: base_url+"index.php/admin_ret_catalog/product_grouping/edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		cache:false,		

		dataType:"JSON",

		success:function(data){

			console.log(data);

			id = data.id_prod_group ;

			$('#ed_product_grpname').val(data.group_name);

			$('#id_metal_prodgrp1').val(data.group_metal);

            $("#metal_prodgrp1").select2("val",(data.group_metal!='' && data.group_metal>0?data.group_metal:''));

			

			var c_status = data.status;

			if(c_status == 1)

			{

			$('#ed_pro_grp_status').bootstrapSwitch('state', true);  

			}

			else

			{

			$('#ed_pro_grp_status').bootstrapSwitch('state', false);

			}

		}

	});

}

$("#update_productgrp").on('click',function()

{

	var ed_grpname=$("#ed_product_grpname").val();

	if($("#ed_product_grpname").val() !='')

	{

		update_pro_grpname($("#ed_product_grpname").val(),$('#edit_pro_grp_status').val(),$('#id_metal_prodgrp1').val());

	}

	else

	{

		msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Group Name .</div>';

		$("div.overlay").css("display", "none");

		$('#error-msg').html(msg);

		return false;

	}

});

function update_pro_grpname(ed_product_grpname,ed_prodstatus,ed_prodgrp_metal)

{

	$("div.overlay").css("display", "block"); 

	my_Date = new Date();

    var data = new FormData();  

	var form_data = new FormData();  

    form_data.append('ed_product_grpname', ed_product_grpname);	

    form_data.append('ed_prodstatus', ed_prodstatus);

	form_data.append('ed_prodgrp_metal',ed_prodgrp_metal);

	$.ajax({

		data:form_data,

		url: base_url+"index.php/admin_ret_catalog/product_grouping/update/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",	

		cache: false,

		enctype: 'multipart/form-data',

		contentType : false,

		processData : false,

		success:function(data){

				$("div.overlay").css("display", "none"); 

				window.location.reload(true);

		}		

	});

}

function getActiveproductgrp(id)

{

    my_Date = new Date();

    $.ajax({

        type: 'GET',

        url: base_url+"index.php/admin_ret_catalog/get_product_group/"+id+"?nocache=" + my_Date.getUTCSeconds(),

        dataType:'json',

        success:function(data){

            var id = $("#id_pro_grp").val();

            $("#prodgrp_sel option").remove();

            $.each(data,function(key, item){

                $("#prodgrp_sel").append(

                    $("<option></option>")

                    .attr("value",item.id_prod_group)

                    .text(item.group_name)

                );

            });

            $("#prodgrp_sel").select2("val",(id!='' && id>0?id:''));

            $(".overlay").css("display","none");

        }

    })

}

$('#prodgrp_sel').on('change',function(){

	if(this.value!='')

	{

		$('#id_pro_grp').val(this.value);

	}

	else{

		$('#id_pro_grp').val('');

	}

});

// Function to validate weight ranges

// function validateWeightRanges() {

// 	let ranges = []; // Temporary array to hold current ranges

// 	let isValid = true; // Flag to track validation

  

// 	$('#wc_detail tr').each(function () {

// 	  const fromWeight = parseFloat($(this).find('.from_weight').val());

// 	  const toWeight = parseFloat($(this).find('.to_weight').val());

  

// 	  if (!isNaN(fromWeight) && !isNaN(toWeight)) {

// 		if (fromWeight >= toWeight) {

// 		  alert('From weight should be less than To weight.');

// 		  $(this).find('.from_weight, .to_weight').addClass('error');

// 		  isValid = false;

// 		  return; // Exit the loop if invalid

// 		}

  

// 		// Check for overlapping and equal ranges

// 		for (let range of existingRanges) {

// 		  if (

// 			(fromWeight === range.from && toWeight === range.to) ||  // Check for duplicate ranges

// 			(fromWeight >= range.from && fromWeight < range.to) ||   // Check for overlapping ranges

// 			(toWeight > range.from && toWeight <= range.to) || 

// 			(fromWeight < range.from && toWeight > range.to)

// 		  ) {

// 			alert('Weight range overlap or duplicate detected. Please select a unique non-overlapping range.');

// 			$(this).find('.from_weight, .to_weight').addClass('error');

// 			isValid = false;

// 			break;

// 		  } else {

// 			$(this).find('.from_weight, .to_weight').removeClass('error');

// 		  }

// 		}

  

// 		if (isValid) {

// 		  ranges.push({ from: fromWeight, to: toWeight });

// 		}

// 	  }

// 	});

  

// 	if (isValid) {

// 	  existingRanges = ranges; // Update the array with valid ranges

// 	}

//   }

$('#wc_detail').on('input', 'input', function() {

    $(this).removeClass('error-field');

});

// Flag to track whether the warning toaster is displayed

let isToasterVisible = false;

// Function to validate and show the warning

function showWarning(message) {

    if (!isToasterVisible) {

        $.toaster({

            priority: 'danger',

            title: 'Warning!',

            message: message,

            settings: { timeout: 5000 }

        });

        isToasterVisible = true;  // Set the flag to true after showing the warning

        // Optional: Reset the flag after the timeout duration

        setTimeout(() => {

            isToasterVisible = false;  // Reset the flag after 5 seconds

        }, 5000); // Match this duration with the toaster timeout

    }


    $("#wastag_value").on("change", function() {
        let min_wastage = parseFloat($("#min_wastag_valuess").val());
		let max_wastage = parseFloat($("#wastag_value").val());
        if (min_wastage > max_wastage) {
		$.toaster({
				priority: 'danger',
				title: 'Warning!',
				message: "The Max V.A(%) Must Be Greater Than Min V.A(%)",
				settings: { 'timeout': 5000 }
			});
        $("#wastag_value").val("");
        } 
    });
    $("#min_wastag_valuess").on("change", function() {
        let min_wastage = parseFloat($("#min_wastag_valuess").val());
		let max_wastage = parseFloat($("#wastag_value").val());
        if (min_wastage > max_wastage) {
		$.toaster({
				priority: 'danger',
				title: 'Warning!',
				message: "The Min V.A(%) Must Less Than Max V.A(%)",
				settings: { 'timeout': 5000 }
			});
        $("#wastag_value").val("");
        } 
    });
    $("#wastag_wt").on("change", function() {
        let min_wastage = parseFloat($("#wastag_min_wt").val());
		let max_wastage = parseFloat($("#wastag_wt").val());
        if (min_wastage > max_wastage) {        
		$.toaster({
				priority: 'danger',
				title: 'Warning!',
				message: "The Max V.A(Wt(g))  MustBe Greater Than Min V.A(Wt(g))",
				settings: { 'timeout': 5000 }
			});  
		$("#wastag_wt").val("");
        } 
    });
    $("#wastag_min_wt").on("change", function() {
        let min_wastage = parseFloat($("#wastag_min_wt").val());
		let max_wastage = parseFloat($("#wastag_wt").val());
        if (min_wastage > max_wastage) {        
		$.toaster({
				priority: 'danger',
				title: 'Warning!',
				message: "The Max V.A(Wt(g)) Must be Less Than MIn V.A(Wt(g))",
				settings: { 'timeout': 5000 }
			});  
		$("#wastag_wt").val("");
        } 
    });
    $("#mc_value").on("change", function() {
        let min_wastage = parseFloat($("#min_mc_value").val());
		let max_wastage = parseFloat($("#mc_value").val());
        if (min_wastage > max_wastage) {
        $.toaster({
				priority: 'danger',
				title: 'Warning!',
				message: "The Max M.C Should Not  Be Greater Than Min M.C",
				settings: { 'timeout': 5000 }
			});
		$("#mc_value").val("");
        } 
    });
    $("#min_mc_value").on("change", function() {
        let min_wastage = parseFloat($("#min_mc_value").val());
		let max_wastage = parseFloat($("#mc_value").val());
        if (min_wastage > max_wastage) {
			$.toaster({
				priority: 'danger',
				title: 'Warning!',
				message: "The Min M.C Must Less Than Max M.C",
				settings: { 'timeout': 5000 }
			});
			
		
		$("#mc_value").val("");
        } 
    });
}

function GST_validate(gst_number, curRow)

{
	var regexp = curRow.find('.id_reg_exp').val();

    var regex = new RegExp('^[0-9]{2}[a-zA-Z]{4}([1-9]|[a-zA-Z]){1}[0-9]{4}[a-zA-Z]{1}([1-9]|[a-zA-Z]){3}$');

	var id_karigar = $('#id_karigar').val();

	if(!regex.test(regexp) && curRow != '')

		{
	
			curRow.find('.id_reg_exp').val('');
	
			alert("Not a valid GST No.");
	
			curRow.find('.id_reg_exp').focus();
	
		} else {
			checkGSTAvail(regexp, id_karigar).then(function(response) {
				if (response.status === false) {
					$('#gst_number_karigar').val("");
					curRow.find('.id_reg_exp').val('');
					$.toaster({ 
						priority: 'danger', 
						title: 'Warning!', 
						message: '</br>' + response.message, 
						settings: { 'timeout': 5000 } 
					});
					$('#gst_number_karigar').focus();
					curRow.find('.id_reg_exp').focus();
				}
			}).catch(function(error) {
				console.error("AJAX Error:", error);
			});
		}

}

function checkGSTAvail(gst_number, id_karigar) {
    return new Promise(function(resolve, reject) {
        $.ajax({
            type: 'POST',
            data: { 'gst_number': gst_number, 'id_karigar': id_karigar },
            url: base_url + 'index.php/admin_ret_purchase/karigar_gst_available',
            dataType: 'json',
            success: function(response) {
                resolve(response); // Resolve promise on success
            },
            error: function(xhr, status, error) {
                reject(error); // Reject promise on error
            }
        });
    });
}

function checkPANAvail(pan_number, id_karigar) {
    return new Promise(function(resolve, reject) {
        $.ajax({
            type: 'POST',
            data: { 'pan_number': pan_number, 'id_karigar': id_karigar },
            url: base_url + 'index.php/admin_ret_purchase/karigar_pan_available',
            dataType: 'json',
            success: function(response) {
                resolve(response); // Resolve promise on success
            },
            error: function(xhr, status, error) {
                reject(error); // Reject promise on error
            }
        });
    });
}

function checkAADHARAvail(aadhar_no, id_karigar) {
    return new Promise(function(resolve, reject) {
        $.ajax({
            type: 'POST',
            data: { 'aadhar_no': aadhar_no, 'id_karigar': id_karigar },
            url: base_url + 'index.php/admin_ret_purchase/karigar_aadhar_available',
            dataType: 'json',
            success: function(response) {
                resolve(response); // Resolve promise on success
            },
            error: function(xhr, status, error) {
                reject(error); // Reject promise on error
            }
        });
    });
}