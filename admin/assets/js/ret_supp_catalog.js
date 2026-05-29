var path =  url_params();

var ctrl_page 		= path.route.split('/');

let weight_range = [];

let purities = [];

let sizes = [];

let karigars = [];

var total_files=[];

var img_resource=[];

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

		case 'supplier_catalog':

		switch(ctrl_page[2]) {

			case 'list':				 	

				get_supplier_catalog_list();

				get_Activeproduct();

			break;

			case 'add':		

			get_all_karigar();

			$(".karigar").multiselect({

				enableFiltering: true,
		
				enableCaseInsensitiveFiltering: true, // Enable search feature
		
				maxHeight: 250, // Set maximum height
		
				includeSelectAllOption: true // Add "Select All" option
		
			});

			$(".purity").multiselect({

				enableFiltering: true,
		
				enableCaseInsensitiveFiltering: true, // Enable search feature
		
				maxHeight: 250, // Set maximum height
		
				includeSelectAllOption: true // Add "Select All" option
		
			});

			get_Activeproduct();

			break;

			case 'edit':

				$(".karigar").multiselect({

					enableFiltering: true,
			
					enableCaseInsensitiveFiltering: true, // Enable search feature
			
					maxHeight: 250, // Set maximum height
			
					includeSelectAllOption: true // Add "Select All" option
			
				});

				$(".purity").multiselect({

					enableFiltering: true,
			
					enableCaseInsensitiveFiltering: true, // Enable search feature
			
					maxHeight: 250, // Set maximum height
			
					includeSelectAllOption: true // Add "Select All" option
			
				});

				let catalog_id = $("#id_supp_catalogue").val();

				edit_supp_cat(catalog_id);

			break;

		}
	}

	$(document).on("click", ".del_img", function() {

		$(this).closest(".images_container").remove();

	});

	$(document).on("blur", "#from_weight", function() {

		from_weight_validation();
	
	});
	
	$(document).on("blur", "#to_weight", function() {
	
		to_weight_validation();
	
	});
	
	
});

function get_supplier_catalog_list() {

	let my_Date = new Date();
	
	$("div.overlay").css("display", "block"); 
	
	$.ajax({
	
		url:base_url+"index.php/admin_ret_supp_catalog/supplier_catalog/ajax?nocache=" + my_Date.getUTCSeconds(),
	
		dataType:"JSON",
	
		type:"POST",
	
		success:function(data) {
	
			set_supplier_catalog_list(data);
	
			$("div.overlay").css("display", "none"); 
	
		},
	
		error:function(error)  {
	
			$("div.overlay").css("display", "none"); 
	
		}	 
	
	});

}

function set_supplier_catalog_list(data)	{

	$("div.overlay").css("display", "none"); 

	var list = data.list;

	var access = data.access;

	var oTable = $('#supp_cat_list').DataTable();

	$("#total_count").text(list.length);

	if(access.add == '0')

	{

		$('#add_supp_cat').attr('disabled','disabled');

	}

	oTable.clear().draw();

	if (list!= null && list.length > 0) {
		
	 	oTable = $('#supp_cat_list').dataTable({

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

					title: "Supplier Catalogue List",

				},

				{

					extend:'excel',

					footer: true,

					title: "Supplier Catalogue List",

				}

			],

			"aaData": list,

			"aoColumns": [{ "mDataProp": "id_supp_catalogue" },

			{ "mDataProp": "ctl_datetime" },

			{ "mDataProp": "product_name" },

			{ "mDataProp": "design_name" },

			{ "mDataProp": "sub_design_name" },

			{ "mDataProp": "design_code" },

			{ "mDataProp": function ( row, type, val, meta ) {


							
				active_url =(access.edit == '1')?base_url+"index.php/admin_ret_supp_catalog/profile_status/"+(row.status==1?0:1)+"/"+row.id_supp_catalogue:'#'; 
			
							
				return "<a href='"+active_url+"'><i class='fa "+(row.status==1?'fa-check':'fa-remove')+"' style='color:"+(row.status==1?'green':'red')+"'></i></a>"
				
			}},
			
			{ "mDataProp": function ( row, type, val, meta ) {

				id	= row.id_supp_catalogue;

				let edit_url		=	(access.add=='1' ? base_url+'index.php/admin_ret_supp_catalog/supplier_catalog/edit/'+id : '#' );

				let delete_url		=	(access.add=='1' ? base_url+'index.php/admin_ret_supp_catalog/supplier_catalog/delete/'+id : '#' );

				// action_content 	= 	'<a href="'+edit_url+'" class="btn btn-success edit_supp_cat" data-toggle="modal" ><i class="fa fa-edit"></i></a> &nbsp; <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal"  data-target="#confirm-delete"><i class="fa fa-trash"></i></a>';

				var action_content=''

				if(access.edit == '1'){
											
					action_content+=  '<a href="'+edit_url+'" class="btn btn-success edit_supp_cat" data-toggle="modal" ><i class="fa fa-edit"></i></a>' ;
					
					}
					if(access.delete == '1'){
					
					action_content+='<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal"  data-target="#confirm-delete"><i class="fa fa-trash"></i></a>'
					}
				return action_content;

			}

			}] 

		});	

	}
}

