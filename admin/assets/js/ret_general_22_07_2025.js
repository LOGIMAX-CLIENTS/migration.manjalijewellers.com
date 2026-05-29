var path =  url_params();

var ctrl_page = path.route.split('/');

var required_otp_approval = 1;

var cus_country = [];

$(document).ready(function() {



	$('#day_close').on('click',function(){



		var proceed = confirm("Are you sure do you want to Day Close ?");

		if (proceed == true) {

		   $("div.overlay").css("display", "block");

		   $('#day_close').prop("disabled",true);

		   $('#day_close').prop("value","Processing..");

		   dayClose();

		}



	});



	$.ajax({

		  type: 'POST',

		  url:  base_url+'index.php/admin_ret_services/required_otp_approval',

		  dataType: 'json',

		  success: function(data) {

		  		console.log(data);

				required_otp_approval = data.otp_required;

				console.log(required_otp_approval);

		  },

	  	  error:function(error)

		  {

			 console.log("Stock Balance Error" );

		  	 console.log(error);

		  }

    });



});



// Branch Name

$(document).ready(function(){

    var isAdminRet = window.location.href.includes("admin_ret");

	if($('#branch_set').val()==1  && ctrl_page [1] != 'ret_section' && isAdminRet){

		getBranchName();

	}

})



$('#branch_select').select2().on("change", function(e) {

if(((ctrl_page[2] !=='add' || ctrl_page[2] !=='edit') && this.value!='')) // based on the branch settings to showed branch filter iN send Notifi page admin//

{

   var id_branch   = $(this).val();

   $('#id_branch').val(id_branch);

}

});

// Branch Name



function dayClose(){

	$.ajax({

		  type: 'POST',

		  url:  base_url+'index.php/admin_ret_services/dayClose',

		  dataType: 'json',

		  success: function(data) {

		  	$("div.overlay").css("display", "block");

		  	$('#day_close').prop("disabled",false);

		  	if(data.status){

				//partlySold();

				$.toaster({ priority : 'success', title : 'Day Close', message : ''+"</br>"+data.message });

		    	/*stock_balance();

		    	old_metal_stock_balance();

		    	sales_return_stock_balance();

		    	partly_sale_stock_balance();

		    	bullion_purchase_stock_balance();

		    	stock_balance_packaging_items();

		    	stock_balance_nt();*/

		    	$("div.overlay").css("display", "none");

			}else{

				alert(data.message);

				$("div.overlay").css("display", "none");

			}

		  },

	  	  error:function(error)

		  {

		  	 $('#day_close').prop("disabled",false);

		  	 console.log(error);

		  }

    });

}



function partlySold(){

	$.ajax({

		  type: 'POST',

		  url:  base_url+'index.php/admin_ret_services/partly_sold',

		  dataType: 'json',

		  success: function(data) {

		  	console.log("Partly Sold" );

		  	console.log(data);

		  },

	  	  error:function(error)

		  {

			 console.log("Partly Sold Error" );

		  	 console.log(error);

		  }

    });

}



function stock_balance(){

    $("div.overlay").css("display", "block");

	$.ajax({

		  type: 'POST',

		  url:  base_url+'index.php/admin_ret_services/stock_balance',

		  dataType: 'json',

		  success: function(data) {

			    console.log("Stock Balance" );

		  		console.log(data);



		  		$("div.overlay").css("display", "none");

		  },

	  	  error:function(error)

		  {

		     //stock_balance_nt();

			 console.log("Stock Balance Error" );

		  	 console.log(error);

		  	 $("div.overlay").css("display", "none");

		  }

    });

}



function old_metal_stock_balance(){

    $("div.overlay").css("display", "block");

	$.ajax({

		  type: 'POST',

		  url:  base_url+'index.php/admin_ret_services/old_metal_stock_balance',

		  dataType: 'json',

		  success: function(data) {

			    console.log("Stock Balance" );

		  		console.log(data);

		  		$("div.overlay").css("display", "none");

		  },

	  	  error:function(error)

		  {

			 console.log("Stock Balance Error" );

		  	 console.log(error);

		  	 $("div.overlay").css("display", "none");

		  }

    });

}



function sales_return_stock_balance(){

    $("div.overlay").css("display", "block");

	$.ajax({

		  type: 'POST',

		  url:  base_url+'index.php/admin_ret_services/sales_return_stock_balance',

		  dataType: 'json',

		  success: function(data) {

			    console.log("Stock Balance" );

		  		console.log(data);

		  		$("div.overlay").css("display", "none");

		  },

	  	  error:function(error)

		  {

			 console.log("Stock Balance Error" );

		  	 console.log(error);

		  	 $("div.overlay").css("display", "none");

		  }

    });

}



function partly_sale_stock_balance(){

    $("div.overlay").css("display", "block");

	$.ajax({

		  type: 'POST',

		  url:  base_url+'index.php/admin_ret_services/partly_sale_stock_balance',

		  dataType: 'json',

		  success: function(data) {

			    console.log("Stock Balance" );

		  		console.log(data);

		  		$("div.overlay").css("display", "none");

		  },

	  	  error:function(error)

		  {

			 console.log("Stock Balance Error" );

		  	 console.log(error);

		  	 $("div.overlay").css("display", "none");

		  }

    });

}



function bullion_purchase_stock_balance(){

    $("div.overlay").css("display", "block");

	$.ajax({

		  type: 'POST',

		  url:  base_url+'index.php/admin_ret_services/bullion_purchase_stock_balance',

		  dataType: 'json',

		  success: function(data) {

			    console.log("Stock Balance" );

		  		console.log(data);

		  		$("div.overlay").css("display", "none");

		  },

	  	  error:function(error)

		  {

			 console.log("Stock Balance Error" );

		  	 console.log(error);

		  	 $("div.overlay").css("display", "none");

		  }

    });

}



function stock_balance_nt(){

    $("div.overlay").css("display", "block");

	$.ajax({

		  type: 'POST',

		  url:  base_url+'index.php/admin_ret_services/stock_balance_nontag',

		  dataType: 'json',

		  success: function(data) {

			    console.log("Stock Balance" );

		  		console.log(data);

		  		$("div.overlay").css("display", "none");

		  },

	  	  error:function(error)

		  {

			 console.log("Stock Balance Error" );

		  	 console.log(error);

		  	 $("div.overlay").css("display", "none");

		  }

    });

}





function stock_balance_packaging_items(){

    $("div.overlay").css("display", "block");

	$.ajax({

		  type: 'POST',

		  url:  base_url+'index.php/admin_ret_services/stock_balance_packaging_items',

		  dataType: 'json',

		  success: function(data) {

			    console.log("Stock Balance" );

		  		console.log(data);

		  		$("div.overlay").css("display", "none");

		  },

	  	  error:function(error)

		  {

			 console.log("Stock Balance Error" );

		  	 console.log(error);

		  	 $("div.overlay").css("display", "none");

		  }

    });

}





//Image Compression



$('#order_images_new').on('change',function(){

	validateOrderImages();

});







function validateOrderImages()

