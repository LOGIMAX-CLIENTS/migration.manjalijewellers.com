var path =  url_params();
var ctrl_page = path.route.split('/');

$(document).ready(function() {
	if(ctrl_page[0]=='catalog' && ctrl_page[1]=='category')
	{ 
      
		$('body').addClass("sidebar-collapse");
		if(ctrl_page[2] == 'list'){
			load_category_list();
		}
	
	
	if($('#idparent').length)
		{
			var resetting = false;
			$('#idparent').jstree({
				'core': {
					'multiple': false,
					'themes': { 'dots': true, 'icons': true }
				},
				'plugins': ['wholerow']
			}).on('ready.jstree', function() {
				// Bind select_node event here
				$('#idparent').on('select_node.jstree', function(e, data){
					$('#id_parent').val(data.node.id);
				});
			});

				
			if(ctrl_page[2] == 'add')
				{
					id_parent = $("#id_parent").val();
				$('#idparent').jstree().check_node(id_parent);
				
				}
			if(ctrl_page[2] == 'edit')
			{
				catid     = $("#catagory_id").val();
				id_parent = $("#id_parent").val();
				$('#idparent').jstree().check_node(id_parent);
				$('#idparent').jstree().disable_node(catid);
				$.each($('#idparent').jstree().get_node(catid).children, function(key,val)
					{
						$('#idparent').jstree().disable_node(val);
					});

			}
			$('#idparent').on('changed.jstree', function (e, data)
				{
					if (resetting) //ignoring the changed event
					{
						resetting = false;
						return;
					}
					if (data.selected.length > 1)
					{
						resetting = true; //ignore next changed event
						data.instance.uncheck_all(); //will invoke the changed event once
						data.instance.check_node(data.node/*currently selected node*/);
						return;
					}
					selectedId = [];
					id_parent = "";
					for(var i = 0; i < data.selected.length; i++)
					{
						selectedId.push(data.instance.get_node(data.selected[i]).id);
						id_parent = data.instance.get_node(data.selected[i]).id;
						
						$('#id_parent').val(id_parent);
					}
				});

		}else{
			console.log('11');
		}
	
	 }
	 else if(ctrl_page[0]=='catalog' && ctrl_page[1]=='product')
	 {
		$('body').addClass("sidebar-collapse");
	    load_product_list();
		
		 if($('#idparent').length)
		{
			
			  var resetting = false;
			$('#idparent').jstree(
				{
					'checkbox':
					{
						three_state: false,
						cascade: 'none'
					},
					'plugins':["checkbox"]
				});

			$('#idparent').jstree('open_all');
			/* $('.jstree-node').each(function()
				{
					var id   = $(this).attr('id');
					if($('#idparent').jstree().get_node(id).children.length)
					{
						$('#idparent').jstree().disable_node(id);
					}
				}); */
				
			if(ctrl_page[2] == 'edit')
			{
				catid     = $("#catagory_id").val();
				id_parent = $("#id_parent").val();
				$('#idparent').jstree().check_node(id_parent);
				/* $('#idparent').jstree().disable_node(catid);
				$.each($('#idparent').jstree().get_node(catid).children, function(key,val)
					{
						$('#idparent').jstree().disable_node(val);
					}); */

			}
			$('#idparent').on('changed.jstree', function (e, data)
				{
					if (resetting) //ignoring the changed event
					{
						resetting = false;
						return;
					}
					if (data.selected.length > 1)
					{
						resetting = true; //ignore next changed event
						data.instance.uncheck_all(); //will invoke the changed event once
						data.instance.check_node(data.node/*currently selected node*/);
						return;
					}
					selectedId = [];
					id_parent = "";
					for(var i = 0; i < data.selected.length; i++)
					{
						selectedId.push(data.instance.get_node(data.selected[i]).id);
						id_parent = data.instance.get_node(data.selected[i]).id;
						
						$('#id_parent').val(id_parent);
					}
				});

		}
		
		
	 }
});