function validate_catalog_weight() {

	let validateRows = true;

	let product_id = $("#product_id").val();

	let design_id = $("#design_id" ).val();
		
	let subdesign_id = $("#subdesign_id").val();

	let weight = $.trim($('.weight').val());

	let from_weight = $.trim($('.from_weight').val());

	let to_weight = $.trim($('.to_weight').val());

	let purity = $.trim($('.purity').val());

	let size = $.trim($('.size').val());

	let mc_type = $.trim($('.mc_type').val());

	let mc_value = $.trim($('.mc_value').val());

	let wastage = $.trim($('.wastage').val());

	let delivery_duration = $.trim($('.delivery_duration').val());

	let karigar = $.trim($('.karigar').val());

	let has_images = false;

	let is_default_selected = false;

	$(".images_container").each(function() {

		let is_default = $(this).find(".img_is_default").val();

		has_images = true;

		if(is_default == 1) {

			is_default_selected = true;

			return false;

		}

	});

	if(!(product_id > 0)) {
		
		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Product Required!'});

		validateRows = false;

	} else if(!(design_id > 0)) {

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Design Required!'});

		validateRows = false;

	} else if(!(subdesign_id > 0)) {

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Sub Design Required!'});

		validateRows = false;

	} else if(!(purity?.length > 0)) {

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Purity Required!'});

		validateRows = false;

	} else if (!(/^\d+(\.\d+)?$/.test(weight))) {

		if(weight == "") {

			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Weight Required!'});

		} else {
		
			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Not a valid Weight!'});

		}

		validateRows = false;

	} else if (!(/^\d+(\.\d+)?$/.test(from_weight))) {

		if(from_weight == "") {

			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'From Weight Required!'});

		} else {
		
			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Not a valid From Weight!'});

		}

		validateRows = false;

	} else if (parseFloat(from_weight) > parseFloat(weight)) {

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'From Weight should be less than Weight!'});

		validateRows = false;

	} else if (!(/^\d+(\.\d+)?$/.test(to_weight))) {

		if(to_weight == "") {

			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'To Weight Required!'});

		} else {
		
			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Not a valid To Weight!'});

		}

		validateRows = false;

	} else if (parseFloat(to_weight) < parseFloat(weight)) {

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'To Weight should be greater than Weight!'});

		validateRows = false;

	} else if(!(mc_type > 0)) {

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'MC Type Required!'});

		validateRows = false;

	} else if(!(mc_value > 0)) {

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'MC Value Required!'});

		validateRows = false;

	}  else if(!(wastage > 0)) {

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Wastage Required!'});

		validateRows = false;
	
		return false;

	}  else if(!(delivery_duration > 0)) {

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Delivery Duration Required!'});

		validateRows = false;
	
		return false;

	} else if(!(karigar?.length > 0)) {

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Karigar Required!'});

		validateRows = false;

	} else if(!has_images) {

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Images Required!'});

		validateRows = false;

	} else if(!is_default_selected) {

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Default Image Required!'});

		validateRows = false;

	}


	if(!validateRows) {

		return false;

	} else {

		return true;

	}

}