{

		var preview = $('#order_images');

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

			var image_details=[];

					$.each(resource,function(key,item){

						if(item)

						{

							var div = document.createElement("div");



							div.setAttribute('class','col-md-3 images');



							div.setAttribute('id','order_img_'+key);



							param = {"key":key};



							div.innerHTML+="<div class='form-group'><div class='image-input image-input-outline' id='kt_image_4'><div class='image-input-wrapper'><a onclick='remove_order_images("+JSON.stringify(param)+")'><i class='fa fa-trash'></i>Delete</a><img class='thumbnail' src='" + item.src + "'" + "style='width: 115px;height: 115px;'/></div></div>";

							preview.append(div);

							image_details.push(item);

					  }

					});

					localStorage.setItem('img_details',JSON.stringify(image_details));

		  },3000);



}



function remove_order_images(param)

{

	localStorage.removeItem("img_details");

	$('#order_img_'+param.key).remove();

	img_resource.splice(param.key,1);

	localStorage.setItem('img_details',JSON.stringify(img_resource));

	console.log(localStorage);

}



//Image Compression







//common add customer modal







function add_customer(){ //replace the function

    // var billing_for = $("input[name='billing[billing_for]']:checked").val();

    var billing_for = ctrl_page[1] == 'billing' ? $("input[name='billing[billing_for]']:checked").val() : $("input[name='estimation[esti_for]']:checked").val() ;

    var gender = $("input[name='customer[gender]']:checked").val();



	var vip = $("input[name='customer[vip]']:checked").val();



	var form_data = new FormData();



	form_data.append('cusName',$('#cus_first_name').val());



	form_data.append('cusMobile',$('#cus_mobile').val());



	form_data.append('cusBranch',$('#id_branch').val());



	form_data.append('id_village',$('#sel_village').val());



	form_data.append('gst_no',$('#gst_no').val());



	form_data.append('cus_type',billing_for==2 ? 2 :1);



	form_data.append('id_country',$('#country').val() != "" ? $('#country').val() : $('#id_country').val());



	form_data.append('id_state',$('#state').val());



	form_data.append('id_city',$('#city').val());



	form_data.append('address1',$('#address1').val());



	form_data.append('address2',$('#address2').val());



	form_data.append('address3',$('#address3').val());



	form_data.append('pincode',$('#pin_code_add').val());



	form_data.append('mail',$('#cus_email').val());



	form_data.append('cust_img',$("#cus_image")[0].files[0]);



	form_data.append('pan_no',$('#pan').val());



	form_data.append('aadharid',$('#aadharid').val());



	form_data.append('title',$('#title').val());



	form_data.append('gender',gender);



	form_data.append('is_vip',vip);



	form_data.append('id_profession',$('#professionval').val());



	form_data.append('date_of_birth',$('#date_of_birth').val());



	form_data.append('date_of_wed',$('#date_of_wed').val());



	form_data.append('dl_no',$('#dl').val());



	form_data.append('pp_no',$('#pp').val());



	my_Date = new Date();



	$.ajax({



        url: base_url+'index.php/admin_ret_billing/createNewCustomer/?nocache=' + my_Date.getUTCSeconds(),



        dataType: "json",



        method: "POST",



	    data:form_data,



		cache : false,



		enctype: 'multipart/form-data',



		contentType : false,



		processData : false,



        success: function (data) {



			if(data.success == true){



				// $('#confirm-add').modal('toggle');



				$("#add_customer_close").trigger("click");



				if(ctrl_page[1]=='receipt')



                {



                    $('#id_customer').val(data.response.id_customer);



                    $("#name").val(data.response.firstname + " - " + data.response.username);



                    var receipt_type            =$("input:radio[name='receipt[receipt_type]']:checked").val();



                    if(receipt_type==1)



                    {



                        get_customer_credit_details(data.response.id_customer);



                    }







                }



                else if(ctrl_page[1]=='service_bill')



                {



                    $('#bill_cus_id').val(data.response.id_customer);



                    $("#cus_search").val(data.response.firstname + " - " + data.response.username);



                }



                else if(ctrl_page[1]=='billing')



                {



                    	$("#bill_cus_name").val(data.response.firstname + " - " + data.response.username);



				        $("#bill_cus_id").val(data.response.id_customer);



				        $('#cus_first_name').val('');



				        $('#cus_mobile').val('');



				        get_customer_address_det();



                }
	



				else if(ctrl_page[1]=='estimation'){



					$("#est_cus_name").val(data.response.firstname + " - " + data.response.mobile);



					$("#cus_id").val(data.response.id_customer);



					$("#cus_del_pincode").val(data.response.pincode);

					$("#cus_del_address1").val(data.response.address1);

					$("#cus_del_country").val(data.response.id_country);

					$("#cus_del_state").val(data.response.id_state);

				}



				else if(ctrl_page[1]=='repair_order'){



					$("#cus_name").val(data.response.firstname + " - " + data.response.mobile);



					$("#cus_id").val(data.response.id_customer);

				}

				else if(ctrl_page[1]=='order'){



					$("#cus_name").val(data.response.firstname + " - " + data.response.mobile);



					$("#cus_id").val(data.response.id_customer);

				}

				else if(ctrl_page[1]=='receipt'){



					$("#name").val(data.response.firstname + " - " + data.response.mobile);



					$("#id_customer").val(data.response.id_customer);

				}



                else if(ctrl_page[1]=='bill_split')



                {



                        var catRow=$('#row_active_id').val();



                    	$('#'+catRow).find('.id_customer').val(data.response.id_customer);



                    	$('#'+catRow).find('.cus_name').val(data.response.firstname + " - " + data.response.username);



                }

				if (ctrl_page[1] == "bill_split"){
					$('#confirm-add').modal('hide');
				} else {
					$('.offcanvas').offcanvas('hide');
				}




			}else{



				alert(data.message);



			}



        }



     });



}



