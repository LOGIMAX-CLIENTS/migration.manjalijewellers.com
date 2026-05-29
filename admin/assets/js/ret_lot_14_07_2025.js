// const { access } = require("fs");



var path =  url_params();



var ctrl_page = path.route.split('/');



var img_resource=[];



var pre_img_files=[];



var pre_img_resource=[];



var sp_img_files=[];



var sp_img_resource=[];



var n_img_files=[];



var n_img_resource=[];



var uom_details=[];



var total_files=[];



var lot_cat_details=[];



var section_details=[];



var lot_product_details =[];



var purities=[];



var lot_design=[];



var lot_sub_design=[];



var lot_designs=[];



var lot_sub_designs=[];



var modalStoneDetail = [];



var modalOthermetal = [];



var charges_list = [];



var category_lists = [];



var metal_details=[];







$(document).ready(function() {



	 var path =  url_params();



	 $('#status').bootstrapSwitch();



     prod_info = [];







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



	 	case 'lot_inward':



				 switch(ctrl_page[2]){







				 	case 'list':



				 	        get_ActiveMetals();



				 	        get_employee();



				 		    var date = new Date();



                            var firstDay  = new Date(date.getFullYear(), date.getMonth(),date.getDate() - 0, 1);



                            var from_date =  firstDay.getFullYear()+'-'+(firstDay.getMonth()+1)+'-'+firstDay.getDate();



                            var to_date= (date.getFullYear()+"-"+(date.getMonth() + 1)+"-"+date.getDate());



                            $('#lt_date1').html(from_date);



                            $('#lt_date2').html(to_date);



							$('#lot_type').select2(

							{

								placeholder:'Select Stock Type ',

								allowClear: true

							});

							$('#lot_type').select2('val','');



				 			get_lotInward_list(from_date,to_date);



				 		break;



				 	case 'edit':



					 console.log(lot_preview_item);







					 setTimeout(function(){



						set_edit_lot_row();





					},2000);



					setTimeout(function(){



					get_Branchwise_Sections();



					get_ActiveProduct();



					getActiveUOM();



					get_category();



					get_stones();



					get_stone_types();



					getActive_quality_code();



					getQualityDiamondRates();



					get_ActiveMetals();



					get_ActivePurity();



					get_charges();



					get_taxgroup_items();



				   },1000);





					 set_lot_preview(lot_preview_item);



					// get_category();



					 get_karigar();



					 get_employee();





							$('.lot_product').select2(



								{



									placeholder:'Product',



									allowClear: true



								});





								$('.design').select2(



									{



										placeholder:'Design',



										allowClear: true



									});

								$('.lot_sub_design').select2(



										{



											placeholder:'Sub Design',



											allowClear: true



									});

								$('.lot_section').select2(



									{



										placeholder:'Section',



										allowClear: true



									});





							$("#lt_product").on("keyup",function(e){



								var prod = $("#lt_product").val();



								if(prod.length == 3) {



									getSearchProd(prod);



				                }



							});



							/*$("#design").on("keyup",function(e){



								var sub_prod = $("#design").val();



								if(sub_prod.length == 3) {



									getSearchSubProd(sub_prod,$("#lt_product").val());



				                }



							});*/



							$("#design").on("keyup",function(e){



								var design = $("#design").val();



								if(design.length >= 2 || design.length <= 5) {



									getSearchDesign(design,$("#lt_product_id").val());



						        }



							});



							$("#lt_order_no").on("keyup",function(e){



								var order = $("#lt_order_no").val();



								if(order.length >=2) {



									getSearchOrderNo(order,$("#lt_order_no").val());



				                }



							});



						break;



				 	case 'add':



				 	        get_ActiveGRNS();



				 	        get_ActiveProduct();



				 			get_category();



				 			get_karigar();



				 			get_employee();



				 			getActiveUOM();



				 			get_Branchwise_Sections();





							 get_stones();



							 get_stone_types();



						//	 get_ActiveUOM();



							 getActive_quality_code();



							 getQualityDiamondRates();



							 get_ActiveMetals();



							 get_ActivePurity();



							 get_charges();



							 get_taxgroup_items();



							$("#lt_product").on("keyup",function(e){



								var prod = $("#lt_product").val();



								if(prod.length == 3) {



									getSearchProd(prod);



				                }



							});



							$("#design").on("keyup",function(e){



								var design = $("#design").val();



								if(design.length >= 2 || design.length <= 5) {



									getSearchDesign(design,$("#lt_product_id").val());



						        }



							});



							$("#lt_order_no").on("keyup",function(e){



								var order = $("#lt_order_no").val();



								if(order.length >= 2) {



									getSearchOrderNo(order,$("#lt_order_no").val());



				                }



							});



						       $('.lot_product').select2(



								{



									placeholder:'Product',



									allowClear: true



								});





								$('.design').select2(



									{



										placeholder:'Design',



										allowClear: true



									});

								$('.lot_sub_design').select2(



										{



											placeholder:'Sub Design',



											allowClear: true



									});

								$('.lot_section').select2(



									{



										placeholder:'Section',



										allowClear: true



									});



				 		break;



				 }



	 		break;



			case 'lot_merge':



				$('#gold_smith').select2(



				{



					placeholder:'Select Gold Smith',



					allowClear: true



				});



				$('#lot_no_merge').select2(



				{



					placeholder:'Select Lot No',



					allowClear: true



				});



				get_karigar();



				getActiveUOM();



				get_category();



				getLotidsforMerge();



			break;



			case 'lot_split':



				$('#lot_employee').select2(



				{



					placeholder:'Select Employee',



					allowClear: true



				});



				$('#lot_no_split').select2(



				{



					placeholder:'Select Lot No',



					allowClear: true



				});



				getActiveUOM();



				get_employee();



				getLotidsforSplit();



			break;



	}



	$('#save_all').on('click',function(){



	    if(validateItemDetailRow()){



            $('#lot_form').submit();



            var id_category=$('#id_category').val();



            var id_purity=$('#id_purity').val();



            var id_product_division=$('#id_product_division').val();



            window.location.href= base_url+'index.php/admin_ret_lot/lot_inward/list';



			window.location.reload();





	    }else{



			alert("Please fill required fields..");



			return false;



		}







	})



	$('#ltInward-dt-btn').daterangepicker(



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



			$('#lt_date1').text(start.format('YYYY-MM-DD'));



			$('#lt_date2').text(end.format('YYYY-MM-DD'));



		    get_lotInward_list(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));



		}



	);



	// Filter



	$('#rcvd_branch').on('change',function(e){



		get_lotInward_list($('#lt_date1').text(),$('#lt_date2').text())



	});



	$('#lt_date').datepicker({ dateFormat: 'yyyy-mm-dd', })



	/*Net Weight Calculation*/



	$("#lt_less_wt").on('keyup blur',function(e){



		if($("#lt_net_wt").val() < 0){



			$("#lt_net_wt").css('color','red');



		}else{



			$("#lt_net_wt").css('color','#555');



		}



	})



	$('input[type=radio][name="inward[stock_type]"]').change(function() {





		if(ctrl_page[1]=='lot_merge')



		{



			$('#lot_det > tbody').empty();



			$('#lot_search_list > tbody').empty();



			calculateLotTotal();



			TotalLotMerge();



			getLotidsforMerge();



		}else

		{

			$('#lt_item_list > tbody').empty();



			$('#select_section').select2('val','');



			$('#select_product').select2('val','');



			$('#select_design').select2('val','');



			$('#select_sub_design').select2('val','');



			$('#select_section').prop('disabled', true);



			if(this.value == 1){



				$("#purity").prop('required',true);



				$("#category").prop('required',true);



				$(".tagged").css('display','inline');



				//$('#select_section').prop('disabled', true);



			}



			else if(this.value == 2){



				$("#purity").prop('required',false);



				$("#category").prop('required',false);



				$(".tagged").css('display','none');



				$('#select_section').prop('disabled', false);



			}

		}











	});



	$('input[type=radio][name="search_order_by"]').change(function() {



		if(this.value == 1){



			$(".byCus").css('display','block');



			$(".byOrderno").css('display','none');



		}



		else if(this.value == 2){



			$(".byOrderno").css('display','block');



			$(".byCus").css('display','none');



		}



	});



	$("#cus_name").on("keyup",function(e){



		var customer = $("#cus_name").val();



		if(customer.length >= 3) {



			getSearchCustomers(customer);



		}



	});



	/*$("#lt_gross_wt,#lt_less_wt").on('keyup blur',function(e){



		var gross = $("#lt_gross_wt").val();



		var less = $("#lt_less_wt").val();



		var net = 0;



		if(less){



			if(gross){



				net = (gross > 0 ? gross-less : 0);



				$("#lt_net_wt").val(net);



				console.log(net);



			}



		}else if(gross){



			if(less){



				net = (less > 0 ? gross-less : 0);



				$("#lt_net_wt").val(net);



				console.log(net);



			}



		}



		if(net < 0){



			$("#lt_net_wt").css('color','red');



		}else{



			$("#lt_net_wt").css('color','#555');



		}



	})*/



	/* Lot Received At */



	$('#lt_rcvd_branch_sel').on('change',function(e){



		if(this.value!='')



		{



			$("#id_branch").val(this.value);



		}



		else



		{



			$("#id_branch").val('');



		}



	});



	/* Gold Smith - Starts */



	/*$('#lt_type_select').on('change',function(e){



		if( $('#lt_gold_smith > option').length == 0){



			get_karigar();



		}



	});*/



	$("#lt_gold_smith").select2({



	 	placeholder: "Select Gold Smith",



	 	allowClear: true



 	});



	$('#lt_gold_smith').on('change',function(e){



		if(this.value!='')



		{



			$("#lt_gold_smith_id").val(this.value);



			get_karigar_details(this.value);



		}



		else



		{



			$("#lt_gold_smith_id").val('');



		}



	});



	/* Ends - Gold Smith */



	//Dynamic image upload



	$("#uploadImg_p_stn").on('click',function(){ // For Precious Stone



		$('#uploadArea_p_stn').css('display','block');



	});



	$("#uploadImg_sp_stn").on('click',function(){ // For Semi-Precious Stone



		$('#uploadArea_sp_stn').css('display','block');



	});



	$("#uploadImg_n_stn").on('click',function(){ // For Normal Stone



 		$('#uploadArea_n_stn').css('display','block');



	});



	$("#pre_images,#semi_pre_imgs,#norm_pre_imgs").on('change',function(){



		validateCertifImg(this.id,$('#row_id').val());



	});



// Enable/Disable Stone fields



$('#precious_stone').on('change',function() {



	if ($('#precious_stone').is(":checked")){



		$('#precious_stone').val(1);



		$('#precious_st_pcs').attr('disabled',false);



		$('#precious_st_wt').attr('disabled',false);



		$('#uploadImg_p_stn').attr('disabled',false);



	}else{



		$('#precious_stone').val(0);



		$('#precious_st_pcs').attr('disabled',true);



		$('#precious_st_wt').attr('disabled',true);



		$('#uploadImg_p_stn').attr('disabled',true);



	}



});



$('#semi_precious_stn').on('change',function() {



	if ($('#semi_precious_stn').is(":checked")){



		$('#semi_precious_stn').val(1);



		$('#semi_precious_st_pcs').attr('disabled',false);



		$('#semi_precious_st_wt').attr('disabled',false);



		$('#uploadImg_sp_stn').attr('disabled',false);



	}else{



		$('#semi_precious_stn').val(0);



		$('#semi_precious_st_pcs').attr('disabled',true);



		$('#semi_precious_st_wt').attr('disabled',true);



		$('#uploadImg_sp_stn').attr('disabled',true);



	}



});



$('#normal_stn').on('change',function() {



	if ($('#normal_stn').is(":checked")){



		$('#normal_stn').val(1);



		$('#normal_st_pcs').attr('disabled',false);



		$('#normal_st_wt').attr('disabled',false);



		$('#uploadImg_n_stn').attr('disabled',false);



	}else{



		$('#normal_stn').val(0);



		$('#normal_st_pcs').attr('disabled',true);



		$('#normal_st_wt').attr('disabled',true);



		$('#uploadImg_n_stn').attr('disabled',true);



	}



});



//on selecting category



$('#category').select2().on("change", function(e) {



	var id_category= $("#id_category").val();



	var lt_order_no= '';



	var pro_id=$('#lt_item_list > tbody').find('.pro_id').val();



	var lot_details_count = $('#lt_item_list tbody tr').length;



	if(lot_details_count > 0)



	{



		if(this.value!='')



		{



			var selected_cat = this.value;



			if(id_category != selected_cat && id_category != '')



			{



				proceed = confirm("This Category will be applied to all Added Items. Do you want to proceed ?");



				$("#id_category").val(this.value);



				$('#id_purity').val('');



				$('#purity option').remove();



				if(lt_order_no=='')



				{



					get_cat_purity();



				}







			}



			else



			{



				$(this).val(id_category);



				$("#id_category").val(id_category);



				if(lt_order_no=='')



				{



					get_cat_purity();



				}



			}



		}else



		{



			$("#id_category").val('');



			$("#id_purity").val('');



		}



	}



	else



	{



			if(this.value!='')



			{



				$("#id_category").val(this.value);



				$('#purity option').remove();



				if(ctrl_page[2] != 'edit' ){

					$('#id_purity').val('');

				}







				if(lt_order_no=='')



				{



					get_cat_purity();



				}



			}



			else



			{



				$("#id_category").val('');



				$("#id_purity").val('');



			}



	}



});



$('#cancel').on('click',function(){



			//	$("#id_category").val('');



			$('#purity option').remove();



			$("#Userconfirm").modal('toggle');



		});



//on selecting purity



$('#purity').select2().on("change", function(e) {



	var id_purity= $("#id_purity").val();



	var pro_id=$('#lt_item_list > tbody').find('.pro_id').val();



	if(pro_id!=undefined && pro_id!='')



	{



		if(this.value!='')



		{



				var value=this.value;



				if(($("#id_purity").val())!=this.value)



				{



						proceed = confirm("This Purity Will Change in Your Added Items..?");



						if(this.value!='')



					      {



					      	 $("#id_purity").val(this.value);



						  }



						  else{



						  	 $("#id_purity").val('');



						  }



				}



		}



		else



		{



			$(this).val(id_purity);



			$("#id_purity").val(id_purity);



		}



	}



	else



	{



		if(this.value!='')



	      {



	      	 $("#id_purity").val(this.value);



		  }



		  else{



		  	 $("#id_purity").val('');



		  }



	}



});



$('#lot_images').on('change',function(){



		item_validateImage();



});



$("#lot_order_no").select2({



    placeholder: "Select Order No",



    allowClear: true



});



//on selecting order no



$('#lot_order_no').select2()



    .on("change", function(e) {



      if(this.value!='')



      {



      	 $("#lt_order_id").val(this.value);



	  }



});



$(document).on('click',"#lot_img_upload", function(){



 	$('#lot_img_upload').prop('disabled',true);



	var formData = new FormData();



	var current=$('#cur_id').val();



	for(var i = 0;i<total_files.length;i++){



        formData.append("file[]", total_files[i]);



    }



	var my_Date = new Date();



	$.ajax({



        url: base_url+'index.php/admin_ret_lot/upload_lotimg/?nocache=' + my_Date.getUTCSeconds(),



        dataType: "json",



        method: "POST",



         cache:false,



            contentType: false,



            processData: false,



        data:formData,



        success: function (data) {



        	$('#lot_img_upload').prop('disabled',false);



			total_files=[];



			$('#image_name').val(data.name);



        }



     });



});



});



	function remove_img(file,folder,field,id,imgs ) {



		$("div.overlay").css("display", "block");



		$.ajax({



			   url:base_url+"index.php/admin_ret_lot/remove_img",



			   type : "POST",



			   data : {'file':file,'folder':folder,'field':field,'id':id,'imgs':imgs},



			   success : function(result) {



			   	$("div.overlay").css("display", "none");



				 window.location.reload();



			   },



			   error : function(error){



				$("div.overlay").css("display", "none");



			   }



			});



	}



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







$('#lot_inward_search').click(function (event) {



	 get_lotInward_list();



});



function get_lotInward_list(from_date,to_date)



{



	my_Date = new Date();



	$("div.overlay").css("display", "block");



	$.ajax({



		 url:base_url+"index.php/admin_ret_lot/lot_inward/ajax?nocache=" + my_Date.getUTCSeconds(),



		 dataType:"JSON",



		 type:"POST",



		 data:{'lot_type':$('#lot_type').val(),'from_date':$('#lt_date1').text(),'to_date':$('#lt_date2').text(),'id_metal':$('#metal').val(),'emp_id':$("#select_emp").val()},



		 success:function(data){



   			set_lotInward_list(data);



   			 $("div.overlay").css("display", "none");



		  },



		  error:function(error)



		  {



			 $("div.overlay").css("display", "none");



		  }



	});



}



function findUOM(data, uom_id){



  for(var x in data){



    if(data[x].uom_id && data[x].uom_id.split(",").indexOf(uom_id.toString())!=-1) return data[x].uom_short_code;



  }



  return " ";



}



function getActiveUOM()



{



	my_Date = new Date();



	$.ajax({



		 url:base_url+"index.php/admin_ret_catalog/uom/active_uom?nocache=" + my_Date.getUTCSeconds(),



		 dataType:"JSON",



		 type:"POST",



		 success:function(data){



		 		uom_details=data;



				 $.each(data, function (key, item) {



					$('.wt_uom').append(



					 $("<option></option>")



					   .attr("value", item.uom_id)



					   .text(item.code)



				 );



		        });



   			 $("div.overlay").css("display", "none");



		  },



		  error:function(error)



		  {



			 $("div.overlay").css("display", "none");



		  }



	});



}



function lot_branch_copy(lot_details)



{



	if(lot_details.length>0)



	{



		console.log(lot_details);



		$.each(lot_details,function(key,item){



			window.open( base_url+'index.php/admin_ret_lot/branch_acknowladgement/2/'+item.lot_no+'/'+item.id_branch,'_blank');



		});



		//window.location.reload();



	}



}



$("#br_copy").click(function(){



		if($("input[name='lot_no[]']:checked").val())



		{



			var selected = [];



			$("input[name='lot_no[]']:checked").each(function() {







				$("#lot_inward_list tbody tr").each(function(index, value){



						if($(value).find("input[name='lot_no[]']:checked").is(":checked")){



							transData = {



								'lot_no'  : $(value).find(".lot_no").val(),



								'id_branch'  : $('#filter_branch').val(),



							}



							selected.push(transData);



				 		}



					})



			});



			lot_details=selected;



			lot_branch_copy(lot_details);



		}else{



			alert('Please Select Any One Lot');



		}



   });







function set_lotInward_list(data)



{



	$('body').addClass("sidebar-collapse");



    $("div.overlay").css("display", "none");



    var list = data.list;



    //var uom = data.list.uom;



    var access = data.access;



	var profile = data.profile;



	console.log(profile);





	var currentDate = new Date();



   var formattedCurrentDate =

	currentDate.getDate().toString().padStart(2, '0') + '-' +

	(currentDate.getMonth() + 1).toString().padStart(2, '0') + '-' +

	currentDate.getFullYear();





    var oTable = $('#lot_inward_list').DataTable();



    $("#total_product").text(list.length);



    if(access.add == '0')



	{



		$('#add_lot').attr('disabled','disabled');



	}



	 oTable.clear().draw();



   	 if (list!= null && list.length > 0)



	 {







	 	oTable = $('#lot_inward_list').dataTable({



	                "bDestroy": true,



	                "bInfo": true,



	                "bFilter": true,



	                "bSort": true,



	                "dom": 'lBfrtip',



	                 "order": [[ 0, "desc" ]],



	                "lengthMenu": [ [ 10, 25, 50, -1], [10, 25, 50, "All"] ],



	                "columnDefs": [



								{



									targets: [6,7,8,9,12,13,14],



									className: 'dt-body-right'



								},



							],



	                "buttons" : ['excel','print'],



			        "tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },



	                "aaData": list,







	                "aoColumns": [



	                            { "mDataProp": function ( row, type, val, meta ){



                                    var total_pcs=0;



				                	var tag_details=row.tag_det;





	                                $.each(tag_details,function(key,items){



				                	        total_pcs+=parseFloat(items.tot_pcs);



			                	    });



			                	    let bal_pcs = row.tot_pcs-total_pcs;





								(chekbox=row.is_closed == 0 && row.stock_type==1 && bal_pcs<0 ? '<input type="checkbox" name="lot_no[]" value="'+row.lot_no+'"  /><input type="hidden" class="lot_no" value="'+row.lot_no+'"  />' : '');



								return chekbox+" "+row.lot_no;



								}



								},



				                { "mDataProp": "lot_date" },



				                { "mDataProp": "lotFrom" },



				                { "mDataProp": "pur_ref_no" },



				                { "mDataProp": "karigar_name" },



				                { "mDataProp": "emp_name" },



				                { "mDataProp": function ( row, type, val, meta ){



				                	    return money_format_india(row.tot_pcs);



				                	}



				                },







				                { "mDataProp": function ( row, type, val, meta ){



				                	    return money_format_india(row.gross_wt);



				                	}



				                },







				                { "mDataProp": function ( row, type, val, meta )



				                    {



				                    var total_pcs=0;



				                	var tag_details=row.tag_det;



				                	    $.each(tag_details,function(key,items){



				                	        total_pcs+=parseFloat(items.tot_pcs);



				                	    });



				                	    return money_format_india(total_pcs);



				                	}



				                },



				                { "mDataProp": function ( row, type, val, meta )



				                    {



				                	 var tag_details=row.tag_det;



				                	 var total_gross_wt=0;



				                	    $.each(tag_details,function(key,items){



				                	        total_gross_wt+=parseFloat(items.gross_wt);



				                	    });



				                	    return money_format_india(parseFloat(total_gross_wt).toFixed(3));



				                	}



				                },



				                {



                                        "mDataProp": null,



                                        "sClass": "control center",



                                        "sDefaultContent":  '<span class="drill-val"><i class="fa fa-chevron-circle-down text-teal"></i></span>'



                                },



				                {



									"mDataProp": function ( row, type, val, meta )



				                    {



				                        return '<input type="hidden" class="lot_purchase_details" value =\''+JSON.stringify(row.lot_details)+'\' ><span onclick="view_purchase_details($(this).closest(\'tr\'))" ><i class="fa fa-plus-circle" aria-hidden="true" ></i></span>'



				                    }







							    },



				                { "mDataProp": function ( row, type, val, meta )



				                    {



				                        var total_pcs=0;



    				                	var tag_details=row.tag_det;



				                	    $.each(tag_details,function(key,items){



				                	        total_pcs+=parseFloat(items.tot_pcs);



				                	    });



				                        return money_format_india(parseFloat(row.tot_pcs-total_pcs));



				                    }



				                },



				                { "mDataProp": function ( row, type, val, meta )



				                    {



				                        var total_gross_wt=0;



				                        var tag_details=row.tag_det;



				                	    $.each(tag_details,function(key,items){



				                	        total_gross_wt+=parseFloat(items.gross_wt);



				                	    });



				                        return money_format_india(parseFloat(row.gross_wt-total_gross_wt).toFixed(3));



				                    }



				                },



								{ "mDataProp": "pure_wt" },



								{ "mDataProp": function ( row, type, val, meta ) {


									var total_pcs=0;

										var tag_details=row.tag_det;

										$.each(tag_details,function(key,items){

											total_pcs+=parseFloat(items.tot_pcs);

										});

				                	 id= row.lot_no



				                	 edit_url=(access.edit=='1' ? base_url+'index.php/admin_ret_lot/lot_inward/edit/'+id : '#' );



				                	 vendor_url=base_url+'index.php/admin_ret_lot/vendor_acknowladgement/1/'+id;



				                	 ho_url=base_url+'index.php/admin_ret_lot/lot_acknowladgement/2/'+id;



									customer_url=base_url+'index.php/admin_ret_lot/customer_acknowladgement/2/'+id;



				                	 branch_url=base_url+'index.php/admin_ret_lot/branch_acknowladgement/2/'+id;



				                	 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_lot/lot_inward/delete/'+id : '#' );



				                	 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');



				                	//  action_content=(row.lot_from!=5 ? '<a href="'+edit_url+'" class="btn btn-primary btn-edit" ><i class="fa fa-edit" ></i></a>' :'')+'<a href="'+vendor_url+'" target="_blank" class="btn btn-secondary btn-print" data-toggle="tooltip" title="Vendor Copy"><i class="fa fa-print" ></i></a><a href="'+ho_url+'" target="_blank" class="btn btn-info btn-print" data-toggle="tooltip" title="Office Copy"><i class="fa fa-print" ></i></a> <a href="'+customer_url+'" target="_blank" class="btn btn-custom-orange btn-print" data-toggle="tooltip" title="Customer Copy"><i class="fa fa-print" ></i></a> '+(row.tag_lot_id =="" || row.tag_lot_id == null && row.lot_status!= 2 && (row.lot_date == formattedCurrentDate || profile.allow_lot_cancel ==1)? '<button class="btn btn-warning" onclick="confirm_delete('+id+')"><i class="fa fa-close" ></i></button>' :'');



									action_content = 

									(row.lot_from != 5 && access.edit == 1  && (parseFloat(row.tot_pcs) == parseFloat(row.tot_pcs-total_pcs)) ? 

										'<a href="' + edit_url + '" class="btn btn-primary btn-edit" ><i class="fa fa-edit" ></i></a>' 

										: '') +

									'<a href="' + vendor_url + '" target="_blank" class="btn btn-secondary btn-print" data-toggle="tooltip" title="Vendor Copy"><i class="fa fa-print" ></i></a>' +

									'<a href="' + ho_url + '" target="_blank" class="btn btn-info btn-print" data-toggle="tooltip" title="Office Copy"><i class="fa fa-print" ></i></a>' +

									'<a href="' + customer_url + '" target="_blank" class="btn btn-custom-orange btn-print" data-toggle="tooltip" title="Customer Copy"><i class="fa fa-print" ></i></a>' +

									(row.tag_lot_id === "" || row.tag_lot_id == null && row.lot_status != 2 && 

										(row.lot_date == formattedCurrentDate || profile.allow_lot_cancel == 1) && access.delete == 1  && (parseFloat(row.tot_pcs) == parseFloat(row.tot_pcs-total_pcs)) ? 

										'<button class="btn btn-warning" onclick="confirm_delete(' + id + ')"><i class="fa fa-close" ></i></button>' 

										: '');





									//  <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>';

                                    console.log(row.tag_lot_id);



									console.log(profile.allow_lot_cancel);



				                	return action_content;



			                	}



				            }],



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



        								$(api.column(4).footer()).html('Total');



        								pcs = api



        									.column(6)



        									.data()



        									.reduce(function (a, b) {



        										return intVal(a) + intVal(b);



        									}, 0);



        								$(api.column(6).footer()).html(money_format_india(parseFloat(pcs).toFixed(0)));



        								grs_wgt = api



        									.column(7)



        									.data()



        									.reduce(function (a, b) {



        										return intVal(a) + intVal(b);



        									}, 0);



        								$(api.column(7).footer()).html(money_format_india(parseFloat(grs_wgt).toFixed(3)));







        								tag_pcs = api



        									.column(8)



        									.data()



        									.reduce(function (a, b) {



        										return intVal(a) + intVal(b);



        									}, 0);



        								$(api.column(8).footer()).html(money_format_india(parseFloat(tag_pcs).toFixed(0)));







        								tag_gwt = api



        									.column(9)



        									.data()



        									.reduce(function (a, b) {



        										return intVal(a) + intVal(b);



        									}, 0);



        								$(api.column(9).footer()).html(money_format_india(parseFloat(tag_gwt).toFixed(3)));







        								blc_pcs = api



        									.column(12)



        									.data()



        									.reduce(function (a, b) {



        										return intVal(a) + intVal(b);



        									}, 0);



        								$(api.column(12).footer()).html(money_format_india(parseFloat(blc_pcs).toFixed(0)));







        								blc_wt = api



        									.column(13)



        									.data()



        									.reduce(function (a, b) {



        										return intVal(a) + intVal(b);



        									}, 0);



        								$(api.column(13).footer()).html(money_format_india(parseFloat(blc_wt).toFixed(3)));



										col_14 = api



        									.column(14)



        									.data()



        									.reduce(function (a, b) {



        										return intVal(a) + intVal(b);



        									}, 0);



        								$(api.column(14).footer()).html((parseFloat(col_14).toFixed(3)));







        							}



        						} else {



        							var api = this.api(), data;



        							$(api.column(5).footer()).html('');



        							$(api.column(6).footer()).html('');



        							$(api.column(7).footer()).html('');



        							$(api.column(8).footer()).html('');



        							$(api.column(9).footer()).html('');



        							$(api.column(10).footer()).html('');



        							$(api.column(11).footer()).html('');



        							$(api.column(12).footer()).html('');



        							$(api.column(13).footer()).html('');



        							$(api.column(14).footer()).html('');



        						}



        					}



	            });



	            var anOpen =[];



            		$(document).on('click',"#lot_inward_list .control", function(){



            		   var nTr = this.parentNode;



            		   var i = $.inArray( nTr, anOpen );







            		   if ( i === -1 ) {



            				$('.drill-val', this).html('<i class="fa fa-chevron-circle-up text-teal"></i>');



            				oTable.fnOpen( nTr, fnFormatRowTagDetails(oTable, nTr), 'details' );



            				anOpen.push( nTr );



            		    }



            		    else {



            				$('.drill-val', this).html('<i class="fa fa-chevron-circle-down text-teal"></i>');



            				oTable.fnClose( nTr );



            				anOpen.splice( i, 1 );



            		    }



            		} );



		 }



}