function get_cat_purity(id = Array) { 

	$(".overlay").css('display','block');

	let id_category = $("#cat_id").val();

	return $.ajax({

			type: 'POST',

			url: base_url+'index.php/admin_ret_catalog/category/cat_purity',

			dataType:'json',

			data: {

				'id_category' : id_category
			},

			success:function(data) {

				purities = data;

				$("#purity option").remove();

				$.each(purities, function (key, item) {   

					$(".purity").append(

						$("<option></option>")

						.attr("value", item.id_purity)    

						.text(item.purity)  
					);
				
				});	

				if(id.length > 0) {

					$("#purity").val(id);

				}

				$("#purity").multiselect('rebuild');

				$(".overlay").css("display", "none");
			}
		});
}

function get_all_karigar(id = Array) {

	$(".overlay").css("display", "block");

	return $.ajax({

			type: 'GET',

			url: base_url+'index.php/admin_ret_catalog/karigar/active_list',

			dataType:'json',

			success:function(data){

				karigars = data;

				$(".karigar option").remove();

				$.each(karigars, function (key, item) {   

					$(".karigar").append(

						$("<option></option>")

						.attr("value", item.id_karigar)    

						.text(item.karigar)  

					);

				});

				if(id.length > 0) {

					$(".karigar").val(id);
			
				}
			
				$(".karigar").multiselect('rebuild');

				$(".overlay").css("display", "none");

			}

		});

}

function validate_image() {

	if(arguments[0].files[0].size > 1048576) {

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'File size cannot be greater than 1 MB'});

		arguments[0].value = "";

		$('#img_preview').attr('src',  base_url+"assets/img/no_image.png");
	
	} else {

		var fileName =arguments[0].value;

		var ext = fileName.substring(fileName.lastIndexOf('.') + 1);

		ext = ext.toLowerCase();

		if(ext != "jpg" && ext != "png" && ext != "jpeg") {

			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Upload JPG or PNG Images only'});

			//arguments[0].value = "";

			//$('#img_preview').attr('src', base_url+"assets/img/no_image.png");

		} else {

			const file = arguments[0].files[0];
			
			if (file) {

				let reader = new FileReader();

				reader.readAsDataURL(file);

				reader.onload = function(event){

					let img = event.target.result;

					create_image_container(img, 0);

				}

			}

		}
	}
}

function create_image_container(img, default_value) {

	let base64String = img.split( "base64," );

	base64String = base64String[base64String.length - 1 ];

	let img_container = `<div class="images_container col-md-2">

					<input type="hidden" class="id_supp_cat_img" />

					<input type="hidden" class="id_supp_catalogue" />

					<span class="img_name"></span>

					<div class="img_buttons">

						<input type="hidden" value="`+default_value+`" name="images[is_default][]" class="img_is_default" />

						<input type="hidden" value="`+base64String+`" name="images[value][]" class="img_value" />

						<label> Is Default </label> &nbsp; <input type="checkbox" class="is_default" `+(default_value == 1 ? "checked" : "")+` > </label>
						
						&nbsp; &nbsp; &nbsp;
						
						<span class="del_img">Delete</span>

					</div>

					<div class="img">
						
						<img src="`+img+`" id="img_preview" alt="Supplier Catalogue Image">

					</div>

					</div>`;

	$(".images_preview").append(img_container);

}

$(document).on("click", ".is_default", function() {

	let obj = $(this);

	let obj_container = $(obj).closest(".images_container");

	let is_checked = obj_container.find(".is_default").prop("checked");

	$(".is_default").prop("checked", false);

	$(".img_is_default").val(0);

	if(is_checked) {

		obj_container.find(".is_default").prop("checked", true);

		obj_container.find(".img_is_default").val(1);

	} else {

		obj_container.find(".is_default").prop("checked", false);

		obj_container.find(".img_is_default").val(0);

	}

});