function update_customer(){ ////replace the function



    var billing_for = $("input[name='billing[billing_for]']:checked").val();



    var gender = $("input[name='customer[gender]']:checked").val();



	var vip = $("input[name='customer[vip]']:checked").val();



	var form_data = new FormData();



	form_data.append('cusName',$('#cus_first_name').val());



	form_data.append('cusMobile',$('#cus_mobile').val());



	form_data.append('cusBranch',$('#id_branch').val());



	form_data.append('id_village',$('#sel_village').val());



	form_data.append('gst_no',$('#gst_no').val());



	form_data.append('cus_type',billing_for==2 ? 2 :1);



	form_data.append('id_country',$('#country').val());



	form_data.append('id_state',$('#state').val());



	form_data.append('id_city',$('#city').val());



	form_data.append('address1',$('#address1').val());



	form_data.append('address2',$('#address2').val());



	form_data.append('address3',$('#address3').val());



	form_data.append('pincode',$('#pin_code_add').val());



	form_data.append('mail',$('#cus_email').val());



	form_data.append('cust_img',$("#cus_image")[0].files[0]);



	form_data.append('pan_no',$('#pan').val());



	form_data.append('aadharid',$('#aadharid').val());



	if(ctrl_page[1]=='repair_order' || ctrl_page[1]=='order'){

		form_data.append('id_customer',$('#cus_id').val());

	}else{

		form_data.append('id_customer',$('#id_customer').val());

	}







	form_data.append('title',$('#title').val());



	form_data.append('gender',gender);



	form_data.append('is_vip',vip);



	form_data.append('id_profession',$('#professionval').val());



	form_data.append('date_of_birth',$('#date_of_birth').val());



	form_data.append('date_of_wed',$('#date_of_wed').val());



	form_data.append('dl_no',$('#dl').val());



	form_data.append('pp_no',$('#pp').val());



	my_Date = new Date();



	$.ajax({



        url: base_url+'index.php/admin_ret_billing/updateNewCustomer/?nocache=' + my_Date.getUTCSeconds(),



        dataType: "json",



        method: "POST",



		data:form_data,



		cache : false,



		enctype: 'multipart/form-data',



		contentType : false,



		processData : false,



       //Need to update login branch id here from session



        success: function (data) {



			if(data.success == true){



				if(ctrl_page[1]=='billing'){

					$("#cus_state").val($('#state').val());



					$("#cus_country").val($('#country').val());



					$("#bill_cus_name").val(data.response.firstname + " - " + data.response.mobile);



					$("#bill_cus_id").val(data.response.id_customer);



					calculateSaleBillRowTotal();



					calculate_salesReturn_details();



					$.toaster({ priority : 'success', title : 'Success!', message : ''+"</br>"+'Customer Updated SuccessFully.'});



					// $('.offcanvas').offcanvas('hide');



					get_customer_address_det();



				}else if(ctrl_page[1]=='estimation'){



					$("#est_cus_name").val(data.response.firstname + " - " + data.response.mobile);



					$("#cus_id").val(data.response.id_customer);



					$("#cus_del_pincode").val(data.response.pincode);

					$("#cus_del_address1").val(data.response.address1);

					$("#cus_del_country").val(data.response.id_country);

					$("#cus_del_state").val(data.response.id_state);





				}



				else if(ctrl_page[1]=='repair_order'){



					$("#est_cus_name").val(data.response.firstname + " - " + data.response.mobile);



					$("#cus_id").val(data.response.id_customer);

				}

				else if(ctrl_page[1]=='order'){



					$("#cus_name").val(data.response.firstname + " - " + data.response.mobile);



					$("#cus_id").val(data.response.id_customer);

				}

				else if(ctrl_page[1]=='receipt'){



					$("#name").val(data.response.firstname + " - " + data.response.mobile);



					$("#id_customer").val(data.response.id_customer);

				}



				// $('#confirm-add').modal('toggle');



				$('.offcanvas').offcanvas('hide');



			}else{



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message});



			}



        }



     });



}





$('#cus_image,#ed_cus_image').on('change', function () {



	validateImage(this);



});





function validateImage() {



	if (arguments[0].id == 'cus_image') {



		var preview = $('#cus_img_preview');



	}



	else if (arguments[0].id == 'ed_cus_image') {



		var preview = $('#ed_cus_img_preview');



	}



	if (arguments[0].files[0].size > 1048576) {



		alert('File size cannot be greater than 1 MB');



		arguments[0].value = "";



		preview.css('display', 'none');



	}



	else {



		var fileName = arguments[0].value;



		var ext = fileName.substring(fileName.lastIndexOf('.') + 1);



		ext = ext.toLowerCase();



		if (ext != "jpg" && ext != "png" && ext != "jpeg") {



			alert("Upload JPG or PNG Images only");



			arguments[0].value = "";



			preview.css('display', 'none');



		} else {



			var file = arguments[0].files[0];



			var reader = new FileReader();



			reader.onloadend = function () {



				preview.prop('src', reader.result);



			}



			if (file) {



				reader.readAsDataURL(file);



				preview.css('display', '');



			}



			else {



				preview.prop('src', '');



				preview.css('display', 'none');



			}



		}



	}



}





//common add customer modal







$(document).on('keyup', '#pin_code_add,#ed_cus_pin_code_add', function () {



	var pin_code = $("#pin_code_add").val() || $('#ed_cus_pin_code_add').val();

	if (pin_code.length == 6) {

		get_villages_by_pincode(pin_code)



	}else{

		$('#id_village').val('');

		$('#ed_id_village').val('');

		$('#sel_village').select2("val",'');

		$('#ed_sel_village').select2("val",'');

		$('#sel_village option').remove();

		$('#ed_sel_village option').remove();



	}





});





function add_new_village(village, pincode) {

	my_Date = new Date();

	$("div.overlay").css("display", "block");

	$.ajax({

		url: base_url + "index.php/admin_ret_estimation/get_village?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),

		data: { 'village_name': village, 'pincode': pincode },

		type: "POST",

		dataType: "JSON",

		async: false,

		success: function (data) {

			console.log(data);

	$("div.overlay").css("display", "none");



			if (data.status) {





				var ins_id = data.ins_id;



				// $('#pin_code_add').val(pincode);

				var newVillage = village;

				var $newOption = $('<option>', {

					value: ins_id,

					text: newVillage

				});



				$.toaster({ priority: 'success', title: 'Success!', message: '' + "</br>" + data.message });





				// $('#sel_village').append($newOption);



				$('#sel_village').select2("val",(ins_id!='' ? ins_id: ''));



				if(ins_id!='')

				{

					$('#ed_id_village').val(ins_id);

					$('#id_village').val(ins_id);



				}

				$('#sel_village').select2("val",(ins_id!='' ? ins_id: ''));





				// $('#sel_village').val(ins_id).trigger('change');

				$('#confirm-area').modal('hide');

				$("div.overlay").css("display", "none");

				/* if($('#ed_cus_pin_code_add').val().length ==6){

					get_villages_by_pincode($('#ed_cus_pin_code_add').val())

				}else  */

				if($('#pin_code_add').val().length==6){

					get_villages_by_pincode($('#pin_code_add').val())

				}





			} else {

				$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + data.message });



			}



		},

		error: function (error) {

			console.log(error);

			$("div.overlay").css("display", "none");

		}



	});

}





function get_villages_by_pincode(pincode) {



	my_Date = new Date();

	$.ajax({

		url: base_url + "index.php/admin_ret_estimation/get_village_by_pincode?nocache=" + my_Date.getUTCSeconds(),

		dataType: "json",

		type: 'POST',

		data: { 'pin_code': pincode },

		success: function (data) {



			if (data.length > 0) {

				var id_village = $('#id_village').val();

				var ed_id_village = $('#ed_id_village').val();

				$('#sel_village option').remove();

				$('#ed_sel_village option').remove();



				$("#sel_village,#ed_sel_village").select2({

					placeholder: "Select Area",

					allowClear: true

				});

				$.each(data, function (key, item) {

					$("#sel_village,#ed_sel_village").append(

						$("<option></option>")

							.attr("value", item.id_village)

							.text(item.village_name)

					);

				});



				if ($('#sel_village').length > 0) {

					$('#sel_village').select2("val", (id_village != '' ? id_village : ''));

				}

				/* if ($('#ed_sel_village').val().length > 0 ) {

					$('#ed_sel_village').select2("val", (ed_id_village != '' ? ed_id_village : ''));

				} */

				if(id_village!=''){

					$('#sel_village').select2("val", (id_village != '' ? id_village : ''));

				}

				/* if(ed_id_village!=''){

					$('#ed_sel_village').select2("val", (ed_id_village != '' ? ed_id_village : ''));



				} */

				$("body").on("hidden.bs.modal", function () { // to use multiple model in one page

					if ($(".modal.in").length > 0) {

						$("body").addClass("modal-open")

					}

				});

			} else {



				$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'No Area Found For this Pincode' });

				$('#sel_village option').remove();

				// $('#ed_sel_village option').remove();





				$('#id_village').val('');

				$('#ed_id_village').val('');



				$("#sel_village,#ed_sel_village").select2({

					placeholder: "Select Area",

					allowClear: true

				});





			}



		}

	});



}