function confirm_delete(lot_id)



{



	$('#lot_id').val(lot_id);



	$('#confirm-delete').modal('show');



}





function fnFormatRowTagDetails( oTable, nTr )



{



  var oData = oTable.fnGetData( nTr );



  var rowDetail = '';



  var prodTable =



     '<div class="innerDetails">'+



      '<table class="table table-responsive table-bordered text-center table-sm">'+



        '<tr class="bg-teal">'+



        '<th>S.No</th>'+



        '<th>Branch</th>'+



        '<th>Tot Pcs</th>'+



        '<th>Tot Gwt</th>'+



        '<th>Action</th>'+



        '</tr>';



    var tag_details = oData.branch_wise;



  $.each(tag_details, function (idx, val) {



      branch_summary_url=base_url+'index.php/admin_ret_lot/branch_acknowladgement/1/'+val.tag_lot_id+'/'+val.current_branch;



      branch_url=base_url+'index.php/admin_ret_lot/branch_acknowladgement/2/'+val.tag_lot_id+'/'+val.current_branch;



  	prodTable +=



        '<tr class="prod_det_btn">'+



        '<td>'+parseFloat(idx+1)+'</td>'+



        '<td>'+val.branch_name+'</td>'+



        '<td>'+val.tot_pcs+'</td>'+



        '<td>'+val.gross_wt+'</td>'+



        '<td><a href="'+branch_url+'" target="_blank" class="btn btn-info btn-print" data-toggle="tooltip" title="Branch Copy Detailed"><i class="fa fa-print" ></i></a><a href="'+branch_summary_url+'" target="_blank" class="btn btn-secondary btn-print" data-toggle="tooltip" title="Branch Copy Summary"><i class="fa fa-print" ></i></a></td>'+



        '</tr>';



  });



  rowDetail = prodTable+'</table></div>';



  return rowDetail;



}



function get_category()



{



	$(".overlay").css('display','block');



	$.ajax({



		type: 'POST',



		url: base_url+'index.php/admin_ret_catalog/category/active_category',



		dataType:'json',



		success:function(data){



		  lot_cat_details = data;



		  category_lists = data;



		  var id_category =  $('#id_category').val();



		   $.each(data, function (key, item) {



			   		$('#category').append(



						$("<option></option>")



						  .attr("value", item.id_ret_category)



						  .text(item.name)



					);



			});



			$("#category").select2({



			    placeholder: "Select Category",



			    allowClear: true



			});



			$("#category").select2("val",(id_category!='' && id_category>0?id_category:''));



			if(id_category!= '' ){

				$("#category").trigger();

			}



			 $(".overlay").css("display", "none");



		}



	});



}



function get_cat_purity()



{



	$(".overlay").css('display','block');



	$.ajax({



		type: 'POST',



		url: base_url+'index.php/admin_ret_catalog/category/cat_purity',



		dataType:'json',



		data: {



			'id_category' :$('#id_category').val()



		},



		success:function(data){



		  var id_purity =  $('#id_purity').val();



		   $.each(data, function (key, item) {



			   		$('#purity').append(



						$("<option></option>")



						  .attr("value", item.id_purity)



						  .text(item.purity)



					);



			});



			$("#purity").select2({



			    placeholder: "Select Purity",



			    allowClear: true



			});



			$("#purity").select2("val",(id_purity!='' && id_purity>0?id_purity:''));



			 $(".overlay").css("display", "none");



		}



	});



}



function get_karigar(){



	$.ajax({



	 	type: 'GET',



	 	url: base_url+'index.php/admin_ret_catalog/karigar/active_list',



	 	dataType:'json',



	 	success:function(data){



		 	var id =  $('#lt_gold_smith_id').val();



		 	$.each(data, function (key, item) {



	    	 	$("#lt_gold_smith,#gold_smith").append(



	    	 	$("<option></option>")



	    	 	.attr("value", item.id_karigar)



	    	 	.text(item.karigar+' '+ item.code)



	    	 	);



	     	});



	     	$("#lt_gold_smith,#gold_smith").select2("val",(id!='' && id>0?id:''));



	     	$(".overlay").css("display", "none");



	 	}



	});



}



function getSearchProd(searchTxt){



	my_Date = new Date();



	$.ajax({



        url: base_url+'index.php/admin_ret_catalog/product/active_prodBySearch/?nocache=' + my_Date.getUTCSeconds(),



        dataType: "json",



        method: "POST",



        data: {'searchTxt':searchTxt},



        success: function (data) {



			$( "#lt_product" ).autocomplete(



			{



				source: data,



				select: function(e, i)



				{



					e.preventDefault();



					$("#lt_product" ).val(i.item.label);



					$("#lt_product_id" ).val(i.item.value);



					$("#lot_id_design" ).val('');



					$("#design" ).val('');



				},



				response: function(e, i) {



		            // ui.content is the array that's about to be sent to the response callback.



		            console.log(i);



		            if (i.content.length === 0) {



		               $("#prodAlert").html('<p style="color:red">Enter a valid Product</p>');



		               //$('#lt_product').val('');



		            }else{



						$("#prodAlert").html('');



					}



		        },



				 minLength: 0,



			});



        }



     });



}



/*function getSearchSubProd(searchTxt,prodId){



	my_Date = new Date();



	$.ajax({



        url: base_url+'index.php/admin_ret_catalog/sub_product/active_subprodBySearch/?nocache=' + my_Date.getUTCSeconds(),



        dataType: "json",



        method: "POST",



        data: {'searchTxt':searchTxt,'prodId':prodId},



        success: function (data) {



			$( "#design" ).autocomplete(



			{



				source: data,



				select: function(e, i)



				{



					e.preventDefault();



					$("#design" ).val(i.item.label);



					$("#lot_id_design" ).val(i.item.value);



				},



				response: function(e, i) {



		            // ui.content is the array that's about to be sent to the response callback.



		            console.log(i);



		            if (i.content.length === 0) {



		               $("#subprodAlert").html('<p style="color:red">Enter a valid Sub Product</p>');



		               //$('#lt_product').val('');



		            }else{



						$("#subprodAlert").html('');



					}



		        },



				 minLength: 0,



			});



        }



     });



}*/