function load_category_list()
{
	
	$("div.overlay").css("display", "block"); 
		var oTable = $('#catagory_list').DataTable();
		$.ajax({
				  type: 'GET',
				  url:  base_url+'index.php/catalog/category/ajax_list',
				  dataType: 'json',
				  success: function(data) {
						var access=data.access;
					    console.log(access);
				        oTable = $('#catagory_list').dataTable({
				                "bDestroy": true,
				                "bInfo": true,
				                "bFilter": true,
				                "bSort": true,
				                "aaData": data.category,
				                "aoColumns": [
							                    { "mDataProp": "id_category" },
							                    { "mDataProp": "category_name" },
								                { "mDataProp": "description" },
								                { "mDataProp": "active" },		
								                { "mDataProp": function ( row, type, val, meta ) {
							                	 id         = row.id_category;
												 edit_url   =(access.edit=='1'? base_url+'index.php/catalog/category/edit/'+id : "#");
							                	 delete_url = (access.delete=='1'?base_url+'index.php/catalog/category/delete/'+id :"#");
							                	 delete_confirm= '#confirm-delete';
							                	 action_content='<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button><ul class="dropdown-menu">'+
							    '<li><a href="'+edit_url+'" class="btn-edit"><i class="fa fa-edit" ></i> Edit</a></li>'+
							    '<li><a href="#" class="btn-del" data-href="'+delete_url+'" data-toggle="modal" data-target="'+delete_confirm+'"  ><i class="fa fa-trash"></i> Delete</a></li></ul></div>';
							                	return action_content;
							                	}
							               
							            }
								             ]
					            });
					                
					    $("div.overlay").css("display", "none");           
				  },
			  	  error:function(error)  
				  {
					 $("div.overlay").css("display", "none"); 
				  }	 
	        });	
}

function load_product_list()
{
	
	$("div.overlay").css("display", "block"); 
		var oTable = $('#product_list').DataTable();
		$.ajax({
				  type: 'GET',
				  url:  base_url+'index.php/catalog/product/ajax_list',
				  dataType: 'json',
				  success: function(data) {
						var access=data.access;
					    console.log(access);
				        oTable = $('#product_list').dataTable({
				                "bDestroy": true,
				                "bInfo": true,
				                "bFilter": true,
				                "bSort": true,
				                "aaData": data.product,
				                "aoColumns": [
							                    { "mDataProp": "id_product" },
												{ "mDataProp": function ( row, type, val, meta ) {
							                	 id         = row.proimage;
							                	 action_content='<a href="#" class="btn-del"><img src='+id+' width="50px;" height="40px;"></a>';
							                	return action_content;
							                	}},
							                    { "mDataProp": "product_name" },
							                    { "mDataProp": "category_name" },
								                { "mDataProp": "description" },
								                { "mDataProp": "type" },
								                { "mDataProp": "weight" },
								                { "mDataProp": "size" },
								                { "mDataProp": "code" },
								                { "mDataProp": "purity" },
								                { "mDataProp": "active" },		
								                { "mDataProp": function ( row, type, val, meta ) {
							                	 id         = row.id_product;
												 edit_url   =(access.edit=='1'? base_url+'index.php/catalog/product/edit/'+id : "#");
							                	 delete_url = (access.delete=='1'?base_url+'index.php/catalog/product/delete/'+id :"#");
							                	 delete_confirm= '#confirm-delete';
							                	 action_content='<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button><ul class="dropdown-menu">'+
							    '<li><a href="'+edit_url+'" class="btn-edit"><i class="fa fa-edit" ></i> Edit</a></li>'+
							    '<li><a href="#" class="btn-del" data-href="'+delete_url+'" data-toggle="modal" data-target="'+delete_confirm+'"  ><i class="fa fa-trash"></i> Delete</a></li></ul></div>';
							                	return action_content;
							                	}
							               
							            }
								             ]
					            });
					                
					    $("div.overlay").css("display", "none");           
				  },
			  	  error:function(error)  
				  {
					 $("div.overlay").css("display", "none"); 
				  }	 
	        });	
}
$("#catimage").change( function(){
		event.preventDefault();

		validate_catImage(this);

		}); 
		

  function validate_catImage()
   {

   	 if(arguments[0].id == 'catimage')

      {

		 var preview = $('#catimage_preview');

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

$("#product_img").change( function(){
		event.preventDefault();

		validateImage(this);

		}); 
		

  function validateImage()
   {

   	 if(arguments[0].id == 'product_img')

      {

		 var preview = $('#product_img_preview');

	  }


	if(arguments[0].files[0].size > 5242880 )

	  {

		 alert('File size cannot be greater than 5 MB');

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