$(document).ready(function(){





    $('.add_new_village').on('click', function () {

	if ($('#pin_code_add').val().length == 6) {

		$('#confirm-area').modal('show');

		var pin_code = $('#pin_code_add').val();

		$('#new_pincode').val(pin_code);

	} else if ($('#ed_cus_pin_code_add').val().length == 6) {

		$('#confirm-area').modal('show');

		var pin_code = $('#ed_cus_pin_code_add').val();

		$('#new_pincode').val(pin_code);

	} else {

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter Valid Pin Code' });



	}

});



$('#add_newcutomer').click(function (event) {



	var allow_submit    = true;



	var esti_for = $("input[name='estimation[esti_for]']:checked").val();







	if ($('#cus_first_name').val() == '') {



		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter the Firstname..' });



		allow_submit = false;



		return false;



	}



	else if ($('#cus_mobile').val() == '' || $('#cus_mobile').val() == null) {



		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter the Mobile Number..' });



		allow_submit = false;

		return false;



	}
	
	else if ($('#branch_select').val() == '' || $('#branch_select').val() == null && $('#id_branch').val() == '') {

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please select branch..' });

		allow_submit = false;

		return false;

	}



	else if ($('#country').val() == '' || $('#country').val() == null && $('#id_country').val() == '') {



		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Select the Country..' });



		allow_submit = false;

		return false;



	}



	else if ($('#state').val() == '' || $('#state').val() == null) {



		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Select the State..' });



		allow_submit = false;

		return false;



	} else if ($('#city').val() == '' || $('#city').val() == null) {



		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Select the City..' });



		allow_submit = false;

		return false;



	}



	else if ($('#sel_village').val() == '' || $('#sel_village').val() == null && $('#country').val() == 101) {



		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Select the Area..' });



		allow_submit = false;

		return false;



	}



	else if ($('#address1').val() == '') {



		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter the Address..' });



		allow_submit = false;

		return false;



	}



	else if ($('#pin_code_add').val() == '' && $('#country').val() == 101) {



		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter the Pincode..' });



		allow_submit = false;

		return false;



	}



	else if ($('#pin_code_add').val() != '' && ($('#pin_code_add').val().length != 6)) {



		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter the Valid Pincode..' });



		allow_submit = false;

		return false;



	}



	else if ($('#country').val() != 101) {

		if ($('#pp').val() == '') {

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter the Passport No..' });



			allow_submit = false;

			return false;

		}

	}



	else if (esti_for == 3) {



		if ($('#gst_no').val() == '') {



			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter the GST No..' });



			allow_submit = false;

			return false;



		} else {



			var reggst = new RegExp('^[0-9]{2}[a-zA-Z]{4}([1-9]|[a-zA-Z]){1}[0-9]{4}[a-zA-Z]{1}([1-9]|[a-zA-Z]){3}$');



			if (!reggst.test($('#gst_no').val())) {



				$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter the Valid GST No..' });



				allow_submit = false;

				return false;



			}



		}



	}



	if(ctrl_page[1]=='billing' || ctrl_page[1]=='estimation' || ctrl_page[1]=='receipt' || ctrl_page[1]=='bill_split'){

		if(allow_submit)

		{

			if($('#id_customer').val() == "")

			{

				add_customer();

			}

			else

			{

				update_customer();

			}

		}

	}else if(ctrl_page[1]=='repair_order' || ctrl_page[1]=='order'){

		if(allow_submit)

		{

			if($('#cus_id').val() == "")

			{

				add_customer();

			}

			else

			{

				update_customer();

			}

		}

	}









	$('#cus_first_name').val('');



	$('#cus_mobile').val('');







});



$('#add_new_area').click(function (event) {

	if ($('#village').val() == '' || $('#village').val() == null) {

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter the Village..' });

		return false;

	}

	else if ($('#new_pincode').val() == '') {

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter the Pincode..' });

		return false;

	}

	else if ($('#new_pincode').val() != '' && ($('#new_pincode').val().length != 6)) {

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter the Valid Pincode..' });

		return false;

	}



	add_new_village($('#village').val(), $('#new_pincode').val());

	$('#village').val('');

	$('#new_pincode').val('');



});



    $('#profession').on('change',function()



    {



    	if(this.value!='')



    	{



    		$('#professionval').val(this.value);



    	}



    	else



    	{



    		$('#professionval').val('');



    	}



    });





    $('.new_village_close,.add_new_area').on('click', function () {

	$("body").on("hidden.bs.modal", function () { // to use multiple model in one page

		if ($(".modal.in").length > 0) {

			$("body").addClass("modal-open")

		}

	});

    });





$('#new_pincode').on('change', function () {

	if (this.value.length != 6) {

		$('#new_pincode').val("");

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter Valid PIN Code' });

	}

});





})







function get_profession() {





	$.ajax({



		type: 'GET',



		url: base_url + 'index.php/admin_settings/get_profession',



		dataType: 'json',



		success: function (data) {



			$.each(data, function (key, data) {



				$('#profession').append(



					$("<option></option>")



						.attr("value", data.id_profession)



						.text(data.name)



				);



				$('#ed_profession').append(



					$("<option></option>")



						.attr("value", data.id_profession)



						.text(data.name)



				);



			});



			if ($("#profession").length > 0) {



				$("#profession").select2("val", ($('#professionval').val() != null ? $('#professionval').val() : ''));



			}



			if ($("#ed_profession").length > 0) {



				$("#ed_profession").select2("val", ($('#ed_professionval').val() != '' ? $('#ed_professionval').val() : ''));



			}



			$('.overlay').css('display', 'none');



		},



		error: function (error) {



			$("div.overlay").css("display", "none");



		}



	});



}







//common add customer modal



/*Validation for PAN , GST , AADHAR*/



$(document).on('change','#pan,#ed_pan_no,#kyc_pan,#pan_no',function(){

	if(this.value!='')

	{
		var id_customer = $('.cus_id').val();
		var regexp = /^[a-zA-Z]{5}\d{4}[a-zA-Z]{1}$/;

		if(!regexp.test(this.value))

		{

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Enter The Valid PAN No.." });

			$("#pan,#ed_pan_no,#kyc_pan,#pan_no").focus();

			$("#pan,#ed_pan_no,#kyc_pan,#pan_no").val("");

		}else

		{
			checkPANAvail(this.value, id_customer);
		}

	}

});