function getSearchDesign(searchTxt,prodId){



	my_Date = new Date();



	$.ajax({



        url: base_url+'index.php/admin_ret_brntransfer/branch_transfer/getDesignByFilter/?nocache=' + my_Date.getUTCSeconds(),



        dataType: "json",



        method: "POST",



        data: {'searchTxt':searchTxt,'prodId':prodId},



        success: function (data) {



			$( "#design" ).autocomplete(



			{



				source: data,



				select: function(e, i)



				{



					e.preventDefault();



					$("#design" ).val(i.item.label);



					$("#lot_id_design" ).val(i.item.value);



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



function getSearchOrderNo(searchTxt){



	my_Date = new Date();



	$.ajax({



        url: base_url+'index.php/admin_ret_lot/getOrderNosBySearch/?nocache=' + my_Date.getUTCSeconds(),



        dataType: "json",



        method: "POST",



        data: {'searchTxt':searchTxt},



        success: function (data) {



			$( "#lt_order_no" ).autocomplete(



			{



				source: data,



				select: function(e, i)



				{



					e.preventDefault();



					$("#lt_order_no" ).val(i.item.label);



					$("#lt_order_id" ).val(i.item.order_no);



					$("#order_from" ).val(i.item.order_from);



					get_karigar_by_order(i.item.order_no)



					//$("#lt_order_id" ).val(i.item.label);



				},



				response: function(e, i) {



		            // ui.content is the array that's about to be sent to the response callback.



		            console.log(i);



		            if (i.content.length === 0) {



		               $("#orderAlert").html('<p style="color:red">Enter a valid Order No</p>');



		               //$('#lt_product').val('');



		            }else{



						$("#orderAlert").html('');



					}



		        },



				 minLength: 0,



			});



        }



     });



}



function item_validateImage()



 {



 		$('#lot_img').empty();



		var files = event.target.files;



		//var a = $('#cur_id').val();



		var preview=$('#lot_img');



		var html_1="";



		 for (var i = 0; i < files.length; i++)



		 {



                var file = files[i];



                total_files.push(file);



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



					    var id=i;



						reader.onload = function (event) {



						img_resource.push({'src':event.target.result,'name':fileName});



						}



						if (file)



						{



						reader.readAsDataURL(file);



						}



						else



						{



						preview.prop('src','');



						}



					}



			 	}



            }



	setTimeout(function(){



		console.log(img_resource);



		$.each(img_resource,function(key,item){



			   if(item)



			   {



			   		var div = document.createElement("div");



					div.setAttribute('class','col-md-2');



					div.setAttribute('id','img_'+key);



					div.innerHTML+= "<a onclick='img_remove("+key+")'><i class='fa fa-trash'></i></a><img class='thumbnail' src='" + item.src + "'" +



					"style='width: 100px;height: 100px;'/>";



					preview.append(div);



			   }



			   $('#lot_img_upload').css('display','');



		});



	},3000);



}



 function img_remove(id)



 {



 		$('#img_'+id).remove();



		const index = total_files.indexOf(img_resource[id]);



		total_files.splice(index,1);



		console.log(total_files);



 }



function getSearchCustomers(searchTxt){



	my_Date = new Date();



	$.ajax({



        url: base_url+'index.php/admin_ret_estimation/getCustomersBySearch/?nocache=' + my_Date.getUTCSeconds(),



        dataType: "json",



        method: "POST",



        data: {'searchTxt': searchTxt},



        success: function (data) {



			$( "#cus_name" ).autocomplete(



			{



				source: data,



				select: function(e, i)



				{



					e.preventDefault();



					$("#cus_name").val(i.item.label);



					$("#cus_village").html(i.item.village_name);



					$("#chit_cus").html((i.item.accounts==0 ?'No' :'Yes'));



					$("#vip_cus").html(i.item.vip);



					getOrdersByCus(i.item.value);



				},



				change: function (event, ui) {



					if (ui.item === null) {



						$(this).val('');



						$('#cus_name').val('');



						$("#cus_id").val("");



						$("#cus_village").html("");



						$("#chit_cus").html("");



						$("#vip_cus").html("");



					}



			    },



				response: function(e, i) {



		            // ui.content is the array that's about to be sent to the response callback.



		            if(searchTxt != ""){



						if (i.content.length === 0) {



						   $("#customerAlert").html('<p style="color:red">Enter a valid customer name / mobile</p>');



						}else{



						   $("#customerAlert").html('');



						}



					}else{



					}



		        },



				 minLength: 3,



			});



        }



     });



}



function getOrdersByCus(idcus)



{



	$(".overlay").css('display','block');



	$.ajax({



		type: 'POST',



		url: base_url+'index.php/admin_ret_order/order/getOrderByCus',



		dataType:'json',



		data: {



			'id_customer' : idcus



		},



		success:function(data){



		   $.each(data, function (key, item) {



			   		$('#lot_order_no').append(



						$("<option></option>")



						  .attr("value", item.id_orderdetails)



						  .text(item.orderno)



					);



			});



			//$("#lot_order_no").select2("val",'');



			$(".overlay").css("display", "none");



		}



	});



}



//Employee Filter



	function get_employee()



	{



		my_Date = new Date();



		$.ajax({



		url:base_url+ "index.php/admin_ret_estimation/get_employee?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),



        type:"POST",



        dataType:"JSON",



        success:function(data)



        {



           var id_employee=$('#id_employee').val();



           emp_details=data;



           $.each(data, function (key, item) {



                	 	$("#select_emp").append(



                	 	$("<option></option>")



                	 	.attr("value", item.id_employee)



                	 	.text(item.emp_name)



                	 	);



                 	});



             	$("#select_emp").select2({



            	 	placeholder: "Select Employee",



            	 	allowClear: true



             	});

				if($("#select_emp").length > 0){



         	         $("#select_emp").select2("val",(id_employee!='' && id_employee>0?id_employee:''));



			    }



         	    $(".overlay").css("display", "none");



        },



        error:function(error)



        {



        }



    	});



	}



	$('#select_emp').on('change',function(){



		if(this.value!='')



		{



			$('#id_employee').val(this.value);



		}



		else



		{



			$('#id_employee').val('');



		}



	});



//Product search



$(document).on('keyup',	".lot_product", function(e){



	var row = $(this).closest('tr');



	var product = row.find(".lot_product").val();



	getSearchProducts(product, row);



});



//Design search



$(document).on('keyup',	".design", function(e){



	var row = $(this).closest('tr');



	var design = row.find(".design").val()



	getSearchDesigns(design, row);



});



$(document).on('keyup',	'.gross_wt, .lot_lwt', function(e){



	var row = $(this).closest('tr');



	var gross_wt = (isNaN(row.find('.gross_wt').val()) || row.find('.gross_wt').val()=='' ?0 :row.find('.gross_wt').val());



	var lot_lwt	 = (isNaN(row.find('.lot_lwt').val()) ||  row.find('.lot_lwt').val()=='' ?0 :row.find('.lot_lwt').val());



	var net_wt   =  parseFloat(parseFloat(gross_wt)-parseFloat(lot_lwt)).toFixed(3);



	row.find('.lot_nwt').val(net_wt);



});



$(document).on('change','.mc_type ', function(e){



	var row = $(this).closest('tr');



	if(this.value!='')



	{



			row.find('.id_mc_type').val(this.value);



	}



	else



	{



		row.find('.id_mc_type').val(1);



	}



});



$(document).on('change','.sel_design_for ', function(e){



	var row = $(this).closest('tr');



	if(this.value!='')



	{



			row.find('.design_for').val(this.value);



	}



	else



	{



		row.find('.design_for').val(1);



	}



});



function create_new_empty_lot_row()



{



	var orderno=$('#lt_order_id').val();



	var id_karigar=$('#lt_gold_smith_id').val();



	var stock_type       = $("input[name='inward[stock_type]']:checked").val();



	var lot_receive_branch = $('#lt_rcvd_branch_sel').val();



    if(ctrl_page[2]=='edit')



	{



		var row_id = $('#lt_item_list tbody tr').length;



		$('#curRow').val(row_id);



	}



	var row = "";



	if(orderno=='')



	{



		var a = $("#curRow").val();



		var i = ++a;



		$("#curRow").val(i);



		var uom='';



		$.each(uom_details,function(key,item){



			uom += "<option value='"+item.uom_id+"'>"+item.code+"</option>";



		});



		row += '<tr id='+i+'>'



					/*+'<td><input type="text" class="lot_product" name="inward_item['+i+'][product]" value="" required style="width:80px;" autocomplete="off"/><input type="hidden" class="pro_id" name="inward_item['+i+'][lot_product]" value="" /><input type="hidden" class="sales_mode" name="inward_item['+i+'][sales_mode]" value="" /><input type="hidden" class="calculation_based_on" name="inward_item['+i+'][calculation_based_on]" value="" /<input type="hidden" class="id_lot_inward_detail" id="id_lot_inward_detail" value=""></td>'



					+'<td><input type="text" class="design" name="inward_item['+i+'][design]" value="" style="width:80px;" autocomplete="off" '+(stock_type==1 ? "readonly" : '')+'/><input type="hidden" class="des_id" name="inward_item['+i+'][lot_id_design]" value="" /></td>'



					*/



					+'<td><select class="lot_section" name="inward_item['+i+'][id_section]"  value="" placeholder="Search Section" style="width:150px;" '+(stock_type == 1? 'disabled' : '')+' ><input type="hidden" class="section_id"  value="" /></td>'



					+'<td><select class="lot_product" name="inward_item['+i+'][product]" value="" placeholder="Search Product" style="width:150px;"><input type="hidden" class="pro_id" name="inward_item['+i+'][lot_product]" value="" /><input type="hidden" class="sales_mode" name="inward_item['+i+'][sales_mode]" value="" /><input type="hidden" class="calculation_based_on" name="inward_item['+i+'][calculation_based_on]" value="" /<input type="hidden" class="id_lot_inward_detail" id="id_lot_inward_detail" value=""></td>'



					+'<td><select class="design" name="inward_item['+i+'][design]"'+(stock_type == 1? 'disabled' : '')+' value="" placeholder="Search Design" style="width:150px;"><input type="hidden" class="des_id" name="inward_item['+i+'][lot_id_design]"  value=""/></td>'



					+'<td><select class="lot_sub_design" name="est_catalog[sub_design][]" '+(stock_type == 1? 'disabled' : '')+' value="" placeholder="Search SubDesign" required style="width:150px;"></select><input type="hidden" class="lot_id_sub_design" name="inward_item['+i+'][id_sub_design]"value=""/></td>'



					+'<td><select class="sel_design_for" style="width:80px;" autocomplete="off"><option value="1">Male</option><option value="2">Female</option><option value="3">Unisex</option></select><input type="hidden" class="design_for" name="inward_item['+i+'][design_for]" value="1"></td>'



					+'<td><input type="number" step="any" value="" name="inward_item['+i+'][pcs]" class="lot_pcs" style="width:60px;"></td>'



					+'<td><div class="input-group"><input type="number" step="any" value="" name="inward_item['+i+'][gross_wt]" class="gross_wt" style="width:80px;" autocomplete="off"><span class="input-group-addon input-sm no-padding" style="background-color: transparent;height: 12px;font-size: 16px;border: none;"><select class="gross_wt_uom" name="inward_item['+i+'][gross_wt_uom]">'+uom+'</select></span></div></td>'



					+'<td><div class="input-group"><input type="number" step="any" value="" name="inward_item['+i+'][less_wt]" class="lot_lwt" style="width:80px;" autocomplete="off"><span class="input-group-addon input-sm no-padding" style="background-color: transparent;height: 12px;font-size: 16px;border: none;"><select class="less_wt_uom" name="inward_item['+i+'][less_wt_uom]">'+uom+'</select></span></div></td>'



					+'<td><div class="input-group"><input type="number" step="any" value="" name="inward_item['+i+'][net_wt]" class="lot_nwt" style="width:80px;" autocomplete="off"><span class="input-group-addon input-sm no-padding" style="background-color: transparent;height: 12px;font-size: 16px;border: none;"><select class="net_wt_uom" name="inward_item['+i+'][net_wt_uom]">'+uom+'</select></span></div></td>'



					+'<td><input type="number" step="any" class="wastage_percentage" name="inward_item['+i+'][wastage_percentage]" style="width:60px;"></td>'



					+'<td><div class="input-group"><input type="number" step="any" class="making_charge" name="inward_item['+i+'][making_charge]" style="width:70px;"><span class="input-group-addon input-sm no-padding" style="background-color: transparent;height: 12px;border: none;"><select class="form-control mc_type" style="width:80px;height: 29px;" name="inward_item['+i+'][id_mc_type]" autocomplete="off"><option value="1">Gram</option><option value="2" selected>Piece</option></select></span></div><input type="hidden" value=""  class="id_mc_type"></td>'



					+'<td><input type="number" step="any" class="buy_rate" name="inward_item['+i+'][buy_rate]" style="width:80px;" autocomplete="off"><span class="buy_rt_type"></span></td>'



					+'<td><input type="number" step="any" class="sell_rate" name="inward_item['+i+'][sell_rate]" style="width:80px;" autocomplete="off"><span class="sell_rt_type"></span></td>'



					+'<td><div class="input-group"><input type="number" step="any" class="size" name="inward_item['+i+'][size]" style="width:70px;"><span class="input-group-addon input-sm no-padding" style="background-color: transparent;height: 12px;border: none;"></span></div></td>'



					+'<td><a href="#" onClick="show_stone_modal($(this).closest(\'tr\'),'+i+');" class="btn btn-default btn-sm"><i class="fa fa-plus-circle"></i></a><input type="hidden" class="precious_stone" name="inward_item['+i+'][precious_stone]"/><input type="hidden" class="precious_stone_pcs" name="inward_item['+i+'][precious_st_pcs]"/><input type="hidden" class="precious_stone_wt" name="inward_item['+i+'][precious_st_wt]"><input type="hidden" class="p_stn_certif_uploaded" name="inward_item['+i+'][p_stn_certif_uploaded]"><input type="hidden" class="precious_st_certif" name="inward_item['+i+'][precious_st_certif]"><input type="hidden" class="semi_precious_stn" name="inward_item['+i+'][semi_precious_stn]"/><input type="hidden" class="semi_precious_st_pcs" name="inward_item['+i+'][semi_precious_st_pcs]"/><input type="hidden" class="semi_precious_st_wt" name="inward_item['+i+'][semi_precious_st_wt]"><input type="hidden" class="sp_stn_certif_uploaded" name="inward_item['+i+'][sp_stn_certif_uploaded]"><input type="hidden" class="semiprecious_st_certif" name="inward_item['+i+'][semiprecious_st_certif]"><input type="hidden" class="normal_stn" name="inward_item['+i+'][normal_stn]"/><input type="hidden" class="normal_st_pcs" name="inward_item['+i+'][normal_st_pcs]"/><input type="hidden" class="normal_st_wt" name="inward_item['+i+'][normal_st_wt]"><input type="hidden" class="normal_st_certif" name="inward_item['+i+'][normal_st_certif]"><input type="hidden" class="n_stn_certif_uploaded" name="inward_item['+i+'][n_stn_certif_uploaded]"><input type="hidden" name="inward_item['+i+'][nor_wt_uom]" class="nor_wt_uom"><input type="hidden" name="inward_item['+i+'][semi_wt_uom]" class="semi_wt_uom"><input type="hidden" name="inward_item['+i+'][pre_wt_uom]" class="pre_wt_uom"></td>'



					+'<td><a href="#" onClick="remove_row($(this).closest(\'tr\'));" class="btn btn-danger"><i class="fa fa-trash"></i></a></td>'



					+'</tr>';



		$('#lt_item_list tbody').append(row);



		$('#lt_item_list > tbody').find('tr:last td:eq(0) .cat_product').focus();



		/*$('#lt_item_list > tbody').find('.mc_type').select2();



		$('#lt_item_list > tbody').find('.mc_type').select2({



		    placeholder: "Charge Type",



		    allowClear: true



		});



		$('#lt_item_list > tbody').find('.id_mc_type').val(2); //1-Gram,2-Pices



		$('#lt_item_list > tbody').find('.mc_type ').val(2); //1-Gram,2-Pices*/



		var id_mc_type=$('#lt_item_list > tbody').find('.id_mc_type').val();



		$('.lot_product').append(



    		$("<option></option>")



    		.attr("value", "")



    		.text('-Choose-')



		);







		$.each(lot_product_details, function (key, item){



		    if(item.cat_id== $('#category').val())



		    {



		        if(item.stock_type == 1 && stock_type == 1)



        		{



        			$('.lot_product').append(



        			$("<option></option>")



        			.attr("value", item.pro_id)



        			.text(item.product_name)



        			);



        	    }



        		else if(item.stock_type == 2 && stock_type == 2)



        		{



        			$('.lot_product').append(



        				$("<option></option>")



        				.attr("value", item.pro_id)



        				.text(item.product_name)



        			);



        		}



		    }







		});



		if(stock_type == 2)



		{



		    $.each(section_details, function (key, item){



		        if(lot_receive_branch==item.id_branch)



		        {



		            $('.lot_section').append(



        		 	 $("<option></option>")



        		 	 .attr("value",item.id_section)



        		 	 .text(item.section_name)



        		 	 );



		        }







    		});



		}











		$('#lt_item_list > tbody').find('.lot_product').select2();



		$('#lt_item_list > tbody').find('.design').select2();



		$('#lt_item_list > tbody').find('.lot_sub_design').select2();







		$('#lt_item_list > tbody').find('.lot_product').select2({



		    placeholder: "Product",



		    allowClear: true



		});







		$('#lt_item_list > tbody').find('.design').select2({



		    placeholder: "Design",



		    allowClear: true



		});







		$('#lt_item_list > tbody').find('.lot_sub_design').select2({



		    placeholder: "Sub Design",



		    allowClear: true



		});



		$('#lt_item_list > tbody').find('tr:last td:eq(0) .lot_product').focus();







		$('#lt_item_list > tbody').find('.lot_section').select2({



		    placeholder: "Section",



		    allowClear: true



		});







	}



	else{



		my_Date = new Date();



		$.ajax({



		url: base_url+'index.php/admin_ret_lot/get_order_details?nocache=' + my_Date.getUTCSeconds(),



		dataType: "json",



		method: "POST",



		data: { 'orderno': orderno,'id_karigar':id_karigar,'id_branch':$('#order_from').val()},



		success: function (data) {



				if(data)



				{



					$.each(data,function(i,list){



					var a = $("#curRow").val();



					var i = ++a;



					$("#curRow").val(i);



					var uom='';



					$.each(uom_details,function(key,item){



					uom += "<option value='"+item.uom_id+"'>"+item.code+"</option>";



					});



					row = '<tr id='+i+'>'



					+'<td><input type="text" class="lot_product" name="inward_item['+i+'][product]" value='+list.product_name+' readonly style="width:80px;" autocomplete="off"/><input type="hidden" class="pro_id" name="inward_item['+i+'][lot_product]" value='+list.id_product+' /><input type="hidden" class="sales_mode" name="inward_item['+i+'][sales_mode]" value='+list.sales_mode+' /><input type="hidden" class="calculation_based_on" name="inward_item['+i+'][calculation_based_on]" value='+list.calculation_based_on+' /<input type="hidden" class="id_lot_inward_detail" id="id_lot_inward_detail" value=""><input type="hidden" class="order_no"  value='+list.order_no+' /></td>'



					+'<td><input type="text" class="design" name="inward_item['+i+'][design]" value='+list.design_name+' readonly style="width:80px;" autocomplete="off"/><input type="hidden" class="des_id" name="inward_item['+i+'][lot_id_design]"  value='+list.design_no+' /></td>'



					+'<td><select class="sel_design_for" style="width:80px;" autocomplete="off"><option value="1">Male</option><option value="2">Female</option><option value="3">Unisex</option></select><input type="hidden" class="design_for" name="inward_item['+i+'][design_for]" value="1"></td>'



					+'<td><input type="number" step="any" value='+list.tot_pcs+' name="inward_item['+i+'][pcs]" class="lot_pcs" style="width:60px;"></td>'



					+'<td><div class="input-group"><input type="number" step="any" value='+list.tot_net_wt+' name="inward_item['+i+'][gross_wt]" class="gross_wt" style="width:80px;" autocomplete="off"><span class="input-group-addon input-sm no-padding" style="background-color: transparent;height: 12px;font-size: 16px;border: none;"><select class="gross_wt_uom" name="inward_item['+i+'][gross_wt_uom]">'+uom+'</select></span></div></td>'



					+'<td><div class="input-group"><input type="number" step="any" value="" name="inward_item['+i+'][less_wt]" class="lot_lwt" style="width:80px;" autocomplete="off"><span class="input-group-addon input-sm no-padding" style="background-color: transparent;height: 12px;font-size: 16px;border: none;"><select class="less_wt_uom" name="inward_item['+i+'][less_wt_uom]">'+uom+'</select></span></div></td>'



					+'<td><div class="input-group"><input type="number" step="any" value='+list.tot_net_wt+' name="inward_item['+i+'][net_wt]" class="lot_nwt" style="width:80px;" autocomplete="off"><span class="input-group-addon input-sm no-padding" style="background-color: transparent;height: 12px;font-size: 16px;border: none;"><select class="net_wt_uom" name="inward_item['+i+'][net_wt_uom]">'+uom+'</select></span></div></td>'



					+'<td><input type="number" step="any" class="wastage_percentage" name="inward_item['+i+'][wastage_percentage]" value='+list.wastage_per+' style="width:60px;"></td>'



					+'<td><div class="input-group"><input type="number" step="any" class="making_charge" name="inward_item['+i+'][making_charge]" style="width:70px;"><span class="input-group-addon input-sm no-padding" style="background-color: transparent;height: 12px;border: none;"><select class="form-control mc_type" style="width:80px;height: 29px;" name="inward_item['+i+'][id_mc_type]" autocomplete="off"><option value="1">Gram</option><option value="2" selected>Piece</option></select></span></div><input type="hidden" value=""  class="id_mc_type"></td>'



					+'<td><input type="number" step="any" class="buy_rate" name="inward_item['+i+'][buy_rate]" style="width:80px;" autocomplete="off"><span class="buy_rt_type"></span></td>'



					+'<td><input type="number" step="any" class="sell_rate" name="inward_item['+i+'][sell_rate]" style="width:80px;" autocomplete="off"><span class="sell_rt_type"></span></td>'



					+'<td><div class="input-group"><input type="number" step="any" class="size" name="inward_item['+i+'][size]" value='+list.tot_size+' style="width:70px;"><span class="input-group-addon input-sm no-padding" style="background-color: transparent;height: 12px;border: none;"></span></div></td>'



					+'<td><a href="#" onClick="show_stone_modal($(this).closest(\'tr\'),'+i+');" class="btn btn-default btn-sm"><i class="fa fa-plus-circle"></i></a><input type="hidden" class="precious_stone" name="inward_item['+i+'][precious_stone]"/><input type="hidden" class="precious_stone_pcs" name="inward_item['+i+'][precious_st_pcs]"/><input type="hidden" class="precious_stone_wt" name="inward_item['+i+'][precious_st_wt]"><input type="hidden" class="p_stn_certif_uploaded" name="inward_item['+i+'][p_stn_certif_uploaded]"><input type="hidden" class="precious_st_certif" name="inward_item['+i+'][precious_st_certif]"><input type="hidden" class="semi_precious_stn" name="inward_item['+i+'][semi_precious_stn]"/><input type="hidden" class="semi_precious_st_pcs" name="inward_item['+i+'][semi_precious_st_pcs]"/><input type="hidden" class="semi_precious_st_wt" name="inward_item['+i+'][semi_precious_st_wt]"><input type="hidden" class="sp_stn_certif_uploaded" name="inward_item['+i+'][sp_stn_certif_uploaded]"><input type="hidden" class="semiprecious_st_certif" name="inward_item['+i+'][semiprecious_st_certif]"><input type="hidden" class="normal_stn" name="inward_item['+i+'][normal_stn]"/><input type="hidden" class="normal_st_pcs" name="inward_item['+i+'][normal_st_pcs]"/><input type="hidden" class="normal_st_wt" name="inward_item['+i+'][normal_st_wt]"><input type="hidden" class="normal_st_certif" name="inward_item['+i+'][normal_st_certif]"><input type="hidden" class="n_stn_certif_uploaded" name="inward_item['+i+'][n_stn_certif_uploaded]"><input type="hidden" name="inward_item['+i+'][nor_wt_uom]" class="nor_wt_uom"><input type="hidden" name="inward_item['+i+'][semi_wt_uom]" class="semi_wt_uom"><input type="hidden" name="inward_item['+i+'][pre_wt_uom]" class="pre_wt_uom"></td>'



					+'<td><a href="#" onClick="remove_row($(this).closest(\'tr\'));" class="btn btn-danger"><i class="fa fa-trash"></i></a></td>'



					+'</tr>';



					$('#lt_item_list tbody').append(row);



					$('#lt_item_list > tbody').find('tr:last td:eq(0) .cat_product').focus();







					});



				}



				else



				{



					$('#errorMsg').css("display","block");



					$('#errorMsg').html(data.message);



				}



		}



		});



	}



}



function get_ActiveProduct()



{



	$.ajax({



		url: base_url + 'index.php/admin_ret_estimation/get_ActiveProduct',



		data: ({ 'id_category': ''}),



		dataType: "JSON",



		type: "POST",



		success: function (data) {



		 lot_product_details  = data;



    		//  $.each(data, function (key, item) {



            //             $('#select_product').append(



            //             $("<option></option>")



            //             .attr("value", item.pro_id)



            //             .text(item.product_name)



            //              .attr("data-purmode", item.purchase_mode)



			// 			 .attr("data-salesmode", item.purchase_mode)



			// 			 .attr("data-calculation_based_on", item.purchase_mode)



            //              .attr("data-tax_type", item.tax_type)



            //              .attr("data-stone_type", item.stone_type)



            //             );



            //     	});







            // 	$('#select_product').select2({



            // 	    placeholder: "Product",



            // 	    allowClear: true



            // 	});





			// 	$('#select_product').select2("val",'');



	        // if($('#select_product').length)



	        // {







	        // }



		}



	});



}



$(document).on('change','.lot_product',function()



{



	if(this.value!='')



	{



		var row = $(this).closest('tr');



		row.find('.pro_id').val(this.value);



		getActiveDesigns(row,this.value);



	}



});



$(document).on('change','.design',function()



{



	if(this.value!='')



	{



		var row = $(this).closest('tr');



		row.find('.des_id').val(this.value);



		get_ActivelotSubDesigns(row,this.value);



	}



});



$(document).on('change','.lot_sub_design',function()



{



	if(this.value)



	{



		var row = $(this).closest('tr');



		row.find('.lot_id_sub_design').val(this.value);



	}



});



function getActiveDesigns(curRow,pro_id)



{



	curRow.find('.design option').remove();



	$('#select_design option').remove();



	var stock_type=$('#stock_type:checked').val();



	my_Date = new Date();



	$.ajax({



		url: base_url+'index.php/admin_ret_catalog/get_active_design_products',



        dataType: "json",



        method: "POST",



        data: {'id_product':pro_id},



		success: function (data)



		{



			if(data.length > 0 && stock_type != 2 ){



			$('#select_design').append(



				$("<option></option>")



				.attr("value", 0)



				.text('ALL')



				);



			}

			var design_id = $('#id_design').val();



			$.each(data, function (key, item) {



				// $(curRow.find('.design')).append(



				// $("<option></option>")



				// .attr("value", item.design_no)



				// .text(item.design_name)



				// );



				$('#select_design').append(



					$("<option></option>")



					.attr("value", item.design_no)



					.text(item.design_name)



					);



			});



			// $(curRow.find('.design')).select2(



			// {



			// 	placeholder:"Select Design",



			// 	allowClear: true



			// });



			$('#select_design').select2(



				{



					placeholder:"Select Design",



					allowClear: true



				});



			//curRow.find('.design').select2("val",(design_id!=''&& design_id>0 ? design_id :""));





			$('#select_design').select2("val",(design_id!=''&& design_id>0 ? design_id :""));













		}



	});



}



function get_ActivelotSubDesigns(curRow,des_id)



{



	//curRow.find('.lot_sub_design option').remove();



	//var pro_id = curRow.find('.lot_product').val();

	var stock_type=$('#stock_type:checked').val();



	$('#select_sub_design option').remove();



	var pro_id = $('#select_product').val();



	my_Date = new Date();



	$.ajax({



		url: base_url+'index.php/admin_ret_catalog/get_ActiveSubDesigns',



        dataType: "json",



        method: "POST",



        data: {'id_product':pro_id,'design_no':des_id},



		success: function (data)



		{



			if((data.length > 0 || des_id == 0) && stock_type != 2 ){



				$('#select_sub_design').append(



					$("<option></option>")



					.attr("value", 0)



					.text('ALL')



					);



				}



			var sub_design_id = $('#id_sub_design').val()



			$.each(data, function (key, item) {



				// $(curRow.find('.lot_sub_design')).append(



				// $("<option></option>")



				// .attr("value", item.id_sub_design)



				// .text(item.sub_design_name)



				// );



				$('#select_sub_design').append(



					$("<option></option>")



					.attr("value", item.id_sub_design)



					.text(item.sub_design_name)



					);



			});



			$(curRow.find('.lot_sub_design')).select2(



			{



				placeholder:"Select Sub Design",



				allowClear: true



			});



			$('#select_sub_design').select2(



				{



					placeholder:"Select Sub Design",



					allowClear: true



				});



			//curRow.find('.lot_sub_design').select2("val",(sub_design_id!=''&& sub_design_id>0 ? sub_design_id :""));





			$('#select_sub_design').select2("val",(sub_design_id!=''&& sub_design_id>0 ? sub_design_id :""));







		}



	});



}



function remove_row(curRow)



{



	if(ctrl_page[2]!='edit')



	{



		curRow.remove();



		get_lot_preview();



	}



	else if(ctrl_page[1]=='lot_merge')



	{



		curRow.remove();



		TotalLotMerge();



	}



	else



	{



			$("#lot_inwards_detail").modal({



			backdrop: 'static',



			keyboard: false



			});



			var id_lot_inward_detail =  curRow.find('.id_lot_inward_detail').val();



			$('#delete_confirm').on('click',function(){



					my_Date = new Date();



					$.ajax({



					url: base_url+'index.php/admin_ret_lot/lot_inwards_detail/?nocache=' + my_Date.getUTCSeconds(),



					dataType: "json",



					method: "POST",



					data: { 'id_lot_inward_detail': id_lot_inward_detail},



					success: function (data) {



							if(data.status==true)



							{



								$('#successMsg').css("display","block");



								$('#successMsg').html(data.message);



								curRow.remove();



								get_lot_preview();



							}



							else



							{



								$('#errorMsg').css("display","block");



								$('#errorMsg').html(data.message);



							}



							setTimeout(function(){



								$('#lot_inwards_detail').modal('toggle');



							},800);



					}



					});



			});



	}



	calculate_lot_inward_Total();



}



/*$('#delete_confirm').on('click',function(){



		my_Date = new Date();



		$.ajax({



		url: base_url+'index.php/admin_ret_lot/lot_inwards_detail/?nocache=' + my_Date.getUTCSeconds(),



		dataType: "json",



		method: "POST",



		data: { 'id_lot_inward_detail': id_lot_inward_detail},



		success: function (data) {



		}



		});



});*/



$('#add_lot_item').on('click',function(){



	if($("#id_category").val() != "" && $("#id_purity").val() != "" && $("#lt_gold_smith_id").val() != ""){



		if(validateItemDetailRow()){



			create_new_empty_lot_row();



		}else{



			alert("Please fill required fields in current row");



			return false;



		}



	}else{



		alert("Please fill required fields");



	}



});



function validateItemDetailRow(){



	var row_validate = true;



	var stock_type=$('#stock_type:checked').val();



	if($('#lt_item_list > tbody  > tr').length){



	$('#lt_item_list > tbody  > tr').each(function(index, tr) {



		// 1 - Fixed Rate, 2 - Fixed Rate based on Weight, 3 - Metal Rate



		if($(this).find('.sales_mode').val() == 1 ){



			if($(this).find('.calculation_based_on').val() == 3){



				if($(this).find('.pro_id').val() == ""  || $(this).find('.lot_pcs').val() == "" ){



					row_validate = false;



				}



			}



			else if($(this).find('.calculation_based_on').val() == 4){



				if($(this).find('.pro_id').val() == ""  || $(this).find('.lot_pcs').val() == "" ||  $(this).find('.gross_wt').val() == ""){



					row_validate = false;



				}



			}



		}



		else if($(this).find('.sales_mode').val() == 2){



			if($(this).find('.pro_id').val() == ""  || $(this).find('.wastage_percentage').val() == "" || $(this).find('.lot_pcs').val() == "" || $(this).find('.gross_wt').val() == "" || $(this).find('.lot_nwt').val() == ""){



				row_validate = false;



			}



		}



		else{



			if($(this).find('.pro_id').val() == ""  || $(this).find('.wastage_percentage').val() == "" || $(this).find('.lot_pcs').val() == "" || $(this).find('.gross_wt').val() == "" || $(this).find('.lot_nwt').val() == ""){



				row_validate = false;



			}



		}



		if(($(this).find('.lot_product').val()=="" || $(this).find('.lot_product').val()==null || $(this).find('.design').val()=="" || $(this).find('.design').val()==null || $(this).find('.lot_sub_design').val()=="" || $(this).find('.lot_sub_design').val()==null || $(this).find('.lot_section').val()=="" || $(this).find('.lot_section').val()==null )&&(stock_type==2))



		{



		    row_validate = false;



		}



	});



	}else{



		row_validate = false;



	}



	return row_validate;



}



function b64toBlob(b64Data, contentType, sliceSize) {



        contentType = contentType || '';



        sliceSize = sliceSize || 512;



        var byteCharacters = atob(b64Data);



        var byteArrays = [];



        for (var offset = 0; offset < byteCharacters.length; offset += sliceSize) {



            var slice = byteCharacters.slice(offset, offset + sliceSize);



            var byteNumbers = new Array(slice.length);



            for (var i = 0; i < slice.length; i++) {



                byteNumbers[i] = slice.charCodeAt(i);



            }



            var byteArray = new Uint8Array(byteNumbers);



            byteArrays.push(byteArray);



        }



      var blob = new Blob(byteArrays, {type: contentType});



      return blob;



}



function validateCertifImg(type,row_id)



 {



 	if(type == 'pre_images'){



		preview = 'uploadArea_p_stn';



	}



	else if(type == 'semi_pre_imgs'){



		preview = 'uploadArea_sp_stn';



	}



	else if(type == 'norm_pre_imgs'){



		preview = 'uploadArea_n_stn';



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



						pre_img_resource.push({'row_id':row_id,'src':event.target.result,'name':fileName});



						pre_img_files.push(file);



						/*// Split the base64 string in data and contentType



						var ImageURL = event.target.result;



						var block = ImageURL.split(";");



						// Get the content type of the image



						var contentType = block[0].split(":")[1];// In this case "image/gif"



						// get the real base64 content of the file



						var realData = block[1].split(",")[1];// In this case "R0lGODlhPQBEAPeoAJosM...."



						// Convert it to a blob to upload



						var blob = b64toBlob(realData, contentType);



						// Create a FormData and append the file with "image" as parameter name



						var form = document.getElementById("lot_form");



						var formDataToUpload = new FormData(form);



						formDataToUpload.append("pre_image_test", blob);*/



					}



					else if(type == 'semi_pre_imgs'){



						sp_img_resource.push({'row_id':row_id,'src':event.target.result,'name':fileName});



						sp_img_files.push(file);



					}



					else if(type == 'norm_pre_imgs'){



						n_img_resource.push({'row_id':row_id,'src':event.target.result,'name':fileName});



						n_img_files.push(file);



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



		else if(type == 'semi_pre_imgs'){



			$('#pre_img div').remove();



			resource = sp_img_resource;



		}



		else if(type == 'norm_pre_imgs'){



			$('#pre_img div').remove();



			resource = n_img_resource;



		}



		$.each(resource,function(key,item){



		   if(item.row_id == $("#row_id").val())



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



 function remove_stn_img(param)



 {



	$('#'+param.preview+' #'+param.key).remove();



	if(param.stone_type == 'pre_images'){



		pre_img_resource.splice(param.key,1);



		if(ctrl_page[2] == 'edit' && param.preview == "uploadedArea_p_stn")



		remove_img(file,'certificates','precious_st_certif',id,imgs);



		console.log(pre_img_resource);



	}



	else if(param.stone_type == 'semi_pre_imgs'){



		$('#pre_img div').remove();



		sp_img_resource.splice(param.key,1);



		if(ctrl_page[2] == 'edit' && param.preview == "uploadedArea_p_stn")



		remove_img(file,'certificates','semiprecious_st_certif',id,imgs);



	}



	else if(param.stone_type == 'norm_pre_imgs'){



		$('#pre_img div').remove();



		n_img_resource.splice(param.key,1);



		if(ctrl_page[2] == 'edit' && param.preview == "uploadedArea_p_stn")



		remove_img(file,'certificates','normal_st_certif',id,imgs);



	}



	/*const index = pre_img_files.indexOf(pre_img_resource[id]);



	pre_img_files.splice(index,1);*/



 }



//  $('#update_stone_details').on('click',function(){



// 		// PRECIOUS



// 		var precious_stone 		= $('#precious_stone').val();



// 		var precious_st_pcs 	= $('#precious_st_pcs').val();



//  		var precious_st_wt 		= $('#precious_st_wt').val();



//  		var pre_wt_uom 			= $('#pre_wt_uom').val();



//  		var curRow 				= $('#row_id').val();



//  		console.log('curRow : '+curRow);



//  		console.log('precious_stone: '+precious_stone);



//  		console.log('precious_st_pcs: '+precious_st_pcs);



//  		console.log('precious_st_wt: '+precious_st_wt);



//  		console.log('pre_img_resource: '+pre_img_resource);



//   		var p_img_data			= [];



//  		$.each(pre_img_resource,function(key,item){



// 		   if(item.row_id == curRow)



// 		   {



// 		   		p_img_data.push(item);



// 		   }



// 		});



// 		$('#'+curRow).find('.precious_st_certif').val(JSON.stringify(p_img_data));



// 	   	$('#'+curRow).find('.precious_stone_pcs').val(precious_st_pcs);



// 	   	$('#'+curRow).find('.precious_stone_wt').val(precious_st_wt);



// 	   	$('#'+curRow).find('.precious_stone').val(precious_stone);



// 	   	$('#'+curRow).find('.pre_wt_uom').val(pre_wt_uom);



// 	   	// SEMI PRECIOUS



// 	   	var semi_precious_stn 		= $('#semi_precious_stn').val();



// 		var semi_precious_st_pcs	= $('#semi_precious_st_pcs').val();



//  		var semi_precious_st_wt		= $('#semi_precious_st_wt').val();



//  		var semi_wt_uom				= $('#semi_wt_uom').val();



// 		var sp_img_data 			= [];



//  		$.each(sp_img_resource,function(key,item){



// 		   if(item.row_id == curRow)



// 		   {



// 		   		sp_img_data.push(item);



// 		   }



// 		})



// 		$('#'+curRow).find('.semiprecious_st_certif').val(JSON.stringify(sp_img_data));



// 	   	$('#'+curRow).find('.semi_precious_st_pcs').val(semi_precious_st_pcs);



// 	   	$('#'+curRow).find('.semi_precious_st_wt').val(semi_precious_st_wt);



// 	   	$('#'+curRow).find('.semi_precious_stn').val(semi_precious_stn);



// 	   	$('#'+curRow).find('.semi_wt_uom').val(semi_wt_uom);



// 	   	//Normal



// 	   	var normal_stn 		= $('#normal_stn').val();



// 		var normal_st_pcs	= $('#normal_st_pcs').val();



//  		var normal_st_wt	= $('#normal_st_wt').val();



//  		var nor_wt_uom		= $('#nor_wt_uom').val();



// 		var n_img_data 		= [];



//  		$.each(n_img_resource,function(key,item){



// 		   if(item.row_id == curRow)



// 		   {



// 		   		n_img_data.push(item);



// 		   }



// 		})



// 		$('#'+curRow).find('.normal_st_certif').val(JSON.stringify(n_img_data));



// 	   	$('#'+curRow).find('.normal_st_pcs').val(normal_st_pcs);



// 	   	$('#'+curRow).find('.normal_st_wt').val(normal_st_wt);



// 	   	$('#'+curRow).find('.normal_stn').val(normal_stn);



// 	   	$('#'+curRow).find('.nor_wt_uom').val(nor_wt_uom);



// 	   	$('#stoneModal').modal('hide');



//  });



function getSearchProducts(searchTxt, curRow){



    if(searchTxt.length>=2)



    {



        var stock_type=$('#stock_type:checked').val();



	my_Date = new Date();



	$.ajax({



        url: base_url+'index.php/admin_ret_lot/getProductBySearch/?nocache=' + my_Date.getUTCSeconds(),



        dataType: "json",



        method: "POST",



        data: {'searchTxt': searchTxt,'cat_id' : $("#id_category").val(),'stock_type':stock_type},



        success: function (data) {



			$( ".lot_product" ).autocomplete(



			{



				source: data,



				select: function(e, i)



				{



					e.preventDefault();



					//var tax_percentage=[];



					curRow.find('.lot_product').val(i.item.label);



					curRow.find('.pro_id').val(i.item.value);



					curRow.find('.sales_mode').val(i.item.sales_mode);



					curRow.find('.calculation_based_on').val(i.item.calculation_based_on);



					if(i.item.sales_mode == 1){



						if(i.item.calculation_based_on == 3){



							curRow.find('.buy_rt_type').html("Per Piece");



							curRow.find('.sell_rt_type').html("Per Piece");



						}else if(i.item.calculation_based_on == 4){



							curRow.find('.buy_rt_type').html("Per Gram");



							curRow.find('.sell_rt_type').html("Per Gram");



						}



					}



					else{



						curRow.find('.buy_rt_type').html("");



						curRow.find('.sell_rt_type').html("");



					}



					curRow.find('.design').val("");



    				curRow.find('.des_id').val("");



					$('#lt_item_list > tbody').find('.design').focus();



				},



				change: function (event, ui) {



					if (ui.item === null) {



						console.log(1);



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



						curRow.find('td:eq(0) .lot_product').val("");



						curRow.find('td:eq(0) .pro_id').val("");



					}



		        },



				 minLength: 2,



			});



        }



     });



    }



}



function getSearchDesigns(searchTxt, curRow){



	console.log(curRow.find('.pro_id').val());



	my_Date = new Date();



	$.ajax({



        url: base_url+'index.php/admin_ret_estimation/getProductDesignBySearch/?nocache=' + my_Date.getUTCSeconds(),



        dataType: "json",



        method: "POST",



        data: {'searchTxt': searchTxt,'ProCode' :curRow.find('.pro_id').val() },



        success: function (data) {



			$( ".design" ).autocomplete(



			{



				source: data,



				select: function(e, i)



				{



					e.preventDefault();



					var exist = false;



					$('#lt_item_list> tbody  > tr').each(function(index, tr) {



					    if($(this).find('td:first .pro_id').val() == curRow.find('.pro_id').val() && $(this).find('td:eq(1) .des_id').val() == i.item.value){



                			exist = true;



                		}



					})



					if(!exist){



    					curRow.find('.design').val(i.item.label);



    					curRow.find('.des_id').val(i.item.value);



    					$('#lt_item_list > tbody').find('tr:last td:eq(2) .cat_qty').focus();



					}else{



					    alert("Already Same Product and design Exist.");



					    curRow.find('.design').val("");



					    curRow.find('.des_id').val("");



					}



				},



				change: function (event, ui) {



					if (ui.item === null) {



					    curRow.find('.design').val("");



					    curRow.find('.des_id').val("");



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



						curRow.find('.design').val("");



						curRow.find('.des_id').val("");



					}



		        },



				 minLength: 1,



			});



        }



     });



}



$('#add_more_lot').on('click',function(e){



	get_lot_preview();



});



function get_lot_preview()



{



	$("div.overlay").css("display", "block");



	var lot_preview_item=[];



	$('#lt_item_list> tbody  > tr').each(function(index, tr) {



		var lt_type_select=$('#lt_type_select').val();



		var lt_gold_smith_id=$('#lt_gold_smith_id').val();



		var stock_type=$('#stock_type:checked').val();



		var order_no=$('#lt_order_no').val();



	    var purity=($('#purity').select2('data')[0]!=undefined ? $('#purity').select2('data')[0].text:'-');



		var category=($('#category').select2('data')[0]!=undefined ? $('#category').select2('data')[0].text:'-');



		var lt_gold_smith=($('#lt_gold_smith').select2('data')[0]!= null ? $('#lt_gold_smith').select2('data')[0].text:'-');



		var gross_wt=parseFloat($(this).find('.gross_wt').val()).toFixed(3);



		var lot_lwt=parseFloat(isNaN($(this).find('.lot_lwt').val()) || $(this).find('.lot_lwt').val()=='' ? 0:$(this).find('.lot_lwt').val()).toFixed(3);



		var lot_nwt=parseFloat($(this).find('.lot_nwt').val()).toFixed(3);



		var precious_st_wt=$(this).find('.precious_stone_wt').val();



		var semi_precious_st_wt=$(this).find('.semi_precious_st_wt').val();



		var normal_st_wt=$(this).find('.normal_st_wt').val();



		var lot_pcs=$(this).find('.lot_pcs').val();



		lot_preview_item.push({



			'lt_type_select':(lt_type_select==1 ? 'Normal':(lt_type_select==2 ? 'Custom' :'Repair')),



			'category'			:category,



			'purity'			:purity,



			'stock_type'		:(stock_type==1 ?'Tagged' :'Non-Tagged'),



			'lt_gold_smith'		:lt_gold_smith,



			'order_no'			:order_no,



			'lot_pcs'			:lot_pcs,



			'gross_wt'			:gross_wt,



			'lot_lwt'			:lot_lwt,



			'lot_nwt'			:lot_nwt,



			'precious_st_wt'	:precious_st_wt,



			'semi_precious_st_wt':semi_precious_st_wt,



			'normal_st_wt'		 :normal_st_wt,



			});



	});



	console.log(lot_preview_item);



	set_lot_preview(lot_preview_item);



	$("div.overlay").css("display", "none");



}



function set_lot_preview(data)



{



        	var oTable = $('#lt_preview').DataTable();



        	oTable.clear().draw();



        	oTable = $('#lt_preview').dataTable({



        	"bDestroy": true,



        	"bInfo": true,



        	"bFilter": true,



        	"bSort": true,



        	"aaData": data,



        	"order": [[ 0, "desc" ]],



        	"aoColumns": [



        	{ "mDataProp": "lt_type_select" },



        	{ "mDataProp": "category" },



        	{ "mDataProp": "purity" },



        	{ "mDataProp": "stock_type" },



        	{ "mDataProp": "lt_gold_smith" },



        	{ "mDataProp": "order_no" },



        	{ "mDataProp": "lot_pcs" },



        	{ "mDataProp": "gross_wt" },



        	{ "mDataProp": "lot_lwt" },



        	{ "mDataProp": "lot_nwt" },



        	{ "mDataProp": "precious_st_wt" },



        	{ "mDataProp": "semi_precious_st_wt"},



        	{ "mDataProp": "normal_st_wt"},



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











            				tot_pcs = api



            				.column(6)



            				.data()



            				.reduce( function (a, b) {



            					return intVal(a) + intVal(b);



            				}, 0 );



            				$(api.column(6).footer()).html(parseFloat(tot_pcs).toFixed(2));







            				tot_gwt = api



            				.column(7)



            				.data()



            				.reduce( function (a, b) {



            					return intVal(a) + intVal(b);



            				}, 0 );



            				$(api.column(7).footer()).html(parseFloat(tot_gwt).toFixed(2));







            				tot_lwt = api



            				.column(8)



            				.data()



            				.reduce( function (a, b) {



            					return intVal(a) + intVal(b);



            				}, 0 );



            				$(api.column(8).footer()).html(parseFloat(tot_lwt).toFixed(2));







            				tot_nwt = api



            				.column(9)



            				.data()



            				.reduce( function (a, b) {



            					return intVal(a) + intVal(b);



            				}, 0 );



            				$(api.column(9).footer()).html(parseFloat(tot_nwt).toFixed(2));











            		}



            		}else{



            			 var api = this.api(), data;



            			 $(api.column(6).footer()).html('');



            			 $(api.column(7).footer()).html('');



            			 $(api.column(8).footer()).html('');



            			 $(api.column(9).footer()).html('');



            		}



            	}



        	});



}



function clearInputs(){



	$("#precious_stone,#semi_precious_stn,#normal_stn").prop("checked", false);



	$("#precious_stone,#semi_precious_stn,#normal_stn").val(0);



	$("#precious_stone_pcs,#semi_precious_st_pcs,#normal_st_pcs").attr("disabled",true);



	$("#precious_stone_pcs,#semi_precious_st_pcs,#normal_st_pcs").val();



	$("#precious_stone_wt,#semi_precious_st_wt,#normal_st_wt").attr("disabled",true);



	$("#precious_stone_wt,#semi_precious_st_wt,#normal_st_wt").val("");



	$("#uploadImg_p_stn,#uploadImg_sp_stn,#uploadImg_n_stn").attr("disabled",true);



	$("#pre_images,#semi_pre_imgs,#norm_pre_imgs").val("");



	$("#precious_st_certif,#semiprecious_st_certif,#normal_st_certif").val("");



	$("#uploadedArea_p_stn,#uploadedArea_sp_stn,#uploadedArea_n_st").css("dispay","none");



	$("#uploadedArea_p_stn div,#uploadedArea_sp_stn div,#uploadedArea_n_stn div").remove();



	$("#uploadArea_p_stn,#uploadArea_sp_stn,#uploadArea_n_stn").css("dispay","none");



	$("#uploadArea_p_stn div,#uploadArea_sp_stn div,#uploadArea_n_stn div").remove();



}



function show_stone_modal(curRow,id)



{



	clearInputs();



	var img_url = base_url+'/assets/img/lot/'+ctrl_page[3]+'/certificates';



	$('#row_id').val(id);



	var precious_stone 	= curRow.find('.precious_stone').val();



	var precious_st_pcs = curRow.find('.precious_stone_pcs').val();



	var precious_st_wt 	= curRow.find('.precious_stone_wt').val();



	var pre_wt_uom 		= curRow.find('.pre_wt_uom').val();



	var p_stn_certif_uploaded= curRow.find('.p_stn_certif_uploaded').val();



	var semi_precious_stn 		= curRow.find('.semi_precious_stn').val();



	var semi_precious_st_pcs	= curRow.find('.semi_precious_st_pcs').val();



	var semi_precious_st_wt		= curRow.find('.semi_precious_st_wt').val();



	var semi_wt_uom				= curRow.find('.semi_wt_uom').val();



	var sp_stn_certif_uploaded	= curRow.find('.sp_stn_certif_uploaded').val();



	var normal_stn 		= curRow.find('.normal_stn').val();



	var normal_st_pcs	= curRow.find('.normal_st_pcs').val();



	var normal_st_wt	= curRow.find('.normal_st_wt').val();



	var nor_wt_uom		= curRow.find('.nor_wt_uom').val();



	var n_stn_certif_uploaded= curRow.find('.n_stn_certif_uploaded').val();



	$('#stoneModal').modal('show');



	$('#precious_st_wt').val(precious_st_wt);



	$('#semi_precious_st_wt').val(semi_precious_st_wt);



	$('#normal_st_wt').val(normal_st_wt);



	$('#precious_st_pcs').val(precious_st_pcs);



	$('#semi_precious_st_pcs').val(semi_precious_st_pcs);



	$('#normal_st_pcs').val(normal_st_pcs);



	if(precious_stone == 1){



		$("#precious_stone").prop("checked", true);



		$("#precious_stone").val(1);



		$("#uploadImg_p_stn").prop("disabled",false);



		$("#precious_stone_pcs").attr("disabled",false);



		$("#precious_stone_wt").attr("disabled",false);



		var p_imgs = [];



		if(p_stn_certif_uploaded != ''){



			var p_imgs = p_stn_certif_uploaded.split('#');



			if(p_imgs.length > 0){



				$('#uploadedArea_p_stn').css('display','block');



			}



		}



		$.each(p_imgs,function(key,item){



		   if(item)



		   {



		   		var src = img_url+'/'+item;



				var div = document.createElement("div");



				div.setAttribute('class','col-md-4');



				div.setAttribute('id',+key);



				param = {"key":key,"preview":"uploadedArea_p_stn","stone_type":"pre_images"};



				div.innerHTML+= "<a onclick='remove_stn_img("+JSON.stringify(param)+")'><i class='fa fa-trash'></i>Delete</a><img class='thumbnail' src='" + src + "'" +



				"style='width: 100px;height: 100px;'/>";



				$('#uploadedArea_p_stn').append(div);



		   }



		   $('#lot_img_upload').css('display','');



		});



		$.each(pre_img_resource,function(key,item){



		   if(item.row_id == id)



		   {



		   		var div = document.createElement("div");



				div.setAttribute('class','col-md-4');



				div.setAttribute('id',+key);



				param = {"key":key,"preview":"uploadArea_p_stn","stone_type":"pre_images"};



				div.innerHTML+= "<a onclick='remove_stn_img("+JSON.stringify(param)+")'><i class='fa fa-trash'></i>Delete</a><img class='thumbnail' src='" + item.src + "'" +



				"style='width: 100px;height: 100px;'/>";



				$('#uploadArea_p_stn').append(div);



		   }



		   $('#lot_img_upload').css('display','');



		});



	}



	if(semi_precious_stn == 1){



		$("#semi_precious_stn").val(1);



		$("#semi_precious_stn").prop("checked", true);



		$("#uploadImg_sp_stn").prop("disabled",false);



		$("#semi_precious_st_pcs,#semi_precious_st_wt").attr("disabled",false);



		var sp_imgs = []



		if(sp_stn_certif_uploaded != ''){



			var sp_imgs = sp_stn_certif_uploaded.split('#');



			if(sp_imgs.length > 0){



				$('#uploadedArea_sp_stn').css('display','block');



			}



		}



		$.each(sp_imgs,function(key,item){



		   if(item)



		   {



		   		var src = img_url+'/'+item;



				var div = document.createElement("div");



				div.setAttribute('class','col-md-4');



				div.setAttribute('id',+key);



				param = {"key":key,"preview":"uploadedArea_sp_stn","stone_type":"semi_pre_imgs"};



				div.innerHTML+= "<a onclick='remove_stn_img("+JSON.stringify(param)+")'><i class='fa fa-trash'></i>Delete</a><img class='thumbnail' src='" + src + "'" +



				"style='width: 100px;height: 100px;'/>";



				$('#uploadedArea_sp_stn').append(div);



		   }



		   $('#lot_img_upload').css('display','');



		});



		$.each(sp_img_resource,function(key,item){



		   if(item.row_id == id)



		   {



		   		var div = document.createElement("div");



				div.setAttribute('class','col-md-4');



				div.setAttribute('id',+key);



				param = {"key":key,"preview":"uploadArea_sp_stn","stone_type":"semi_pre_images"};



				//div.innerHTML+= "<a onclick='remove_stn_img('"+key+"','"+preview+"','"+type+"')'><i class='fa fa-trash'></i>Delete</a><img class='thumbnail' src='" + item.src + "'" +



				div.innerHTML+= "<a onclick='remove_stn_img("+JSON.stringify(param)+")'><i class='fa fa-trash'></i>Delete</a><img class='thumbnail' src='" + item.src + "'" +



				"style='width: 100px;height: 100px;'/>";



				$('#uploadArea_sp_stn').append(div);



		   }



		   $('#lot_img_upload').css('display','');



		});



	}



	if(normal_stn == 1){



		$("#normal_stn").val(1);



		$("#normal_stn").prop("checked", true);



		$("#uploadImg_n_stn").prop("disabled",false);



		$("#normal_st_pcs,#normal_st_wt").attr("disabled",false);



		var n_imgs = [];



		if(n_stn_certif_uploaded != ''){



			var n_imgs = n_stn_certif_uploaded.split('#');



			if(n_imgs.length > 0){



				$('#uploadedArea_n_stn').css('display','block');



			}



		}



		$.each(n_imgs,function(key,item){



		   if(item)



		   {



		   		var src = img_url+'/'+item;



				var div = document.createElement("div");



				div.setAttribute('class','col-md-4');



				div.setAttribute('id',+key);



				param = {"key":key,"preview":"uploadedArea_n_stn","stone_type":"norm_pre_imgs"};



				div.innerHTML+= "<a onclick='remove_stn_img("+JSON.stringify(param)+")'><i class='fa fa-trash'></i>Delete</a><img class='thumbnail' src='" + src + "'" +



				"style='width: 100px;height: 100px;'/>";



				$('#uploadedArea_n_stn').append(div);



		   }



		   $('#lot_img_upload').css('display','');



		});



		$.each(n_img_resource,function(key,item){



		   if(item.row_id == id)



		   {



		   		var div = document.createElement("div");



				div.setAttribute('class','col-md-4');



				div.setAttribute('id',+key);



				param = {"key":key,"preview":"uploadArea_n_stn","stone_type":"norm_pre_imgs"};



				//div.innerHTML+= "<a onclick='remove_stn_img('"+key+"','"+preview+"','"+type+"')'><i class='fa fa-trash'></i>Delete</a><img class='thumbnail' src='" + item.src + "'" +



				div.innerHTML+= "<a onclick='remove_stn_img("+JSON.stringify(param)+")'><i class='fa fa-trash'></i>Delete</a><img class='thumbnail' src='" + item.src + "'" +



				"style='width: 100px;height: 100px;'/>";



				$('#uploadArea_n_stn').append(div);



		   }



		   $('#lot_img_upload').css('display','');



		});



	}



}



function get_karigar_by_order(order_no){



	$.ajax({



	 	type: 'POST',



	 	url: base_url+'index.php/admin_ret_lot/get_karigar_list',



	 	dataType:'json',



	 	data :{'order_no':order_no},



	 	success:function(data){



	 		$('#lt_gold_smith option').remove();



	 		$('#category option').remove();



	 		$('#purity option').remove();







		 	if(data.karigar.length==1)



		 	{



		 		$('#lt_gold_smith_id').val(data.karigar[0]['id_karigar']);



		 	}



		 	if(data.category.length==1)



		 	{



		 		$('#id_category').val(data.category[0]['id_ret_category']);



		 	}



		 	if(data.purity.length==1)



		 	{



		 		$('#id_purity').val(data.purity[0]['id_purity']);



		 	}



		 	var id =  $('#lt_gold_smith_id').val();



		 	var id_purity =  $('#id_purity').val();



		 	var id_category =  $('#id_category').val();



		 	$.each(data.karigar, function (key, item) {



	    	 	$("#lt_gold_smith").append(



	    	 	$("<option></option>")



	    	 	.attr("value", item.id_karigar)



	    	 	.text(item.karigar+' '+ item.code)



	    	 	);



	     	});



	     	$("#lt_gold_smith").select2("val",(id!='' && id>0?id:''));



			$.each(data.category, function (key, item) {



				$('#category').append(



				$("<option></option>")



				.attr("value", item.id_ret_category)



				.text(item.name)



				);



			});



			$("#category").select2("val",(id_category!='' && id_category>0?id_category:''));







			$.each(data.purity, function (key, item) {



				$('#purity').append(



				$("<option></option>")



				.attr("value", item.id_purity)



				.text(item.purity)



				);



			});



			$("#purity").select2({



			    placeholder: "Select Purity",



			    allowClear: true



			});



			$("#purity").select2("val",(id_purity!='' && id_purity>0?id_purity:''));



	     	$(".overlay").css("display", "none");



	 	}



	});



}



/*Lot Merge Module



-- Created By vijay --



-- Created On 14/04/23 --*/



function getLotidsforMerge()



{



	$('#lot_no_merge option').remove();



	my_Date = new Date();



	$.ajax({



		url:base_url+ "index.php/admin_ret_lot/lot_merge/getLotidsforMerge",



        type:"GET",



        dataType:"JSON",



		// data:{'stock_type':$('#stock_type:checked').val()},



        success:function(data)



        {



			$("#lot_no_merge").append(



				$("<option></option>")



					.attr("value","")



					.text('')



			);



           $.each(data, function (key, item) {



				$("#lot_no_merge").append(



				$("<option></option>")



				.attr("value", item.lot_no)



				.text(item.lot_no)



				);



			});



			$("#lot_no_merge").select2("val","");



			$(".overlay").css("display", "none");



		}



	});



}



$('.lot_no_search').on('click',function()



{



	if($('#lot_no').val()=='')



	{



		$.toaster({ priority : 'danger', title : 'Warning!', message : "Please Enter Lot No..."});



	}



	else



	{



		getLotNoForMerge();



	}



	$('#lot_search_list > tbody').empty();



	TotalLotMerge();



});



function getLotNoForMerge()



{



	my_Date = new Date();



		$.ajax({



		url:base_url+ "index.php/admin_ret_lot/lot_merge/getLotNos",



        type:"POST",



        dataType:"JSON",



		data:{'lot_no':$('#lot_no_merge').val()},



        success:function(data)



        {

			





			if(data!='' && data!=null)



			{



				var trHtml='';



				var a = $("#curRow").val();



				var i = ++a;



				$("#curRow").val(i);



				rowExist=false;







				$('#lot_det > tbody tr').each(function(bidx, brow){



					lot_id = $(this);



					if(lot_id.find('.lot_id').val() != '')



					{



						if( data[0].lot_no == lot_id.find('.lot_id').val()){



							rowExist = true;



						$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Lot Already Exists..'});



						}



					}



				});



				if(!rowExist)



				{







					$.each(data,function(key,item)



					{



						var stone_lot_details = [];



						$.each(item.stone_details,function(key,stn)



						{



							stone_lot_details.push({"stone_id":stn.stone_id,"uom_id":stn.uom_id,



							"stn_pcs":stn.stn_pcs,"stn_wt":stn.stn_wt});



						});







						trHtml +='<tr id='+i+'>'



							+'<td class="l_lot_no">'+item.lot_no+'<input type="hidden" class="lot_id" name="lot_merge['+i+'][lot_id]" value="'+item.lot_no+'"></td>'



							+'<td class="l_lot_no">'+item.id_lot_inward_detail+'<input type="hidden" class="lot_det_id" name="lot_merge['+i+'][lot_det_id]" value="'+item.id_lot_inward_detail+'"></td>'



							+'<td class="l_lot_pro">'+item.product_name+'</td>'



							+'<td class="l_lot_pcs">'+item.no_of_piece+'</td>'



							+'<td class="l_lot_wt">'+item.gross_wt+'</td>'



							+'<td class="l_lot_nwt">'+item.net_wt+'</td>'



							+'<td class="l_lot_stnpcs">'+item.stn_pcs+'<input type="hidden" class="stone_details" name="lot_merge['+i+'][stone_details]" value='+JSON.stringify(stone_lot_details)+'></td>'



							+'<td class="l_lot_stnwt">'+item.stn_wt+'</td>'



							+'<td class="l_lot_diapcs">'+item.dia_pcs+'</td>'



							+'<td class="l_lot_diawt">'+item.dia_wt+'</td>'



							+'<td>'+item.pur_ref_no+'</td>'



							+(access.delete=='1')? '<td><a href="#" onClick="remove_lot_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>':''



						+'</tr>';



					});



					$('#lot_det tbody').append(trHtml);



				}



			}



			else



			{



				$.toaster({ priority : 'danger', title : 'Warning!', message : "No Records Found..."});



			}



			$('#lot_no_merge').select2("val","");



			calculateLotTotal();



		}



	});



}



function remove_lot_row(curRow)



{



	curRow.remove();



	calculateLotTotal();



}



function calculateLotTotal()



{



	var tot_lot_pcs = 0;



	var tot_lot_wgt = 0;



	var tot_net_wt = 0;



	var tot_stn_pcs = 0;



	var tot_stn_wt = 0;



	var tot_dia_pcs = 0;



	var tot_dia_wt = 0;







	$("#lot_det tbody tr").each(function (index, value)



	{



		var row = $(this).closest('tr');



		tot_lot_pcs = tot_lot_pcs + parseFloat(row.find('.l_lot_pcs').html());



		tot_lot_wgt = tot_lot_wgt + parseFloat(row.find('.l_lot_wt').html());



		tot_net_wt  = tot_net_wt + parseFloat(row.find('.l_lot_nwt').html());







		tot_stn_pcs = tot_stn_pcs + parseFloat(row.find('.l_lot_stnpcs').html());



		tot_stn_wt = tot_stn_wt + parseFloat(row.find('.l_lot_stnwt').html());



		tot_dia_pcs = tot_dia_pcs + parseFloat(row.find('.l_lot_diapcs').html());



		tot_dia_wt = tot_dia_wt + parseFloat(row.find('.l_lot_diawt').html());



	});



	$('.tot_lot_pcs').html(tot_lot_pcs);



	$('.tot_lot_wgt').html(parseFloat(tot_lot_wgt).toFixed(3));



	$('.tot_lot_nwt').html(parseFloat(tot_net_wt).toFixed(3));



	$('.tot_stn_pcs').html(tot_stn_pcs);



	$('.tot_stn_wt').html(parseFloat(tot_stn_wt).toFixed(3));



	$('.tot_dia_pcs').html(tot_dia_pcs);



	$('.tot_dia_wt').html(parseFloat(tot_dia_wt).toFixed(3));



}







function calculate_lot_inward_Total()



{



	var tot_lt_piece = 0;



	var tot_lt_gross_wt = 0;



	var tot_lt_less_wt = 0;



	var tot_lt_net_wt = 0;



	var tot_lt_buy_rate = 0;



	var tot_lt_sell_rate = 0;



	var tot_lt_pur_rate = 0;



	var tot_lt_tax_amount = 0;



	var tot_lt_tax_rate = 0;



	var tot_lt_amount = 0;





	$("#lt_item_list tbody tr").each(function (index, value)



	{



		var row = $(this).closest('tr');



		var lt_piece = (isNaN(row.find('.lot_pcs').val())|| row.find('.lot_pcs').val() == '' ? 0 : row.find('.lot_pcs').val());

		var lt_gross_wt =  (isNaN(row.find('.gross_wt').val()) ||row.find('.gross_wt').val() == ''? 0 :row.find('.gross_wt').val());

		var lt_less_wt =  (isNaN(row.find('.lot_lwt').val()) ||row.find('.lot_lwt').val() == ''? 0 :row.find('.lot_lwt').val());

		var lt_net_wt = (isNaN(row.find('.lot_nwt').val()) ||row.find('.lot_nwt').val() == ''? 0 :row.find('.lot_nwt').val());

		var lt_buy_rate = (isNaN(row.find('.buy_rate').val()) ||row.find('.buy_rate').val() == '' ? 0 :row.find('.buy_rate').val());

		var lt_sell_rate =  (isNaN(row.find('.sell_rate').val()) ||row.find('.sell_rate').val() == ''? 0 :row.find('.sell_rate').val());

		var lt_pur_rate =  (isNaN(row.find('.rate_per_gram').val()) ||row.find('.rate_per_gram').val() == ''? 0 :row.find('.rate_per_gram').val());

		var lt_tax_amount = (isNaN(row.find('.total_taxable').val()) ||row.find('.total_taxable').val() == ''? 0 :row.find('.total_taxable').val());

		var lt_tax_rate = (isNaN(row.find('.total_tax').val()) ||row.find('.total_tax').val() == ''? 0 :row.find('.total_tax').val());

		var lt_amount = (isNaN(row.find('.cost').val()) ||row.find('.cost').val() == ''? 0 :row.find('.cost').val());





		tot_lt_piece += parseFloat(lt_piece) ;

		tot_lt_gross_wt += parseFloat(lt_gross_wt) ;

		tot_lt_less_wt += parseFloat(lt_less_wt);

		tot_lt_net_wt += parseFloat(lt_net_wt);

		tot_lt_buy_rate += parseFloat(lt_buy_rate);

		tot_lt_sell_rate +=parseFloat( lt_sell_rate );

		tot_lt_pur_rate += parseFloat(lt_pur_rate);

		tot_lt_tax_amount += parseFloat(lt_tax_amount);

		tot_lt_tax_rate += parseFloat(lt_tax_rate);

		tot_lt_amount += parseFloat(lt_amount);

	});



	$('.lt_piece').html(tot_lt_piece);



	$('.lt_gross_wt').html(parseFloat(tot_lt_gross_wt).toFixed(3));



	$('.lt_less_wt').html(parseFloat(tot_lt_less_wt).toFixed(3));



	$('.lt_net_wt').html(parseFloat(tot_lt_net_wt).toFixed(3));



	$('.lt_buy_rate').html(parseFloat(tot_lt_buy_rate).toFixed(2));



	$('.lt_sell_rate').html(parseFloat(tot_lt_sell_rate).toFixed(2));



	$('.lt_pur_rate').html(parseFloat(tot_lt_pur_rate).toFixed(2));



	$('.lt_tax_amount').html(parseFloat(tot_lt_tax_amount).toFixed(2));



	$('.lt_tax_rate').html(parseFloat(tot_lt_tax_rate).toFixed(2));



	$('.lt_amount').html(parseFloat(tot_lt_amount).toFixed(2));





}



$('#gold_smith').on('change',function()



{



	if(this.value!='')



	{



		$('#id_gold_smith').val(this.value);



	}



	else



	{



		$('#id_gold_smith').val('');



	}



});



$(document).on('change','.lm_cat',function()



{



	var row = $(this).closest('tr');



	if(this.value!='')



	{



		row.find('.lm_cat_id').val(this.value);



		get_ProductsForlot(row,this.value);



		get_purityForlot(row,this.value);



	}



})



function get_ProductsForlot(curRow,cat_id)



{



	$.ajax({



		url: base_url + 'index.php/admin_ret_lot/get_ActiveProduct',



		data: ({ 'id_category':cat_id}),



		dataType: "JSON",



		type: "POST",



		success: function (data)



		{



			curRow.find('.lm_pro option').remove();



			var pro_id = curRow.find('.lm_pro_id').val()



			$.each(data, function (key, item)



			{







					$(curRow.find('.lm_pro')).append(



					$("<option></option>")



					.attr("value", item.pro_id)



					.text(item.product_name)



					);



			});



			$(curRow.find('.lm_pro')).select2(



			{



				placeholder:"Select Product",



				allowClear: true



			});



			curRow.find('.lm_pro').select2("val",(pro_id!=''&& pro_id>0 ? pro_id :""));



		}



	});



}



function get_purityForlot(curRow,cat_id)



{



	curRow.find('.lm_purity option').remove();



	$.ajax({



		type: 'POST',



		url: base_url+'index.php/admin_ret_catalog/category/cat_purity',



		dataType:'json',



		data: {



			'id_category' :cat_id



		},



		success:function(data){



		  var id_purity =  curRow.find('.lm_id_purity').val();



		   $.each(data, function (key, item) {



			   		curRow.find('.lm_purity').append(



						$("<option></option>")



						  .attr("value", item.id_purity)



						  .text(item.purity)



					);



			});



			curRow.find('.lm_purity').select2({



			    placeholder: "Select Purity",



			    allowClear: true



			});



			curRow.find('.lm_purity').select2("val",(id_purity!='' && id_purity>0?id_purity:''));



			 $(".overlay").css("display", "none");



		}



	});



}



$(document).on('change','.lm_pro',function()



{



	if(this.value!='')



	{



		var row = $(this).closest('tr');



		row.find('.lm_pro_id').val(this.value);



		get_ActiveDesignsForLot(row,this.value);



	}



});



$(document).on('change','.lm_des',function()



{



	if(this.value!='')



	{



		var row = $(this).closest('tr');



		row.find('.lm_des_id').val(this.value);



		getActiveSubDesignsForLot(row,this.value)



	}



});



function get_ActiveDesignsForLot(curRow,pro_id)



{



	curRow.find('.lm_des option').remove();



	my_Date = new Date();



	$.ajax({



		url: base_url+'index.php/admin_ret_catalog/get_active_design_products',



        dataType: "json",



        method: "POST",



        data: {'id_product':pro_id},



		success: function (data)



		{



			var design_id = curRow.find('.lm_des_id').val()



			$.each(data, function (key, item) {



				$(curRow.find('.lm_des')).append(



				$("<option></option>")



				.attr("value", item.design_no)



				.text(item.design_name)



				);



			});



			$(curRow.find('.lm_des')).select2(



			{



				placeholder:"Select Design",



				allowClear: true



			});



			curRow.find('.lm_des').select2("val",(design_id!=''&& design_id>0 ? design_id :""));



		}



	});



}





function getActiveSubDesignsForLot(curRow,des_id)

{

	curRow.find('.lm_sub_des option').remove();



	var pro_id = curRow.find('.lm_pro_id').val();



	my_Date = new Date();



	$.ajax({



		url: base_url+'index.php/admin_ret_catalog/get_ActiveSubDesigns',



        dataType: "json",



        method: "POST",



        data: {'id_product':pro_id,'design_no':des_id},



		success: function (data)



		{



			var sub_design_id = curRow.find('.lm_sub_des_id').val()



			$.each(data, function (key, item) {



				$(curRow.find('.lm_sub_des')).append(



				$("<option></option>")



				.attr("value", item.id_sub_design)



				.text(item.sub_design_name)



				);



			});



			$(curRow.find('.lm_sub_des')).select2(



			{



				placeholder:"Select Sub Design",



				allowClear: true



			});



			curRow.find('.lm_sub_des').select2("val",(sub_design_id!=''&& sub_design_id>0 ? sub_design_id :""));



		}



	});



}





$(document).on('change','.lm_sub_des',function()

{



	if(this.value!='')

	{



		var row = $(this).closest('tr');



		row.find('.lm_sub_des_id').val(this.value);

	}



});



$('#lot_merge_submit').on('click',function()



{



	if(ValidateTotLots())



	{



		if($("#id_gold_smith").val() != "")



		{



			if(validateLotMergeRow())



			{



				$('#lot_merge_form').submit();



				window.location.href= base_url+'index.php/admin_ret_lot/lot_inward/list';



				window.location.reload();



			}



			else



			{



				$.toaster({ priority : 'danger', title : 'Warning!', message : "Please fill All Required Fields..."});



			}



		}



		else



		{



			$.toaster({ priority : 'danger', title : 'Warning!', message : "Please Select Karigar..."});



		}



	}



	else



	{



		$.toaster({ priority : 'danger', title : 'Warning!', message : "Total Pcs & Total wgt Must Be equal..."});



	}



});



function ValidateTotLots()



{



	var totalvalidate = false;



	var total_lotted_pcs = $('.tot_lot_pcs').html();



	var total_lotted_wgt = $('.tot_lot_wgt').html();



	var tot_lotNew_pcs = $('.lot_tot_pcs').html();



	var tot_lotNew_wgt = $('.lot_tot_gwt').html();



	if((total_lotted_pcs == tot_lotNew_pcs) && (total_lotted_wgt == tot_lotNew_wgt))



	{



		totalvalidate = true;



	}



	console.log('total_lotted_pcs',total_lotted_pcs);



	console.log('total_lotted_wgt',total_lotted_wgt);



	console.log('------------------');



	console.log('tot_lotNew_pcs',tot_lotNew_pcs);



	console.log('tot_lotNew_wgt',tot_lotNew_wgt);



	return totalvalidate;



}



$('#add_lot_merge').on('click',function()



{



	if($('.tot_lot_pcs').html()!='' && $('.tot_lot_wgt').html()!='')



	{



		create_new_lot_merge_row();



	}



	else



	{



		$.toaster({ priority : 'danger', title : 'Warning!', message : "No lot Details founded to merge..."});



	}



});



function validateLotMergeRow()



{



	var row_validate = true;



	var stock_type=$('#stock_type:checked').val();





	$('#lot_search_list > tbody > tr').each(function(index, tr)



	{



		if($(this).find('.lm_pro').val() == "" || $(this).find('.lm_pro').val() ==null



		|| $(this).find('.lm_purity').val() == "" || $(this).find('.lm_purity').val() == null



		|| $(this).find('.lm_pcs').val() == "" || $(this).find('.gross_wt').val()=="")



		{



			row_validate = false;



		}



	});



	return row_validate;



}



function create_new_lot_merge_row()



{



	$('#lot_search_list tbody').empty();



	var row = "";





	var stock_type = $('#stock_type:checked').val();



	var stock_type = $('#stock_type:checked').val();



	var a = $("#curRow").val();



	var i = ++a;



	$("#curRow").val(i);



	var uom='';



	var net_wt = parseFloat(parseFloat($('.tot_lot_wgt').html()) - (parseFloat($('.tot_stn_wt').html()) + parseFloat($('.tot_dia_wt').html())));



	var less_wt = parseFloat(parseFloat($('.tot_stn_wt').html()) + parseFloat($('.tot_dia_wt').html())).toFixed(3);











	$.each(uom_details,function(key,item){



		uom += "<option value='"+item.uom_id+"'>"+item.code+"</option>";



	});



	row += '<tr id='+i+'>'







		+'<td><select class="lm_cat" name="merge_item['+i+'][lm_cat]" value="" placeholder="Search Category" style="width:150px;"><input type="hidden" class="cat_id" name="merge_item['+i+'][lm_cat_id]" value="" /><input type="hidden" class="sales_mode" name="merge_item['+i+'][sales_mode]" value="" /><input type="hidden" class="calculation_based_on" name="merge_item['+i+'][calculation_based_on]" value="" /<input type="hidden" class="id_lot_inward_detail" id="id_lot_inward_detail" value=""></td>'



		+'<td><select class="lm_pro" name="merge_item['+i+'][lm_pro]" value="" placeholder="Search Product" style="width:150px;"><input type="hidden" class="lm_pro_id" name="merge_item['+i+'][lm_pro_id]" value="" /><input type="hidden" class="sales_mode" name="merge_item['+i+'][sales_mode]" value="" /><input type="hidden" class="calculation_based_on" name="merge_item['+i+'][calculation_based_on]" value="" /<input type="hidden" class="id_lot_inward_detail" id="id_lot_inward_detail" value=""></td>'



		+'<td><select class="lm_des" name="merge_item['+i+'][lm_des]" value="" placeholder="Search Design" style="width:150px;" '+(stock_type==1 ? 'disabled' : '')+'><input type="hidden" class="lm_des_id" name="merge_item['+i+'][lm_des_id]" value="" /></td>'



		+'<td><select class="lm_sub_des" name="merge_item['+i+'][lm_sub_des]" value="" placeholder="Search Design" style="width:150px;" '+(stock_type==1 ? 'disabled' : '')+'><input type="hidden" class="lm_sub_des_id" name="merge_item['+i+'][lm_sub_des_id]" value="" /></td>'



		+'<td><select class="lm_purity" name="merge_item['+i+'][lm_purity]" value="" placeholder="Search Purity" style="width:150px;"><input type="hidden" class="lm_id_purity" name="merge_item['+i+'][lm_id_purity]" value="" /></td>'



		+'<td><input type="number" step="any" value="'+$('.tot_lot_pcs').html()+'" name="merge_item['+i+'][pcs]" class="lm_pcs" style="width:60px;" readonly></td>'



		+'<td><div class="input-group"><input type="number" step="any"  name="merge_item['+i+'][gross_wt]" value="'+$('.tot_lot_wgt').html()+'" class="gross_wt" style="width:80px;" readonly><span class="input-group-addon input-sm no-padding" style="background-color: transparent;height: 12px;font-size: 16px;border: none;"><select class="gross_wt_uom" name="merge_item['+i+'][gross_wt_uom]">'+uom+'</select></span></div></td>'







		+'<td><input type="number" step="any"  name="merge_item['+i+'][stn_pcs]" value="'+$('.tot_stn_pcs').html()+'" class="lm_stn_pcs" style="width:60px;" readonly><input type="hidden" name="merge_item['+i+'][less_wt]" value="'+less_wt+'"></td>'



		+'<td><div class="input-group"><input type="number" step="any" value="'+$('.tot_stn_wt').html()+'" name="merge_item['+i+'][stn_wt]" class="stn_wt" style="width:80px;" readonly></div></td>'



		+'<td><input type="number" step="any" value="'+$('.tot_dia_pcs').html()+'" name="merge_item['+i+'][dia_pcs]" class="lm_dia_pcs" style="width:60px;" readonly></td>'



		+'<td><div class="input-group"><input type="number" step="any" value="'+$('.tot_dia_wt').html()+'" name="merge_item['+i+'][dia_wt]" class="dia_wt" style="width:80px;" readonly></div></td>'



		+'<td><div class="input-group"><input type="number" step="any" value="'+$('.tot_lot_nwt').html()+'" name="merge_item['+i+'][net_wt]" class="lot_nwt" style="width:80px;" readonly><span class="input-group-addon input-sm no-padding" style="background-color: transparent;height: 12px;font-size: 16px;border: none;"><select class="net_wt_uom" name="merge_item['+i+'][net_wt_uom]">'+uom+'</select></span></div></td>'



	+'</tr>';



	$('#lot_search_list tbody').append(row);



	$('.lm_cat').append(



		$("<option></option>")



		.attr("value", "")



		.text('-Choose-')



	);







	$.each(lot_cat_details, function (key, item)



	{



		$('.lm_cat').append(



		$("<option></option>")



		.attr("value", item.id_ret_category)



		.text(item.name)



		);



	});



	$('#lot_search_list > tbody').find('.lm_cat').select2();



	$('#lot_search_list > tbody').find('.lm_pro').select2();



	$('#lot_search_list > tbody').find('.lm_des').select2();



	$('#lot_search_list > tbody').find('.lm_sub_des').select2();



	$('#lot_search_list > tbody').find('.lm_purity').select2();







	$('#lot_search_list > tbody').find('.lm_cat').select2({



		placeholder: "Category",



		allowClear: true



	});







	$('#lot_search_list > tbody').find('.lm_pro').select2({



		placeholder: "Product",



		allowClear: true



	});







	$('#lot_search_list > tbody').find('.lm_des').select2({



		placeholder: "Design",



		allowClear: true



	});



	$('#lot_search_list > tbody').find('.lm_sub_des').select2({



		placeholder: "Sub Design",



		allowClear: true



	});



	$('#lot_search_list > tbody').find('.lm_purity').select2({



		placeholder: "Purity",



		allowClear: true



	});



	$('#lot_search_list > tbody').find('tr:last td:eq(0) .lm_cat').focus();



	TotalLotMerge();



}



function TotalLotMerge()



{



	var lot_pcs = 0;



	var lot_wgt = 0;



	var lot_stn_pcs = 0;



	var lot_stn_wt = 0;



	var lot_dia_pcs = 0;



	var lot_dia_wt = 0;



	var lot_nwt = 0;



	$("#lot_search_list tbody tr").each(function (index, value)



	{



		var row = $(this).closest('tr');







		lot_pcs = lot_pcs + (isNaN(row.find('.lm_pcs').val()) ? 0 :parseFloat(row.find('.lm_pcs').val()));







		lot_wgt = lot_wgt + (isNaN(parseFloat(row.find('.gross_wt').val())) ? 0 :parseFloat(row.find('.gross_wt').val()));







		lot_stn_pcs = lot_stn_pcs + (isNaN(parseFloat(row.find('.lm_stn_pcs').val())) ? 0 : parseFloat(row.find('.lm_stn_pcs').val()));



		lot_stn_wt = lot_stn_wt + (isNaN(parseFloat(row.find('.stn_wt').val())) ? 0 : parseFloat(row.find('.stn_wt').val()));



		lot_dia_pcs = lot_dia_pcs + (isNaN(parseFloat(row.find('.lm_dia_pcs').val())) ? 0 : parseFloat(row.find('.lm_dia_pcs').val()));



		lot_dia_wt = lot_dia_wt + (isNaN(parseFloat(row.find('.dia_wt').val())) ? 0 : parseFloat(row.find('.dia_wt').val()));



		lot_nwt = lot_nwt + (isNaN(parseFloat(row.find('.lot_nwt').val())) ? 0 : parseFloat(row.find('.lot_nwt').val()));











	});



	$('.lot_tot_pcs').html(lot_pcs);



	$('.lot_tot_gwt').html(parseFloat(lot_wgt).toFixed(3));



	$('.lot_tot_stnpcs').html(lot_stn_pcs);



	$('.lot_tot_stnwt').html(parseFloat(lot_stn_wt).toFixed(3));



	$('.lot_tot_diapcs').html(lot_dia_pcs);



	$('.lot_tot_diawt').html(parseFloat(lot_dia_wt).toFixed(3));



	$('.lot_tot_nwt').html(parseFloat(lot_nwt).toFixed(3));



}



/*Lot Merge Module



-- Created By vijay --



-- Created On 14/04/23 --*/



/*Lot Split Module



-- Created By vijay --



-- Created On 14/04/23 --*/



function getLotidsforSplit()



{



	$('#lot_no_split option').remove();



	my_Date = new Date();



	$.ajax({



		url:base_url+ "index.php/admin_ret_lot/lot_split/getLotidsforSplit",



        type:"GET",



        dataType:"JSON",



        success:function(data)



        {



			$("#lot_no_split").append(



				$("<option></option>")



					.attr("value","")



					.text('')



			);



           $.each(data, function (key, item) {



				$("#lot_no_split").append(



				$("<option></option>")



				.attr("value", item.lot_no)



				.text(item.lot_no)



				);



			});



			$("#lot_no_split").select2("val","");



			$(".overlay").css("display", "none");



		}



	});



}



$('.lot_split_search').on('click',function()



{



	get_LotNos_for_split();



	get_Lot_details_summary();



});



function get_LotNos_for_split()



{



	$.ajax({



		type: 'POST',



		url: base_url+'index.php/admin_ret_lot/lot_split/lotNosForsplit',



		dataType:'json',



		data: {



			'lot_no' :$('#lot_no_split').val()



		},



		success:function(data)



		{



			if(data!='')



			{



				var row = "";



				var stock_type = $('#stock_type:checked').val();



				var a = $("#curRow").val();



				var i = ++a;



				$("#curRow").val(i);



				rowExist=false;







				$('#lot_split_list > tbody tr').each(function(bidx, brow){



					lot_id = $(this);



					if(lot_id.find('.lot_no').val() != '')



					{



						if( data[0].lot_no == lot_id.find('.lot_no').val()){



							rowExist = true;



						$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Lot Already Exists..'});



						}



					}



				});



				if(!rowExist)



				{



					$.each(data,function(key,val)



					{











						row += '<tr id='+key+'>'



							+'<td><input type="checkbox" class="is_lot_select"  value="0"></td>'



							+'<td>'+val.lot_no+'<input type="hidden" class="lot_no" value="'+val.lot_no+'"></td>'



							+'<td>'+val.id_lot_inward_detail+'<input type="hidden" class="id_lot_inward_detail" value="'+val.id_lot_inward_detail+'"></td>'



							+'<td><select class="form_group select_employee" id="select_employee" style="width: 150px;" placeholder="Select Empolyee"></td>'



							+'<td ><span class="cat_name">'+val.category+'</span><input type="hidden" class="cat_id"  value="'+val.id_category+'"></td>'



							+'<td><span class="pro_name">'+val.product_name+'</span><input type="hidden" class="pro_id"  value="'+val.lot_product+'"></td>'



							+'<td><span class="purity">'+val.purity+'</span><input type="hidden" class="id_purity"  value="'+val.id_purity+'"></td>'



							+'<td><input type="number" step="any"  value="'+val.bal_piece+'" class="form-control lot_pcs" style="width:100px;" readonly><div style="color:blue">blcpcs : <span class="bal_lot_pcs">'+val.bal_piece+'</span></div></td>'



							+'<td><input type="number" step="any"  value="'+val.bal_gross_wt+'" class="form-control lot_wt" style="width:100px;" readonly><div style="color:blue">blcGrsWt : <span class="bal_lot_wt">'+val.bal_gross_wt+'</span></div></td>'



							+'<td><input type="number" step="any"   value="" class="form-control custom-inp split_pcs" style="width:100px;"></td>'



							+'<td><input type="number" step="any"   value="" class="form-control split_wt" style="width:100px;"></td>'



							+'<td><input type="number" step="any"   value="" class="form-control split_nwt" style="width:100px;" readonly></td>'



							+'<td><input type="number" step="any"   class="form-control split_stn_pcs" style="width:100px;" value="'+val.bal_stn_pcs+'"><div style="color:blue">blc Stnpcs : <span class="bal_lot_stnpcs">'+val.bal_stn_pcs+'</span></div><input type="hidden" class="tot_stn_pcs" value="'+val.bal_stn_pcs+'"></td>'



							+'<td><input type="number" step="any"   class="form-control split_stn_wt" style="width:100px;" value="'+val.bal_stn_wt+'"><div style="color:blue">blc StnWt : <span class="bal_lot_stnWt">'+val.bal_stn_wt+'</span></div><input type="hidden" class="tot_stn_wt" value="'+val.bal_stn_wt+'"></td>'



							+'<td><input type="number" step="any"   value="'+val.bal_dia_pcs+'" class="form-control split_dia_pcs" style="width:100px;"><div style="color:blue">blc diapcs : <span class="bal_lot_diapcs">'+val.bal_dia_pcs+'</span></div><input type="hidden" class="tot_dia_pcs" value="'+val.bal_dia_pcs+'"></td>'



							+'<td><input type="number" step="any"  value="'+val.bal_dia_wt+'" class="form-control split_dia_wt" style="width:100px;"><div style="color:blue">blc diaWt : <span class="bal_lot_diaWt">'+val.bal_dia_wt+'</span></div><input type="hidden" class="tot_dia_wt" value="'+val.bal_dia_wt+'"><input type="hidden" class="divided_by_value" value="'+val.divided_by_value+'"></td>'



						+'</tr>';



					});



				}



				$('#lot_split_list tbody').append(row);



				$('.select_employee').append(



					$("<option></option>")



					.attr("value",'')



					.text('--Choose--')



				);







				$.each(emp_details,function(key,item)



				{



					$('.select_employee').append(



						$("<option></option>")



                	 	.attr("value", item.id_employee)



                	 	.text(item.emp_name)



                	);



				});







				$('#lot_split_list > tbody').find('.select_employee').select2();



				$('#lot_split_list > tbody').find('.select_employee').select2({



					placeholder: "Employee",



					allowClear: true



				});



			}



			else



			{



				$.toaster({ priority : 'danger', title : 'Warning!', message : "No Records Found..."});



			}



			$('#lot_no').val("");



		}



	});



}



$(document).on('change','.is_lot_select',function()



{



	var row = $(this).closest('tr');



	if(row.find(".is_lot_select").is(":checked"))



	{



		row.find(".is_lot_select").val(1);



	}



	else



	{



		row.find(".is_lot_select").val(0);



	}



});



$(document).on('change','.split_pcs',function()



{



	var row = $(this).closest('tr');



	var tot_lot_pcs = parseInt(row.find('.lot_pcs').val());



	if(this.value > tot_lot_pcs)



	{



		row.find('.split_pcs').val('');



		$.toaster({ priority : 'danger', title : 'Warning!', message : "Split Pcs must be lesser than actually Pcs..."});



	}



	else



	{



		row.find('.split_pcs').val(this.value);



		check_bal_details(row);



	}



});



$(document).on('change','.split_wt',function()



{



	var row = $(this).closest('tr');



	var tot_lot_wt = parseFloat(row.find('.lot_wt').val());



	if(this.value > tot_lot_wt)



	{



		row.find('.split_wt').val('');



		$.toaster({ priority : 'danger', title : 'Warning!', message : "Split Wgt must be lesser than actually Wgt..."});



	}



	else



	{



		row.find('.split_wt').val(this.value);



		check_bal_details(row);



	}



	calculateNwtForSplit(row);



});



$(document).on('change',".split_stn_pcs",function()



{



	var curRow = $(this).closest('tr');







	if(this.value > parseInt(curRow.find('.tot_stn_pcs').val()))



	{



		curRow.find('.split_stn_pcs').val(curRow.find('.tot_stn_pcs').val());



		$.toaster({ priority : 'danger', title : 'Warning!', message : "Split Stn pcs must be lesser than actually Stn pcs..."});



	}



	else



	{



		curRow.find('.split_stn_pcs').val(this.value);



		check_bal_details(curRow);



	}



});



$(document).on('change',".split_stn_wt",function()



{







	var curRow = $(this).closest('tr');



	if(this.value > parseFloat(curRow.find('.tot_stn_wt').val()))



	{



		curRow.find('.split_stn_wt').val(parseFloat(curRow.find('.tot_stn_wt').val()));



		$.toaster({ priority : 'danger', title : 'Warning!', message : "Split Stn Wgt must be lesser than actually Stn pcs..."});



	}



	else



	{



		curRow.find('.split_stn_wt').val(this.value);



		check_bal_details(curRow);



	}



	calculateNwtForSplit(curRow);



});



$(document).on('change','.split_dia_pcs',function()



{



	var curRow = $(this).closest('tr');



	if(this.value > parseInt(curRow.find('.tot_dia_pcs').val()))



	{



		curRow.find('.split_dia_pcs').val(curRow.find('.tot_dia_pcs').val());



		$.toaster({ priority : 'danger', title : 'Warning!', message : "Split Dia pcs must be lesser than actually Dia pcs..."});



	}



	else



	{



		curRow.find('.split_dia_pcs').val(this.value);



		check_bal_details(curRow);



	}



});



$(document).on('change','.split_dia_wt',function()



{



	var curRow = $(this).closest('tr');



	if(this.value > parseFloat(curRow.find('.tot_dia_wt').val()))



	{



		curRow.find('.split_dia_wt').val(parseFloat(curRow.find('.tot_dia_wt').val()));



		$.toaster({ priority : 'danger', title : 'Warning!', message : "Split Dia wt must be lesser than actually Dia wt..."});



	}



	else



	{



		curRow.find('.split_dia_wt').val(this.value);



		check_bal_details(curRow);



	}







	calculateNwtForSplit(curRow);



});



function calculateNwtForSplit(curRow)



{



	var net_wt = 0;



	var diamond_wt = 0;



	if(parseFloat(curRow.find('.split_dia_wt').val()) >0 && (curRow.find('.divided_by_value').val()!=null && curRow.find('.divided_by_value').val()!=''))



	{



		diamond_wt = parseFloat(parseFloat(curRow.find('.split_dia_wt').val())/parseFloat(curRow.find('.divided_by_value').val())).toFixed(3);



	}



	net_wt = parseFloat(parseFloat(curRow.find('.split_wt').val()) - (parseFloat(curRow.find('.split_stn_wt').val()) + parseFloat(diamond_wt)));



	if(net_wt < 0)



	{



		curRow.find('.split_nwt').val(curRow.find('.split_wt').val());



	}



	else



	{



		curRow.find('.split_nwt').val(parseFloat(net_wt).toFixed(3));



	}



}



function get_Lot_details_summary()



{



	my_Date = new Date();



		$.ajax({



		url:base_url+ "index.php/admin_ret_lot/lot_split/getLotDetails",



        type:"POST",



        dataType:"JSON",



		data:{'lot_no':$('#lot_no_split').val()},



        success:function(data)



        {



			if(data!='' || data!=null)



			{



				var trHtml='';



				var a = $("#curRow").val();



				var i = ++a;



				$("#curRow").val(i);



				rowExist=false;



				$('#lot_details_summary > tbody tr').each(function(bidx, brow){



					lot_id = $(this);



					if(lot_id.find('.lot_id').val() != '')



					{



						if( data[0].lot_no == lot_id.find('.lot_id').val())



						{



							rowExist = true;



						}



					}



				});



				if(!rowExist)



				{







					$.each(data,function(key,item)



					{



						trHtml +='<tr id='+i+'>'



							+'<td class="l_lot_no">'+item.lot_no+'<input type="hidden" class="lot_id" value="'+item.lot_no+'"></td>'



							+'<td class="l_lot_no">'+item.id_lot_inward_detail+'<input type="hidden" class="lot_det_id" value="'+item.id_lot_inward_detail+'"></td>'



							+'<td class="l_lot_pro">'+item.product_name+'</td>'



							+'<td class="l_lot_pcs">'+item.no_of_piece+'</td>'



							+'<td class="l_lot_wt">'+item.gross_wt+'</td>'



							+'<td class="l_lot_nwt">'+item.net_wt+'</td>'



							+'<td class="l_lot_stnpcs">'+item.stn_pcs+'</td>'



							+'<td class="l_lot_stnwt">'+item.stn_wt+'</td>'



							+'<td class="l_lot_diapcs">'+item.dia_pcs+'</td>'



							+'<td class="l_lot_diawt">'+item.dia_wt+'</td>'



							+'<td>'+item.pur_ref_no+'</td>'



							+'<td><a href="#" onClick="remove_splitdet_row($(this).closest(\'tr\'),'+item.id_lot_inward_detail+');" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'



						+'</tr>';



					});



					$('#lot_details_summary tbody').append(trHtml);



				}



			}



			else



			{



				$.toaster({ priority : 'danger', title : 'Warning!', message : "No Records Found..."});



			}



			$('#lot_no_split').select2("val",'');



		}



	});



}



function remove_splitdet_row(curRow,id_lot_inward_detail)



{



	curRow.remove();



	$('#lot_split_list > tbody > tr').each(function(index, tr) // clears if item in details table



	{



		var row = $(this);



		if(id_lot_inward_detail == row.find('.id_lot_inward_detail').val())



		{



			row.remove();



		}



	});



	$('#lt_split_preview > tbody > tr').each(function(index,tr) // Clears if item in preview table



	{



		var row = $(this);



		if(id_lot_inward_detail == row.find('.id_lot_inward_detail').val())



		{



			row.remove();



		}



	});



	calulateTotalSplitRow();



}



function validateLotSplitRow()



{



	var row_validate = true;



	$('#lot_split_list > tbody > tr').each(function(index, tr)



	{







		if($(this).find('.is_lot_select').is(':checked'))



		{



			if($(this).find('.split_pcs').val() == "" || $(this).find('.split_wt').val() =="" || $(this).find('.select_employee').val() == "")



			{



				row_validate = false;



			}



		}







	});



	return row_validate;



}



$('#add_to_lot_split_list').on('click',function()



{



	if(validateLotSplitRow())



	{



		set_lot_split_preview();



		$('.split_pcs').trigger('change');



	}



	else



	{



		$.toaster({ priority : 'danger', title : 'Warning!', message : "Please Fill required Fields..."});



	}



});



function set_lot_split_preview()



{



	var trHtml = '';



	$("#lot_split_list tbody tr").each(function (index, value)



	{



		var a = $("#split_curRow").val();



		var i = ++a;



		$("#split_curRow").val(i);



		var row = $(this).closest('tr');



		if(row.find(".is_lot_select").is(":checked"))



		{



				trHtml +='<tr id='+i+'>'







					+'<td><input type="hidden" name="split_item['+i+'][lot_no]" class="lot_no" value="'+row.find('td:eq(1) .lot_no').val()+'">'+row.find('td:eq(1) .lot_no').val()+'</td>'







					+'<td><input type="hidden" name="split_item['+i+'][id_lot_inward_detail]" class="id_lot_inward_detail" value="'+row.find('td:eq(2) .id_lot_inward_detail').val()+'">'+ row.find('td:eq(2) .id_lot_inward_detail').val()+'</td>'







					+'<td><input type="hidden" name="split_item['+i+'][id_employee]" class="id_employee" value="'+row.find('td:eq(3) .select_employee').val()+'">'+ row.find('td:eq(3) .select_employee option:selected').text()+'</td>'







					+'<td><input type="hidden" name="split_item['+i+'][cat_id]" class="cat_id" value="'+row.find('td:eq(4) .cat_id').val()+'">'+ row.find('td:eq(4) .cat_name').html()+'</td>'







					+'<td><input type="hidden" name="split_item['+i+'][pro_id]" class="pro_id" value="'+row.find('td:eq(5) .pro_id').val()+'">'+ row.find('td:eq(5) .pro_name').html()+'</td>'







					+'<td><input type="hidden" name="split_item['+i+'][id_purity]" class="id_purity" value="'+row.find('td:eq(6) .id_purity').val()+'">'+ row.find('td:eq(6) .purity').html()+'</td>'







					+'<td><input type="hidden" name="split_item['+i+'][split_pcs]" class="split_pcs" value="'+row.find('td:eq(9) .split_pcs').val()+'">'+ row.find('td:eq(9) .split_pcs').val()+'</td>'







					+'<td><input type="hidden" name="split_item['+i+'][split_wt]" class="split_wt" value="'+row.find('td:eq(10) .split_wt').val()+'">'+ row.find('td:eq(10) .split_wt').val()+'</td>'



					+'<td><input type="hidden" name="split_item['+i+'][split_nwt]" class="split_nwt" value="'+row.find('td:eq(11) .split_nwt').val()+'">'+ row.find('td:eq(11) .split_nwt').val()+'</td>'







					+'<td><input type="hidden" name="split_item['+i+'][split_stn_pcs]" class="split_stn_pcs" value="'+row.find('td:eq(12) .split_stn_pcs').val()+'">'+ row.find('td:eq(12) .split_stn_pcs').val()+'</td>'







					+'<td><input type="hidden" name="split_item['+i+'][split_stn_wt]" class="split_stn_wt" value="'+row.find('td:eq(13) .split_stn_wt').val()+'">'+ row.find('td:eq(13) .split_stn_wt').val()+'</td>'



					+'<td><input type="hidden" name="split_item['+i+'][split_dia_pcs]" class="split_dia_pcs" value="'+row.find('td:eq(14) .split_dia_pcs').val()+'">'+ row.find('td:eq(14) .split_dia_pcs').val()+'</td>'







					+'<td><input type="hidden" name="split_item['+i+'][split_dia_wt]" class="split_dia_wt" value="'+row.find('td:eq(15) .split_dia_wt').val()+'">'+ row.find('td:eq(15) .split_dia_wt').val()+'</td>'



					+'<td><a href="#" onClick="remove_splitlot_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'







				+'</tr>';



		}



	});



	$('#lt_split_preview tbody').append(trHtml);



	calulateTotalSplitRow();



	reset_lot_split_row($(this).closest('tr'));



}



function remove_splitlot_row(curRow)



{



	curRow.remove();



	calulateTotalSplitRow();







}



function calulateTotalSplitRow()



{



	var split_pcs = 0;



	var split_grs_wt = 0;



	var split_net_wt = 0;



	var split_stn_pcs = 0;



	var split_stn_wt = 0;



	var split_dia_pcs = 0;



	var split_dia_wt = 0;



	$("#lt_split_preview  > tbody tr").each(function ()



	{



		var prev_row = $(this).closest('tr');







		split_pcs = split_pcs + (isNaN(prev_row.find('td:eq(6) .split_pcs').val() ) ? 0 : parseFloat(prev_row.find('td:eq(6) .split_pcs').val()));







		split_grs_wt = split_grs_wt + (isNaN( prev_row.find('td:eq(7) .split_wt').val() ) ? 0 : parseFloat(prev_row.find('td:eq(7) .split_wt').val()));



		split_net_wt = split_net_wt + (isNaN( prev_row.find('td:eq(8) .split_nwt').val() ) ? 0 : parseFloat(prev_row.find('td:eq(8) .split_nwt').val()));



		split_stn_pcs = split_stn_pcs + (isNaN(prev_row.find('td:eq(9) .split_stn_pcs').val() ) ? 0 : parseFloat(prev_row.find('td:eq(9) .split_stn_pcs').val()));







		split_stn_wt = split_stn_wt + (isNaN( prev_row.find('td:eq(10) .split_stn_wt').val() ) ? 0 : parseFloat(prev_row.find('td:eq(10) .split_stn_wt').val()));



		split_dia_pcs = split_dia_pcs + (isNaN(prev_row.find('td:eq(11) .split_dia_pcs').val() ) ? 0 : parseFloat(prev_row.find('td:eq(11) .split_dia_pcs').val()));







		split_dia_wt = split_dia_wt + (isNaN( prev_row.find('td:eq(12) .split_dia_wt').val() ) ? 0 : parseFloat(prev_row.find('td:eq(12) .split_dia_wt').val()));







	});



	$('.tot_split_pcs').html(split_pcs);



	$('.tot_split_wt').html(parseFloat(split_grs_wt).toFixed(3));



	$('.tot_split_nwt').html(parseFloat(split_net_wt).toFixed(3));



	$('.tot_split_stn_pcs').html(split_stn_pcs);



	$('.tot_split_stn_wt').html(parseFloat(split_stn_wt).toFixed(3));



	$('.tot_split_dia_pcs').html(split_dia_pcs);



	$('.tot_split_dia_wt').html(parseFloat(split_dia_wt).toFixed(3));



}



function reset_lot_split_row()



{



	/*Reset Values in lot split table*/



	$('#lot_split_list > tbody tr').each(function(bidx, brow)



	{



		var split_row = $(this);



		split_row.find('.split_pcs').val('');



		split_row.find('.split_wt').val('');



		split_row.find('.split_nwt').val('');



		split_row.find('.select_employee ').select2("val","");



		split_row.find('.split_stn_pcs').val(0);



		split_row.find('.split_stn_wt').val(0);



		split_row.find('.split_dia_pcs').val(0);



		split_row.find('.split_dia_wt').val(0);



	});



}



function check_bal_details(curRow)



{



	var id_lot_inward = curRow.find('.id_lot_inward_detail').val();



	var t_len = $('#lt_split_preview tbody tr').length;



	var tot_pcs     = 0;



	var tot_grswt   = 0;



	var tot_stn_pcs = 0;



	var tot_stn_wt  = 0;



	var tot_dia_pcs = 0;



	var tot_dia_wt  = 0;



	var bal_pcs = 0;



	var bal_grswt = 0;



	var bal_stnpcs = 0;



	var bal_stnwt = 0;



	var bal_diapcs = 0;



	var bal_diawt = 0;



	var row_exists = false;



	$("#lt_split_preview  > tbody tr").each(function ()



	{



		var split_row = $(this);







		if(split_row.find('.id_lot_inward_detail').val() == id_lot_inward)



		{



			row_exists=true;



			/* Total pcs,grswt,stnpcs,stnwt,diapcs,diawt in preview table for lot_id*/



			tot_pcs      =   tot_pcs     + (parseInt(split_row.find('.split_pcs').val()));



			tot_grswt    =   tot_grswt   + (parseFloat(split_row.find('.split_wt').val()));



			tot_stn_pcs  =   tot_stn_pcs + (parseInt(split_row.find('.split_stn_pcs').val()));



			tot_stn_wt   =   tot_stn_wt  + (parseFloat(split_row.find('.split_stn_wt').val()));



			tot_dia_pcs  =   tot_dia_pcs + (parseInt(split_row.find('.split_dia_pcs').val()));



			tot_dia_wt   =   tot_dia_wt  + (parseFloat(split_row.find('.split_dia_wt').val()));







 			/* Balance pcs,grswt,stnpcs,stnwt.diapcs,diawt available*/



			bal_pcs     = parseInt(curRow.find('.lot_pcs').val() - tot_pcs);



			bal_grswt   = parseFloat(curRow.find('.lot_wt').val() - tot_grswt).toFixed(3);



			bal_stnpcs  = parseInt(curRow.find('.tot_stn_pcs').val() - tot_stn_pcs);



			bal_stnwt   = parseFloat(curRow.find('.tot_stn_wt').val() - tot_stn_wt).toFixed(3);



			bal_diapcs  = parseInt(curRow.find('.tot_dia_pcs').val() - tot_dia_pcs);



			bal_diawt   = parseFloat(curRow.find('.tot_dia_wt').val() - tot_dia_wt).toFixed(3);







		}



	});



	if(t_len > 0 && row_exists)



	{



		// condition to check whether entered pcs is greater than bal pcs



		if(curRow.find('.split_pcs').val() > bal_pcs)



		{



			curRow.find('.split_pcs').val('');



			curRow.find('.split_pcs').focus();



			$.toaster({ priority : 'danger', title : 'Warning!', settings : {'timeout': 4000}, message : "Balance Pcs is: "+bal_pcs+"..."});



		}



		// condition to check whether entered grswt is greater than bal grswt



		if(parseFloat(curRow.find('.split_wt').val()) > bal_grswt)



		{



			curRow.find('.split_wt').val('');



			curRow.find('.split_nwt').val('');



			curRow.find('.split_wt').focus();



			$.toaster({ priority : 'danger', title : 'Warning!', settings : {'timeout': 4000}, message : "Balance GrsWt is: "+bal_grswt+"..."});



		}



		// condition to check whether entered stn pcs is greater than bal stn pcs



		if(curRow.find('.split_stn_pcs').val() > bal_stnpcs)



		{



			curRow.find('.split_stn_pcs').val(0);



			curRow.find('.split_stn_pcs').focus();



			$.toaster({ priority : 'danger', title : 'Warning!', settings : {'timeout': 4000}, message : "Balance Stn Pcs is: "+bal_stnpcs+"..."});



		}



		// condition to check whether entered stn wgt is greater than bal stn wgt



		if(parseFloat(curRow.find('.split_stn_wt').val()) > bal_stnwt)



		{



			curRow.find('.split_stn_wt').val(0);



			curRow.find('.split_stn_wt').focus();



			$.toaster({ priority : 'danger', title : 'Warning!', settings : {'timeout': 4000}, message : "Balance Stn Wgt is: "+bal_stnwt+"..."});



		}



		// condition to check whether entered Dia pcs is greater than bal Dia pcs



		if(curRow.find('.split_dia_pcs').val() > bal_diapcs)



		{



			curRow.find('.split_dia_pcs').val(0);



			curRow.find('.split_dia_pcs').focus();



			$.toaster({ priority : 'danger', title : 'Warning!', settings : {'timeout': 4000}, message : "Balance Dia Pcs is: "+bal_diapcs+"..."});



		}



		// condition to check whether entered Dia wgt is greater than bal Dia wgt



		if(parseFloat(curRow.find('.split_dia_wt').val()) > bal_diawt)



		{



			curRow.find('.split_dia_wt').val(0);



			curRow.find('.split_dia_wt').focus();



			$.toaster({ priority : 'danger', title : 'Warning!', settings : {'timeout': 4000}, message : "Balance Dia Wgt is: "+bal_diawt+"..."});



		}



		curRow.find('.bal_lot_pcs').html(bal_pcs);



		curRow.find('.bal_lot_wt').html(bal_grswt);



		curRow.find('.bal_lot_stnpcs').html(bal_stnpcs);



		curRow.find('.bal_lot_stnWt').html(bal_stnwt);



		curRow.find('.bal_lot_diapcs').html(bal_diapcs);



		curRow.find('.bal_lot_diaWt').html(bal_diawt);



	}







}



$('#lot_split_submit').on('click',function()



{



	$('#lot_split_form').submit();



});



/*Lot Split Module*/



/*Branchwise Counter*/



function get_Branchwise_Sections()



{



    my_Date = new Date();



    $.ajax({



        type: 'POST',



        url: base_url+"index.php/admin_ret_catalog/get_sectionBranchwise?nocache=" + my_Date.getUTCSeconds(),



		data:{'id_branch':''},



        dataType:'json',



        success:function(data){



			section_details =data;



			lot_receive_branch = $('#id_branch').val();



			$.each(section_details, function (key, item){



		        if(lot_receive_branch==item.id_branch)



		        {



		            $('#select_section').append(



        		 	 $("<option></option>")



        		 	 .attr("value",item.id_section)



        		 	 .text(item.section_name)



        		 	 );



		        }





    		});



			$("#select_section").select2(



			{



					placeholder: "Select Section",



					allowClear: true



			});



			$("#select_section").select2('val','');



        }



    })



}



/*Branchwise Counter*/



function get_ActiveMetals() {



	$.ajax({



		type: 'GET',



		url: base_url + 'index.php/admin_ret_catalog/ret_product/active_metal',



		dataType: 'json',



		success: function (data) {



			var id = $("#metal").val();



			$("#metal").append(



				$("<option></option>")



					.attr("value", 0)



					.text('All')



			);



			$.each(data, function (key, item) {



				$('#metal').append(



					$("<option></option>")



						.attr("value", item.id_metal)



						.text(item.metal)



				);



			});



			$("#metal").select2(



				{



					placeholder: "Select Metal",



					allowClear: true



				});



		}



	});



}



$('#lot_closed').on('click',function()



{



	var LotCompletedList = [];



	$("#lot_inward_list tbody tr").each(function (index,value)



	{



		var row=$(this).closest('tr');



		if(row.find("input[name='lot_no[]']:checked").is(":checked"))



		{



			LotCompletedList.push({"lot_no" : row.find('.lot_no').val()});



		}



	});



	console.log(LotCompletedList);



	if(LotCompletedList.length>0)



	{



		Lot_Completed_Details(LotCompletedList);



	}



	else



	{



		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please select Any Id to proceed..'});



	}



});



function Lot_Completed_Details(lot_completed_data)



{



	$(".overlay").css("display", "block");



	var postData = {'completed_lot':lot_completed_data,"branch":1};



	$.ajax({



		type:'POST',



        url : base_url + 'index.php/admin_ret_lot/lot_completed',



		dataType : 'json',



		data : postData,



		success : function(data)



        {



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











function get_ActiveGRNS(){







	$.ajax({



	type: 'POST',



	url: base_url+'index.php/admin_ret_purchase/purchase/active_grns',



	dataType:'json',



	data:{'po_id':''},



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





function set_edit_lot_row()



{





	$(".overlay").css('display','block');



	let lot_no = $("#lot_no").val();



   	if(lot_no > 0)



    {



		my_Date = new Date();



		$.ajax({



		type: 'POST',



		url: base_url+'index.php/admin_ret_lot/lot_inward/lot_edit/'+lot_no+'?nocache=' + my_Date.getUTCSeconds(),



		dataType:'json',



		success:function(data)



		{

			console.log(data);



			var stock_type = $("input[name='inward[stock_type]']:checked").val();



			console.log(data.inward_details);







			$.each(data.inward_details,function(i,item){



				var a = $("#curRow").val();



				var i = ++a;



				$("#curRow").val(i);



                var select_section= "";



				if(stock_type == 2)

				 {

                $.each(section_details, function (key, sec_item) {



                    var selected="";



                    if(item.id_section!=null && sec_item.id_section == item.id_section)



                    {



						selected = "selected";



					}



                     select_section+= "<option value='" + sec_item.id_section + "' "+selected+">" + sec_item.section_name + "</option>";



                });



			     }

				var select_purity= "";



				// $.each(purities, function (key, pitem) {



				// 	var selected="";



				// 	if(item.id_purity == pitem.id_purity){



				// 		selected = "selected";



				// 	}

				// 	select_purity+= "<option value='" + pitem.id_purity + "' "+selected+">" + pitem.purity + "</option>";





				// });



				// $('#purity').html(select_purity);



				// $('#purity').trigger('change');



				var select_product= "";



				console.log(lot_product_details);



				$.each(lot_product_details, function (key, pitem) {



					var selected="";



					if(item.lot_product!=null && item.lot_product == pitem.pro_id){



						selected = "selected";



					}





					select_product+= "<option value='" + pitem.pro_id + "' "+selected+">" + pitem.product_name + "</option>";



				});



				var select_design= "";

				lot_designs = data.design;

				// if(stock_type == 2)

				//  {

				  $.each(data.design, function (key, pitem) {



					var selected="";



					if(item.lot_id_design!=null && item.lot_id_design == pitem.design_no){



						selected = "selected";



					}

					if(item.lot_id_design == 0  ){

						select_design+= "<option value='0' "+selected+">ALL</option>";

					}

					select_design+= "<option value='" + pitem.design_no + "' "+selected+">" + pitem.design_name + "</option>";



				});

			    //  }



				var select_sub_design= "";



				lot_sub_designs = data.sub_design;



				// if(stock_type == 2)

				// {



				if(item.id_sub_design == 0  ){

					select_sub_design+= "<option value='0' selected >ALL</option>";

				}

				$.each(data.sub_design, function (key, pitem) {



					var selected="";



					if(item.id_sub_design!=null && item.id_sub_design == pitem.id_sub_design){



						selected = "selected";

					}





					select_sub_design+= "<option value='" + pitem.id_sub_design + "' "+selected+">" + pitem.sub_design_name + "</option>";



				});

			    // }



				var gwt_uom= "";



				console.log(uom_details);



				$.each(uom_details, function (key, pitem) {



					var selected="";



					if(item.gross_wt_uom == pitem.uom_id){





						selected = "selected";



					}

					gwt_uom+= "<option value='" + pitem.uom_id + "' "+selected+">" + pitem.code + "</option>";



				});

				var lwt_uom= "";



				$.each(uom_details, function (key, pitem) {



					var selected="";



					if(item.less_wt_uom == pitem.uom_id){





						selected = "selected";



					}

					lwt_uom+= "<option value='" + pitem.uom_id + "' "+selected+">" + pitem.code + "</option>";



				});



				var nwt_uom= "";



				$.each(uom_details, function (key, pitem) {



					var selected="";



					if(item.net_wt_uom == pitem.uom_id){





						selected = "selected";



					}

					nwt_uom+= "<option value='" + pitem.uom_id + "' "+selected+">" + pitem.code + "</option>";



				});





				row = '<tr id='+i+'>'



				    +'<td><select class="lot_section" name="inward_item['+i+'][id_section]"  value="" placeholder="Search Section" style="width:150px;" '+(stock_type == 1? 'disabled' : '')+' >'+select_section+'</select><input type="hidden" class="section_id" value='+item.id_section+'></td>'



			    	+'<td><select class="lot_product" disabled name="inward_item['+i+'][product]" value="" placeholder="Search Product" style="width:150px;">'+select_product+'</select><input type="hidden" class="pro_id" name="inward_item['+i+'][lot_product]" value='+item.lot_product+' /><input type="hidden" class="sales_mode" name="inward_item['+i+'][sales_mode]" value='+item.sales_mode+' /><input type="hidden" class="calculation_based_on" name="inward_item['+i+'][calculation_based_on]" value=""/><input type="hidden" class="id_lot_inward_detail" id="id_lot_inward_detail" value='+item.id_lot_inward_detail+'></td>'



					+'<td><select class="design" disabled name="inward_item['+i+'][design]"'+(stock_type == 1? 'disabled' : '')+' value="" placeholder="Search Design" style="width:150px;">'+select_design+'</select><input type="hidden" class="des_id" name="inward_item['+i+'][lot_id_design]"  value='+item.lot_id_design+'></td>'



					+'<td><select class="lot_sub_design" disabled name="inward_item[sub_design][]" '+(stock_type == 1? 'disabled' : '')+' value="" placeholder="Search SubDesign" required style="width:150px;">'+select_sub_design+'</select><input type="hidden" class="lot_id_sub_design" name="inward_item['+i+'][id_sub_design]" value='+item.id_sub_design+'></td>'



					+'<td><input type="number" step="any" readonly value='+item.no_of_piece+' name="inward_item['+i+'][pcs]" class="lot_pcs" style="width:60px;"></td>'



					+'<td><div class="input-group"><input type="number" readonly step="any" value='+item.gross_wt+' name="inward_item['+i+'][gross_wt]" class="gross_wt" style="width:80px;" autocomplete="off"><span class="input-group-addon input-sm no-padding" style="background-color: transparent;height: 12px;font-size: 16px;border: none;"><select class="gross_wt_uom" name="inward_item['+i+'][gross_wt_uom]">'+gwt_uom+'</select></span></div></td>'



					+'<td><div class="input-group"><input type="number" readonly step="any"  value='+item.less_wt+' name="inward_item['+i+'][less_wt]" class="lot_lwt" style="width:80px;" autocomplete="off"><span class="input-group-addon input-sm no-padding" style="background-color: transparent;height: 12px;font-size: 16px;border: none;"><select class="less_wt_uom" name="inward_item['+i+'][less_wt_uom]">'+lwt_uom+'</select></span></div></td>'



					+'<td><div class="input-group"><input type="number" readonly step="any" value='+item.net_wt+' name="inward_item['+i+'][net_wt]" class="lot_nwt" style="width:80px;" autocomplete="off"><span class="input-group-addon input-sm no-padding" style="background-color: transparent;height: 12px;font-size: 16px;border: none;"><select class="net_wt_uom" name="inward_item['+i+'][net_wt_uom]">'+nwt_uom+'</select></span></div></td>'



					+'<td><input type="number" step="any" readonly  value='+item.wastage_percentage+'  class="wastage_percentage" name="inward_item['+i+'][wastage_percentage]" style="width:60px;"></td>'



					+'<td><div class="input-group"><input readonly type="number" step="any" class="making_charge"  value='+item.making_charge+' name="inward_item['+i+'][making_charge]" style="width:70px;"><span class="input-group-addon input-sm no-padding" style="background-color: transparent;height: 12px;border: none;"><select class="form-control mc_type" style="width:80px;height: 29px;" name="inward_item['+i+'][id_mc_type]" autocomplete="off"><option value="1" '+(item.mc_type==1 ? 'selected' : '')+'>Gram</option><option value="2" '+(item.mc_type==2 ? 'selected' : '')+'>Piece</option></select></span></div><input type="hidden" value='+item.mc_type+' class="id_mc_type"></td>'



					+'<td><input type="number" step="any" readonly class="buy_rate" name="inward_item['+i+'][buy_rate]" value='+item.buy_rate+' style="width:80px;" autocomplete="off"><span class="buy_rt_type"></span></td>'



					+'<td><input type="number" step="any" readonly class="sell_rate" name="inward_item['+i+'][sell_rate]" value='+item.sell_rate+' style="width:80px;" autocomplete="off"><span class="sell_rt_type"></span></td>'



					+'<td><input type="number" step="any" readonly class="purchase_touch"  value="'+item.purchase_touch+'" style="width:80px;" autocomplete="off"></td>'



					+'<td><input type="hidden" name="inward_item['+i+'][calc_type]" class="calc_type" value="'+item.calc_type+'"><input type="hidden" name="inward_item['+i+'][purchase_touch]" class="purchase_touch" value="'+item.purchase_touch+'"><input type="hidden" name="inward_item['+i+'][rate]" class="rate_per_gram" value="'+item.rate+'"><input type="hidden" name="inward_item['+i+'][rate_calc_type]" class="rate_calc_type" value="'+item.rate_calc_type+'"><input type="number" step="any" readonly class="pur_rate"  value="'+item.rate+'" style="width:80px;" autocomplete="off"><input type ="hidden" class="lot_details" value=\''+JSON.stringify(item)+'\'> <input type="hidden" class="lot_stones_details" name="inward_item['+i+'][stones_details]"  value=\''+(item.stone_details)+'\'  ></td>'



					+'<td><a href="#" onClick="show_stone_details($(this).closest(\'tr\'),'+i+');" class="btn btn-default btn-sm"><i class="fa fa-plus-circle"></i></a></td>'



					+'<td><input type="number" step="any" readonly class="total_taxable"  value="'+item.item_total_taxable+'" style="width:80px;" autocomplete="off"></td>'



					+'<td><input type="number" step="any" readonly class="total_tax"  value="'+item.item_total_tax+'" style="width:80px;" autocomplete="off"></td>'



					+'<td><input name="inward_item['+i+'][item_cost]" type ="number" readonly class="cost item_cost" value="'+item.item_cost+'"></input><input name="inward_item['+i+'][item_cgst_cost]" type ="hidden" class="item_cgst_cost" value="'+item.item_cgst_cost+'"></input><input name="inward_item['+i+'][item_sgst_cost]" type ="hidden" class="item_sgst_cost" value="'+item.item_sgst_cost+'"></input><input name="inward_item['+i+'][item_igst_cost]" type ="hidden" class="item_igst_cost" value="'+item.item_igst_cost+'"></input><input name="inward_item['+i+'][item_tax_percentage]" type ="hidden" class="item_tax_percentage" value="'+item.item_tax_percentage+'"></input><input name="inward_item['+i+'][tax_group_id]" type ="hidden" class="tax_group_id" value="'+item.tax_group_id+'"></input>'



					+'<input name="inward_item['+i+'][tax_type]" type ="hidden" class="tax_type" value="'+item.tax_type+'"><input name="inward_item['+i+'][item_total_tax]" type ="hidden" class="item_total_tax" value="'+item.item_total_tax+'"></input><input name="inward_item['+i+'][other_metal_wt]" type ="hidden" class="other_metal_wt" value="'+item.other_metal_wt+'"><input name="inward_item['+i+'][stone_price]" type ="hidden" class="stone_price" value="'+item.stone_price+'"><input name="inward_item['+i+'][other_metal_details]" type ="hidden" class="other_metal_details" value=\''+item.other_metal_details+'\'><input name="inward_item['+i+'][other_charges_details]" type ="hidden" class="other_charges_details" value=\''+item.other_charges_details+'\'> </td>'



					+'<td><input name="inward_item['+i+'][id_lot_inward_detail]" type ="hidden" class="lot_inward_details_id" value="'+item.id_lot_inward_detail+'"><a href="#" onClick="edit_lot_details($(this).closest(\'tr\'));" class="btn btn-primary"><i class="fa fa-edit"></i></a><a href="#" onClick="remove_row($(this).closest(\'tr\'));" class="btn btn-danger"><i class="fa fa-trash"></i></a></td>'



					+'</tr>';



		           $('#lt_item_list tbody').append(row);



				  $('#lt_item_list > tbody').find('.lot_section,.lot_product,.design,.lot_sub_design').select2();



				  $('#lt_item_list > tbody').find('tr:last td:eq(0) .cat_product').focus();



				  $('#lt_item_list > tbody').find('.lot_product').select2({



					placeholder: "Product",



					allowClear: true



				});



				$('#lt_item_list > tbody').find('.design').select2({



					placeholder: "Design",



					allowClear: true



				});







				$('#lt_item_list > tbody').find('.lot_sub_design').select2({



					placeholder: "Sub Design",



					allowClear: true



				});



				$('#lt_item_list > tbody').find('tr:last td:eq(0) .lot_product').focus();







				$('#lt_item_list > tbody').find('.lot_section').select2({



					placeholder: "Section",



					allowClear: true



				});



			});





			calculate_lot_inward_Total();





		}







		});



   	}



  	$(".overlay").css('display','none');

}











$(document).on('keyup',	'#lot_gross_wt, #lot_lwt', function(e){



	//var row = $(this).closest('tr');



	var gross_wt = (isNaN($('#lot_gross_wt').val()) || $('#lot_gross_wt').val()=='' ?0 :$('#lot_gross_wt').val());



	var lot_lwt	 = (isNaN($('#lot_lwt').val()) ||  $('#lot_lwt').val()=='' ?0 :$('#lot_lwt').val());



	var net_wt   =  parseFloat(parseFloat(gross_wt)-parseFloat(lot_lwt)).toFixed(3);



	if(net_wt >= 0){

		$('#lot_net_wt').val(net_wt);

	}else{

		this.value =0;

        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br> Invalid Net Wt"});



	}







});





$('#add_lot_items').on('click',function(){



	if($("#id_category").val() != "" && $("#id_purity").val() != "" && $("#lt_gold_smith_id").val() != ""){

	if(validate_lot_details()){

		add_lot_details();

	}

	}else{



		alert("Please fill required fields");



	}



});





function add_lot_details(){







	var lot_details={



		'id_section' : $('#select_section').val()==null ?'':$('#select_section').val(),

		'lot_product' : $('#select_product').val(),

		'lot_id_design' : $('#select_design').val(),

		'id_sub_design' : $('#select_sub_design').val(),

		'design_for' : $('#sel_desingn_for').val(),

		'lot_pcs' : $('#lot_pcs').val(),

		'gross_wt' : $('#lot_gross_wt').val(),

		'gross_wt_uom' : $('#lot_gross_uom').val(),

		'less_wt' : $('#lot_lwt').val(),

		'less_wt_uom' : $('#lot_lwt_uom').val(),

		'net_wt' : $('#lot_net_wt').val(),

		'net_wt_uom' : $('#lot_net_uom').val(),

		'wastage_percentage' : $('#lot_wastage').val(),

		'mc_type' : $('#mc_type').val(),

		'making_charge' : $('#mc_value').val(),

		'buy_rate' : $('#buy_rate').val(),

		'sales_mode' : $('#sales_mode').val(),

		'sell_rate' : $('#sell_rate').val(),

		'calculation_based_on' : $('#calculation_based_on').val(),

		'purchase_touch' : $('#purchase_touch').val(),

		'calc_type' : $('#karigar_calc_type').val(),

		'rate' : $('#rate_per_gram').val(),

		'rate_calc_type' : $('#rate_calc_type').val(),

		'section_name' : $('#select_section option:selected').text(),

		'product_name' : $('#select_product option:selected').text(),

		'design_name' : $('#select_design option:selected').text(),

		'sub_design_name' : $('#select_sub_design option:selected').text(),

		'stone_details' : $('#lot_stone_details').val() != ''?$('#lot_stone_details').val():JSON.stringify([]),

		'stone_price': $('#stone_price').val(),

		'other_metal_details': $('#other_metal_details').val() != ''?$('#other_metal_details').val():JSON.stringify([]),

		'other_metal_wt': $('#other_metal_wt').val(),

		'other_metal_wast_wt': $('#other_metal_wast_wt').val(),

		'other_metal_mc_amount': $('#other_metal_mc_amount').val(),

		'other_charges_details': $('#other_charges_details').val() != ''?$('#other_charges_details').val():JSON.stringify([]),

		'id_lot_inward_detail': $('#lot_inward_details_id').val(),

		'pur_wt':$('#tot_purewt').val(),

		'tax_group_id':$('#tax_group_id').val(),

		'item_tax_percentage':$('#item_tax_percentage').val(),

		'item_igst_cost':$('#item_igst_cost').val(),

		'item_sgst_cost':$('#item_sgst_cost').val(),

		'item_cgst_cost':$('#item_cgst_cost').val(),

		'item_cost':$('#item_cost').val(),

		'item_total_tax':$('#item_total_tax').val(),

		'item_total_taxable':$('#item_total_taxable').val(),

		'tax_type':$('#tax_type').val(),





	}





console.log(lot_details);

	var item =lot_details;



	selected = "selected";



	var stock_type = $("input[name='inward[stock_type]']:checked").val();



//	console.log(data.inward_details);







		var a = $("#curRow").val();



		var i = ++a;



		$("#curRow").val(i);



		var select_section= "";



		if(stock_type == 2)

			{



				select_section+= "<option value='" + item.id_section + "' "+selected+">" + item.section_name + "</option>";

			}





		var select_product= "";



		select_product+= "<option value='" + item.lot_product + "' selected >" + item.product_name + "</option>";



		console.log(lot_product_details);



		var select_design= "";



		select_design+= "<option value='" + item.lot_id_design + "' "+selected+">" + item.design_name + "</option>";





		var select_sub_design= "";



		select_sub_design+= "<option value='" + item.id_sub_design + "' "+selected+">" + item.sub_design_name + "</option>";



		var gwt_uom= "";



		console.log(uom_details);



		$.each(uom_details, function (key, pitem) {



			var selected="";



			if(item.gross_wt_uom == pitem.uom_id){





				selected = "selected";



			}

			gwt_uom+= "<option value='" + pitem.uom_id + "' "+selected+">" + pitem.code + "</option>";



		});

		var lwt_uom= "";



		$.each(uom_details, function (key, pitem) {



			var selected="";



			if(item.less_wt_uom == pitem.uom_id){





				selected = "selected";



			}

			lwt_uom+= "<option value='" + pitem.uom_id + "' "+selected+">" + pitem.code + "</option>";



		});



		var nwt_uom= "";



		$.each(uom_details, function (key, pitem) {



			var selected="";



			if(item.net_wt_uom == pitem.uom_id){





				selected = "selected";



			}

			nwt_uom+= "<option value='" + pitem.uom_id + "' "+selected+">" + pitem.code + "</option>";



		});





		row = '<tr id='+i+'>'



			+'<td><select disabled class="lot_section" value="" placeholder="Search Section" style="width:150px;" '+(stock_type == 1? 'disabled' : '')+' >'+select_section+'</select><input type="hidden"  name="inward_item['+i+'][id_section]"  class="section_id" value="'+item.id_section+'"></td>'



			+'<td><select disabled class="lot_product" name="inward_item['+i+'][product]" value="" placeholder="Search Product" style="width:150px;">'+select_product+'</select><input type="hidden" class="pro_id" name="inward_item['+i+'][lot_product]" value='+item.lot_product+' /><input type="hidden" class="sales_mode" name="inward_item['+i+'][sales_mode]" value="'+item.sales_mode+'" /><input type="hidden" class="calculation_based_on" name="inward_item['+i+'][calculation_based_on]" value="'+item.calculation_based_on+'" /><input type="hidden" class="id_lot_inward_detail" id="id_lot_inward_detail" value="'+item.id_lot_inward_detail+'"></td>'



			+'<td><select disabled class="design" name="inward_item['+i+'][design]"'+(stock_type == 1? 'disabled' : '')+' value="" placeholder="Search Design" style="width:150px;">'+select_design+'</select><input type="hidden" class="des_id" name="inward_item['+i+'][lot_id_design]"  value="'+item.lot_id_design+'"></td>'



			+'<td><select disabled class="lot_sub_design" name="inward_item[sub_design][]" '+(stock_type == 1? 'disabled' : '')+' value="" placeholder="Search SubDesign" required style="width:150px;">'+select_sub_design+'</select><input type="hidden" class="lot_id_sub_design" name="inward_item['+i+'][id_sub_design]" value="'+item.id_sub_design+'"></td>'



			+'<td><input readonly type="number" step="any" value="'+item.lot_pcs+'" name="inward_item['+i+'][pcs]" class="lot_pcs" style="width:60px;"></td>'



			+'<td><div class="input-group"><input type="number"  readonly step="any" value="'+item.gross_wt+'" name="inward_item['+i+'][gross_wt]" class="gross_wt" style="width:80px;" autocomplete="off"><span class="input-group-addon input-sm no-padding" style="background-color: transparent;height: 12px;font-size: 16px;border: none;"><select class="gross_wt_uom" name="inward_item['+i+'][gross_wt_uom]">'+gwt_uom+'</select></span></div></td>'



			+'<td><div class="input-group"><input type="number" readonly step="any"  value="'+item.less_wt+'" name="inward_item['+i+'][less_wt]" class="lot_lwt" style="width:80px;" autocomplete="off"><span class="input-group-addon input-sm no-padding" style="background-color: transparent;height: 12px;font-size: 16px;border: none;"><select class="less_wt_uom" readonly name="inward_item['+i+'][less_wt_uom]">'+lwt_uom+'</select></span></div></td>'



			+'<td><div class="input-group"><input type="number" readonly step="any" value="'+item.net_wt+'" name="inward_item['+i+'][net_wt]" class="lot_nwt" style="width:80px;" autocomplete="off"><span class="input-group-addon input-sm no-padding" style="background-color: transparent;height: 12px;font-size: 16px;border: none;"><select class="net_wt_uom"  readonly name="inward_item['+i+'][net_wt_uom]">'+nwt_uom+'</select></span></div></td>'



			+'<td><input type="number" step="any" readonly value="'+item.wastage_percentage+'"  class="wastage_percentage" name="inward_item['+i+'][wastage_percentage]" style="width:60px;"></td>'



			+'<td><div class="input-group"><input readonly type="number" step="any" class="making_charge"  value="'+item.making_charge+'" name="inward_item['+i+'][making_charge]" style="width:70px;"><span class="input-group-addon input-sm no-padding" style="background-color: transparent;height: 12px;border: none;"><select  readonly class="form-control mc_type" style="width:80px;height: 29px;" name="inward_item['+i+'][id_mc_type]" autocomplete="off"><option value="1" '+(item.mc_type==1 ? 'selected' : '')+'>Gram</option><option value="2" '+(item.mc_type==2 ? 'selected' : '')+'>Piece</option></select></span></div><input type="hidden" value="'+item.mc_type+'" class="id_mc_type"></td>'



			+'<td><input type="number" readonly step="any" class="buy_rate" name="inward_item['+i+'][buy_rate]" value="'+item.buy_rate+'" style="width:80px;" autocomplete="off"><span class="buy_rt_type"></span></td>'



			+'<td><input type="number" step="any" readonly class="sell_rate" name="inward_item['+i+'][sell_rate]" value="'+item.sell_rate+'" style="width:80px;" autocomplete="off"><span class="sell_rt_type"></span></td>'



			+'<td><input type="number" step="any" readonly class="sell_rate"  value="'+item.purchase_touch+'" style="width:80px;" autocomplete="off"></td>'



			+'<td><input type="hidden" name="inward_item['+i+'][calc_type]" class="calc_type" value="'+item.calc_type+'"><input type="hidden" name="inward_item['+i+'][purchase_touch]" class="purchase_touch" value="'+item.purchase_touch+'"><input type="hidden" name="inward_item['+i+'][rate]" class="rate_per_gram" value="'+item.rate+'"><input type="hidden" name="inward_item['+i+'][rate_calc_type]" class="rate_calc_type" value="'+item.rate_calc_type+'"><input type="number" step="any" readonly class="sell_rate"  value="'+item.rate+'" style="width:80px;" autocomplete="off"></td>'



			+'<td><a href="#" onClick="show_stone_details($(this).closest(\'tr\'),'+i+');" class="btn btn-default btn-sm"><i class="fa fa-plus-circle"></i></a></td>'



			+'<td><input type="number" step="any" readonly class="total_taxable"  value="'+item.item_total_taxable+'" style="width:80px;" autocomplete="off"></td>'



			+'<td><input type="number" step="any" readonly class="total_tax"  value="'+item.item_total_tax+'" style="width:80px;" autocomplete="off"></td>'



			+'<td><input type="number" step="any" readonly class="cost"  value="'+item.item_cost+'" style="width:80px;" autocomplete="off"></td>'



			+'<td><input name="inward_item['+i+'][item_cost]" type ="hidden" class="item_cost" value="'+item.item_cost+'"></input><input name="inward_item['+i+'][item_cgst_cost]" type ="hidden" class="item_cgst_cost" value="'+item.item_cgst_cost+'"></input><input name="inward_item['+i+'][item_sgst_cost]" type ="hidden" class="item_sgst_cost" value="'+item.item_sgst_cost+'"></input><input name="inward_item['+i+'][item_igst_cost]" type ="hidden" class="item_igst_cost" value="'+item.item_igst_cost+'"></input><input name="inward_item['+i+'][item_tax_percentage]" type ="hidden" class="item_tax_percentage" value="'+item.item_tax_percentage+'"></input><input name="inward_item['+i+'][tax_group_id]" type ="hidden" class="tax_group_id" value="'+item.tax_group_id+'"></input>'

			+'<input name="inward_item['+i+'][tax_type]" type ="hidden" class="tax_type" value="'+item.tax_type+'"><input name="inward_item['+i+'][item_total_tax]" type ="hidden" class="item_total_tax" value="'+item.item_total_tax+'"></input><input name="inward_item['+i+'][other_metal_wt]" type ="hidden" class="other_metal_wt" value="'+item.other_metal_wt+'"><input name="inward_item['+i+'][stone_price]" type ="hidden" class="stone_price" value="'+item.stone_price+'"><input name="inward_item['+i+'][other_charges_details]" type ="hidden" class="other_charges_details" value=\''+item.other_charges_details+'\'>'

			+'<input name="inward_item['+i+'][other_metal_details]" type ="hidden" class="other_metal_details" value=\''+item.other_metal_details+'\'><input name="inward_item['+i+'][other_metal_mc_amount]" type ="hidden" class="other_metal_mc_amount" value="'+item.other_metal_mc_amount+'"><input name="inward_item['+i+'][other_metal_wast_wt]" type ="hidden" class="other_metal_wast_wt" value="'+item.other_metal_wast_wt+'"><input name="inward_item['+i+'][pur_wt]" type ="hidden" class="pur_wt" value="'+item.pur_wt+'"><input name="inward_item['+i+'][id_lot_inward_detail]" type ="hidden" class="lot_inward_details_id" value="'+item.id_lot_inward_detail+'"><input type ="hidden" class="lot_details" value=\''+JSON.stringify(item)+'\'><a href="#" onClick="edit_lot_details($(this).closest(\'tr\'));" class="btn btn-primary"><i class="fa fa-edit"></i></a><a href="#" onClick="remove_row($(this).closest(\'tr\'));" class="btn btn-danger"><i class="fa fa-trash"></i></a> <input type="hidden" class="lot_stones_details" name="inward_item['+i+'][stones_details]"  value=\''+item.stone_details+'\'  >'

			+'</td>'



			+'</tr>';



			$('#lt_item_list tbody').append(row);



			$('#lt_item_list > tbody').find('.lot_section,.lot_product,.design,.lot_sub_design').select2();



			$('#lt_item_list > tbody').find('tr:last td:eq(0) .cat_product').focus();



			$('#lt_item_list > tbody').find('.lot_product').select2({



			placeholder: "Product",



			allowClear: true



		});



		$('#lt_item_list > tbody').find('.design').select2({



			placeholder: "Design",



			allowClear: true



		});







		$('#lt_item_list > tbody').find('.lot_sub_design').select2({



			placeholder: "Sub Design",



			allowClear: true



		});



		$('#lt_item_list > tbody').find('tr:last td:eq(0) .lot_product').focus();







		$('#lt_item_list > tbody').find('.lot_section').select2({



			placeholder: "Section",



			allowClear: true



		});









		reset_data();

		 calculate_lot_inward_Total();





}



function validate_lot_details(){



	var stock_type = $("input[name='inward[stock_type]']:checked").val();



	product_id = $('#select_product').val();



	var sales_mode = lot_product_details.filter(product => product.pro_id == product_id).map(product => product.sales_mode)[0];



	var calculation_based_on = lot_product_details.filter(product => product.pro_id == product_id).map(product => product.calculation_based_on)[0];



	var is_purchase_cost_from_lot =$('#is_purchase_cost_from_lot').val();



	if(sales_mode == 1){



		if(calculation_based_on == 3){

			if ($('#select_product').val() == null){



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Product"});



				$('#select_product').focus();



				return false;



			}else if( $.trim($('#lot_pcs').val()) == ''){



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Enter Lot Pcs "});



				$('#lot_pcs').focus();



				return false;



			}

		}

		else if(calculation_based_on == 4){



			if ($('#select_product').val() == null){



				$('#select_product').focus();



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Product"});



				return false;



			}else if($.trim($('#lot_pcs').val()) == ''){



				$('#lot_pcs').focus();



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Enter Lot Pcs "});



				return false;



			}else if($.trim($('#lot_gross_wt').val()) == ''){



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Enter Gross wt "});



				$('#lot_gross_wt').focus();



				return false;



			}

		}



	}else if(sales_mode == 2){



		if ($('#select_product').val() == null){



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Product"});



			$('#select_product').focus();



			return false;



		}else if($.trim($('#lot_pcs').val()) == ''){



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Enter Lot Pcs "});



			$('#lot_pcs').focus();



			return false;



		}else if($.trim($('#lot_gross_wt').val()) == ''){



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Enter Gross wt "});



			$('#lot_gross_wt').focus();



			return false;



		}else if($.trim($('#lot_net_wt').val()) == ''){



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Enter Net wt "});



			$('#lot_net_wt').focus();



			return false;



		}else if($.trim($('#lot_wastage').val()) == ''){



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Enter Wastage "});



			$('#lot_wastage').focus();



			return false;



		}

	}else{



		if ($('#select_product').val() == null){



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Product"});



			$('#select_product').focus();



			return false;



		}else if($.trim($('#lot_pcs').val()) == ''){



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Enter Lot Pcs "});



			$('#lot_pcs').focus();



			return false;



		}else if($.trim($('#lot_gross_wt').val()) == ''){



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Enter Gross wt "});



			$('#lot_gross_wt').focus();



			return false;



		}else if($.trim($('#lot_net_wt').val()) == ''){



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Enter Net wt "});



			$('#lot_net_wt').focus();



			return false;



		}else if($.trim($('#lot_wastage').val()) == ''){



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Enter Wastage "});



			$('#lot_wastage').focus();



			return false;



		}



	}



	if(stock_type==2){



		if ($('#select_product').val() == null){



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Product"});



			$('#select_product').focus();



			return false;



		}else if ($('#select_design').val() == null){



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Design"});



			$('#select_design').focus();



			return false;



		}else if ($('#select_sub_design').val() == null){



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Sub Design"});



			$('#select_sub_design').focus();



			return false;



		}

		else if($.trim($('#lot_pcs').val()) == ''){



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Enter Lot Pcs "});



			$('#lot_pcs').focus();



			return false;



		}else if($.trim($('#lot_gross_wt').val()) == ''){



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Enter Gross wt "});



			$('#lot_gross_wt').focus();



			return false;



		}else if($.trim($('#lot_net_wt').val()) == ''){



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Enter Net wt "});



			$('#lot_net_wt').focus();



			return false;



		}else if($.trim($('#lot_wastage').val()) == ''){



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Enter Wastage "});



			$('#lot_wastage').focus();



			return false;



		}else if($.trim($('#select_section').val()) == ''){



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Section "});



			$('#select_section').focus();



			return false;



		}

	}



	if(is_purchase_cost_from_lot == 1 && stock_type == 1 ){



		if ($('#select_product').val() == null){



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Product"});



			$('#select_product').focus();



			return false;



		}else if ($('#select_design').val() == null){



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Design"});



			$('#select_design').focus();



			return false;



		}else if ($('#select_sub_design').val() == null){



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Sub Design"});



			$('#select_sub_design').focus();



			return false;



		}else if ($.trim($('#purchase_touch').val()) == ''){



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Enter Purchase Touch "});



			$('#purchase_touch').focus();



			return false;



		}else if ($('#karigar_calc_type').val() == ''){



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Type "});



			return false;



		}else if ($('#rate_calc_type').val() == ''){



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Rate Calculation Type"});



			$('#rate_calc_type').focus();



			return false;

		}

		// }else if ($.trim($('#rate_per_gram').val()) == ''){



		// 	$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Enter the Purchase Rate"});



		// 	$('#rate_per_gram').focus();



		// 	return false;



		// }



	}



	return true;

}







$('#select_product').on('change',function(){



if (this.value != '' && this.value != null ){

	product_id = this.value;

	sales_mode = $('#select_product option:selected').attr('data-salesmode');

	calculation_based_on = $('#select_product option:selected').attr('data-calculation_based_on');

	//var sales_mode = lot_product_details.filter(product => product.pro_id == product_id).map(product => product.sales_mode)[0];



	//var calculation_based_on = lot_product_details.filter(product => product.pro_id == product_id).map(product => product.calculation_based_on)[0];



	$('#calculation_based_on').val(calculation_based_on);



	$('#sales_mode').val(sales_mode);

}else{

	$('#calculation_based_on').val('');

	$('#sales_mode').val('');

}



});



$('#category').on('change',function(){



	cat_id = this.value;



	var stock_type       = $("input[name='inward[stock_type]']:checked").val();



	if(cat_id !=''){



		$('#select_product option').remove();



	$.each(lot_product_details, function (key, item) {



		if(cat_id == item.cat_id   && stock_type == item.stock_type){



			$('#select_product').append(



				$("<option></option>")



				.attr("value", item.pro_id)



				.text(item.product_name)



				.attr("data-purmode", item.purchase_mode)



				.attr("data-salesmode", item.sales_mode)



				.attr("data-calculation_based_on", item.calculation_based_on)



				.attr("data-tax_type", item.tax_type)



				.attr("data-stone_type", item.stone_type)



				);



	}







	});







$('#select_product').select2({



	placeholder: "Product",



	allowClear: true





});





$('#select_product').select2("val",'');



}



});





function reset_data(){



	$('#select_product').select2("val",'');

	$('#select_design').select2("val",'');

	$('#select_sub_design').select2("val",'');

    $('#select_section').select2("val",'');

	$('#sel_desingn_for').val();

	$('#lot_pcs').val('');

	$('#lot_gross_wt').val('');

	$('#lot_gross_uom').val(1);

	$('#lot_lwt').val('');

	$('#lot_lwt_uom').val(1);

	$('#lot_net_wt').val('');

	$('#lot_net_uom').val(1);

	$('#lot_wastage').val('');

	$('#mc_type').val('1');

	$('#mc_value').val('');

	$('#buy_rate').val('');

	 $('#sell_rate').val('');

	$('#calculation_based_on').val('');

	$('#purchase_touch').val(92);

	$('#karigar_calc_type').val('3');

	$('#rate_per_gram').val('');

	$('#lot_stone_details').val('');

	$('#lot_inward_details_id').val('');

	$('#tot_purewt').val('');

	$('#id_design').val('');

	$('#id_sub_design').val('');

	$('#other_metal_details').val('');

	$('#other_metal_wt').val('');

	$('#other_metal_wast_wt').val('');

	$('#other_metal_mc_amount').val('');

	$('#other_charges_details').val('');

	$('#item_cost').val('');

	$('#item_total_tax').val('');

	$('#other_charges_amount').val('');

	$('#item_total_taxable').val('');

	$('#other_metal_amount').val('');



	metal_details=[];

	modalOthermetal=[];

	modalStoneDetail=[];

}





//custom



$(document).on('change',".stones_type",function(){



	var row = $(this).closest('tr');



	var stone_type=this.value;



	row.find('.quality_id').val('');



	row.find('.quality_id').prop('disabled', false);



	row.find('.stone_id').html('');



		var stones_list = "<option value=''>-Select Stone-</option>";



	$.each(stones, function (pkey, pitem) {



		 if(pitem.stone_type==stone_type)



		 {



			 stones_list += "<option value='"+pitem.stone_id+"'>"+pitem.stone_name+"</option>";



		 }



	 });



	  row.find('.stone_id').append(stones_list);



	//  set_minmaxStone_rates(row);



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



	// set_minmaxStone_rates(row);



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



	// set_minmaxStone_rates(curRow);



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



			 +'<td><input type="number" class="stone_rate form-control" name="est_stones_item[stone_rate][]" value="" style="width: 100px"/></td>'



			 +'<td><input type="number" class="stone_price form-control" name="est_stones_item[stone_price][]" value="" " style="width: 100px"/></td>'



			 +'<td style="width: 100px;"><button type="button" class="btn btn-success btn-xs create_stone_item_details"><i class="fa fa-plus"></i></button><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-xs btn-del"><i class="fa fa-trash"></i></a></td></tr>';



	 $('#cus_stoneModal .modal-body').find('#estimation_stone_cus_item_details tbody').append(row);



 }



 function create_new_empty_est_cus_stone_item(row_st_details,id)



 {



	//  if(curRow!=undefined)



	//  {



	// 	 $('#custom_active_id').val(curRow.closest('tr').attr('class'));



	// 	 $('#stone_active_row').val(curRow.closest('tr').attr('class'));



	//  }



	//  var row='';



	//  var catRow=$('#custom_active_id').val();



	//  var active_row=$('#stone_active_row').val();



	//  var row_st_details=$('.'+catRow).find('.stone_details').val();



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



//  $(document).on('input',".stone_pcs,.stone_wt",function()



//  {



// 	 var curRow = $(this).closest('tr');



// 	 set_minmaxStone_rates(curRow);



//  });



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



	 // check_min_max_stone_rate(curRow);



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



		 var stone_wt  = 0;



		 var tot_stone_wt  = 0;



		 modalStoneDetail = []; // Reset Old Value of stone modal



		 $('#cus_stoneModal .modal-body #estimation_stone_cus_item_details> tbody  > tr').each(function(index, tr) {



			// stone_price+=parseFloat($(this).find('.stone_price').val());



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



						 'stone_name'        : $(this).find('.stone_id :selected').text(),



						 'stone_quality_id'  : $(this).find('.quality_id').val(),



			 });



		 });



		 modalStoneDetail = stone_details;



		 console.log(modalStoneDetail);







			// var st_details=JSON.parse(stone_details);



			 if(stone_details.length>0)



			 {



				  $.each(stone_details, function (pkey, pitem) {



					  $.each(uom_details,function(key,item){



						  if(item.uom_id==pitem.stone_uom_id)



						  {



							  if(pitem.show_in_lwt==1)



							  {



								  if((item.code=='CT') && (item.divided_by_value!=null && item.divided_by_value!='')) //For Carat Need to convert into gram



								  {



									  stone_wt=parseFloat(parseFloat(pitem.stone_wt)/parseFloat(item.divided_by_value));



								  }else{



									  stone_wt=pitem.stone_wt;



								  }



								  tot_stone_wt+=parseFloat(stone_wt);



							  }



							  stone_price+=parseFloat(pitem.stone_price);



							//   $.each(current_po_details[0].stonedetail, function (spkey, spitem) {



							// 	 if(pitem.stone_id == spitem.po_stone_id){



							// 		 if(spitem.po_stone_calc_based_on == 1){



							// 			 stone_po_price += parseFloat((spitem.po_stone_rate * pitem.stone_wt));



							// 		 }else{



							// 			 stone_po_price += parseFloat((spitem.po_stone_rate * pitem.stone_pcs));



							// 		 }



							// 	 }



							//   });



						  }



					  });



				  });



			 }







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



		 //gross_wt



		 var gross_wt = (isNaN($('#lot_gross_wt').val()) || $('#lot_gross_wt').val()=='' ?0 :$('#lot_gross_wt').val());



		 var net_wt   =  parseFloat(parseFloat(gross_wt)-parseFloat(tot_stone_wt)).toFixed(3);



		 if(net_wt > 0){



			$("#lot_lwt").val( parseFloat(tot_stone_wt).toFixed(3));



			$('#lot_net_wt').val(net_wt);



			$("#lot_stone_details").val(JSON.stringify(stone_details));



			$("#stone_price").val(stone_price);



			//calculateTagFormSaleValue();



			$('#cus_stoneModal .modal-body').find('#estimation_stone_cus_item_details tbody').empty();



			$('#cus_stoneModal').modal('hide');



			calculate_purchase_item_cost();



			//$("#lot_lwt").trigger('keyup');



			//$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br> Invalid Net Wt"});





		 }else{



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br> Invalid Net Wt"});



		 }















		//  $('#tag_wast_perc').focus();







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

// Stone



 $(".add_tag_lwt").on('click',function()



 {



	$('#update_stone_details').prop('disabled', false);

	 openStoneModal();



 });





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



	calculate_stone_amount();



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



			+'<td><input type="number" class="stone_rate form-control" name="est_stones_item[stone_rate][]" value="'+rate+'" style="width: 100px"/></td>'



			+'<td><input type="number" class="stone_price form-control" name="est_stones_item[stone_price][]" value="'+price+'" style="width: 100px"/></td>'



			+'<td style="width: 100px;"><button type="button" class="btn btn-success btn-xs create_stone_item_details"><i class="fa fa-plus"></i></button><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-xs btn-del"><i class="fa fa-trash"></i></a></td></tr>';



		$('#cus_stoneModal .modal-body').find('#estimation_stone_cus_item_details tbody').append(row);



		$("#cus_stoneModal").on('shown.bs.modal', function(){



			$(this).find('.stones_type').focus();



		});



    	$('#custom_active_id').val("st_" + row_cls);



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



			// var id_quality = $('#id_quality').val();



            // $("#quality_code option").remove();



			// $.each(data, function (key, item) {



			// 		$("#quality_code").append(



			// 			$("<option></option>")



			// 			.attr("value", item.quality_id)



			// 			.text(item.code)



			// 		);



			// 	});



			// 	$("#quality_code").select2({



			// 		placeholder: "Quality Code",



			// 		allowClear: true



			// 	});







			// 	$('#quality_code').select2("val",(id_quality!='' ? id_quality :""));



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



$(document).on('change','.stone_rate,.stone_wt,.stone_pcs  ',function()



{



	var row = $(this).closest('tr');



	// if(ctrl_page[1]=='tagging' && ctrl_page[2]=='add')



	// {



	// 	check_min_max_stone_rate(row);



	// }



	calculate_stone_amount();



});



$(document).on('click', '.create_stone_item_details', function (e) {



	if(validateStoneCusItemDetailRow()){



		   create_new_stone_row();



	   }else{



		   alert("Please fill required stone fields");



	   }



});



$(document).on('change', '#lot_wastage,#purchase_touch', function (e) {



	var value = this.value;



	if(value > 100){



		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+' You Have Entered Invalid Value'});



		this.value=0;



	}



});





function show_stone_details(curRow,id){

	$('#cus_stoneModal .modal-body').find('#estimation_stone_cus_item_details tbody').empty();



	$('#cus_stoneModal').modal('show');



	modalStoneDetail =JSON.parse(curRow.find('.lot_stones_details').val());



	$.each(modalStoneDetail, function (key, item) {



		console.log(item);



		if(item){



			create_stone_item(item);



		}



	});



	// $('#update_stone_details').a



	calculate_stone_amount();



	modalStoneDetail=[];



	$('#update_stone_details').prop('disabled', true);

}



function create_stone_item(stn_data){







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



	var rate = (stn_data ? (stn_data.stone_rate == undefined ? 0:stn_data.stone_rate): 0);



	var price = (stn_data ? (stn_data.stone_price == undefined ? 0:stn_data.stone_price) : 0);



	var cal_type = (stn_data ? (stn_data.stone_cal_type == undefined ? 1:stn_data.stone_cal_type) : 1);



	var row_cls = $('#estimation_stone_cus_item_details tbody tr').length;



	row='<tr id="'+$('#estimation_stone_cus_item_details tbody tr').length+'" class="st_'+$('#estimation_stone_cus_item_details tbody tr').length+'">'



		+'<td><select  disabled class="show_in_lwt form-control" name="est_stones_item[show_in_lwt][]" style="width:70px;"><option value="">-Select-</option><option value=1 '+(show_in_lwt==1 || show_in_lwt == undefined ? 'selected' :'')+'>Yes</option><option value=0 '+(show_in_lwt==0 ? 'selected' :'')+'>No</option></select></td>'



		+'<td><select disabled class="stones_type form-control" name="est_stones_item[stones_type][]" style="width:80px;">'+stones_type+'</select></td>'



		+'<td><select disabled class="stone_id form-control" name="est_stones_item[stone_id][]" style="width:80px;">'+stones_list+'</select><input type="hidden" class="stone_type" value=""></td>'



		+'<td><select disabled class="quality_id form-control" name="est_stones_item[quality_id][]" '+disable_quality+' style="width:80px;">'+quality_list+'</select></td>'



		+'<td><input type="number" readonly class="stone_pcs form-control" name="est_stones_item[stone_pcs][]" value="'+stone_pcs+'" style="width: 70px;"/></td>'



		+'<td><div class="input-group" style="width:159px;"><input readonly class="stone_wt form-control" type="number" name="est_stones_item[stone_wt][]" value="'+stone_wt+'" style="width: 78px;"/><span class="input-group-btn" style="width: 138px;"><select class="stone_uom_id form-control" name="est_stones_item[uom_id][]">'+uom_list+'</select></span></div></td>'



		+'<td><div class="form-group" style="width: 100px;"><input  readonlyclass="stone_cal_type" type="radio" name="est_stones_item[cal_type]['+parseFloat(row_cls+1)+']" value="1" '+(cal_type == 1 ? 'checked' : '')+'>Wt&nbsp;<input type="radio" name="est_stones_item[cal_type]['+parseFloat(row_cls+1)+']" class="stone_cal_type" value="2" '+(cal_type == 2 ? 'checked' : '')+'>Pcs</div></td>'



		+'<td><span class="stone_cut">'+cut+'</span></td>'



		+'<td><span class="stone_color">'+color+'</span></td>'



		+'<td><span class="stone_clarity">'+clarity+'</span></td>'



		+'<td><span class="stone_shape">'+shape+'</span></td>'



		+'<td><input type="number"  readonly class="stone_rate form-control" name="est_stones_item[stone_rate][]" value="'+rate+'" style="width: 100px"/></td>'



		+'<td><input type="number"  readonly class="stone_price form-control" name="est_stones_item[stone_price][]" value="'+price+'" style="width: 100px"/></td>'



		+'<td style="width: 100px;"></td></tr>';



	$('#cus_stoneModal .modal-body').find('#estimation_stone_cus_item_details tbody').append(row);



	$("#cus_stoneModal").on('shown.bs.modal', function(){



		$(this).find('.stones_type').focus();



	});



	$('#custom_active_id').val("st_" + row_cls);





}



function view_purchase_details(curRow){



	row ='';



	$('#lot_pur_details tbody').empty();



	$('#purchase_details').modal('show');



	pur_details =JSON.parse(curRow.find('.lot_purchase_details').val());



	$.each(pur_details, function (key, item) {



       row +=`<tr>

	                <td>`+item.id_lot_inward_detail+`</td>

					<td>`+item.pro_name+`</td>

					<td>`+item.design+`</td>

					<td>`+item.sub_design_name+`</td>

					<td>`+item.wastage_percentage+`</td>

					<td>`+item.mc_type_name+`</td>

					<td>`+item.making_charge+`</td>

					<td>`+item.purchase_type+`</td>

					<td>`+item.purchase_touch+`</td>

					<td>`+item.rate+` /  `+item.rate_calc_types+ `</td>

					</tr>`



	});



	$('#lot_pur_details tbody').append(row);



	// $('#update_stone_details').a



	//calculate_stone_amount();



	//modalStoneDetail=[];



	//$('#update_stone_details').prop('disabled', true);







}



function edit_lot_details(curRow){



	var stock_type       = $("input[name='inward[stock_type]']:checked").val();



	lot_details = JSON.parse(curRow.find('.lot_details').val());



	console.log('lot_details',lot_details);



	if(stock_type == 2)

	{

		lot_receive_branch = $('#id_branch').val();



		$.each(section_details, function (key, item){



			if(lot_receive_branch==item.id_branch)



			{



				$('#select_section').append(



				  $("<option></option>")



				  .attr("value",item.id_section)



				  .text(item.section_name)



				  );



			}





		});



		$("#select_section").select2(



		{



				placeholder: "Select Section",



				allowClear: true



		});



		$("#select_section").select2('val','');

	}



	cat_id =$('#category').val();



	$('#select_product option').remove();







	$.each(lot_product_details, function (key, item) {



		if(cat_id == item.cat_id   && stock_type == item.stock_type){



			$('#select_product').append(



				$("<option></option>")



				.attr("value", item.pro_id)



				.text(item.product_name)



				.attr("data-purmode", item.purchase_mode)



				.attr("data-salesmode", item.sales_mode)



				.attr("data-calculation_based_on", item.calculation_based_on)



				.attr("data-tax_type", item.tax_type)



				.attr("data-stone_type", item.stone_type)



				);



			}



		});







	if(lot_designs > 0 || lot_details.lot_id_design == 0 ){



		$('#select_design').append(



			$("<option></option>")



			.attr("value", 0)



			.text('ALL')



			);







		}



	$.each(lot_designs, function (key, pitem) {



       if(pitem.pro_id == lot_details.lot_product){



		$('#select_design').append(



			$("<option></option>")



			.attr("value", pitem.design_no)



			.text(pitem.design_name)



			);

		}





	});





	$('#select_design').select2(



		{



			placeholder:"Select Design",



			allowClear: true



		});



	if(lot_sub_designs > 0 || lot_details.id_sub_design == 0 ){



		$('#select_sub_design').append(



			$("<option></option>")



			.attr("value", 0)



			.text('ALL')



			);



		}





	$.each(lot_sub_designs, function (key, pitem) {





			$('#select_sub_design').append(



				$("<option></option>")



				.attr("value", pitem.id_sub_design)



				.text(pitem.sub_design_name)



				);





	});



	$('#select_sub_design').select2(



	{



		placeholder:"Select Sub Design",



		allowClear: true



	});



	$('#select_product').select2("val",lot_details.lot_product);

	$('#select_design').select2("val",lot_details.lot_id_design);

	$('#select_sub_design').select2("val",lot_details.id_sub_design);

	$('#id_design').val(lot_details.lot_id_design);

	$('#id_sub_design').val(lot_details.id_sub_design);

	if(stock_type == 2){

    $('#select_section').select2("val",lot_details.id_section);

    }

	// $('#sel_desingn_for').val();

	$('#lot_pcs').val(lot_details.lot_pcs);

	$('#lot_gross_wt').val(lot_details.gross_wt);

	$('#lot_gross_uom').val(lot_details.gross_wt_uom);

	$('#lot_lwt').val(lot_details.less_wt);

	$('#lot_lwt_uom').val(lot_details.less_wt_uom);

	$('#lot_net_wt').val(lot_details.net_wt);

	$('#lot_net_uom').val(lot_details.net_wt_uom);

	$('#lot_wastage').val(lot_details.wastage_percentage);

	$('#mc_type').val(lot_details.mc_type);

	$('#mc_value').val(lot_details.making_charge);

	$('#buy_rate').val(lot_details.buy_rate);

	 $('#sell_rate').val(lot_details.sell_rate);

	$('#calculation_based_on').val(lot_details.calculation_based_on);

	$('#purchase_touch').val(lot_details.purchase_touch);

	$('#karigar_calc_type').val(lot_details.calc_type);

	 $('#rate_per_gram').val(lot_details.rate);

	 $('#rate_calc_type').val(lot_details.rate_calc_type);

	 $('#lot_stone_details').val(lot_details.stone_details);

	 $('#lot_inward_details_id').val(lot_details.id_lot_inward_detail);

	 $('#other_metal_details').val(lot_details.other_metal_details);

	 $('#other_charges_details').val(lot_details.other_charges_details);





	modalStoneDetail=JSON.parse(lot_details.stone_details);



	modalOthermetal=JSON.parse(lot_details.other_metal_details);



	 calculate_purchase_item_cost();



	 curRow.remove();



	 calculate_lot_inward_Total();



}



function calculate_pure_wt(){



	var karigar_calc_type       = $('#karigar_calc_type').val();



	var net_wt       = $('#lot_net_wt').val() == '' ? 0 : $('#lot_net_wt').val();



	var purity       = $('#purchase_touch').val()== '' ? 0 : $('#purchase_touch').val();



	var wastage       = $('#lot_wastage').val()== '' ? 0 : $('#lot_wastage').val();



	if(karigar_calc_type==1)



	{



		var purewt = parseFloat((parseFloat(net_wt) * (parseFloat(purity) + parseFloat(wastage))) / 100).toFixed(3);



	}else if(karigar_calc_type==2) //Net weight * touch



	{



		var purewt = parseFloat((parseFloat(net_wt) * (parseFloat(purity)/100))).toFixed(3);



	}



	else if(karigar_calc_type==3) // ((net wt * 3%)*92%)



	{



		var touch_weight       = parseFloat((parseFloat(net_wt)*parseFloat(purity)/100)).toFixed(3);



		var wastage_touch      = parseFloat(parseFloat(touch_weight)*(parseFloat(wastage))/100);



		var purewt             = parseFloat(parseFloat(touch_weight)+parseFloat(wastage_touch)).toFixed(3);



	}



	$('#tot_purewt').val(purewt);

}







$(document).on('change', '#lot_pcs,#lot_gross_wt,#karigar_calc_type,#lot_wastage,#purchase_touch,#rate_calc_type,#rate_per_gram,#mc_value,#mc_type', function (e) {



	calculate_purchase_item_cost();

	calculate_pure_wt();





});









$(".add_other_metals").on('click',function(){



    open_other_metal_modal();



});



function open_other_metal_modal()



{



    $('#other_metalmodal').modal('show');



    $('#other_metalmodal .modal-body').find('#other_metal_table tbody').empty();



    if(modalOthermetal.length>0)



    {



        $.each(modalOthermetal, function(key,item){



            if(item)



            {



                create_new_empty_other_metal_item(item);



            }



        })



    }



    else



    {



        create_new_empty_other_metal_item();



    }



}



$(".add_other_charges").on('click',function(){



	$("#table_charges tbody tr").remove();



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







						'calc_type'          : $(this).find('.calc_type').val(),



						//'name_charge'        : $(this).find('.chargesType :selected').text(),







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







        $('#cus_chargeModal .modal-body').find('#table_charges tbody').empty();



        $('#cus_chargeModal').modal('hide');







        // if(ctrl_page[1]=='grnentry')



        // {



        //     calculate_grn_final_cost();



        // }



        // else



        // {



             calculate_purchase_item_cost();







            // calculate_FinalCost();







            // calculate_purchase_return_item_cost();



        // }















    }



    else



    {



    	alert('Please Fill The Required charge Details');



    }



});



function open_other_charges_modal(){



    $('#cus_chargeModal').modal('show');







         create_new_empty_other_charges_item($('#other_charges_details').val());



}





function create_new_empty_other_metal_item(other_metal_data=[])



{







    //var trHtml='';



    var metal='<option value="">Select Metal</option>';



    var purity='<option value="">Select Purity</option>';







    $.each(category_lists, function (mkey, mitem)



    {



        metal += "<option value='"+mitem.id_ret_category+"' "+(other_metal_data ? (mitem.id_ret_category == other_metal_data.id_metal ? 'selected' : '') : '')+">"+mitem.name+"</option>";



	});







    $.each(purityDetails, function (k, p)



    {



        purity += "<option value='"+p.id_purity+"' "+(other_metal_data ? (p.id_purity == other_metal_data.id_purity ? 'selected' : '') : '')+">"+p.purity+"</option>";



	});



    var othr_mt_pcs = (other_metal_data ? (other_metal_data.pcs == undefined ? '':other_metal_data.pcs) : '');



	var othr_mt_gwt = (other_metal_data ? (other_metal_data.gwt == undefined ? '':other_metal_data.gwt) : '');



	var othr_mt_rate = (other_metal_data ? (other_metal_data.rate_per_gram == undefined ? 0:other_metal_data.rate_per_gram): 0);



	var othr_mt_amt = (other_metal_data ? (other_metal_data.amount == undefined ? 0:other_metal_data.amount) : 0);







    var row_cls = $('#other_metal_table tbody tr').length;



        row = '<tr id="'+$('#other_metal_table tbody tr').length+'" class="st_'+$('#other_metal_table tbody tr').length+'">'







          +'<td><select class="form-control select_metal">'+metal+'</td>'



          +'<td><select class="form-control select_purity">'+purity+'</td>'



          +'<td><input type="number" class="form-control pcs" value="'+othr_mt_pcs+'"></td>'



          +'<td><input type="number" class="form-control gwt" value="'+othr_mt_gwt+'"></td>'



          +'<td ><input type="number" class="form-control wastage_perc" value="0"><input type="hidden" class="wast_wt" value="0"></td>'



          +'<td ><select class="form-control calc_type"><option value="">Mc Type</option><option value="1" selected>Per Gram</option><option value="2">Per Piece</option></select></td>'



          +'<td ><input type="number" class="form-control making_charge" value="0"><input type="hidden" class="mc_value" value="0"></td>'



          +'<td><input type="number" class="form-control rate_per_gram" value="'+othr_mt_rate+'"></td>'



          +'<td><input type="number" class="form-control amount" value="'+othr_mt_amt+'"></td>'



           +'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'



          +'</tr>';







          $('#other_metalmodal .modal-body').find('#other_metal_table tbody').append(row);



        //$('#cus_stoneModal .modal-body').find('#estimation_stone_cus_item_details tbody .st_'+row_cls+ '.show_in_lwt').focus();







        $("#other_metalmodal").on('shown.bs.modal', function(){



        });







    $('#custom_active_id').val("st_"+row_cls);











}



function create_new_empty_other_charges_item(selData = [])



{



    console.log(selData);



	let charges_validated = validate_charges_row();



	if(charges_validated)



    {



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







                let new_row = "<tr class='ch_"+row_cls+"'><td class='sno'>"+sno+"</td><td><select class='form-control chargesType'><option value=''>--Select--</option>"+options+"</select></td><td><select class='form-control calc_type'><option value='1' "+(val.calc_type==1 ? "selected" :'')+" >Per Item</option><option value='2' "+(val.calc_type==2 ? "selected" :'')+" >Per Piece</option></select></td><td><input type='text' value='"+(charges ? (val.charge_value == undefined ? 0 : val.charge_value) : '')+"' class='form-control chargesValue' /></td><td class='chargeModal_buttons'></td></tr>";







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



        modalOthermetal = [];   // Reset Old Value of other metal modal



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



    	modalOthermetal = metal_details;



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











// function get_ActiveMetals()



// {



//     $("div.overlay").css("display", "block");



//     $.ajax({



// 	type: 'GET',



// 	url: base_url+'index.php/admin_ret_catalog/active_metals',



// 	dataType:'json',



// 	success:function(data){



// 	        metalDetails=data;



// 	        var id=$('#select_metal').val();



// 	        $.each(data, function (key, item) {



//                 $('#select_metal').append(



//                 $("<option></option>")



//                 .attr("value", item.id_metal)



//                 .text(item.metal)



//                 );



//         	});







//         	$('#select_metal').select2({



//         	    placeholder: "Metal",



//         	    allowClear: true



//         	});



// 	        if($('#select_metal').length)



// 	        {



// 	            $('#select_metal').select2("val",(id!='' ? id:''));



// 	        }







// 	    	$("div.overlay").css("display", "none");



// 		}



// 	});



// }



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



function calculate_purchase_item_cost()



{



    var item_cost=0;



    var rate_per_gram           = (isNaN($('#rate_per_gram').val()) || $('#rate_per_gram').val()=='' ? 0:$('#rate_per_gram').val());



    var tot_pcs                 = (isNaN($('#lot_pcs').val()) || $('#lot_pcs').val()=='' ? 0:$('#lot_pcs').val());



    var tot_gwt                 = (isNaN($('#lot_gross_wt').val()) || $('#lot_gross_wt').val()=='' ? 0:$('#lot_gross_wt').val());



    var tot_lwt                 = (isNaN($('#lot_lwt').val()) || $('#lot_lwt').val()=='' ? 0:$('#lot_lwt').val());



    var wastage_per             = (isNaN($('#lot_wastage').val()) || $('#lot_wastage').val()=='' ? 0:$('#lot_wastage').val());



    var mc_value                = (isNaN($('#mc_value').val()) || $('#mc_value').val()=='' ? 0:$('#mc_value').val());



    var other_metal_amount      = (isNaN($('#other_metal_amount').val()) || $('#other_metal_amount').val()=='' ? 0:$('#other_metal_amount').val());



    var other_metal_wt          = (isNaN($('#other_metal_wt').val()) || $('#other_metal_wt').val()=='' ? 0:$('#other_metal_wt').val());



    var other_metal_wast_wt     = (isNaN($('#other_metal_wast_wt').val()) || $('#other_metal_wast_wt').val()=='' ? 0:$('#other_metal_wast_wt').val());



    var other_metal_mc_amount   = (isNaN($('#other_metal_mc_amount').val()) || $('#other_metal_mc_amount').val()=='' ? 0:$('#other_metal_mc_amount').val());



    var stone_price             = (isNaN($('#stone_price').val()) || $('#stone_price').val()=='' ? 0:$('#stone_price').val());



    var calculation_based_on    = (isNaN($('#calculation_based_on').val()) || $('#calculation_based_on').val()=='' ? 0:$('#calculation_based_on').val());



    var order_div_gwt           = 0;







    var karigar_type            = $('#select_karigar option:selected').attr('data-karigartpe');







    //var ratecaltype             = ($('#purchase_mode').val()!='' ? $('#purchase_mode').val():$('#select_product option:selected').attr('data-purmode')); //2-Flexible,1-Fixed







    var tax_type                = ''; //1-Inclusive,2-Exclusive







    var purchase_type           = $("input[name='order[purchase_type]']:checked").val();







    var karigar_calc_type       = $('#karigar_calc_type').val();





     var stone_type = $('#select_product option:selected').attr('data-stone_type'); //0-Ornaments,1-stone,2-Diamond



    var ratecaltype = $('#rate_calc_type').val(); // 1-> Gram , 2-> Pcs







    var grn_type                ='';



    var wastage_wt  = (isNaN($('#tot_wastage_wgt').val()) || $('#tot_wastage_wgt').val()=='' ? 0:$('#tot_wastage_wgt').val());



    var wastage_type = ($('#wastage_type').val()!='' ? $('#wastage_type').val():1);



    if(stone_price > 0){



        //stone_price = parseFloat(parseFloat(stone_price) * ((100 + 3) / 100)).toFixed(2);



    }















    var other_charges_amount = 0;











    var other_charges_details = [];



    if($('#other_charges_details').val()!='')



    {



        other_charges_details = JSON.parse($('#other_charges_details').val());



        console.log(other_charges_details);



        $.each(other_charges_details,function(k,val){



        var char_tax = 0;



        var charge_tax_value = 0;



        var item_charges_amount = 0;



            $.each(charges_details, function (ckey, citem) {



			    if(parseInt(val.charge_id) == parseInt(citem.id_charge)){



			        char_tax= parseFloat(citem.charge_tax);



			        }



			    });



            if(val.calc_type==1)



            {



                item_charges_amount=parseFloat(val.charge_value);



            }else if(val.calc_type==2)



            {



                item_charges_amount=(parseFloat(val.charge_value)*tot_pcs);



            }



            if(char_tax > 0){



                charge_tax_value  = (parseFloat(item_charges_amount)*parseFloat(char_tax)/100).toFixed(2);



            }



           item_charges_amount = parseFloat(parseFloat(item_charges_amount)+parseFloat(charge_tax_value)).toFixed(2);



           other_charges_amount+=parseFloat(item_charges_amount);



           other_charges_details[k].total_charge_value = parseFloat(item_charges_amount).toFixed(2);



           other_charges_details[k].tax_percentage = parseFloat(char_tax).toFixed(2);



           other_charges_details[k].charge_tax_value = parseFloat(charge_tax_value).toFixed(2);



           other_charges_details[k].charge_value = parseFloat(val.charge_value).toFixed(2);



           other_charges_details[k].calc_type = val.calc_type;



           other_charges_details[k].charge_id = val.charge_id;



        });



        $('#other_charges_details').val(JSON.stringify(other_charges_details));



        $('#other_charges_amount').val(parseFloat(other_charges_amount).toFixed(2));



    }



    var net_wt                  = parseFloat(tot_gwt)-parseFloat(other_metal_wt)-parseFloat(tot_lwt); //Removing Other Metal Weight and Stone Weight







    $('#lot_net_wt').val(parseFloat(net_wt).toFixed(3));







//     if($('#is_cus_repair_order').val() == 1){



//        if(tot_gwt > 0){



//            var tot_ord_issue_wt = (isNaN($('#order_gwt').val()) || $('#order_gwt').val()=='' ? 0:$('#order_gwt').val());



//            order_div_gwt = parseFloat(net_wt) - parseFloat(tot_ord_issue_wt);



//        }



//    }











    var purity = (isNaN($('#purchase_touch').val()) || $('#purchase_touch').val()=='' ? 0:$('#purchase_touch').val());



    var wastage = (isNaN($('#tot_wastage_perc').val()) || $('#tot_wastage_perc').val()=='' ? 0:$('#tot_wastage_perc').val());



    var mc_type                 = $('#mc_type').val();



    /*



    0 - MC & V.A on Gross Weight



    1 - MC & V.A on Net Weight



    2 - MC On Gross & V.A on Net Weight



    */



    if(calculation_based_on==0)



    {



        var total_mc_value          = (mc_type==1 ? parseFloat(parseFloat(mc_value)*parseFloat(tot_gwt)).toFixed(2) :parseFloat(parseFloat(mc_value)*parseFloat(tot_pcs)).toFixed(2));





    }



    else if(calculation_based_on==1)



    {







        var total_mc_value          = (mc_type==1 ? parseFloat(parseFloat(mc_value)*parseFloat(net_wt)).toFixed(2) :parseFloat(parseFloat(mc_value)*parseFloat(tot_pcs)).toFixed(2));





    }



    else if(calculation_based_on==2)



    {



        var total_mc_value          = (mc_type==1 ? parseFloat(parseFloat(mc_value)*parseFloat(tot_gwt)).toFixed(2) :parseFloat(parseFloat(mc_value)*parseFloat(tot_pcs)).toFixed(2));







    }else if(calculation_based_on==3 || calculation_based_on== 4){



		var total_mc_value          = (mc_type==1 ? parseFloat(parseFloat(mc_value)*parseFloat(net_wt)).toFixed(2) :parseFloat(parseFloat(mc_value)*parseFloat(tot_pcs)).toFixed(2));





	}else{

		total_mc_value = 0;

	}



    if(wastage > 100)



    {



        wastage = 0;



        $('#tot_wastage_perc').val(0);



        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Wastage Percentage Should Not Exceed More Than 100"});



    }







    $('.tot_mc_val').html(total_mc_value);















   // var total_weight            = parseFloat(wast_wt)+parseFloat(net_wt);









        if(karigar_calc_type==1)



        {



            var purewt = (parseFloat((parseFloat(net_wt) * (parseFloat(purity) + parseFloat(wastage))) / 100));



        }else if(karigar_calc_type==2) //Net weight * touch



        {



            var purewt = parseFloat((parseFloat(net_wt) * (parseFloat(purity)/100)));



        }



        else if(karigar_calc_type==3) // ((net wt * 3%)*92%)



        {



            var touch_weight       = parseFloat((parseFloat(net_wt)*parseFloat(purity)/100)).toFixed(3);



            var wastage_touch      = parseFloat(parseFloat(touch_weight)*(parseFloat(wastage_per))/100);



            var purewt             = parseFloat(parseFloat(touch_weight)+parseFloat(wastage_touch)).toFixed(3);



        }











        if(stone_type==0) // for ornaments Product

        {

            if(ratecaltype==1) // Rate Calc By Grm(Wt)

            {

                item_cost   = parseFloat((parseFloat(purewt)*parseFloat(rate_per_gram))+parseFloat(total_mc_value)+parseFloat(other_metal_amount) + parseFloat(other_charges_amount)+parseFloat(stone_price)).toFixed(2);

            }else

            {

                item_cost   = parseFloat((parseFloat(tot_pcs)*parseFloat(rate_per_gram))+parseFloat(total_mc_value)+parseFloat(other_metal_amount) + parseFloat(other_charges_amount)+parseFloat(stone_price)).toFixed(2);

            }

        }

        else // for stone and Diamond Product

        {

            if(ratecaltype==1) // Rate Calc By Grm(Wt)

            {

                item_cost   = parseFloat((parseFloat(tot_gwt)*parseFloat(rate_per_gram))).toFixed(2);

            }

            else

            {

                item_cost   = parseFloat((parseFloat(tot_pcs)*parseFloat(rate_per_gram))).toFixed(2);

            }

        }









        var tax_group = '';



        var total_tax_rate = 0;



        var igst_cost = 0;



        var sgst_cost = 0;



        var cgst_cost = 0;



        $.each(lot_product_details,function(key,val){



            if(val.pro_id == $('#select_product').val())



            {



                tax_group = val.tax_group_id;



                tax_type = val.tax_type;



            }



        });

        console.log('tax_group',tax_group);

        if(tax_group!='')



        {



            var supplier_country = '';



            var supplier_state = '';



            var cmp_country = $('#cmp_country').val();



            var cmp_state = $('#cmp_state').val();



            var supplier_country = $('#supplier_country').val();



            var supplier_state = $('#supplier_state').val();







            if(tax_type==1) // Inclusive of Tax



            {



                tax_details = calculate_inclusiveGST(item_cost,tax_group);



                console.log(tax_details);



                var total_tax_rate = tax_details['totaltax'];



                var tax_percentage = tax_details['tax_percentage'];



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



            }else



            {



                var tax_details     = calculate_base_value_tax(item_cost, tax_group);



                var total_tax_rate  = tax_details['totaltax'];



                var tax_percentage  = tax_details['tax_percentage'];



                item_cost           = parseFloat(parseFloat(item_cost)+parseFloat(total_tax_rate)).toFixed(2);



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



            $('#item_cgst_cost').val(parseFloat(cgst_cost).toFixed(2));



            $('#item_sgst_cost').val(parseFloat(sgst_cost).toFixed(2));



            $('#item_igst_cost').val(parseFloat(igst_cost).toFixed(2));



            $('#item_total_tax').val(parseFloat(total_tax_rate).toFixed(2));



            $('#item_tax_percentage').val(parseFloat(tax_percentage).toFixed(2));



            $('#tax_type').val(tax_type);



            $('#tax_group_id').val(tax_group);



        }



    $('.tax_type').html('');



    $('.tax_type').html((tax_type!='' ? (tax_type==1 ? ' - Inclusive' :' - Exclusive') :''));



    $('#tot_purewt').val(parseFloat(purewt).toFixed(3));



    $('#item_total_taxable').val(parseFloat(item_cost - total_tax_rate).toFixed(2));



    $('#item_cost').val(item_cost);



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



$('#cancel_remark').on('keypress',function(){



	if(this.value.length>6)



	{



		$('#lot_cancel').prop('disabled',false);



	}else{



		$('#lot_cancel').prop('disabled',true);



	}



});





$('#lot_cancel').on('click',function(){



    $('#lot_cancel').prop('disabled',true);



	my_Date = new Date();



	$.ajax({



		type: 'POST',



		url:base_url+ "index.php/admin_ret_lot/lot_inward/cancel_lot_entry?nocache=" + my_Date.getUTCSeconds(),



		dataType:'json',



		data:{'cancel_reason':$('#cancel_remark').val(),'lot_no':$('#lot_id').val()},



		success:function(data){



		    window.location.reload();



		}



	});



});









function calculate_base_value_tax(taxcallrate, taxgroup){



	var totaltax = 0;



	console.log(tax_details);







	var return_details=[];



	var tax_percentage = 0;



	$.each(tax_details, function(idx, taxitem){



		if(taxitem.tgi_tgrpcode == taxgroup){



		    console.log(taxitem);



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



function calculate_inclusiveGST(taxcallrate, taxgroup){



	var totaltax = 0;



	var tax_percentage = 0;



	var return_details = [];



	$.each(tax_details, function(idx, taxitem){



		if(taxitem.tgi_tgrpcode == taxgroup){



		    tax_percentage = taxitem.tax_percentage;



		//	Remove GST = 490*100/(100+3) = 475.7281553398058



        //	GST 3% = 490 - 475.7281553398058 = 14.2718446601942



			amt_without_gst = (parseFloat(taxcallrate)*100)/(100+parseFloat(taxitem.tax_percentage));



			totaltax += parseFloat(taxcallrate)	- parseFloat(amt_without_gst);



		}



	});



	return_details['totaltax'] = parseFloat(totaltax).toFixed(2);



	return_details['tax_percentage'] = tax_percentage;



	return return_details;



}



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







$(document).ready(function() {

  $('#lot_gross_wt').on('input', function() {

    var value = $(this).val();

    

    // Regular expression to match valid numbers with up to 2 digits after the decimal point

    var validPattern = /^\d+(\.\d{0,3})?$/;

    

    // If the value does not match the pattern, remove the last character

    if (!validPattern.test(value)) {

      $(this).val(value.slice(0, -1));

      alert('You can only enter numbers with up to 2 decimal places!');

    }

  });

});