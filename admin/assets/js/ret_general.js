var customerattributes = {
    mantory: [
        { is_vip: "no" },
        { is_title: "yes" },
        { is_cus_first_name: "yes" },
        { is_cus_gender: "no" },
        { is_cus_mobile: "yes" },
        { is_cus_email: "no" },
        { is_country: "yes" },
        { id_state: "yes" },
        { id_city: "yes" },
        { is_address1: "yes" },
        { is_address2: "yes" },
        { is_address3: "no" },
        { is_pin_code_add: "no" },
        { is_sel_village: "no" },
        { is_profession: "no" },
        { is_date_of_birth: "no" },
        { is_date_of_wed: "no" },
        { is_gst_no: "no" },
        { is_pan: "no" },
        { is_aadharid: "no" },
        { is_dl: "no" },
        { is_pp: "no" }
    ],
    default_value: []
};

var path =  url_params();


var ctrl_page = path.route.split('/');

var required_otp_approval = 1;

var branch_details=[];

var cus_country = [];

$(document).ready(function() {


	switch (ctrl_page[1]) {
		case 'netbanking_collection_report':
			get_sch_code();
		break;	
		}
	switch (ctrl_page[1]) {
		case 'card_collection_report':
			get_sch_code();
		break;	
		}
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
	setTimeout(function() {
		if ($('#id_branch').val() && ['estimation', 'order', 'billing', 'receipt'].includes(ctrl_page[1])) {
			get_branches();
			$.each(branch_details, function(key, items) {
				if ($('#id_branch').val() == items.id_branch) {
					$('#cmp_state').val(items.id_state);
					$('#cmp_country').val(items.id_country);
					$('#cmp_city').val(items.id_city);
				}
			});
		}
	},2000);



});



// Branch Name