function checkPANAvail(pan_number, id_customer)
{ 

  $("div.overlay").css("display", "block");

  $.ajax({

	type: 'POST',
	data:{'pan_number':pan_number, 'id_customer' : id_customer },
	url:  base_url+'index.php/admin_ret_estimation/pan_available',
	async: false,
	dataType: 'json',

	success: function(data) 

	{

		if(data.status==false)

		{

			$('#pan,#ed_pan_no,#kyc_pan,#pan_no').val('');

			$('#pan,#ed_pan_no,#kyc_pan,#pan_no').focus();

			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message, settings: { timeout: 5000 }});
			$('#updPanNo').prop('disabled', true);
		}

		$("div.overlay").css("display", "none");  

	},



	error:function(error)  

	{

		$("div.overlay").css("display", "none"); 

	}

  });	



}


$(document).on('change','#gst_no,#gst_number,#gst_num',function(){
	if(this.value!='')

	{
		var id_customer = $('.cus_id').val();
		var gst = $(this).val();

		var gstinformat = new RegExp('^[0-9]{2}[a-zA-Z]{4}([1-9]|[a-zA-Z]){1}[0-9]{4}[a-zA-Z]{1}([1-9]|[a-zA-Z]){3}$');

		if (!gstinformat.test(gst)) {
			$('#gst_no,#gst_number,#gst_num').val("");
			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter Valid GST NO', settings: { timeout: 5000 } });
			$('#gst_no,#gst_number,#gst_num').focus();
		}

		else

		{
			checkGSTAvail(this.value, id_customer);
		}

	}

});

function checkGSTAvail(gst_number, id_customer)
{ 

  $("div.overlay").css("display", "block");

  $.ajax({

	type: 'POST',
	data:{'gst_number':gst_number, 'id_customer' : id_customer },
	url:  base_url+'index.php/admin_ret_estimation/gst_available',
	async: false,
	dataType: 'json',

	success: function(data) 

	{

		if(data.status==false)

		{
			$('#gst_no,#gst_number,#gst_num').val('');
			$('#gst_no,#gst_number,#gst_num').focus();
			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message, settings: { timeout: 5000 }});
			$('#updGstNo').prop('disabled',true);
		}

		$("div.overlay").css("display", "none");  

	},



	error:function(error)  

	{

		$("div.overlay").css("display", "none"); 

	}

  });	



}





$(document).on('change','#aadharid,#kyc_aadhar,#aadhar_no,#aadhaar_no',function(){
	if(this.value!='')

	{
		var id_customer = $('.cus_id').val();
		var addhar = $(this).val();
		if (addhar.indexOf('-') !== -1){
			addhar = addhar.replace(/-/g,'');
		}
		var regexp = /^\d{12}$/;

		if(!regexp.test(addhar))

		{
			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Enter The Valid AADHAR No..", settings: { timeout: 5000 } });
			$("#aadharid,#kyc_aadhar,#aadhar_no,#aadhaar_no").focus();
			$("#aadharid,#kyc_aadhar,#aadhar_no,#aadhaar_no").val("");
		}else

		{
			checkAADHARAvail(addhar, id_customer);
		}
	}
});

function checkAADHARAvail(aadhar_number, id_customer)
{ 

  $("div.overlay").css("display", "block");

  $.ajax({

	type: 'POST',
	data:{'aadhar_number':aadhar_number, 'id_customer' : id_customer },
	url:  base_url+'index.php/admin_ret_estimation/aadhar_available',
	async: false,
	dataType: 'json',

	success: function(data) 

	{

		if(data.status==false)

		{
			$('#aadharid,#kyc_aadhar,#aadhar_no,#aadhaar_no').val('');
			$('#aadharid,#kyc_aadhar,#aadhar_no,#aadhaar_no').focus();
			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message, settings: { timeout: 5000 }});
			$("#updAadharNo").prop("disabled", true);
		}

		$("div.overlay").css("display", "none");  

	},



	error:function(error)  

	{

		$("div.overlay").css("display", "none"); 

	}

  });	



}



/*Validation for PAN , GST , AADHAR*/



// Branch based on login branch

function getBranchName(){ 

	$.ajax({

		type: 'GET',

		url: base_url+'index.php/branch/branchname_list',

		dataType:'json',

		success:function(data){

		   $("#branch_select option").remove();



		   var id_branch =  $('#id_branch').val();

		   

		   

		   $("#branch_select,#sync_branch,.ret_branch,.branch_filter").select2(

			$("<option></option>")

				   .attr("value", "")

				   .text('Select Branch' )

				   );

			

		   if((data.profile==1 || data.profile==2 || data.profile==3))

		   {

			   

			   

				var add_all = ((ctrl_page[1]=='close' || ctrl_page[1]=='add' || ctrl_page[2]=='add' || ctrl_page[1]=='edit' || ctrl_page[2]=='edit' || ctrl_page[1]=='get_yet_to_issue' || ctrl_page[1]=='gift_report' || ctrl_page[1]=='admin_ret_reports' ||  (ctrl_page[1]=='reorder_settings' && ctrl_page[2]=='list') || (ctrl_page[0]=='admin_manage' && ctrl_page[1]=='gift_issue_form') || ctrl_page[2] == 'dynamic' || ctrl_page[0]=='dashboard') ? false : true);

			   if(add_all === true && branchSettings == 1 && loggedInBranch == 0){

				   $("#branch_select,.branch_filter").append(						

				   $("<option></option>")						

				   .attr("value", 0)						  						  

				   .text('All' )

				   );

			   }

		   }

		   if(ctrl_page[1] == 'nontag_receipt' && ctrl_page[2] == 'add'){

				$.each(data.branch, function (key, item) {

					if(item.id_branch != 1){

						$("#branch_select").append(

						$("<option></option>")

						.attr("value", item.id_branch)

						.text(item.name )

						);
					}
				});

		    }

		else {

		 $.each(data.branch, function (key, item) {

				$("#branch_select,#sync_branch,.ret_branch,.branch_filter,#ed_branch_select").append(

				$("<option></option>")

				.attr("value", item.id_branch)

				.text(item.name )

				);

			});

			}

			

			$("#branch_select,#sync_branch,.ret_branch,.branch_filter,#ed_branch_select").select2({

				placeholder: "Select Branch",

				// allowClear: true,

				width: '100%'

			});

		

		if($(".ret_branch").length){

			   $(".ret_branch").select2("val",(id_branch!='' && id_branch>0?id_branch:''));

		   }



		   if($("#branch_select").length){

	   

			   if(ctrl_page[0]=='account' && (ctrl_page[1]=='add' || ctrl_page[1]=='edit' ))

			   {

				   if(ctrl_page[1]=='add'){

					   var login_branch=$("#emp_branch").val();

				   }else{

					   var login_branch =  $('#id_branch').val();

				   }

				   if(login_branch>0)

				   {

					   $("#branch_select").select2("val",(login_branch!='' && login_branch>0?login_branch:''));

				   }

			   }

			   if(ctrl_page[0]=='account' && ctrl_page[1]=='close')

			   {

				   var login_branch=$("#id_branch").val();

				   if(login_branch>0)

				   {

					   $("#branch_select").select2("val",(login_branch!='' && login_branch>0?login_branch:''));

				   }

			   }

			   if(ctrl_page[0]=='payment' && ctrl_page[1]=='add')

			   {

				   var login_branch=$("#login_branch").val();



				   if(login_branch>0)

				   {

					   $("#branch_select").select2("val",(login_branch!='' && login_branch>0?login_branch:''));

				   }

			   }

			   if(ctrl_page[0]=='customer' && ctrl_page[1]=='add')

			   {

				   var cus_branch = $('#emp_branch').val();

				   $("#branch_select").select2("val",(cus_branch!='' && cus_branch>0?cus_branch:''));

			   }

			   if((ctrl_page[1]=='receipt' || ctrl_page[1] == 'issue' || ctrl_page[1] == 'stock_age' || ctrl_page[1] == 'order' || ctrl_page[1] == 'ret_section' || ctrl_page[1] == 'estimation' || ctrl_page[1] == 'tagging')&& (ctrl_page[2]=='add' || ctrl_page[2]=='list' || ctrl_page[2]=='edit' || ctrl_page[2]=='dynamic' || ctrl_page[2] == 'bulk_edit'))

				{

					var id_branch=$("#id_branch").val();

					if(id_branch>0)

					{

						$("#branch_select").select2("val",(id_branch!='' && id_branch>0?id_branch:''));
						

					}

				}

				if(ctrl_page[1]=='ret_section' && ctrl_page[2]=='list')

					{

						var ed_branch = $('#ed_branch_id').val();

						$("#ed_branch_select").select2("val",(ed_branch!='' && ed_branch>0?ed_branch:''));
					}
		   		}

				if(ctrl_page[1] == 'order' || ctrl_page[1] == 'estimation' || ctrl_page[2] == 'add' || ctrl_page[2] == 'edit'){
					var id_branch = $('#branch_select').val();
					$.each(data.branch, function (key, item) {
						if(id_branch == item.id_branch){
							$('#branch_id_country').val(item.id_country);
							$('#branch_id_state').val(item.id_state);
							$('#branch_id_city').val(item.id_city);
						}
						});
					$('#branch_select').on('change',function(){
					var id_branch = $('#branch_select').val();
					$.each(data.branch, function (key, item) {
						if(id_branch == item.id_branch){
							$('#branch_id_country').val(item.id_country);
							$('#branch_id_state').val(item.id_state);
							$('#branch_id_city').val(item.id_city);
						}
				});
			});
			}
				if(ctrl_page[1] == 'billing' && ctrl_page[2] == 'add'){
					var id_branch = $('#id_branch').val();
					$.each(data.branch, function (key, item) {
						if(id_branch == item.id_branch){
							$('#branch_id_country').val(item.id_country);
							$('#branch_id_state').val(item.id_state);
							$('#branch_id_city').val(item.id_city);
						}
						});
					$('#id_branch').on('change',function(){
					var id_branch = $('#id_branch').val();
					$.each(data.branch, function (key, item) {
						if(id_branch == item.id_branch){
							$('#branch_id_country').val(item.id_country);
							$('#branch_id_state').val(item.id_state);
							$('#branch_id_city').val(item.id_city);
						}
				});
			});
			}
		}
   });
}
// function get_country() {
//   $("#country option").remove();