function get_Activeproduct(id_category='')
{
    $(".overlay").css("display", "block");

    $('#ret_product option').remove();
    
	let my_Date = new Date();
	
	$.ajax({
	
		type: 'POST',
	
		url: base_url+"index.php/admin_ret_supp_catalog/get_ActiveProducts?nocache=" + my_Date.getUTCSeconds(),
	
		data :{'id_ret_category':id_category},
	
		dataType:'json',
	
		success:function(data){
	
			let id = $("#product_id" ).val(); 
	
			$.each(data, function (key, item) {   
	
				$("#ret_product").append(
	
					$("<option></option>")
	
					.attr("value", item.pro_id)    
	
					.text(item.product_name)  
	
					);
	
			});
		
	
			$("#ret_product").select2({

				placeholder:"Select Product",

				allowClear: true		    

			});	

			$("#ret_product").select2("val",(id != '' && id > 0 ? id:'' ));

			if(id != '' && id > 0 ) {

				getFilterDesign(id);

			}

			$(".overlay").css("display", "none");
		   
		}
	});
}

function getFilterDesign(id_product ='')
{
	$('#ret_design option').remove();
	
	$.ajax({
	
		type: 'POST',
	
		url: base_url+'index.php/admin_ret_supp_catalog/get_Activedesign',
	
		data :{'id_product': id_product !=''? id_product : $('#ret_product').val() },
	
		dataType:'json',
	
		success:function(data) {

			var id=$("#design_id" ).val(); 
    	
			$.each(data, function (key, item) {   
    	
				$("#ret_design").append(
	
					$("<option></option>")
		
					.attr("value", item.design_no)    
		
					.text(item.design_name)  

				);
	
			});
    		   
    		$("#ret_design").select2({
    	
				placeholder:"Select Design",
    	
				allowClear: true		    
    	
			});
    	
			//  $("#ret_design").select2("val",'');
		
			$("#ret_design").select2("val",( id != '' && id > 0 ? id : ''));

			if(id_product != '' && id_product > 0 && id != '' && id > 0 ) {

				get_ActiveSubDesign(id_product, id);

			}
		
		}
		
	});
	
}

function get_ActiveSubDesign(id_product ='',id_design='') {

	$('#ret_sub_design option').remove();

	$.ajax({

		type: 'POST',

		url: base_url+'index.php/admin_ret_supp_catalog/get_ActiveSubDesign',

		dataType:'json',

		data :{'id_product':id_product !=''? id_product :$('#ret_product').val(),'id_design':id_design !=''? id_design :$('#ret_design').val()},

		success:function(data){

			var id =  $("#subdesign_id").val();

			$.each(data, function (key, item) {   

				$("#ret_sub_design").append(

					$("<option></option>")

					.attr("value", item.id_sub_design)    

					.text(item.sub_design_name)  

				);

			});
		   
			$("#ret_sub_design").select2({

				placeholder:"Select Sub Design",

				allowClear: true		    

			});

			$("#ret_sub_design").select2("val",(id!='' && id>0?id:''));

		}

	});
}

$("#ret_sub_design").select2(
{
	placeholder:"Select Sub Design",
	allowClear: true		    
});
$("#ret_design").select2(
{
	placeholder:"Select Design",
	allowClear: true		    
});

$("#ret_product").select2(
{
	placeholder:"Select Product",
	allowClear: true		    
});	


$('#ret_product').on('select2:select',function() {

	if(this.value != '') {
		
		getFilterDesign(this.value);
		
		$("#product_id").val(this.value);

		get_catgory_id(this.value);
		
	}

});

$('#ret_sub_design').on('select2:select',function(){

	if(this.value!='') {

		$("#subdesign_id").val(this.value);
		
		$("#weight_details tbody").empty();
		
	}

});

$('#ret_design').on('select2:select',function(){

	if(this.value!='') {

		$("#design_id" ).val(this.value); 

		$("#weight_details tbody").empty();

		get_ActiveSubDesign();
		
    }

});