$(document).ready(function(){

    var isAdminRet = window.location.href.includes("admin_ret");

	if($('#branch_set').val()==1 && isAdminRet){

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



// PUR-CLT02: Pass event to validateOrderImages so event.target.files works in all browsers
$('#order_images_new').on('change',function(e){

	validateOrderImages(e);

});







// PUR-CLT02: Accept event parameter (cross-browser fix) and use Promise.all instead of setTimeout
function validateOrderImages(e)

{

		var preview = $('#order_images');

		// PUR-CLT02: Use direct DOM access as primary method, event as fallback
		var files = $('#order_images_new')[0].files;

		var compressionPromises = []; // PUR-CLT02: Collect promises for proper async handling


		 for (var i = 0; i < files.length; i++)

		 {

			    const compress           = new Compress();



			    const product_images     = [files[i]];



			    var promise = compress.compress(product_images, {

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

			compressionPromises.push(promise); // PUR-CLT02: Track each compression promise

		  }

		  // PUR-CLT02: Wait for ALL compressions to finish, then render previews and save to localStorage
		  Promise.all(compressionPromises).then(function(){

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

		  });



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
    gender = (typeof gender !== 'undefined') ? gender : '';  // no radio checked → NULL in DB

	var vip = $("input[name='customer[vip]']:checked").val();
	vip = (typeof vip !== 'undefined') ? vip : 0;  // fallback to 0 (No)



	var form_data = new FormData();



	form_data.append('cusName',$('#cus_first_name').val());



	form_data.append('cusMobile',$('#cus_mobile').val());



	form_data.append('cusBranch',$('#id_branch').val());



	form_data.append('id_village',$('#sel_village').val());



	form_data.append('gst_no',$('#gst_no').val());



	// cus_type: esti_for=1 → Individual (1), esti_for=3 → Company (2)
	// billing_for=2 → Company (2), otherwise Individual (1)
	var derived_cus_type;
	if (ctrl_page[1] === 'estimation') {
		derived_cus_type = (billing_for == 3) ? 2 : 1;
	} else {
		derived_cus_type = (billing_for == 2) ? 2 : 1;
	}
	form_data.append('cus_type', derived_cus_type);



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
                
                $.toaster({ priority : 'success', title : 'Success!', message : ''+"</br>"+'Customer Added SuccessFully.'});



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

				else if(ctrl_page[1]=='stock_issue'){

					// Populate the customer search field and store the customer ID
					$("#est_cus_name").val(data.response.firstname + " - " + data.response.mobile);

					$("#cus_id").val(data.response.id_customer);

				}


				else if(ctrl_page[0]=='admin_ret_tag_parts'){
					// Partly Sale: fill the active child row
					if (window._tsActiveChildRow) {
						var $row = window._tsActiveChildRow;
						var newCusId = data.response.id_customer;
						// Check duplicate across other child rows
						var dupFound = false;
						$('.ts-child-row').not($row).each(function() {
							if ($(this).find('.ts-cus-id').val() == newCusId) { dupFound = true; return false; }
						});
						if (dupFound) {
							$.toaster({ priority: 'warning', title: 'Duplicate', message: 'This customer is already assigned to another part' });
						} else {
							$row.find('.ts-cus-id').val(newCusId);
							$row.find('.ts-cus-mobile').val(data.response.firstname + '-' + data.response.mobile);
							$row.find('.ts-edit-cus-btn').show();
						}
						window._tsActiveChildRow = null;
					}
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

				$('.offcanvas').offcanvas('hide');




			}else{



				alert(data.message);



			}



        }



     });



}



function update_customer(){ ////replace the function



    var billing_for = $("input[name='billing[billing_for]']:checked").val();



    var gender = $("input[name='customer[gender]']:checked").val();
    gender = (typeof gender !== 'undefined') ? gender : '';  // no radio checked → NULL in DB

	var vip = $("input[name='customer[vip]']:checked").val();
	vip = (typeof vip !== 'undefined') ? vip : 0;  // fallback to 0 (No)



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

				else if(ctrl_page[0]=='admin_ret_tag_parts'){
					// Partly Sale: update the active child row
					if (window._tsActiveChildRow) {
						var $row = window._tsActiveChildRow;
						$row.find('.ts-cus-id').val(data.response.id_customer);
						$row.find('.ts-cus-mobile').val(data.response.firstname + '-' + data.response.mobile);
						$row.find('.ts-edit-cus-btn').show();
						window._tsActiveChildRow = null;
					}
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







$(document).on('keyup', '#pin_code_add,#ed_cus_pin_code_add,#branch_pincode,#ed_pincode', function () {



	var pin_code = $("#pin_code_add").val() || $('#ed_cus_pin_code_add').val() || $("#branch_pincode").val() || $("#ed_pincode").val();

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





/* function add_new_village(village, pincode) {

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

				$("div.overlay").css("display", "none"); */

				/* if($('#ed_cus_pin_code_add').val().length ==6){

					get_villages_by_pincode($('#ed_cus_pin_code_add').val())

				}else  */

				/* if($('#pin_code_add').val().length==6){

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

} */





/* function get_villages_by_pincode(pincode) {



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
					$('#sel_village').val(id_village || '').trigger('change');
					$('#sel_village').trigger('change.select2');

				} */

				/* if ($('#ed_sel_village').val().length > 0 ) {

					$('#ed_sel_village').select2("val", (ed_id_village != '' ? ed_id_village : ''));

				} */

				/* if (id_village != '') {
					// This block is redundant if the first block already executed
					$('#sel_village').select2("val", id_village);
					// Ensure change event is triggered here as well (if element exists)
					$('#sel_village').trigger('change.select2');
				} */

				/* if(ed_id_village!=''){

					$('#ed_sel_village').select2("val", (ed_id_village != '' ? ed_id_village : ''));



				} */

				/* $("body").on("hidden.bs.modal", function () { // to use multiple model in one page

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



} */



$(document).ready(function(){





    $('.add_new_village').on('click', function () {

		if (ctrl_page[0] == 'branch' && ctrl_page[1] == 'list') {
			if ($('#branch_pincode').val().length == 6) {

				$('#confirm-area').modal('show');

				var pin_code = $('#branch_pincode').val();

				$('#new_pincode').val(pin_code);

			} else {

				$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter Valid Pin Code' });



			}
		} else {
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
	}

});



$('#add_newcutomer').click(function (event) {

    event.preventDefault(); // button is <a href="#"> — prevent # appending to URL

    var esti_for = $("input[name='estimation[esti_for]']:checked").val();

	var allow_submit = true;
	var configArray = (typeof customerattributes !== 'undefined' && Array.isArray(customerattributes.mantory)) ? customerattributes.mantory : [];

	function getMantoryValue(key) {
		var item = configArray.find(function(obj) { return obj.hasOwnProperty(key); });
		return item ? item[key] : "no";
	}

	function validateField(value, configKey, message) {
		if (getMantoryValue(configKey) === "yes" && (value === '' || value === null || value === undefined)) {
			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + message });
			allow_submit = false;
			return false;
		}
		return true;
	}

	if (!validateField($("input[name='customer[vip]']:checked").val(), 'is_vip', 'Select VIP status..')) return false;
	if (!validateField($('#title').val(), 'is_title', 'Select Title..')) return false;
	if (!validateField($('#cus_first_name').val(), 'is_cus_first_name', 'Enter the Firstname..')) return false;
	if (!validateField($("input[name='customer[gender]']:checked").val(), 'is_cus_gender', 'Select Gender..')) return false;
	if (!validateField($('#cus_mobile').val(), 'is_cus_mobile', 'Enter the Mobile Number..')) return false;
	if (!validateField($('#cus_email').val(), 'is_cus_email', 'Enter the Email ID..')) return false;

	if (getMantoryValue('is_country') === "yes") {
		if (($('#country').val() == '' || $('#country').val() == null) && ($('#id_country').val() == '' || $('#id_country').val() == null)) {
			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Select the Country..' });
			return false;
		}
	}

	if (!validateField($('#state').val(), 'id_state', 'Select the State..')) return false;
	if (!validateField($('#city').val(), 'id_city', 'Select the City..')) return false;

	if (getMantoryValue('is_sel_village') === "yes") {
		if ($('#sel_village').val() == '' || $('#sel_village').val() == null) {
			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Select the Area..' });
			return false;
		}
	} else if ($('#sel_village').val() == '' && $('#country').val() == 101) {
		// Legacy fallback if not explicitly "no"
		if (getMantoryValue('is_sel_village') !== "no") {
			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Select the Area..' });
			return false;
		}
	}

	if (!validateField($('#address1').val(), 'is_address1', 'Enter the Address1..')) return false;
	if (!validateField($('#address2').val(), 'is_address2', 'Enter the Address2..')) return false;
	if (!validateField($('#address3').val(), 'is_address3', 'Enter the Address3..')) return false;
	if (!validateField($('#profession').val(), 'is_profession', 'Select the Profession..')) return false;
	if (!validateField($('#date_of_birth').val(), 'is_date_of_birth', 'Enter the Date of Birth..')) return false;
	if (!validateField($('#date_of_wed').val(), 'is_date_of_wed', 'Enter the Wedding Date..')) return false;
	if (!validateField($('#pan').val(), 'is_pan', 'Enter the Pan ID..')) return false;

    // Condition based on pan_verification setting for customer slider/modal/billing
    let panVerificationSetting = ($("#pan_verification_slider").val() == 1 || $("#pan_verification_modal").val() == 1 || $("#pan_verification_billing").val() == 1);
    let panVerifiedFlag = ($("#billing_pan_verified_flag").val() == 1 || $("#pan_verified_flag").val() == 1);
    let panValue = ($('#pan').val() ? $('#pan').val() :  ($('#pan_no').val() ? $('#pan_no').val() : ""));

    if (panVerificationSetting && panValue != "" && !panVerifiedFlag) {
      $.toaster({
        priority: "danger",
        title: "Warning!",
        message: "" + "</br>" + "Please Verify PAN Number..",
      });
      return false;
    }
	if (!validateField($('#aadharid').val(), 'is_aadharid', 'Enter the Aadhar ID..')) return false;
	if (!validateField($('#dl').val(), 'is_dl', 'Enter the Driving License No..')) return false;
	if (!validateField($('#pp').val(), 'is_pp', 'Enter the Passport No..')) return false;

	if (getMantoryValue('is_pin_code_add') === "yes") {
		if ($('#pin_code_add').val() == '' || $('#pin_code_add').val() == null) {
			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter the Pincode..' });
			return false;
		}
	} else if (getMantoryValue('is_pin_code_add') === "no") {
		// Explicitly not mandatory
	} else if ($('#pin_code_add').val() == '' && $('#country').val() == 101) {
		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter the Pincode..' });
		return false;
	}


	if ($('#pin_code_add').val() != '' && ($('#pin_code_add').val().length != 6)) {




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



			var reggst = new RegExp('^[0-9]{2}[a-zA-Z]{4}([1-9]|[a-zA-Z]){1}[0-9]{4}[a-zA-Z]{1}([1-9]|[a-zA-Z]){2}([0-9]|[a-zA-Z]){1}$');



			if (!reggst.test($('#gst_no').val())) {



				$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter the Valid GST No..' });



				allow_submit = false;

				return false;



			}



		}



	}



	if(ctrl_page[1]=='billing' || ctrl_page[1]=='estimation' || ctrl_page[1]=='receipt' || ctrl_page[1]=='bill_split' || ctrl_page[0]=='admin_ret_tag_parts'){

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

	}else if(ctrl_page[1]=='repair_order' || ctrl_page[1]=='order' || ctrl_page[1]=='stock_issue'){

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

			   

			   

				var add_all = ((ctrl_page[1]=='close' || ctrl_page[1]=='add' || ctrl_page[2]=='add' || ctrl_page[1]=='edit' || ctrl_page[2]=='edit' || ctrl_page[1]=='get_yet_to_issue' || ctrl_page[1]=='gift_report' || ctrl_page[1]=='admin_ret_reports' ||ctrl_page[1]=='stock_details_v1'||ctrl_page[1]=='stock_details_v2'||  (ctrl_page[1]=='reorder_settings' && ctrl_page[2]=='list') || (ctrl_page[0]=='admin_manage' && ctrl_page[1]=='gift_issue_form') || ctrl_page[2] == 'dynamic' || ctrl_page[0]=='dashboard') ? false : true);

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

			   if((ctrl_page[1]=='receipt' || ctrl_page[1] == 'issue' || ctrl_page[1] == 'stock_issue' || ctrl_page[1] == 'stock_age' || ctrl_page[1] == 'order' || ctrl_page[1] == 'ret_section' || ctrl_page[1] == 'estimation' || ctrl_page[1] == 'tagging')&& (ctrl_page[2]=='add' || ctrl_page[2]=='list' || ctrl_page[2]=='edit' || ctrl_page[2]=='dynamic' || ctrl_page[2] == 'bulk_edit'))

				{

					var id_branch=$("#id_branch").val();

					if(id_branch>0)

					{

						$("#branch_select").select2("val",(id_branch!='' && id_branch>0?id_branch:''));
						

					}

				}

				// Retagging page: ctrl_page = ['admin_ret_tagging','retagging','add']
				if(ctrl_page[1]=='retagging' && (ctrl_page[2]=='add' || ctrl_page[2]=='list'))
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
						$("#branch_pincode").val(item.pincode);
						$("#branch_id_village").val(item.id_village);
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
						$("#branch_pincode").val(item.pincode);
						$("#branch_id_village").val(item.id_village);
					}
				});
			}

			if(ctrl_page[1] == "estimation" && (ctrl_page[2] == "add" || ctrl_page[2] == "edit")){
				var id_branch = $('#branch_select').val();
				$.each(data, function (key, item) {
					if (id_branch == item.id_branch) {
						$("#cmp_country").val(item.id_country);
						$("#cmp_state").val(item.id_state);
						$("#cmp_pincode").val(item.pincode);
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
			// Fall back to ed_id_country for main #country dropdown (used on karigar edit page)
			var effective_country = id_country || ed_id_country;

			if ($("#country").length && effective_country) {
				$("#country").selectpicker('val', effective_country).trigger('change');
			}
			if ($("#id_country").length && id_country) {
				$("#id_country").selectpicker('val', id_country).trigger('change');
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

                // if (state.is_default == 1) {
                //     option.attr("selected", "selected");
                //     $('#id_state').val(state.id);
                // }

                $('#state, #ed_cus_state').append(option);
            });

            // Refresh the bootstrap-select after adding options
            $('#state, #ed_cus_state').selectpicker('refresh');

            // Set selected values if present
            var id_state = $('#id_state').val() !== '' ? $('#id_state').val() : $('#branch_id_state').val();
            var ed_id_state = $('#ed_id_state').val();
            // Fall back to ed_id_state for main #state dropdown (used on karigar edit page)
            var effective_state = id_state || ed_id_state;

            if ($("#state").length && effective_state) {
                $("#state").selectpicker('val', effective_state).trigger('change');
            }
            if ($("#id_state").length && id_state) {
                $("#id_state").selectpicker('val', id_state).trigger('change');
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
            var id_city = $('#id_city').val() != '' ? $('#id_city').val() : $('#branch_id_city').val();
            var ed_id_city_raw = $('#ed_id_city').val() != '' ? $('#ed_id_city').val() : '';
            var ed_id_city = ed_id_city_raw != '' ? ed_id_city_raw : $('#branch_id_city').val();
            // Fall back to ed_id_city for main #city dropdown (used on karigar edit page)
            var effective_city = id_city || ed_id_city_raw;

            if ($("#city").length && effective_city) {
                $("#city").selectpicker('val', effective_city).trigger('change');
            }
            if ($("#id_city").length && id_city) {
                $("#id_city").selectpicker('val', id_city).trigger('change');
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

/**
 * Generates dynamic header titles for DataTable's Print and Excel export buttons.
 * This function uses common input fields from the DOM and constructs:
 *   1. HTML content (for use with DataTables 'print' -> messageTop)
 *   2. Plain text content (for use with 'excel' -> title)
 * 
 * @param {string} report_name  - The name of the report (e.g., "Lot-wise Sold & Pending")
 * @param {string} from_date    - Report start date (usually from a date filter)
 * @param {string} to_date      - Report end date (usually from a date filter)
 * @param {string} optional     - Any extra information to be added (e.g., selected karigar name)
 * 
 * @returns {object}            - An object with two properties:
 *                                  1. htmlTitle: HTML string for print view
 *                                  2. plainTitle: Plain string for Excel export
 */
function generateReportHeader(report_name, from_date, to_date, optional) {
	// Get company name from the input field
	var company_name = $('#company_name').val();

	// Determine the branch name from #branch_name input or selected option in #branch_select
	var branch_name = ($('#branch_name').val() != '' && $('#branch_name').val() != undefined
		? $('#branch_name').val()
		: $("#branch_select option:selected").text());

	// Fallback to 'ALL' if no branch name is provided
	if (!branch_name || branch_name.trim() === '') {
		branch_name = "ALL";
	}

	// Default empty string if optional field is not passed
	if (!optional) {
		optional = '';
	}

	// Construct the HTML version of the title for use in the Print view
	let htmlTitle = `
		<div style='text-align: center;'>
			<b><span style='font-size:15pt;'>${company_name}</span></b><br/>
			<span>${report_name} - ${branch_name}</span><br/>
			${optional ? `<span>${optional}</span><br/>` : ''}
			${(from_date && to_date) ? `<span>FROM: ${from_date} TO: ${to_date}</span><br/>` : ''}
			<span>${$('.hidden-xs').html()} - <span style='font-size:11pt;'>${getDisplayDateTime()}</span></span><br/>
		</div>
	`;

	// Construct a plain string version of the title for use in the Excel export
	let plainTitle = `${company_name} - ${report_name} - ${branch_name}` + 
		(optional ? ` - ${optional.replace(/<[^>]*>?/gm, '').trim()}` : '') + 
		((from_date && to_date) ? ` - FROM: ${from_date} TO: ${to_date}` : '');

	// Return both formats in an object so it can be used flexibly
	return {
		htmlTitle: htmlTitle.trim(),    // HTML formatted title for printing
		plainTitle: plainTitle.trim()   // Plain text title for Excel
	};
}


function getDisplayDateTime() {
	var today = new Date();
	var dispdate = today.getDate() + '-' + (today.getMonth() + 1) + '-' + today.getFullYear();
	var disptime = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
	return dispdate + " " + disptime;
}
function get_sch_code() {
	$("div.overlay").css("display", "none");
	$("#chit_name option").remove();
	$.ajax({
		type: 'POST',
		url: base_url + 'index.php/admin_ret_reports/active_sch_code',
		dataType: 'json',
		// data: { 'id_metal': $('#metal').val() },
		success: function (data) {
			$("#chit_name").append(
				$("<option></option>")
					.attr("value", 0)
					.text('All')
			);
			$.each(data, function (key, item) {
				$('#chit_name').append(
					$("<option></option>")
						.attr("value", item.code)
						.text(item.code)
				);
			});
			$("#chit_name").select2({
				placeholder: "Select Scheme Code",
				allowClear: true
			});
			
		}
	});
	$("div.overlay").css("display", "none");
}
function add_new_village(village, pincode) {
	initializeBootstrapSelect("#sel_village,#ed_sel_village");
    var my_Date = new Date();
    // $("div.overlay").css("display", "block");
    $.ajax({
        url: base_url + "index.php/admin_ret_estimation/get_village?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),
        data: { 'village_name': village, 'pincode': pincode },
        type: "POST",
        dataType: "JSON",
        async: false,
        success: function (data) {
            console.log(data);
            if (data.status) {

                var ins_id = data.ins_id;

                var newVillage = village;
                var $newOption = $('<option>', {
                    value: ins_id,
                    text: newVillage
                });

                // Append the new option to the dropdown
                $('#sel_village').append($newOption);

                // Refresh bootstrap-select to recognize the new option
                $('#sel_village').selectpicker('refresh');

                // Set the selected value
                $('#sel_village').selectpicker('val', ins_id);

                if (ins_id != '') {
                    $('#ed_id_village').val(ins_id);
                    $('#id_village').val(ins_id);
                }

                // Hide modal
                $('#confirm-area').modal('hide');

                $("div.overlay").css("display", "none");

                if ($('#pin_code_add').val().length == 6) {
                    get_villages_by_pincode($('#pin_code_add').val());
                }

                $.toaster({
                    priority: 'success',
                    title: 'Success!',
                    message: '</br>' + data.message
                });

            } else {
                $.toaster({
                    priority: 'danger',
                    title: 'Warning!',
                    message: '</br>' + data.message
                });
				$('#confirm-area').modal('hide');
            }
        },
        error: function (error) {
            console.log(error);
            // $("div.overlay").css("display", "none");
        }
    });
}


function get_villages_by_pincode(pincode) {
	initializeBootstrapSelect("#sel_village,#ed_sel_village");
    var my_Date = new Date();
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

                $.each(data, function (key, item) {
                    $("#sel_village,#ed_sel_village").append(
                        $("<option></option>")
                            .attr("value", item.id_village)
                            .text(item.village_name)
                    );
                });

                // Refresh the bootstrap-select after updating options
                $("#sel_village,#ed_sel_village").selectpicker('refresh');

                if (id_village != '') {
                    $('#sel_village').selectpicker('val', id_village);
                } else {
                    $('#sel_village').selectpicker('val', $("#branch_id_village").val());
                }

                if (ed_id_village != '') {
                    $('#ed_sel_village').selectpicker('val', ed_id_village);
                } else {
                    $('#ed_sel_village').selectpicker('val', $("#branch_id_village").val());
                }

                $("body").on("hidden.bs.modal", function () {
                    if ($(".modal.in").length > 0) {
                        $("body").addClass("modal-open");
                    }
                });

            } else {
                $.toaster({
                    priority: 'danger',
                    title: 'Warning!',
                    message: '</br>No Area Found For this Pincode'
                });

                $('#sel_village option').remove();
                $('#ed_sel_village option').remove();

                $('#id_village').val('');
                $('#ed_id_village').val('');

                $("#sel_village,#ed_sel_village").selectpicker('refresh');
            }

        }
    });
}
function getActive_quality_code()
{
   $('#stone_uom_id option').remove();

    $.ajax({

	type: 'GET',

	url: base_url+'index.php/admin_ret_catalog/get_quality_code',

	dataType:'json',

	success:function(data)

	{

		quality_code =data;

		if((ctrl_page[1] == 'estimation' && (ctrl_page[2] == 'add' || ctrl_page[2] == 'edit')) || (ctrl_page[1] == 'billing' && ctrl_page[2] == 'add')){

		var id_quality = $('#id_quality').val();

		$(".quality_id_select option").remove();

		$.each(data, function (key, item) {

			$(".quality_id_select").append(

			$("<option></option>")

			.attr("value", item.quality_id)

			.text(item.code)


		);

	});


	$(".quality_id_select").select2({

		placeholder: "Quality Code",

		allowClear: true

	});

	$('.quality_id_select').select2("val",(id_quality!='' ? id_quality :""));

	$(".overlay").css("display", "none");

}

	}

});
}

$(document).ready(function () {

    $('#branch_select, #prod_select, #des_select, #sub_des_select').select2({
        allowClear: true,
        width: '100%'
    });

    $('#branch_select').on('change', () => $('#id_branch').val($('#branch_select').val()));
    $('#prod_select').on('change', () => $('#id_product').val($('#prod_select').val()));

    // No special character restriction for select2 search


});




function getCustomerSalesDetails(id_customer, row = '') {
	if (ctrl_page[1] == 'billing' && ctrl_page[2] == 'add') {
	$("#filter_bill_no option").remove();
	}
	my_Date = new Date();

	$.ajax({
		url:
			base_url +
			"index.php/admin_ret_billing/getCustomerSalesDetails/?nocache=" +
			my_Date.getUTCSeconds(),

		dataType: "json",

		method: "POST",

		data: { id_customer: id_customer, id_branch: $("#id_branch").val() },

		success: function (data) {
			if (data.length > 0) {
				if (ctrl_page[1] == 'billing' && ctrl_page[2] == 'add') {
					$.each(data, function (key, val) {
						$("#filter_bill_no").append(
							$("<option></option>")
								.attr("value", val.bill_no)
								.text(val.bill_no)
						);
					});
				}
				else if (ctrl_page[1] == 'estimation' && (ctrl_page[2] == 'add' || ctrl_page[2] == 'edit')) {
					const $select = $(row).find("#filter_bill_no");

					// Add placeholder as first option
					$select.append(
						$("<option></option>")
							.attr("value", "")
							.attr("disabled", true)
							.attr("selected", true)
							.text("Bill No")
					);

					// Append the actual bill number options
					$.each(data, function (key, val) {
						$select.append(
							$("<option></option>")
								.attr("value", val.bill_no)
								.attr("data-bill-id", val.bill_id)
								.text(val.bill_no)
						);
					});
				}


			} else {
				$.toaster({
					priority: "danger",
					title: "Warning!",
					message: "" + "</br>No records Found..",
				});
				$('#amount').val(0);
			}
		},
	});
	
}

function checkPanNoValidation(pan_no, focusElem) {
	var status = false;
	if (pan_no != '') {
		var regexp = /^[a-zA-Z]{5}\d{4}[a-zA-Z]{1}$/;
		if (regexp.test(pan_no)) {
			status = true;
		} else {
			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Enter The Valid PAN No.." });
			if (focusElem) $(focusElem).focus();
		}
	}
	return status;
}

function verifyPanNumber(pan, name, endpointUrl, ui) {
	if (typeof endpointUrl === 'object' && ui === undefined) {
		ui = endpointUrl;
		endpointUrl = base_url + 'index.php/admin_pan_verification/verify';
	}
	var endpoint = 'admin_pan_verification/verify';
	if (typeof base_url !== 'undefined') {
		endpoint = base_url + 'index.php/' + endpoint;
	} else {
		endpoint = '/admin/index.php/' + endpoint;
	}
	if (typeof endpointUrl === 'string' && endpointUrl !== '') {
		endpoint = endpointUrl;
	}

	if (!checkPanNoValidation(pan, ui.panInput)) {
		return;
	}

	$(ui.btn).html('<i class="fa fa-spinner fa-spin"></i> Verifying...').prop('disabled', true);
	if (ui.status) {
		$(ui.status).html('Checking...').removeClass('text-success text-danger');
	}

	var my_Date = new Date();
	$.ajax({
		url: endpoint + '?nocache=' + my_Date.getUTCSeconds(),
		dataType: "json",
		method: "POST",
		data: { 'pan': pan, 'name': name },
		success: function(data) {
			$(ui.btn).html('Verify').prop('disabled', false);
			if (data.status && data.verified) {
				if (ui.status) {
					$(ui.status).html('Verified: ' + data.registered_name).addClass('text-success').removeClass('text-danger');
				}
				$(ui.btn).removeClass('btn-primary btn-danger').addClass('btn-success').prop('disabled', true);
				if (ui.flag) $(ui.flag).val('1');
				if (ui.panInput) $(ui.panInput).css('border', '2px solid green');
				$.toaster({ priority: 'success', title: 'Success', message: '</br>PAN Verified: ' + data.registered_name });
			} else {
				var msg = data.message || 'Verification failed';
				if (ui.status) {
					$(ui.status).html(msg).addClass('text-danger').removeClass('text-success');
				}
				$(ui.btn).removeClass('btn-primary btn-success').addClass('btn-danger');
				if (ui.flag) $(ui.flag).val('0');
				if (ui.panInput) $(ui.panInput).css('border', '2px solid red');
				$.toaster({ priority: 'danger', title: 'Error!', message: '</br>' + msg });
			}
		},
		error: function () {
			if (ui.btn) {
				$(ui.btn).html('<i class="fa fa-check-circle"></i> Verify')
					.removeClass('btn-success btn-danger').addClass('btn-primary').prop('disabled', false);
			}
			$.toaster({ priority: 'danger', title: 'Error!', message: '</br>Network error. URL: ' + endpoint });
			if (ui.flag) $(ui.flag).val('0');
		}
	});
}

$(document).ready(function() {
    $(document).on('click', '#btn_verify_pan, #btn_verify_billing_pan, #btn_verify_ed_pan', function() {
        var btnId = this.id;
        var ui = {};
        var pan = '';
        var name = '';

        if (btnId === 'btn_verify_billing_pan') {
            ui = {
                btn: '#btn_verify_billing_pan',
                status: '#status_verify_billing_pan',
                flag: '#billing_pan_verified_flag',
                panInput: '#pan'
            };
            pan = $('#pan').val();
            name = $('#cus_first_name').val();
        } else if (btnId === 'btn_verify_ed_pan') {
            ui = {
                btn: '#btn_verify_ed_pan',
                status: '#ed_pan_verify_status',
                flag: '#ed_pan_verified_flag',
                panInput: '#ed_pan_no'
            };
            pan = $('#ed_pan_no').val();
            name = $('#ed_cus_first_name').val();
        } else {
            ui = {
                btn: '#btn_verify_pan',
                status: '#pan_verify_status',
                flag: '#pan_verified_flag',
                panInput: '#pan_no'
            };
            pan = $('#pan_no').val();
            name = $('#finalname').val() || $('#cus_first_name').val() || $('#cus_search_name').val() || '';
        }
        
        verifyPanNumber(pan, name, ui);
    });

    $(document).on('input change', '#pan, #pan_no, #ed_pan_no', function() {
        var panValue = $(this).val();
        var btnSelector = '';
        if (this.id === 'pan') btnSelector = '#btn_verify_billing_pan';
        else if (this.id === 'ed_pan_no') btnSelector = '#btn_verify_ed_pan';
        else btnSelector = '#btn_verify_pan';

        if (panValue.length === 10) {
            if (typeof checkPanNoValidation === 'function' && checkPanNoValidation(panValue)) {
                $(btnSelector).prop('disabled', false).addClass('btn-primary').removeClass('btn-danger btn-success');
                $(this).css('border', '');
            } else {
                $(btnSelector).prop('disabled', true);
            }
        } else {
            $(btnSelector).prop('disabled', true);
        }
    });

    $('#demo').on('show.bs.offcanvas', function() {
        $('#billing_pan_verified_flag').val('0');
        $('#status_verify_billing_pan').html('').removeClass('text-success text-danger');
        $('#btn_verify_billing_pan').html('Verify')
            .removeClass('btn-success btn-danger').addClass('btn-primary').prop('disabled', true);
        $('#pan').css('border', '');
    });
});

/* =============================================================
 * SHARED CUSTOMER SLIDER UTILITIES  (ret_general.js)
 * Used by: estimation, billing, order, repair_order,
 *          receipt, stock_issue — all share #demo offcanvas
 * ============================================================= */

/**
 * reset_customer_slider()
 * Clears ALL fields in the #demo offcanvas so that opening
 * the slider a second time never shows the previous customer.
 */
function reset_customer_slider() {
    var $s = $('#demo');

    // -- text / number / email / date inputs --
    $s.find('#cus_first_name').val('');
    $s.find('#cus_mobile').val('');
    $s.find('#cus_email').val('');
    $s.find('#gst_no').val('');
    $s.find('#address1').val('');
    $s.find('#address2').val('');
    $s.find('#address3').val('');
    $s.find('#pin_code_add').val('');
    $s.find('#date_of_birth').val('');
    $s.find('#date_of_wed').val('');

    // -- KYC tab --
    $s.find('#pan').val('');
    $s.find('#aadharid').val('');
    $s.find('#dl').val('');
    $s.find('#pp').val('');
    $s.find('#billing_pan_verified_flag').val('0');
    $s.find('#status_verify_billing_pan').text('');

    // -- hidden IDs --
    $s.find('#id_customer').val('');
    $s.find('#id_country').val('');
    $s.find('#id_state').val('');
    $s.find('#id_city').val('');
    $s.find('#id_village').val('');
    $s.find('#professionval').val('');
    $s.find('#customer_img').val('');

    // -- title select back to default --
    $s.find('#title').val('Mr');

    // -- radios: VIP = No, Gender = unset --
    $s.find('input[name="customer[vip]"][value="0"]').prop('checked', true);
    $s.find('input[name="customer[gender]"]').prop('checked', false);

    // -- dropdowns: clear dynamic options --
    $s.find('#state').empty();
    $s.find('#city').empty();
    $s.find('#sel_village').empty();
    $s.find('#profession').empty();

    // -- photo preview back to default --
    var defaultImg = typeof base_url !== 'undefined'
        ? base_url + 'assets/img/default.png'
        : '/assets/img/default.png';
    $s.find('#cus_img_preview').attr('src', defaultImg);
    $s.find('#cus_image').val('');    // clear file input

    // -- error/help messages --
    $s.find('.help-block').text('');

    // -- GST row hidden by default --
    $s.find('.gst').hide();

    // -- switch back to GENERAL tab --
    $s.find('.nav-tabs a[href="#tab_general"]').tab('show');
}

/**
 * open_customer_slider(opts)
 * Shared open logic for all pages. Each page's #add_new_customer
 * click handler just validates its branch field, then calls this.
 *
 * opts (optional):
 *   mobile_no  {string}  — pre-fill mobile field (used by create_customer)
 *   gst_for    {string}  — value of billing_for / esti_for to decide GST row
 *                          '2'=billing company, '3'=estimation company
 */
function open_customer_slider(opts) {
    opts = opts || {};

    // 1. Reset all fields
    reset_customer_slider();

    // 2. Pre-fill mobile if supplied
    if (opts.mobile_no) {
        $('#cus_mobile').val(opts.mobile_no);
    }

    // 3. Load country dropdown
    if (typeof get_country === 'function') { get_country(); }

    // 4. Pre-fill branch defaults and load villages if 6-digit pincode
    var pincode = $('#branch_pincode').val();
    $('#pin_code_add').val(pincode);
    if (pincode && pincode.length === 6) {
        if (typeof get_villages_by_pincode === 'function') {
            get_villages_by_pincode(pincode);
        }
    }

    // 5. Show/hide GST row
    var gst_for = opts.gst_for ||
        $(  "input[name='estimation[esti_for]']:checked," +
            "input[name='billing[billing_for]']:checked"
        ).first().val();
    // esti_for=3 → Company; billing_for=2 → Company
    if (gst_for == 3 || gst_for == 2) {
        $('#demo .gst').show();
    } else {
        $('#demo .gst').hide();
    }

    // 6. Open the offcanvas (supports both jQuery plugin and Bootstrap 5 native API)
    if (typeof bootstrap !== 'undefined' && bootstrap.Offcanvas) {
        var el = document.getElementById('demo');
        var oc = bootstrap.Offcanvas.getInstance(el) || new bootstrap.Offcanvas(el);
        oc.show();
    } else {
        $('.offcanvas').offcanvas('show');
    }

    // 7. Reset labels
    $('#myModalLabel').text('Add Customer');
    $('#add_newcutomer').text('Add');
    $('#cus_mobile').prop('readonly', false);
}

// Load village dropdown on blur of #pin_code_add — shared across all pages
$(document).on('blur', '#pin_code_add', function () {
    var pin = $(this).val();
    if (pin && pin.length === 6) {
        if (typeof get_villages_by_pincode === 'function') {
            get_villages_by_pincode(pin);
        }
    }
});