//   $.ajax({
//     type: "GET",

//     url: base_url + "index.php/settings/company/getcountry",

//     dataType: "json",

//     success: function (country) {
//       cus_country = country;
// 			$.each(country, function (key, country) {
// 				var option = $("<option></option>")
// 					.attr("value", country.id)
// 					.attr("min_mob_len", country.min_mob_len)
// 					.attr("max_mob_len", country.max_mob_len)
// 					.text(country.name);
			
// 				// Set default selected value
// 				// if (country.is_default == 1) {
// 				// 	option.attr("selected", "selected");
// 				// 	$('#id_country').val(country.id);
// 				// 	$('#mob_no_len').val(country.mob_no_len);
// 				// }
			
// 				$('#country, #ed_cus_country').append(option);
// 			});
			
// 			// Determine the default selected country
// 			var id_country = $('#id_country').val() !== '' ? $('#id_country').val() : $('#branch_id_country').val();
// 			var ed_id_country = $('#ed_id_country').val();
			
// 			// Ensure the dropdowns have the correct selected values
// 			if ($("#country").length && id_country) {
// 				$("#country").val(id_country).trigger("change");
// 			}
			
// 			if ($("#ed_cus_country").length && ed_id_country > 0) {
// 				$("#ed_cus_country").val(ed_id_country).trigger("change");
// 			}
			
//     },

//     error: function (error) {},
//   });
// }
// Common function for getting branch details
function getBranchDetails(id_branch) {
	$.ajax({
		url: base_url +"index.php/admin_manage/getBranchDetails",
		dataType: "json",
		method: "POST",
		async: 'false',
		data: { 'id_branch': id_branch },
		success: function (data) {
			if (
				(ctrl_page[1] == "order" || ctrl_page[1] == "estimation" || ctrl_page[1] == "repair_order" || ctrl_page[1] == "stock_issue" ) &&
				(ctrl_page[2] == "add" || ctrl_page[2] == "edit")
			) {
				var id_branch = $("#branch_select").val();
				$.each(data, function (key, item) {
					if (id_branch == item.id_branch) {
						$("#branch_id_country").val(item.id_country);
						$("#branch_id_state").val(item.id_state);
						$("#branch_id_city").val(item.id_city);
					}
				});
			}

			if ((ctrl_page[1] == "billing" && ctrl_page[2] == "add")  || (ctrl_page[1] == "bill_split")  || ctrl_page[1] == "receipt") {
				var id_branch = $("#id_branch").val();
				$.each(data, function (key, item) {
					if (id_branch == item.id_branch) {
						$("#branch_id_country").val(item.id_country);
						$("#branch_id_state").val(item.id_state);
						$("#branch_id_city").val(item.id_city);
					}
				});
			}

			if(ctrl_page[1] == "estimation" && (ctrl_page[2] == "add" || ctrl_page[2] == "edit")){
				var id_branch = $('#branch_select').val();
				$.each(data, function (key, item) {
					if (id_branch == item.id_branch) {
						$("#cmp_country").val(item.id_country);
						$("#cmp_state").val(item.id_state);
					}
				});
			}
		},
	});

	/* if(ctrl_page[0] == 'payment' && ctrl_page[1] == 'add'){
		var idBranch = $('#branch_select').val();
		// console.log(idBranch);
		$.ajax({
			type : 'POST',
			url : base_url + 'index.php/admin_manage/getEmployeeByBranch',
			data : { 'id_branch' : idBranch },
			dataType : 'json',
			success : function(data){
				// console.log(data);
				
				// console.log(data[0].id_employee);
				// $('#id_employee').val(data.id_employee);
				if(data.length > 0){
					$('#employee_select').empty();
					$.each(data, function(key, item){
						$('#employee_select').append(
							$("<option></option>")
								.attr("value", item.id_employee)
								.text(item.emp_data)
						);
					});  		
				}

				

				
			}
		})
		
		
	} */
}
// Common function for getting branch details

$("#id_branch, #branch_select").on("change", function (e) {
    var id_branch = this.value;
    if (id_branch != "" && id_branch != 0) {
        setTimeout(function() {
            getBranchDetails(id_branch);
        }, 500);
    }
});
// function get_city(id) {
//   $("#city option").remove();

//   $.ajax({
//     type: "POST",

//     data: { id_state: id },

//     url: base_url + "index.php/settings/company/getcity",

//     dataType: "json",

