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

var total_files=[];

$(document).ready(function() {

    //console.log(ctrl_page[1]);

	 var path =  url_params();

	 $("#status,.status,.switch").bootstrapSwitch();

     switch(ctrl_page[1]){ // Retail masters

		case 'branch_floor':

						set_floor_table(); 

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

		break;

		case 'category':

						set_category_table();

						get_ActiveMetal();

						get_ActivePurity();

					break;

		case 'karigar':
						set_karigar_table();

						get_country();

						//add_karigar_wastage();

						$(document).on('change', '.wastage_product', function() {

							if($(this).val() > 0) {
								getActiveDesignsForKarigar($(this));
							} else {
								let parent_class = $(this).parent().parent().parent();
								$(parent_class).find(".wastage_design").empty();
								$(parent_class).find(".wastage_design").select2("val",'');
								$(parent_class).find(".wastage_sub_design").empty();
								$(parent_class).find(".wastage_sub_design").select2("val",'');
							}

						});

						$(document).on('change', '.wastage_design', function() {

							if($(this).val() > 0) {
								getActiveSubDesignsForKarigar($(this));
							} else {
								let parent_class = $(this).parent().parent().parent();
								$(parent_class).find(".wastage_sub_design").empty();
								$(parent_class).find(".wastage_sub_design").select2("val",'');
							}

						});

						$(document).on('click', '.add_karigar_wastage, .add_wastage', function() {
							add_karigar_wastage();
						});

						$(document).on('click', '.remove_karigar_wastage', function() {
							remove_karigar_wastage($(this));
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

						

						$("#product_section_select,#section_sel").select2(

                        {

                            placeholder:"Select section",

                            allowClear: true            

                        });

                        

                        getRetailSections();

                        getActiveSections();

        

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

		              }

		              if(ctrl_page[2]='bulk_edit')

		              {

		               get_ActiveRetMasters();   

		              }

		               get_ActiveCategory();

					   get_ActiveTheme();

					   get_ActiveKarigar(); get_hooktype();

					   get_ScrewType();

					   get_ActivePurity();get_ActiveMaterial();

					   get_ActiveUOM();

					   get_Activesize();

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

	        get_ActiveSubDesigns();

	        get_ActiveKarigar();

	    break;

	    

		case 'financial_year':

		               get_financial_year_list();

		break;

		case 'reorder_settings':

		               get_Activeproduct('');

		               //get_reorder_settings();

		               

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

		               get_section_details();

		break;

		

		case 'feedback':

		               get_feedback_details();

		break;

		case 'charges':

		               get_charges_list();

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

        		$("#des_des_name").select2("val",'');

    		}

    	});

    }

    

    function get_ActiveSubDesingns()

    {

    	$('#des_select option').remove();

    	$.ajax({

    	type: 'POST',

    	url: base_url+'index.php/admin_ret_catalog/get_ActiveSubDesingns',

    	data :{'id_product':$('#des_prod_name').val(),'design_no':$('#des_des_name').val()},

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

    

	

	/*$('#get_tag_details').on('click',function(){

        $("div.overlay").css("display", "block"); 

        var des_cat_id = $('#des_cat_name').val();

    	var des_pro_id = $('#des_prod_name').val();

    	var des_des_id = $('#des_des_name').val();

    	var des_mc_type = $("#old_mc_type").val();

    	var des_mc_val =  $("#old_mc_value").val();

    	var des_was_val =  $("#old_wast_per").val();

    	var catname = ret_selected_category.name;

    	console.log(ret_pro_sellecgted);

    	var proname = ret_pro_sellecgted.product_name;

    	var designupdatedata = [];

        if(des_cat_id != "" && des_pro_id != "" && des_des_id != ""){

            $.each(ret_selected_design, function (dkey, ditem) {

                if(des_mc_type != 0 && des_mc_val == "" && des_was_val == ""){

                    designupdatedata = $.grep(ret_selected_design, function(v) {

                        return v.mc_cal_type == des_mc_type;

                    });

                }else if(des_mc_type != 0 && des_mc_val != "" && des_was_val == ""){

                     designupdatedata = $.grep(ret_selected_design, function(v) {

                        return v.mc_cal_type == des_mc_type && v.mc_cal_value ==des_mc_val;

                    });

                }else if(des_mc_type != 0 && des_mc_val != "" && des_was_val != ""){

                     designupdatedata = $.grep(ret_selected_design, function(v) {

                        return v.mc_cal_type == des_mc_type && parseFloat(v.mc_cal_value) == parseFloat(des_mc_val) && parseFloat(v.wastag_value) == parseFloat(des_was_val);

                    });

                }else{

                    designupdatedata.push(ditem);

                }

            });

            set_design_update_list(designupdatedata, catname, proname);

        }

        

        $("div.overlay").css("display", "none"); 

	});*/

	

	

	

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

                                                action_content= row.wastage_type == 2 ? '<a href="#" class="btn btn-primary btn-view" id="edit" role="button" data-toggle="modal" data-id='+id+' data-target="#wastage"><i>view</i></a>' : '-';

                                                return action_content;

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

	    if($("#update_mc_type").val() == 0 && $("#wastage_percent").val() == "" && $("#mc_value").val() == ""){

	         $.toaster({ priority : 'danger', title : 'Warning!', message : "Please select / enter proper values to update"});

	    }else{

	        $("#bulk_edit_confirm_modal").modal('show');

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

			var wastageType = $("input[name='design[wastage_type]']:checked").val();

			if(wastageType == 1)

			{

				wastage = $("#wastag_value").val();

			}

			else

			{

				$("#wc_detail tbody tr").each(function(index, value){

					var wastages = { "from_wt"    :  $(value).find(".from_wt").val(), 

									 "to_wt"      :  $(value).find(".to_wt").val(), 

									 "wc_percent" :  $(value).find(".wc_percent").val(), 

									  "mc"        :  $(value).find(".mc_percent").val()

									} ;



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

            			'wastageType'    :  wastageType,

						'wastag_value'  :   wastage

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

 	$("#add_stonenew,#add_newstone").on('click',function(){

		var isChecked = $('#stone_type:checked').val();		

		if($("#uom_id").val() != '' && $("#stone_name").val() != '' && $("#stone_code").val() != '' && $("#is_certificate_req").val() != '' && $("#is_4c_req").val() != ''){

		    add_stone($("#uom_id").val(),$("#stone_name").val(),$("#stone_code").val(),$("input[name='stone_type']:checked").val(),$("#is_certificate_req").val(),$("#is_4c_req").val(),$("#stone_status").val());

			$("#uom_id").val('');

			$("#stone_name").val('');

			$("#stone_code").val('');

			$("input[name='stone_type']:checked").val('');

			$("#is_certificate_req").val('');

			$("#is_4c_req").val('');

			}

		else if($("#uom_id").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter UOM name.</div>';				

			$("div.overlay").css("display", "none"); 	

	        $('#error-msg').html(msg);	

	        return false;        

		}

		else if($("#stone_name").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Stone name.</div>';				

			$("div.overlay").css("display", "none"); 	

	        $('#error-msg').html(msg);

	        return false;

		}

		else if($("#stone_code").val() == ''){

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Stone code.</div>';				

			$("div.overlay").css("display", "none"); 	

	        $('#error-msg').html(msg);

	        return false;

		}

	 });

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

	   

		if($("#category_name").val() != '' && $("#id_metal_category").val() != '' && $("#cat_code").val() != '' && $("#pur_id").val() != '' && $("#hsn_code").val() != ''){

			var file = $("#categorymtr_img")[0].files[0];

			add_categorymtr($('#category_name').val(),$('#cat_code').val(),$('#category_desc').val(),$('#id_metal_category').val(),$('#pur_id').val(),$('#add_category_status').val(),file,$("#hsn_code").val());

			$('#category_name').val('');

			$('#hsn_code').val('');

			$('#cat_code').val('');

			$('#category_desc').val('');

			$('#id_metal_category').val('');

			$('#pur_id').val('');

			$('#categorymtr_img').val('');

			$('#add_category_status').val('');

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

		var description   = $("#ed_category_desc").val();

		var id_metal      = $('#id_metal_cate').val();

		var id_purity     = $('#ed_pur_id').val();

		var status        = $('#edit_category_status').val();

		if($("#ed_category_name").val() != ''  && $("#id_metal_cate").val() != '' && $("#ed_cate_code").val() != '' && $("#ed_hsn_code").val() != ''){

	    	update_categorymtr(name,cat_code,description,id_metal,id_purity,status,file,hsn_code);

	    }

		else if($("#ed_category_name").val() == ''){			

			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Enter Category Name .</div>';				

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
		});
	}

	function getActiveSubDesignsForKarigar(itemObj) {

		let parent_class = $(itemObj).parent().parent().parent();

		$(parent_class).find(".wastage_sub_design").empty();

		let karigar_wast_design_id = $(parent_class).find(".wastage_design").val();
		let karigar_wast_product_id = $(parent_class).find(".wastage_product").val();

		let karg_wast_sub_design_href = base_url+'index.php/admin_ret_catalog/get_ActiveSubDesingns';
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
		});
	}

	function add_karigar_wastage()
	{
		if(validate_karigar_wastages()) {

			let _html_add = '<div class="row wastages">'+					       
								'<div class="col-md-3">'+
									'<div class="form-group">'+
										'<select class="form-control wastage_product" name="karigar[wastage][product][]" placeholder="Product Name"></select>'+
									'</div>'+
								'</div>'+
								'<div class="col-md-3">'+
									'<div class="form-group">'+
										'<select class="form-control wastage_design"  name="karigar[wastage][design][]" placeholder="Design Name"></select>'+
									'</div>'+
								'</div>'+
								'<div class="col-md-3">'+
									'<div class="form-group">'+
										'<select class="form-control wastage_sub_design"  name="karigar[wastage][sub_design][]" placeholder="Sub Design Name"></select>'+
									'</div>'+
								'</div>'+
								'<div class="col-md-2">'+
									'<div class="form-group">'+
										'<input class="form-control wastage_value" name="karigar[wastage][value][]"  type="number" placeholder="Wastage Value" />'+
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
			let wast_prod =  $.trim($(this).find('.wastage_product').val());
			let wast_desn =  $.trim($(this).find('.wastage_design').val());
			let wast_sub_desn 	=  $.trim($(this).find('.wastage_sub_design').val());
			let wast_val 		=  $.trim($(this).find('.wastage_value').val());

			if(!wast_prod > 0 || !wast_desn > 0 || !wast_sub_desn > 0 || !wast_val > 0) {
				wastages_validated = false;
				return false;
			}

		});
		return wastages_validated;
	}

   $('#add_newuser').on('click',function(){

	  var user_type = $("input[name='karigar[user_type]']:checked").val();

	  if($('#first_name').val()=='' || $('#karigar_code').val()=='' || $('#mobile').val()=='')

	  {

		  $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill The Required Fields..'});

	  }

	  else if(user_type==1 && ($('#company_karigar').val()=='' || $('#gst_number_karigar').val()=='' || $('#pan_number_karigar').val()=='' ))

	  {

		  $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill The Required Fields..'});

	  }

	  else if(!validate_karigar_wastages())

	  {

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Wastages Not Added.Please Fill The Required Fields..'});

	  }

	  else
	
	  {

		  $('#karigar_form').submit();

	  }

  });

  

  $("#country,#ed_country").on('change', function() {

	  get_state(this.value);

  });

  $("#state,#ed_state").on('change', function() {

	  get_city(this.value);

  });

  $("#user_img,#ed_user_img").change( function(e){

	  e.preventDefault(); 

	  validate_user_Image(this);

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

	$("#tgrp_sel,#ed_tgrp_sel").select2();

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

					                	    		status_url = base_url+"index.php/admin_ret_catalog/purity_status/"+(row.status==1?0:1)+"/"+row.id_purity; 

							                		return "<a href='"+status_url+"'><i class='fa "+(row.status==1?'fa-check':'fa-remove')+"' style='color:"+(row.status==1?'green':'red')+"'></i></a>"

						                				}

								              		},

													{ "mDataProp": function ( row, type, val, meta ) {

														 id= row.id_purity;

														 edit_target=(access.edit=='0'?"":"#confirm-edit");

														 delete_url=(access.delete=='1' ? base_url+'index.php/purity/delete/'+id : '#' );

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

					                	    		status_url = base_url+"index.php/admin_ret_catalog/color_status/"+(row.status==1?0:1)+"/"+row.id_color; 

							                		return "<a href='"+status_url+"'><i class='fa "+(row.status==1?'fa-check':'fa-remove')+"' style='color:"+(row.status==1?'green':'red')+"'></i></a>"

						                				}

								              		},

													{ "mDataProp": function ( row, type, val, meta ) {

														 id= row.id_color;

														 edit_target=(access.edit=='0'?"":"#confirm-edit");

														 delete_url=(access.delete=='1' ? base_url+'index.php/color/delete/'+id : '#' );

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

					                	    		status_url = base_url+"index.php/admin_ret_catalog/cut_status/"+(row.status==1?0:1)+"/"+row.id_cut; 

							                		return "<a href='"+status_url+"'><i class='fa "+(row.status==1?'fa-check':'fa-remove')+"' style='color:"+(row.status==1?'green':'red')+"'></i></a>"

						                				}

								              		},

													{ "mDataProp": function ( row, type, val, meta ) {

														 id= row.id_cut;

														 edit_target=(access.edit=='0'?"":"#confirm-edit");

														 delete_url=(access.delete=='1' ? base_url+'index.php/cut/delete/'+id : '#' );

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

					                	    		status_url = base_url+"index.php/admin_ret_catalog/clarity_status/"+(row.status==1?0:1)+"/"+row.id_clarity; 

							                		return "<a href='"+status_url+"'><i class='fa "+(row.status==1?'fa-check':'fa-remove')+"' style='color:"+(row.status==1?'green':'red')+"'></i></a>"

						                				}

								              		},

													{ "mDataProp": function ( row, type, val, meta ) {

														 id= row.id_clarity;

														 edit_target=(access.edit=='0'?"":"#confirm-edit");

														 delete_url=(access.delete=='1' ? base_url+'index.php/clarity/delete/'+id : '#' );

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

					                	 status_url = base_url+"index.php/admin_ret_catalog/branch_floor/update_status/"+row.floor_id+"/"+(row.floor_status==1?0:1); 

							             return "<a href='"+status_url+"'><i class='fa "+(row.floor_status==1?'fa-check':'fa-remove')+"' style='color:"+(row.floor_status==1?'green':'red')+"'></i></a>"

						                }

							      		},

										{ "mDataProp": function ( row, type, val, meta ) {

											 id= row.floor_id;

											 edit_target=(access.edit=='0'?"":"#confirm-edit");

											 delete_confirm=(access.delete=='1' ?'#confirm-delete':'');

											 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/branch_floor/delete/'+id : '#' );

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

					                	    		status_url = base_url+"index.php/admin_ret_catalog/floor_counter/update_status/"+row.counter_id+"/"+(row.counter_status==1?0:1); 

							                		return "<a href='"+status_url+"'><i class='fa "+(row.counter_status==1?'fa-check':'fa-remove')+"' style='color:"+(row.counter_status==1?'green':'red')+"'></i></a>"

						                				}

								              		},

													{ "mDataProp": function ( row, type, val, meta ) {

														 id= row.counter_id;

														 edit_target=(access.edit=='0'?"":"#confirm-edit");

														 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/floor_counter/delete/'+id : '#' );

														 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

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

	$.ajax({

	type: 'GET',

	url: base_url+'index.php/admin_ret_catalog/branch_floor/active_floors',

	dataType:'json',

	success:function(data){

		var id =  $("#counter_flr_id,#ed_counter_flr_id").val();

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

		    $("#floor_filter").select2("val",'');

		    $("#counter_flr_sel,#ed_counter_flr_sel").select2("val",(id!='' && id>0?id:''));

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

				                	    		status_url = base_url+"index.php/admin_ret_catalog/making_type/update_status/"+row.mak_id+"/"+(row.mak_status==1?0:1); 

						                		return "<a href='"+status_url+"'><i class='fa "+(row.mak_status==1?'fa-check':'fa-remove')+"' style='color:"+(row.mak_status==1?'green':'red')+"'></i></a>"

					                				}

							              		},

												{ "mDataProp": function ( row, type, val, meta ) {

													 id= row.mak_id;

													 edit_target=(access.edit=='0'?"":"#confirm-edit");

													 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/making_type/delete/'+id : '#' );

													 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

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

					                	 status_url = base_url+"index.php/admin_ret_catalog/material/update_status/"+row.material_id+"/"+(row.material_status==1?0:1); 

							             return "<a href='"+status_url+"'><i class='fa "+(row.material_status==1?'fa-check':'fa-remove')+"' style='color:"+(row.material_status==1?'green':'red')+"'></i></a>"

						                }

							      		},

										{ "mDataProp": function ( row, type, val, meta ) {

											id= row.material_id;

											edit_target=(access.edit=='0'?"":"#confirm-edit");

											delete_confirm=(access.delete=='1' ?'#confirm-delete':'');

											delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/material/delete/'+id : '#' );

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

				                	    		status_url = base_url+"index.php/admin_ret_catalog/stone/update_status/"+row.stone_id+"/"+(row.stone_status==1?0:1); 

						                		return "<a href='"+status_url+"'><i class='fa "+(row.stone_status==1?'fa-check':'fa-remove')+"'style='color:"+(row.stone_status==1?'green':'red')+"'></i></a>"

					                				}

							              		},

												{ "mDataProp": function ( row, type, val, meta ) {

													 id= row.stone_id;

													 edit_target=(access.edit=='0'?"":"#confirm-edit");

													 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/stone/delete/'+id : '#' );

													 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

													 action_content='<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i> </a><a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

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

//to add stone

function add_stone(uom_id,stone_name,stone_code,stone_type,is_certificate_req,is_4c_req,stone_status)

{

	my_Date = new Date();

	$.ajax({

		url: base_url+"index.php/admin_ret_catalog/stone/add?nocache=" + my_Date.getUTCSeconds(),

		data:{"uom_id":uom_id,"stone_name":stone_name,"stone_code":stone_code,"stone_type":stone_type,"is_certificate_req":is_certificate_req,"is_4c_req":is_4c_req,"stone_status":stone_status},

		type:"POST",

		dataType:"JSON",

		cache:false,

		success:function(data){

			$('#stone_id').val('');

			msg='<div class ="alert alert-'+data.class+'"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>   '+data.title+'   </strong> '+data.message+' </div>';

			$('#chit_alert').html(msg);

		    set_stone_table();

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

					                	 status_url = base_url+"index.php/admin_ret_catalog/uom/update_status/"+row.uom_id+"/"+(row.uom_status==1?0:1); 

							             return "<a href='"+status_url+"'><i class='fa "+(row.uom_status==1?'fa-check':'fa-remove')+"' style='color:"+(row.uom_status==1?'green':'red')+"'></i></a>"

						                }

							      		},

										{ "mDataProp": function ( row, type, val, meta ) {

											id= row.uom_id;

											edit_target=(access.edit=='0'?"":"#confirm-edit");

											delete_confirm=(access.delete=='1' ?'#confirm-delete':'');

											delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/uom/delete/'+id : '#' );

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

				if(access.add == '0')

				{ 	

					$('#add_category').attr('disabled','disabled');

				}

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

										{ "mDataProp": function ( row, type, val, meta ){

					                	 status_url = base_url+"index.php/admin_ret_catalog/category/update_status/"+row.id_ret_category+"/"+(row.status==1?0:1); 

							             return "<a href='"+status_url+"'><i class='fa "+(row.status==1?'fa-check':'fa-remove')+"' style='color:"+(row.status==1?'green':'red')+"'></i></a>"

						                }

							      		},

										{ "mDataProp": function ( row, type, val, meta ) {

											id= row.id_ret_category;

											edit_target=(access.edit=='0'?"":"#confirm-edit");

											delete_confirm=(access.delete=='1' ?'#confirm-delete':'');

											delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/category/delete/'+id : '#' );

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

function add_categorymtr(name,cat_code,description,id_metal,id_purity,status,file,hsn_code)

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

			$('#ed_category_desc').val(data.description);

			$('#id_metal_cate').val(data.id_metal);

			$("#metal_category1").select2("val",(data.id_metal!='' && data.id_metal>0?data.id_metal:''));

			 $('#ed_purity_sel').select2("val",data.purity);

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

function update_categorymtr(name,cat_code,description,id_metal,id_purity,status,file,hsn_code)

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

function get_country()

{

    my_Date = new Date();

	$.ajax({

	  type: 'GET',

	  url:  base_url+'index.php/settings/company/getcountry?nocache='+ my_Date.getUTCSeconds(),

	  dataType: 'json',

	  cache:false,

	  success: function(country) {

		$.each(country, function (key, country) {

			$("#country,#ed_country").append(

				$("<option></option>")

				  .attr("value",country.id)

				  .text(country.name)

			);

		});

			var selectid=$('#countryval').val();

			if(selectid!=null)

			{

				$('#country').val(selectid);

				$('#ed_country').val(selectid);

				get_state(selectid);

			}

		}

	});

}

//to get state details based on country selection

function get_state(id)

{

	my_Date = new Date();

	$('#state option').remove();

	$.ajax({

	  type: 'POST',

	   data:{'id_country':id },

	  url:  base_url+'index.php/settings/company/getstate/?nocache='+ my_Date.getUTCSeconds(),

	  cache:false,

	  dataType: 'json',

	  success: function(state) {

		$.each(state, function (key, state) {			  	

			$("#state,#ed_state").append(

				$("<option></option>")

				  .attr("value", state.id)

				  .text(state.name)

			);

		});

		var selectid=$('#stateval').val();

		if(selectid!=null)

		{

			$('#state').val(selectid);

			$('#ed_state').val(selectid);

		    get_city(selectid);

	    }

	  }

	});

}

//to get city based on state selection

function get_city(id)

{  	

	my_Date = new Date();

    $('#city option').remove();

	my_Date = new Date();

	$.ajax({

	  type: 'POST',

	  data:{'id_state':id },

	  url:  base_url+"index.php/settings/company/getcity?nocache=" + my_Date.getUTCSeconds(),

	  cache:false,

	  dataType: 'json',

	  success: function(city) {

		$.each(city, function (key, city) {

			$("#city,#ed_city").append(

				$("<option></option>")

				  .attr("value", city.id)

				  .text(city.name)

			);

		});

		var selectid=$('#cityval').val();

		if(selectid!=null)

		{

			$('#city').val(selectid);

			$('#ed_city').val(selectid);

	    }

	  }

	});

}

function set_karigar_table(from_date="",to_date="")

{

	my_Date = new Date();

	$.ajax({

			 url:base_url+ "index.php/admin_ret_catalog/karigar?nocache=" + my_Date.getUTCSeconds(),

			 data: ( {'from_date':from_date,'to_date':to_date}),

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

           		                "buttons" : ['excel','print'],

						        "tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },

									"aaData": karigar,

									"aoColumns": [	{ "mDataProp": "id_karigar" },

													{ "mDataProp": "firstname" },

													{ "mDataProp": "urname" },

													{ "mDataProp": "contactno1" },

													{ "mDataProp": function ( row, type, val, meta ){

					                	    		status_url = base_url+"index.php/admin_ret_catalog/karigar/update_status/"+row.id_karigar+"/"+(row.status_karigar==1?0:1); 

							                		return "<a href='"+status_url+"'><i class='fa "+(row.status_karigar==1?'fa-check':'fa-remove')+"'style='color:"+(row.status_karigar==1?'green':'red')+"'></i></a>"

						                				}

								              		},

													{ "mDataProp": function ( row, type, val, meta ) {

														 id= row.id_karigar;

														 edit_url=(access.edit=='1' ? base_url+'index.php/admin_ret_catalog/karigar/edit/'+id : '#' );

														 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/karigar/delete/'+id : '#' );

														 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

														 action_content='<a href="'+edit_url+'" class="btn btn-primary btn-edit"><i class="fa fa-edit" ></i></a><a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

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

					                	 status_url = base_url+"index.php/admin_ret_catalog/tag/update_status/"+row.tag_id+"/"+(row.tag_status==1?0:1); 

							             return "<a href='"+status_url+"'><i class='fa "+(row.tag_status==1?'fa-check':'fa-remove')+"' style='color:"+(row.tag_status==1?'green':'red')+"'></i></a>"

						                }

							      		},

										{ "mDataProp": function ( row, type, val, meta ) {

											id= row.tag_id;

											edit_target=(access.edit=='0'?"":"#confirm-edit");

											delete_confirm=(access.delete=='1' ?'#confirm-delete':'');

											delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/tag/delete/'+id : '#' );

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

					                	 status_url = base_url+"index.php/admin_ret_catalog/tax/update_status/"+row.tax_id+"/"+(row.tax_status==1?0:1); 

							             return "<a href='"+status_url+"'><i class='fa "+(row.tax_status==1?'fa-check':'fa-remove')+"' style='color:"+(row.tax_status==1?'green':'red')+"'></i></a>"

						                }

							      		},

										{ "mDataProp": function ( row, type, val, meta ) {

											id= row.tax_id;

											edit_target=(access.edit=='0'?"":"#confirm-edit");

											delete_confirm=(access.delete=='1' ?'#confirm-delete':'');

											delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/tax/delete/'+id : '#' );

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

		   $("#metal_sel,#metal_category,#metal_category1").append(

		   $("<option></option>")

		   .attr("value", item.id_metal)    

		   .text(item.metal)  

		   );

		});

		 

		$("#metal_sel,#metal_category,#metal_category1").select2(

		{

		placeholder:"Select metal",

		allowClear: true    

		});

		   $("#metal_sel,#metal_category,#metal_category1").select2("val",(id!='' && id>0?id:''));

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

		$.each(data, function (key, item) {   

		    $("#tgrp_sel,#ed_tgrp_sel,#tax_sel,#filtertax_sel,#up_tax_sel").append(

		    $("<option></option>")

		    .attr("value", item.tgrp_id)    

		    .text(item.tgrp_name)  

		    );

		}); 

		

		if($("#tax_sel").length > 0){

			$("#tax_sel").select2("val",(id!='' && id>0?id:'')); 

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

													{ "mDataProp": "name" },

													{ "mDataProp": "section_name" },

                                                    /*	{ "mDataProp": function ( row, type, val, meta ) {

                                                    id         = row.image;

                                                    action_content='<a href="#" class="btn-del"><img src='+id+' width="50px;" height="40px;"></a>';

                                                    return action_content;

                                                    }},*/

                                                    //	{ "mDataProp": "image" },

													{ "mDataProp": function ( row, type, val, meta ){

					                	    		status_url = base_url+"index.php/admin_ret_catalog/ret_product/update_status/"+row.pro_id+"/"+(row.product_status==1?0:1); 

							                		return "<a href='"+status_url+"'><i class='fa "+(row.product_status==1?'fa-check':'fa-remove')+"'style='color:"+(row.product_status==1?'green':'red')+"'></i></a>"

						                				}

								              		},

													{ "mDataProp": function ( row, type, val, meta ) {

														 id= row.pro_id;

														 edit_target=(base_url+'index.php/admin_ret_catalog/ret_product/edit/'+id);

														 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/ret_product/delete/'+id : '#' );

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

					                	 status_url = base_url+"index.php/admin_ret_catalog/tgrp/update_status/"+row.tgrp_id+"/"+(row.tgrp_status==1?0:1); 

							             return "<a href='"+status_url+"'><i class='fa "+(row.tgrp_status==1?'fa-check':'fa-remove')+"' style='color:"+(row.tgrp_status==1?'green':'red')+"'></i></a>"

						                }

							      		},

										{ "mDataProp": function ( row, type, val, meta ) {

											id= row.tgrp_id;

											edit_url=(base_url+'index.php/admin_ret_catalog/tgrp/edit/'+id);

											delete_confirm=(access.delete=='1' ?'#confirm-delete':'');

											delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/tgrp/delete/'+id : '#' );

											action_content='<a href='+edit_url+' class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'><i class="fa fa-edit" ></i></a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

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

				if(access.add == '0')

				{ 	

					$('#add_metal').attr('disabled','disabled');

				}

				

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

					                	 status_url = base_url+"index.php/admin_ret_catalog/metal/update_status/"+row.id_metal+"/"+(row.metal_status==1?0:1); 

							             return "<a href='"+status_url+"'><i class='fa "+(row.metal_status==1?'fa-check':'fa-remove')+"' style='color:"+(row.metal_status==1?'green':'red')+"'></i></a>"

						                }

							      		},

										{ "mDataProp": function ( row, type, val, meta ) {

											id= row.id_metal;

											edit_target=(access.edit=='0'?"":"#confirm-edit");

											delete_confirm=(access.delete=='1' ?'#confirm-delete':'');

											delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/metal/delete/'+id : '#' );

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

		

		$("#des_cat_name").select2("val",'');

		    

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

	     $("#id_product").val('');

		var id              =  $("#id_product").val();

		var ed_product      =  $("#ed_product").val();

		var product         =  $("#product").val();

		var size_product    =  $("#size_product").val();

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

		    $("#des_prod_name").select2("val",'');

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

			closeOnSelect: false		    

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

					                	    		status_url = base_url+"index.php/admin_ret_catalog/ret_design/update_status/"+row.design_no+"/"+(row.design_status==1?0:1); 

							                		return "<a href='"+status_url+"'><i class='fa "+(row.design_status==1?'fa-check':'fa-remove')+"'style='color:"+(row.design_status==1?'green':'red')+"'></i></a>"

						                				}

								              		},

													{ "mDataProp": function ( row, type, val, meta ) {

														 id= row.design_no;

														 edit_target=(base_url+'index.php/admin_ret_catalog/ret_design/edit/'+id);

														 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/ret_design/delete/'+id : '#' );

														 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

														 action_content='<a href='+edit_target+' class="btn btn-primary btn-edit" id="edit" role="button" onclick="getproduct_edit('+id+')"><i class="fa fa-edit" ></i></a> <a href="#" class="btn btn-danger btn-del" onclick="delete_design('+id+')"  ><i class="fa fa-trash"></i></a>'

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

					                	 status_url = base_url+"index.php/admin_ret_catalog/screw/update_status/"+row.screw_id+"/"+(row.screw_status==1?0:1); 

							             return "<a href='"+status_url+"'><i class='fa "+(row.screw_status==1?'fa-check':'fa-remove')+"' style='color:"+(row.screw_status==1?'green':'red')+"'></i></a>"

						                }

							      		},

										{ "mDataProp": function ( row, type, val, meta ) {

											id= row.screw_id;

											edit_target=(access.edit=='0'?"":"#confirm-edit");

											delete_confirm=(access.delete=='1' ?'#confirm-delete':'');

											delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/screw/delete/'+id : '#' );

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

					                	 status_url = base_url+"index.php/admin_ret_catalog/hook/update_status/"+row.hook_id+"/"+(row.hook_status==1?0:1); 

							             return "<a href='"+status_url+"'><i class='fa "+(row.hook_status==1?'fa-check':'fa-remove')+"' style='color:"+(row.hook_status==1?'green':'red')+"'></i></a>"

						                }

							      		},

										{ "mDataProp": function ( row, type, val, meta ) {

											id= row.hook_id;

											edit_target=(access.edit=='0'?"":"#confirm-edit");

											delete_confirm=(access.delete=='1' ?'#confirm-delete':'');

											delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/hook/delete/'+id : '#' );

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

						active_url =base_url+"index.php/admin_ret_catalog/financial_status/"+(row.fin_status==1?0:1)+"/"+row.fin_id; 

						return "<a href='"+active_url+"'><i class='fa "+(row.fin_status==1?'fa-check':'fa-remove')+"' style='color:"+(row.fin_status==1?'green':'red')+"'></i></a>"

						}

						},

						{ "mDataProp": "created_on" },		

						{ "mDataProp": function ( row, type, val, meta ) {

							 id= row.fin_id

							 edit_url=(access.edit=='1' ? base_url+'index.php/admin_ret_catalog/financial_year/edit/'+id : '#' );

							 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/financial_year/delete/'+id : '#' );

							 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

							 action_content='<a href="'+edit_url+'" class="btn btn-primary btn-edit"><i class="fa fa-edit" ></i></a><a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>';

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

        		

		        $("#weight_design").select2("val",'');

		        

		       if(ed_wt_range_des!='' && ed_wt_range_des!=undefined  && ed_wt_range_des!=null)

		        {

		            $("#ed_wt_range_des").select2("val",ed_wt_range_des);

		        }else{

		            $("#ed_wt_range_des").select2("val",'');

		        }

		        

		}

	});

}

function set_weight_table(id_product)

{

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

									"bSort": true,

									"aaData": weight,

									"order": [[ 0, "desc" ]],

									"aoColumns": [	

								                    { "mDataProp": "id_weight" },

								                    { "mDataProp": "product_name" },

								                    { "mDataProp": "design_name" },

								                    { "mDataProp": "value" },

													{ "mDataProp": "from_weight" },

													{ "mDataProp": "to_weight" },

													{ "mDataProp": function ( row, type, val, meta ) {

														 id= row.id_weight;

														 edit_target=(access.edit=='0'?"":"#confirm-edit");

														 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/weight/Delete/'+id : '#' );

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

$(document).on('click', "#weight_list a.btn-edit", function(e) {

		$("#ed_from_weight").val('');

		$("#ed_to_weight").val('');

		e.preventDefault();

		id=$(this).data('id');

	    get_weight(id);

	    $("#edit-id").val(id);  

});

$("#add_weight").on('click',function(){

    

    var from_weight     =$('#from_weight').val();

    var to_weight       =$('#to_weight').val();

    var name            =$('#name').val();

    var id_product      =$('#weight_prod').val();

    var id_design       =$('#weight_design').val();

    var uom             =$('#uom').val();

    

    if(name!='' && from_weight!='' && to_weight!='' && id_product!=null && uom!='')

    {

        add_weight(name,from_weight,to_weight,id_product,id_design,uom);

    }else{

        alert('Please Fill The Required Fields');

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

		if(name!='' && from_weight!='' && to_weight!='' && id_product!=null)

		{

		    update_weight(name,from_weight,to_weight,id_product,id_design,uom,id);

		}else{

            alert('Please Fill The Required Fields');

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

		    get_weight_range_product('');

		    product_design(data.id_product);

		    get_weight_range_details(data.id_product,data.id_design);

		    getActive_uom();

		}

	});

}

//to add weight 

function add_weight(name,from_weight,to_weight,id_product,id_design,uom)

{

	my_Date = new Date();

	$.ajax({

		data:{'name':name,'from_weight':from_weight,'to_weight':to_weight,'id_product':id_product,'id_design':id_design,'uom':uom},

		url: base_url+"index.php/admin_ret_catalog/weight/Add?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		dataType: 'json',

		cache:false,

		success:function(data){

		    //$('#confirm-add').modal('toggle');

			$('#from_weight').val('');

			$('#to_weight').val('');

			//$('#name').val('');

            if(data.status)

		    {

		        msg='<div class = "alert alert-success"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Success!</strong>'+data.msg+'</div>';

		    }else

		    {

		       	msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Success!</strong>'+data.msg+'</div>';

		    }

			$('#chit_alert').html(msg);

			//window.location.reload();

			set_weight_table();

		}

	});

}

function update_weight(name,from_weight,to_weight,id_product,id_design,uom,id)

{

	my_Date = new Date();

	$.ajax({

		data:{'name':name,'from_weight':from_weight,'to_weight':to_weight,'id_product':id_product,'id_design':id_design,'uom':uom},

		url: base_url+"index.php/admin_ret_catalog/weight/Update/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",	

		dataType: 'json',

		cache: false,

		success:function(data){

		    $('#confirm-edit').modal('toggle');

			$('#ed_from_weight').val('');

			$('#ed_to_weight').val('');

			$('#ed_name').val('');

		    //set_weight_table();

		  

		    window.location.reload();

		}

	});

}

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

$('#from_weight').on('change',function(){

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

});

//Re-Order settings

function getFilteWeightRange()

{

    var wt_range_des=$('#weight_range').val();

        my_Date = new Date();

        $.ajax({

        url: base_url+"index.php/admin_ret_catalog/get_weight_range_details?nocache=" + my_Date.getUTCSeconds(),

        type:"POST",

        data:{'id_product':$('#ret_product').val(),'id_design':$('#ret_design').val()},

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

	}

	get_reorder_settings();

});

$('#ret_design').on('change',function(){

    getFilteWeightRange();

	get_reorder_settings();

});

$('#weight_range').on('change',function(){

	get_reorder_settings();

});

function get_reorder_settings()

{

	$("div.overlay").css("display", "block"); 

	my_Date = new Date();

	$.ajax({

		data:{'id_product':$('#ret_product').val(),'id_design':$('#ret_design').val(),'id_wt_range':$('#weight_range').val()},

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

						"buttons" : ['excel','print'],

						"order": [[ 0, "desc" ]],

						"tableTools": { "buttons": [ { "sExtends": "xls",

						"oSelectorOpts": { page: 'current' 

						} },

						{ "sExtends": "pdf", 

						"oSelectorOpts": { page: 'current' 

						} } ] },				

						"aaData"  : settings,

						"aoColumns": [

						{ "mDataProp": "id_reorder_settings" },

						{ "mDataProp": "branch_name" },

						{ "mDataProp": "product_name" },

						{ "mDataProp": "design_name" },

						{ "mDataProp": "wt_name" },

						{ "mDataProp": "size" },

						{ "mDataProp": "min_pcs" },

						{ "mDataProp": "max_pcs" },

						{ "mDataProp": function ( row, type, val, meta ) {

							 id= row.id_reorder_settings;

							 edit_target=(access.edit=='0'?"":"#confirm-edit");

							 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/reorder_settings/delete/'+id : '#' );

							 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

							 action_content='<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

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

    get_branchname();

    $("#des_select").select2("val",'');

    $("#wt_select").select2("val",'');

});

$('#product_select').on('change',function(){

    if(this.value!='')

    {

        get_design_product(this.value);

        $('#ed_wt_select option').remove();

        get_wt_range_product(this.value,'');

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

	else if(wt_range=='' || wt_range==null)

	{

	    msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Please Select Weight Range</div>';

	    $('#chit_alert').html(msg);

	}

	else

	{

	    if($('#branch_settings').val()==1)

	    {

	        if(validateSettingsDetailRow())

            {

                add_retsettings_new();

            }

            else

            {

                msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Please Fill The Required Fields.</div>';

                $('#chit_alert').html(msg);

            }

	    }

        

	    

	}

});

function validateSettingsDetailRow()

{

    var validate = true;

	$('#total_items > tbody > tr').each(function(index, tr) {

	    

		 if($(this).find('.min_pcs').val() == "" || $(this).find('.max_pcs').val() == "")

            {

                validate=false;

            }

	});

	return validate;

}

$('#update_retsettings').on('click',function(e){

    

	var id_branch   =$('#ed_branch_select').val();

	var wt_range    =$('#ed_wt_select').val();

	var product     =$('#ed_prod_select').val();

	var id_design   =$('#ed_des_select').val();

	var size        =$('#ed_select_size').val();

	var min_pcs     =$('#ed_min_pcs').val();

	var max_pcs     =$('#ed_max_pcs').val();

	var id          =$("#edit-id").val();

	

	$('#update_alert').html('');

	if(product=='' || product==null)

	{

	     msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Please Select Product</div>';

	     $('#update_alert').html(msg);

	}

	else if(id_design=='' || id_design==null)

	{

	    msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Please Select Design</div>';

	    $('#chit_alert').html(msg);

	}

	else if(wt_range=='' || wt_range==null)

	{

	    msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Please Select Weight Range</div>';

	    $('#update_alert').html(msg);

	}

	else if(min_pcs=='')

	{

	    msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Please Enter Minimum Pcs</div>';

	    $('#update_alert').html(msg);

	}

	else if(max_pcs=='')

	{

	    msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Please Enter Maximum Pcs</div>';

	    $('#update_alert').html(msg);

	}

	else if(id_branch=='')

	{

	    msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Please Select Branch</div>';

	    $('#update_alert').html(msg);

	}

	else{

	    update_retsettings(id_branch,wt_range,product,id_design,size,min_pcs,max_pcs);

	}

});

$(document).on('click', "#reorder_set_list a.btn-edit", function(e) {

		e.preventDefault();

		id=$(this).data('id');

	    $("#edit-id").val(id); 

	    getReorderDetails(id);

});

     function get_branchname(){	

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

                        allowClear: true		    

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

                        allowClear: true		    

                        });					

                        $("#branch_select").select2("val",(branch!='' && branch>0?branch:''));	 

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

function getReorderDetails(id)

{

	 get_branchname();

	my_Date = new Date();

	$.ajax({

		url: base_url+"index.php/admin_ret_catalog/reorder_settings/edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		type:"GET",

		dataType: 'json',

		cache:false,

		success:function(data){

		    				

		    $('#id_branch').val(data.id_branch);

		    $('#ed_wt_range').val(data.wt_range);

		    $('#ed_prod_select').val(data.id_product);

		    $('#ed_product').val(data.id_product);

		    $('#ed_id_design').val(data.id_design);

		    $('#ed_id_size').val(data.id_size);

		    $('#ed_select_size').val(data.size);

		    $('#ed_min_pcs').val(data.min_pcs);

		    $('#ed_max_pcs').val(data.max_pcs);

		    $("#edit-id").val(id); 

		    get_Activesize(data.id_product);

		    get_Activeproduct('');

		}

	});

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

function update_retsettings(id_branch,wt_range,product,id_design,size,min_pcs,max_pcs,id)

{

    

	my_Date = new Date();

	$.ajax({

		data:{'id':id,'id_branch':$('#ed_branch_select').val(),'wt_range':wt_range,'product':product,'id_design':id_design,'size':size,'min_pcs':min_pcs,'max_pcs':max_pcs},

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

function get_wt_range_product(id_product,id_design)

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

}

$('#des_select,#ed_des_select').on('change',function(){

    var id_product=$('#ed_product').val();

    if(this.value!='' && id_product!='')

    {

        $('#ed_wt_select option').remove();

        setTimeout(function(){ get_wt_range_product(id_product,this.value);}, 1000);

        

    }

});

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

		get_wt_range_product(this.value,'');

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

	if($("#product").val() != '')

	{

	add_newsize($('#product').val(),$('#size').val(),$('#units').val());

	$('#name').val('');

	}

	else if($("#name").val() == ''){			

	msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter location.</div>';				

	$("div.overlay").css("display", "none"); 	

	$('#error-msg').html(msg);		

	return false;

	}

});

function add_newsize(id_product,size,units)

{

	my_Date = new Date();

	$.ajax({

		data:{"id_product":id_product,"size":size,"unit":units},

		url: base_url+"index.php/admin_ret_catalog/ret_size/Add?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		cache:false,

		success:function(data){

			$('#name').val('');

			window.location.reload(true);

		}

	});

}

function get_size_details()

{

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

						"aoColumns": [

						{ "mDataProp": "id_size" },

						{ "mDataProp": "product_name" },

						{ "mDataProp": "value" },

						{ "mDataProp": "name" },

						{ "mDataProp": function ( row, type, val, meta ){

						active_url =base_url+"index.php/admin_ret_catalog/update_size_status/"+(row.active==0?1:0)+"/"+row.id_size; 

						return "<a href='"+active_url+"'><i class='fa "+(row.active==1?'fa-check':'fa-remove')+"' style='color:"+(row.active==1?'green':'red')+"'></i></a>"

						}

						},

						{ "mDataProp": function ( row, type, val, meta ) {

							 id= row.id_size;

							 edit_target=(access.edit=='0'?"":"#confirm-edit");

							 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/ret_size/delete/'+id : '#' );

							 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

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

			alert('Please Fill Required Fields');

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

				if(access.add == '0')

				{ 	

					$('#add_metal').attr('disabled','disabled');

				}

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

						{ "mDataProp": "touch_from" },

						{ "mDataProp": "touch_to" },

						{ "mDataProp": function ( row, type, val, meta ) {

							 id= row.id_old_metal_cat;

							 edit_target=(access.edit=='0'?"":"#confirm-edit");

							 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/old_metal_cat/delete/'+id : '#' );

							 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

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

		    $('#ed_touch_from').val(data.touch_from);

		    $('#ed_touch_to').val(data.touch_to);

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

							 action_content='<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

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

		  }else{

		       $('#ed_silver_rate').val(data.rate);

		      $('#silver_row').css('display','block');

		      $('#gold_row').css('display','none');

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

    	msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Section Name.</div>';				

    	$('#error-msg').html(msg);		

    	return false;

	}else{

	    add_section();

	}

});

$("#add_new_section").on('click',function(){

	if($("#section_name").val() == '')

	{			

    	msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Section Name.</div>';				

    	$('#error-msg').html(msg);		

    	return false;

	}else{

	    add_new_section();

	}

});

function add_new_section()

{

	my_Date = new Date();

	$.ajax({

		data:{"section_name":$("#section_name").val()},

		url: base_url+"index.php/admin_ret_catalog/ret_section/Add?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",

		cache:false,

		dataType: 'json',

		success:function(data){

			$('#section_name').val('');

			if(data['status']==true)

			{

			   msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>'+data['message']+'</strong></div>';

			}else{

			    msg='<div class = "alert alert-success"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>'+data['message']+'</strong> </div>';

			}

		

			$('#error-msg').html(msg);	

		    get_section_details();

		}

	});

}

function add_section()

{

	my_Date = new Date();

	$.ajax({

		data:{"section_name":$("#section_name").val()},

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

						{ "mDataProp": function ( row, type, val, meta ){

						active_url =base_url+"index.php/admin_ret_catalog/update_size_status/"+(row.status==0?1:0)+"/"+row.id_section; 

						return "<a href='"+active_url+"'><i class='fa "+(row.status==1?'fa-check':'fa-remove')+"' style='color:"+(row.status==1?'green':'red')+"'></i></a>"

						}

						},

						{ "mDataProp": function ( row, type, val, meta ) {

							 id= row.id_section;

							 edit_target=(access.edit=='0'?"":"#confirm-edit");

							 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/ret_section/delete/'+id : '#' );

							 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

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

		}

	});

}

 $("#update_section").on('click',function(){

			  

	var id=$("#edit-id").val();			  

	if($("#ed_section_name").val() != '')

	{		

	    update_section(id);

	}

	else 

	{

		 msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Please Enter Section Name</strong></div>';

		 $('#error_message').html(msg);	

	}

});

 function update_section(id)

{

	 $("div.overlay").css("display", "block"); 

	my_Date = new Date();

    

	$.ajax({

		data:{"section_name":$("#ed_section_name").val()},

		url: base_url+"index.php/admin_ret_catalog/ret_section/Update/"+id+"?nocache=" + my_Date.getUTCSeconds(),

		type:"POST",	

		cache: false,

		success:function(){

			$("div.overlay").css("display", "none"); 

			location.reload(true);

		}		

	});

}

//Section Master

//Section Mapping

function getRetailSections(){

    my_Date = new Date();

    $.ajax({

        type: 'GET',

        url: base_url+"index.php/admin_ret_catalog/get_section?nocache=" + my_Date.getUTCSeconds(),

        dataType:'json',

        success:function(data){

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

            $("#product_section_select").select2("val",(id!='' && id>0?id:''));

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

   console.log("tag_display", tag_display);

   my_Date = new Date();

        $.ajax({

            url:base_url+ "index.php/admin_ret_catalog/charges/Add?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),

            data:{'charge_name':charge_name,'charge_code':charge_code,'charge_description':charge_description,'value_charge':value_charge,'tag_display':tag_display},

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

   my_Date = new Date();

        $.ajax({

            url:base_url+ "index.php/admin_ret_catalog/charges/Add?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),

            data:{'charge_name':charge_name_save_and_new,'charge_code':charges_code_save_and_new,'charge_description':charges_description_save_and_new,'value_charge':charges_value_save_and_new,'tag_display':tag_display_save_and_new},

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

			         charges_list(data);

			   			$("div.overlay").css("display", "none");

					  },

					  error:function(error)

					  {

						 $("div.overlay").css("display", "none");

					  }

		  });

}

function charges_list(data)

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

							                action_content='<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a> <a href="#" class="btn btn-danger btn-del" onClick="delete_confirm_charges('+id+')" data-toggle="modal" ><i class="fa fa-trash"></i></a>'

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

   my_Date = new Date();

        $.ajax({

            url:base_url+ "index.php/admin_ret_catalog/charges/update?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),

            data:{'name_charge':charge_name_update,'charge_code':charge_code_update,'description_charge':charge_description_update,'value_charge':charge_value_update,'tag_display':tag_display_update,'id_charge':charges_id},

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

						active_url =base_url+"index.php/admin_ret_catalog/ret_sub_design/update_status/"+row.id_sub_design+'/'+(row.status==0?1:0); 

						return "<a href='"+active_url+"'><i class='fa "+(row.status==1?'fa-check':'fa-remove')+"' style='color:"+(row.status==1?'green':'red')+"'></i></a>"

						}

						},

						{ "mDataProp": function ( row, type, val, meta ) {

							 id= row.id_sub_design;

							

							 edit_target=(base_url+'index.php/admin_ret_catalog/ret_sub_design/edit/'+id);

							 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/ret_sub_design/delete/'+id : '#' );

							 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

							 action_content='<a href='+edit_target+' class="btn btn-primary btn-edit" id="edit" role="button"><i class="fa fa-edit" ></i></a><a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

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

							 action_content='<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

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

	html+="<tr id='wc"+i+"'><td>"+i+"</td><td><input name='wcrange["+i+"][from_wt]' style='width:100%;' step='.001' id='from_wt"+i+"' class='form-control from_wt' placeholder='From weight' type='number'></td>"+i+"<td><input name='wcrange["+i+"][to_wt]'  step='.001' style='width:100%;' id='to_wt"+i+"' class='form-control to_wt' placeholder='To weight' type='number'></td><td><input name='wcrange["+i+"][wc_percent]'  step='.001' style='width:100%;' id='wc_percent"+i+"' class='form-control wc_percent' placeholder='WC(%)' type='number'></td><td><input name='wcrange["+i+"][mc_percent]'  step='.001' style='width:100%;' id='mc_percent"+i+"' class='form-control mc_percent' placeholder='MC(%)' type='number'></td><td><a href='#' onClick='remove_row($(this).closest(\'tr\'));' class='btn btn-danger btn-del'><i class='fa fa-trash'></i></a></td>";

  	$('#wc_detail').append(html);



});



$(document).on('click',"#edit_mas_design_list tbody tr a.btn-view", function(){

	id=$(this).data('id');

	 wastage_view(id);

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

            { "mDataProp" : "mc"}

            ] 

            });

        }

    });

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
		  $.toaster({ priority : 'danger', title : 'Warning!', message : "Error occured in retriving data."});
		  console.log("Error on "+href+" : ", error);
		  reject(error);
		},
	  })
	})

}


/** Attribute Master */

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

									{ "mDataProp": "attr_type" },

									{ "mDataProp": function ( row, type, val, meta ){

										status_url = base_url+"index.php/admin_ret_catalog/attribute/update_status/"+row.attr_id+"/"+(row.attr_status==1?0:1); 

										return "<a href='"+status_url+"'><i class='fa "+(row.attr_status==1?'fa-check':'fa-remove')+"'style='color:"+(row.attr_status==1?'green':'red')+"'></i></a>"

									}

									},

									{ "mDataProp": function ( row, type, val, meta ) {

										id= row.attr_id;

										edit_url=(access.edit=='1' ? base_url+'index.php/admin_ret_catalog/attribute/edit/'+id : '#' );

										delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/attribute/delete/'+id : '#' );

										delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

										action_content='<a href="'+edit_url+'" class="btn btn-primary btn-edit"><i class="fa fa-edit" ></i></a><a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'

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

$('#add_newattribute').on('click',function() {

	if($.trim($('#attr_name').val()) == '' || $.trim($('#attr_group_type').val()) == '' || $.trim($('#attr_status').val()) == '')
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