function get_catgory_id(pro_id) {
   
	$.ajax({

		type: 'POST',

		url: base_url+'index.php/admin_ret_supp_catalog/get_catgory_id',

		dataType:'json',

		data :{'pro_id':pro_id },

		success:function(data){

			$("#cat_id").val(data.cat_id);

			get_cat_purity();

		}

	});

}

function save_image(id=""){

	let	images=[];

	var row_id=$('#current_row').val();	

	$('#'+row_id).find('.images_preview').val(JSON.stringify(img_resource));

	$.each(img_resource,function(key,item){

		var src = item.src

		src = src.replace(/^data:image\/[a-z]+;base64,/,'');

		item.src= src;

		images.push(item)

	});

	$('#'+row_id).find('.images').val(JSON.stringify(images));

	$('#design_img_preview tbody tr').remove();

	$('#sub_design_images').val('');

	$('#imageModal_new').modal('toggle');

	img_resource =[];
}

$("#imageModal_new").on("hidden.bs.modal", function () {
 
	$('#design_img_preview tbody tr').remove();
   
	$('#sub_design_images').val('');
   
	img_resource =[]
  
});


$(document).on('change','.is_default_img',function(e) {

    var imgsrc=$(this).closest('tr').attr('class');

    $.each(img_resource,function(key,item){

		img_resource[key].is_default=0;
        if(key==imgsrc)
        {
            img_resource[key].is_default=1;
        }
		
    });

   // $('#wast_pro_image').val(JSON.stringify(img_resource));
    
	$('#design_img_preview > tbody tr').each(function(idx, row) {

		curRow = $(this);
		
		if(imgsrc==idx) {
		
			curRow.find('.is_default_img').prop('checked',true);
		
		}else{
		
			curRow.find('.is_default_img').prop('checked',false);
		
		}
	
	});

});

function from_weight_validation() {

	let from_weight = $.trim($("#from_weight").val()) != "" ? $("#from_weight").val() : 0;

	let weight = $.trim($("#weight").val()) != "" ? $("#weight").val() : 0;

	if(parseFloat(from_weight) > parseFloat(weight)) {

		$("#from_weight").val("");

		$("#from_weight").focus();

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'From Weight should be less than Weight!'});

	}

}

function to_weight_validation() {

	let to_weight = $.trim($("#to_weight").val()) != "" ? $("#to_weight").val() : 0;

	let weight = $.trim($("#weight").val()) != "" ? $("#weight").val() : 0;

	if(parseFloat(to_weight) < parseFloat(weight)) {

		$("#to_weight").val("");

		$("#to_weight").focus();

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'To Weight should be greater than Weight!'});

	}

}