//     success: function (city) {

// 			$.each(city, function (key, city) {
// 				var option = $("<option></option>")
// 					.attr("value", city.id)
// 					.text(city.name);
			
// 				// Set default selected value
//         // if (city.is_default == 1) {
// 				// 	option.attr("selected", "selected");
// 				// 	$('#id_city').val(city.id);
//         // }
			
// 				$('#city, #ed_cus_city').append(option);
// 			});
			
// 			// Determine the default selected city
// 			var id_city = $('#id_city').val() != '' ? $('#id_city').val() : $('#branch_id_city').val() ;
// 			var ed_id_city = $('#ed_id_city').val();
			
// 			// Set the default selected value for #city dropdown
// 			if ($("#city").length && id_city) {
// 				$("#city").val(id_city).trigger("change");
// 			}
			
// 			// Set the default selected value for #ed_cus_city dropdown
// 			if ($("#ed_cus_city").length && ed_id_city > 0) {
// 				$("#ed_cus_city").val(ed_id_city).trigger("change");
// 			}
//     },

//     error: function (error) {},
//   });
// }
// function get_state(id) {
//   $("#state option").remove();

//   $.ajax({
//     type: "POST",

//     data: { id_country: id },

//     url: base_url + "index.php/settings/company/getstate",

//     dataType: "json",

//     success: function (state) {
//       // $.each(state, function (key, state) {
// 			// 	if (state.is_default == 1) {
// 			// 		$('#id_state').val(state.id);
// 			// 	}
// 			// 	$('#state,#ed_cus_state').append(
// 			// 		$("<option></option>")
// 			// 			.attr("value", state.id)
// 			// 			.text(state.name)
// 			// 	);
// 			// });
// 			// var id_state = $('#id_state').val() != ''? $('#id_state').val():$('#cmp_state').val() ;
// 			// var ed_id_state =$('#ed_id_state').val();
// 			// $("#state,#ed_cus_state").select2({
// 			// 	placeholder: "Enter State",
// 			// 	allowClear: true
// 			// });
// 			// if ($("#state").length) {
// 			// 	$("#state").select2("val", (id_state != '' ? id_state : ''));
// 			// }
// 			// if ($("#ed_cus_state").length) {
// 			// 	$("#ed_cus_state").select2("val", (ed_id_state != null && ed_id_state > 0 ? ed_id_state : ''));
// 			// }
			
// 			// $('#state,#ed_cus_state').select2({
//             //     dropdownParent: $('#demo'),  
//             //     placeholder: "Enter State",
//             //     allowClear: true
//             // });
// 			$.each(state, function (key, state) {
// 				var option = $("<option></option>")
// 					.attr("value", state.id)
// 					.text(state.name);
			
// 				// Set default selected value
//         // if (state.is_default == 1) {
// 				// 	option.attr("selected", "selected");
// 				// 	$('#id_state').val(state.id);
//         // }
			
// 				$('#state, #ed_cus_state').append(option);
// 			});
			
// 			// Determine the default selected state
// 			var id_state = $('#id_state').val() !== '' ? $('#id_state').val() : $('#branch_id_state').val();
// 			var ed_id_state = $('#ed_id_state').val();
			
// 			// Set the default selected value for #state dropdown
// 			if ($("#state").length && id_state) {
// 				$("#state").val(id_state).trigger("change");
// 			}
			
// 			// Set the default selected value for #ed_cus_state dropdown
// 			if ($("#ed_cus_state").length && ed_id_state > 0) {
// 				$("#ed_cus_state").val(ed_id_state).trigger("change");
// 			}
			
// 			// $('#state,#ed_cus_state').select2({
//             //     dropdownParent: $('#demo'),  
//             //     placeholder: "Enter State",
//             //     allowClear: true
//             // });
			
//     },

//     error: function (error) {},
//   });
// }
// $(document).ready(function(){
//     $('#country,#ed_cus_country').on('change', function () {
//     	$('#id_country').val(this.value);
//     	// $('#cus_mobile').val("");
//     	var id_country = this.value;
//     	$.each(cus_country, function (key, item) {
//     		if (id_country == item.id) {
//     			$('#mob_no_len').val(item.mob_no_len);
//     		}
//     	});
//     	if (this.value != '') {
//     		get_state(this.value);
//     	}
//     });
//     $('#state,#ed_cus_state').on('change', function () {
//     	if (this.value != '') {
//     		get_city(this.value);
//     	}
//     });
// 	function get_all_branches() {

// 	my_Date = new Date();

// 	var branch_data = [];

// 	$.ajax({

// 		url: base_url + 'index.php/admin_ret_order/get_all_branch?nocache=' + my_Date.getUTCSeconds(),

// 		method: "get",

// 		dataType: "json",

// 		success: function (data) {

// 			var id = $("#select_branch").val();

// 			var filter_branch = $("#filter_branch").val();
			
// 			$("#branch_select").empty();

// 			$.each(data, function (key, item) {
				
// 					$("#select_branch").append(

// 						$("<option></option>")

// 							.attr("value", item.id_branch)

// 							.text(item.branch_name)

// 					);
// 					$("#branch_select").append(

// 						$("<option></option>")

// 							.attr("value", item.id_branch)

// 							.text(item.branch_name)

// 					);

// 					$("#branch_filter").append(

// 						$("<option></option>")

// 							.attr("value", item.id_branch)

// 							.text(item.branch_name)

// 					);
				

// 			});



// 			$("#select_branch").select2(

// 				{

// 					placeholder: "Assign To Branch",

// 					closeOnSelect: true

// 				});

// 			$("#branch_filter").select2(

// 				{

// 					placeholder: "Branch Filter",

// 					closeOnSelect: true

// 				});
// 			$("#select_branch").select2(

// 				{

// 					placeholder: "Select Branch",

// 					closeOnSelect: true

// 				});

// 			if ($("#select_branch").length) {

// 				$("#select_branch").select2("val", (id != '' && id > 0 ? id : ''));

// 			}



// 			if ($("#branch_filter").length) {

// 				$("#branch_filter").select2("val", (filter_branch != '' && filter_branch > 0 ? filter_branch : ''));

// 			}





// 			$(".overlay").css("display", "none");

// 			branch_data = data;

// 			// console.log(branch_data);
	
// 		}

// 	});
	
// 	return branch_data;
// }
// })
$(document).on('change','.pp_no,#pp_no', function () {
	var id_customer = $('.cus_id').val();
	var value = $(this).val();
	const min = 6;
	const max = 12;
	var passport_validate = value ? validatePassport(value) : false ; 
	if (value.length < min || value.length > max || !passport_validate) {
		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Enter The Valid Passport No.." });
		$("#pp,#pp_no").val("");
		$("#pp,#pp_no").focus();

	}
	else{

	checkPASSPORTAvail(this.value,id_customer);

	}
})
$(document).on('change','.dl_no,#driving_license_no', function () {
	var id_customer = $('.cus_id').val();
	var value = $(this).val();
	const min = 5, max = 20;
	var dl_validate = value ? validateDLno(value) : false;
	if (value.length < min || value.length > max || !dl_validate) {
		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Enter The Valid Driving License No.." });
		$("#dl,#driving_license_no").focus();
		$("#dl,#driving_license_no").val("");

	}
	else{

		checkDLAvail(this.value,id_customer);
	}
});

function checkPASSPORTAvail(passport_no,id_customer){

	$("div.overlay").css("display", "block");
	$.ajax({
		type: 'POST',
	data:{'passport_no':passport_no, 'id_customer' : id_customer },
	url:  base_url+'index.php/admin_ret_estimation/passport_available',
	async: false,
		dataType: 'json',
	success: function(data) 
	{
		if(data.status==false)
		{
			$('#pp_no,#pp').val('');
			$('#pp_no,#pp').focus();
			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message, settings: { timeout: 5000 }});
			
			}
			$("div.overlay").css("display", "none");
		},

	error:function(error)  
	{
			$("div.overlay").css("display", "none");
		}
	});

}
function checkDLAvail(dl_no,id_customer){

	$("div.overlay").css("display", "block");
	$.ajax({
		type: 'POST',
	data:{'dl_no':dl_no, 'id_customer' : id_customer },
	url:  base_url+'index.php/admin_ret_estimation/dl_available',
	async: false,
		dataType: 'json',
	success: function(data) 
	{
		if(data.status==false)
		{
			$('#driving_license_no,#dl').val('');
			$('#driving_license_no,#dl').focus();
			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message, settings: { timeout: 5000 }});
			 
			}
			$("div.overlay").css("display", "none");
		},

	error:function(error)  
	{
			$("div.overlay").css("display", "none");
		}
	});

}
function validateDLno(license) {
	license = license.toUpperCase().trim();
  
	// Accepts alphanumeric, dash, space, between 5 and 20 characters
	const pattern = /^[A-Z0-9\s\-]{5,20}$/;
	return pattern.test(license);
  }
  function validatePassport(passportNumber) {
	passportNumber = passportNumber.toUpperCase().trim();
  
	const pattern = /^[A-Z0-9\-]{6,12}$/; // Alphanumeric + dash, 6–12 characters
  
	return pattern.test(passportNumber);
  }
  function initializeBootstrapSelect(selector) {
    $(selector).selectpicker({
        size: 6,
        liveSearch: true,
        showSubtext: true,
		width: '100%',
        actionsBox: true // For multiple selects
    });
}

function get_country() {

	initializeBootstrapSelect("#country, #ed_cus_country");
	// Clear old options and show loading state
	$("#country, #ed_cus_country")
		.html('<option value="">Loading countries...</option>')
		.selectpicker('refresh');

	$.ajax({
		type: "GET",
		url: base_url + "index.php/settings/company/getcountry",
		dataType: "json",
		success: function (country) {
			cus_country = country;

			// Clear existing options again after loading
			$("#country, #ed_cus_country").empty();

			$.each(country, function (key, country) {
				var option = $("<option></option>")
					.attr("value", country.id)
					.attr("min_mob_len", country.min_mob_len)
					.attr("max_mob_len", country.max_mob_len)
					.text(country.name);

				// If country is default, set it as selected
				// if (country.is_default == 1) {
				// 	option.attr("selected", "selected");
				// 	$('#id_country').val(country.id);
				// 	$('#mob_no_len').val(country.mob_no_len);
				// }

				$('#country, #ed_cus_country').append(option);
			});

			// Refresh the bootstrap-select after adding options
			$('#country, #ed_cus_country').selectpicker('refresh');

			// Set selected values if present
			var id_country = $('#id_country').val() !== '' ? $('#id_country').val() : $('#branch_id_country').val();
			var ed_id_country = $('#ed_id_country').val();

			if ($("#country").length && id_country) {
				$("#country").selectpicker('val', id_country).trigger('change');
			}
			if ($("#ed_cus_country").length && ed_id_country > 0) {
				$("#ed_cus_country").selectpicker('val', ed_id_country).trigger('change');
			}
		},
		error: function (error) {
			console.error("Error loading countries:", error);
		}
	});
}

$(function () {
	$('#country').change(function () {
		const id = this.value;
		if (!id) return;

		const country = cus_country.find(c => c.id == id);
		if (country) $('#mob_no_len').val(country.mob_no_len);

		get_state(id);
	});

	$('#state').change(function () {
		if (this.value) get_city(this.value);
	});
});

function get_state(id) {
	initializeBootstrapSelect("#state, #ed_cus_state");
    // Clear old options and show loading state
    $("#state, #ed_cus_state")
        .html('<option value="">Loading states...</option>')
        .selectpicker('refresh');

    $.ajax({
        type: "POST",
        data: { id_country: id },
        url: base_url + "index.php/settings/company/getstate",
        dataType: "json",
        success: function(state) {
            // Clear existing options again after loading
            $("#state, #ed_cus_state").empty();

            $.each(state, function(key, state) {
                var option = $("<option></option>")
                    .attr("value", state.id)
                    .text(state.name);

                if (state.is_default == 1) {
                    option.attr("selected", "selected");
                    $('#id_state').val(state.id);
                }

                $('#state, #ed_cus_state').append(option);
            });

            // Refresh the bootstrap-select after adding options
            $('#state, #ed_cus_state').selectpicker('refresh');

            // Set selected values if present
            var id_state = $('#id_state').val() !== '' ? $('#id_state').val() : $('#branch_id_state').val();
            var ed_id_state = $('#ed_id_state').val();

            if ($("#state").length && id_state) {
                $("#state").selectpicker('val', id_state).trigger('change');
            }
            if ($("#ed_cus_state").length && ed_id_state > 0) {
                $("#ed_cus_state").selectpicker('val', ed_id_state).trigger('change');
            }
        },
        error: function(error) {
            console.error("Error loading states:", error);
        }
    });
}

function get_city(id) {
	initializeBootstrapSelect("#city, #ed_cus_city");
    // Clear old options and show loading state
    $("#city, #ed_cus_city")
        .html('<option value="">Loading cities...</option>')
        .selectpicker('refresh');

    $.ajax({
        type: "POST",
        data: { id_state: id },
        url: base_url + "index.php/settings/company/getcity",
        dataType: "json",
        success: function(city) {
            // Clear existing options again after loading
            $("#city, #ed_cus_city").empty();

            // Add placeholder as the first option
            var placeholder = $('<option disabled selected value="">Select City</option>');
            $("#city, #ed_cus_city").append(placeholder);

            $.each(city, function(key, city) {
                var option = $("<option></option>")
                    .attr("value", city.id)
                    .text(city.name);

                $('#city, #ed_cus_city').append(option);
            });

            // Refresh the bootstrap-select after adding options
            $('#city, #ed_cus_city').selectpicker('refresh');

            // Set selected values if present
            var id_city = $('#id_city').val() != '' ? $('#id_city').val() : $('#branch_id_city').val() ;
            var ed_id_city = $('#ed_id_city').val() != '' ? $('#ed_id_city').val() : $('#branch_id_city').val() ;

            if ($("#city").length && id_city) {
                $("#city").selectpicker('val', id_city).trigger('change');
            }
            if ($("#ed_cus_city").length && ed_id_city > 0) {
                $("#ed_cus_city").selectpicker('val', ed_id_city).trigger('change');
            }
        },
        error: function(error) {
            console.error("Error loading cities:", error);
        }
    });
}