$('#save_supp').on('click',function (){
	
	if(validate_catalog_weight()){

		$(".overlay").css("display", "block");

		let id_supp_catalogue = $("#id_supp_catalogue").val();

		let product_id = $("#product_id").val();

		let design_id = $("#design_id" ).val();
			
		let subdesign_id = $("#subdesign_id").val();

		let design_code = $("#design_code").val();

		let status = $('input[name=status]:checked').val();

		let weight = $.trim($('#weight').val());

		let from_weight = $.trim($('#from_weight').val());

		let to_weight = $.trim($('#to_weight').val());

		let purity = $.trim($('#purity').val());

		let size = $.trim($('#size').val());

		let mc_type = $.trim($('#mc_type').val());

		let mc_value = $.trim($('#mc_value').val());

		let wastage = $.trim($('#wastage').val());

		let karigar = $.trim($('#karigar').val());

		let display_duration = $('input[name=display_duration]:checked').val();

		let display_va = $('input[name=display_va]:checked').val();

		let display_mc = $('input[name=display_mc]:checked').val();

		let delivery_duration = $.trim($('#delivery_duration').val());

		let img_array = [];

		$(".images_container").each(function() {

			let is_default = $(this).find(".img_is_default").val();

			let img_value = $(this).find(".img_value").val();

			let img_obj = {
		
				"is_default" : is_default,
		
				"value" : img_value
			}

			img_array.push(img_obj); // push the object to the array

		});

		let ajaxUrl = "";

		if(id_supp_catalogue > 0) {

			ajaxUrl = base_url+'index.php/admin_ret_supp_catalog/supplier_catalog/update';

		} else {

			ajaxUrl = base_url+'index.php/admin_ret_supp_catalog/supplier_catalog/save';

		}

		$.ajax({

			type: 'POST',

			url: ajaxUrl,

			dataType:'json',

			data :{"id_supp_catalogue" : id_supp_catalogue, "product_id" : product_id, "design_id" : design_id, "subdesign_id" : subdesign_id, "weight" : weight,"from_weight" : from_weight, "to_weight" : to_weight, "purity" : purity, "size" : size, "mc_type" : mc_type, "mc_value" : mc_value, "wastage" : wastage, "karigar" : karigar, "display_duration" : display_duration, "display_va":display_va, "display_mc":display_mc, "delivery_duration":delivery_duration, "design_code" : design_code, "status":status, "images" : img_array},

			success:function(data){

				display_mc = display_mc == 1 ? "Yes" : "No";

				mc_type = mc_type == 1 ? "Piece" : "Gram";

				display_va = display_va == 1 ? "Yes" : "No";

				display_duration = display_duration == 1 ? "Yes" : "No";

				status = status == 1 ? "Yes" : "No";

				let purityTextOptions = $('.purity option:selected').map(function() {
				
					return $(this).text();
				
				}).get();

				purityTextOptions = purityTextOptions.join(",");

				let karigarTextOptions = $('.karigar option:selected').map(function() {
				
					return $(this).text();
				
				}).get();

				karigarTextOptions = karigarTextOptions.join(",");

				let productTextOptions = $('#ret_product option:selected').text();

				let designTextOptions = $('#ret_design option:selected').text();

				let subdesignTextOptions = $('#ret_sub_design option:selected').text();

				img_array = JSON.stringify(img_array);

				let disp_images = `<span class='disp_images'>Images</span><input type='hidden' class='images_value' value='`+img_array+`' />`;

				if(data.status == true) {

					$.toaster({ priority : 'success', title : 'Success!', message : ''+"</br>"+data.message});

					let row = `<tr>
						<td class="catalog_id">`+data.supp_cat_id+`</td>
						<td>`+productTextOptions+`</td>
						<td>`+designTextOptions+`</td>
						<td>`+subdesignTextOptions+`</td>
						<td>`+data.design_code+`</td>
						<td>`+weight+`</td>
						<td>`+from_weight+`</td>
						<td>`+to_weight+`</td>
						<td>`+purityTextOptions+`</td>
						<td>`+size+`</td>
						<td>`+display_mc+`</td>
						<td>`+mc_type+`</td>
						<td>`+mc_value+`</td>
						<td>`+display_va+`</td>
						<td>`+wastage+`</td>
						<td>`+display_duration+`</td>
						<td>`+delivery_duration+`</td>
						<td>`+karigarTextOptions+`</td>
						<td>`+disp_images+`</td>
						<td>`+status+`</td>
						<td><a class="edit_supp_cat" href="#"><span class="glyphicon glyphicon-edit"></span></a></td>
					</tr>`;

					$('#supp_details > tbody:last-child').append(row);

					reset_form_fields();

				} else {

					$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message});

				}

				$(".overlay").css("display", "none");

			}

		});
	}
	
});

$(document).on("click", ".disp_images", function() {

	let images_data = $(this).closest("tr").find(".images_value").val();

	images_data = JSON.parse(images_data);

	let imgI = 1;

	$(images_data).each(function(i, val) {

		let is_default_json = val.is_default == 1 ? "Yes" : "No";

		let img_string = `<img class="disp_img_tag" src="`+("data:image/png;base64,"+val.value)+`" />`;

		rowImg = `<tr>
					<th>`+imgI+`</th>
					<th>`+is_default_json+`</th>
					<th>`+img_string+`</th>
				</tr>`;

		$("#design_img_preview tbody").append(rowImg);

		imgI++;

	});

	$("#imageModal_new").modal("show");

});

function reset_form_fields() {

	$('#add_catalog').find('input:text, input:hidden, textarea, select').val("");

	$('#add_catalog input[type="number"]').val('');

	$('#add_catalog input[type="file"]').val('');

	$('#add_catalog').find('select').each(function() {

		if ($(this).hasClass('select2-hidden-accessible')) {

		  	// For Select2 select boxes
		  	$(this).val(null).trigger('change');

		}
	  
	});

	$("#purity, #karigar").multiselect('rebuild');

	$('#add_catalog').find('input:radio, input:checkbox').removeAttr('checked');

	$("#mc_type").val(1);

	$("#status_active").prop("checked",true);

	$("#display_mc_no").prop("checked",true);

	$("#display_va_no").prop("checked",true);

	$("#display_duration_no").prop("checked",true);

	$(".images_preview").html("");

}

$(document).on("click",".edit_supp_cat", function(e) {

	e.preventDefault();

	let closest_tr = $(this).closest("tr");

	let catalog_id = closest_tr.find(".catalog_id").html();

	edit_supp_cat(catalog_id);

	closest_tr.remove();

});

function edit_supp_cat(catalog_id) {

	let my_Date = new Date();

	if(catalog_id > 0) {

		$(".overlay").css("display", "block");

		$.ajax({
		
			url:base_url+"index.php/admin_ret_supp_catalog/supplier_catalog/get_data_by_id/"+catalog_id+"?nocache=" + my_Date.getUTCSeconds(),
		
			dataType:"JSON",
		
			type:"POST",
		
			success:function(data) {
		
				update_catalog_form(data);
		
				$("div.overlay").css("display", "none"); 
		
			},
		
			error:function(error)  {
		
				$("div.overlay").css("display", "none"); 
		
			}	 
		
		});

		$(".overlay").css("display", "none");

	} else {

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Supplier Catalogue ID Required!'});

	}

}

function update_catalog_form(data) {

	$("#cat_id").val(data.cat_id);

	$("#id_supp_catalogue").val(data.id_supp_catalogue);

	$("#product_id").val(data.product_id);

	$("#design_id").val(data.design_id);

	$("#subdesign_id").val(data.id_sub_design);

	$("#design_code").val(data.design_code);

	$("#size").val(data.weightRange.size);

	$("#purity").val(data.weightRange.purity);

	$("#weight").val(data.weightRange.weight);

	$("#from_weight").val(data.weightRange.from_weight);

	$("#to_weight").val(data.weightRange.to_weight);

	$("#mc_type").val(data.weightRange.mc_type);

	$("#mc_value").val(data.weightRange.mc_value);

	$("#wastage").val(data.weightRange.wastage);

	$("#delivery_duration").val(data.weightRange.delivery_duration);

	$("#karigar").val(data.weightRange.karigar);

	if(data.weightRange.display_va == 1) {

		$("#display_va_yes").prop("checked",true);

		$('#display_va_yes').val('1');

	} else {

		$("#display_va_no").prop("checked",true);

		$('#display_va_no').val('0');

	}

	if(data.weightRange.display_mc == 1) {

		$("#display_mc_yes").prop("checked",true);

	} else {

		$("#display_mc_no").prop("checked",true);

	}

	if(data.weightRange.display_duration == 1) {

		$("#display_duration_yes").prop("checked",true);

	} else {

		$("#display_duration_no").prop("checked",true);

	}

	if(data.status == 1) {

		$("#status_active").prop("checked",true);

	} else {

		$("#status_inactive").prop("checked",true);

	}

	$(data.images_details).each(function(i,imgVal) {

		create_image_container(imgVal.base64, imgVal.is_default);

	});

	get_Activeproduct();

	var purArr = $.map(data.weightRange.purity.split(","), function(val) {
	
		return parseInt(val, 10);
	  
	});

	get_cat_purity(purArr);

	var karigarArr = $.map(data.weightRange.karigar.split(","), function(val) {
	
		return parseInt(val, 10);
	
	});

	get_all_karigar(karigarArr);

}