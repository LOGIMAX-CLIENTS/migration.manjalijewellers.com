var path =  url_params();

var ctrl_page = path.route.split('/');

$(document).ready(function() {
	$('#emp_join').submit(function(e)  {		   	 
	    $('#save').prop('disabled', true);  
	    
	});
	if(path.route=='employee')

	{
	    //lines Added by Durga starts here 
	    var login_profile=$("#login_profile").val();
        //var enable_device_count=$("#device_enable_count").val();
		//var enable_device_limit=$("#enable_device_limit").val();
		if(login_profile==1)
		{
			$("#enable_device_modal_button").css('display','block');
		}
		else
		{
			$("#enable_device_modal_button").css('display','none');
		}
		get_employee_name_list();
		//get_device_list();
		//lines Added by Durga ends here 
		get_emp_list();

	}

    get_branchnames();

	$(document).on('blur','#emp_code',function(){
		let empcode = $('#emp_code').val();
		if (empcode.length < 3) {
			$('#empErr').html("Employee code should be atleast 3 characters");
			$('#emp_code').val('');
		}else{
			$('#empErr').html('');
		}
	})

	if($('#dept').length>0)

	{

	    	get_dept();	

	}

	if($('#designation').length>0)

	{

	    	get_designation();	

	}

	

	get_usertype();

	$('#emp_image').on('change',function() {

		validateImage(this);

});

	$("#state").select2({

				  placeholder: "Enter State",

				    allowClear: true

	});	

	$("#city").select2({

				  placeholder: "Enter City",

				    allowClear: true

	});

   

	$("#country").select2({

				  placeholder: "Enter Country",

				    allowClear: true

	});

	$("#profile").select2({

				  placeholder: "Enter User Type",

				    allowClear: true

	});	

	$("#dept").select2({

				  placeholder: "Enter Department",

				    allowClear: true

	});	

	$("#designation").select2({

				  placeholder: "Enter Designation",

				    allowClear: true

	});	

	if($('#country').length>0)

	{

	    	get_country();	

	}

    $('#country').on('change', function() {

    	

 	   if(this.value!=null && this.value>0 )

		 	{	

		 

				 get_state(this.value);

			}

	});

	

	

	 $('#state').on('change', function() {

 	   if(this.value!=null && this.value>0 )

	 		{

	 	   	 get_city(this.value);

	 	    }

	});

    

	$('#username').on('blur', function() {

		

		if(this.value != "")

		{

			

			checkuser(this.value);

		}

		else

		{

			$(this).attr("placeholder", "Enter username")

		}	

 	    

	});
	
	$('#emp_code').on('blur', function() {

		

		if(this.value != "")

		{

			

			checkempcode(this.value);

		}

		else

		{

			$(this).attr("placeholder", "Enter employee code")

		}	

 	    

	});

	if($('#email').length>0)

	{

		

	   $('#email').on('blur onchange',function(){

	   	$('.overlay').css('display','block');

	   	   var emailRegEx = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i;

		     if (this.value.search(emailRegEx) == -1) 

		     {

			 	$(this).val('');

			   	 $(this).attr('placeholder', 'Enter valid email id')

			 }

			 $('.overlay').css('display','none');

	   }); 	

	   

	}

	if($('#mobile').length>0)

	{

		$("#mobile").keypress(function (e){

			  var charCode = (e.which) ? e.which : e.keyCode;

			  if (charCode > 31 && (charCode < 48 || charCode > 57)) 

			  {

				return false;

			  }

	    });

	   $('#mobile').on('blur onchange',function(){

	   	
	   	   if(this.value.length == 10)

	   	   {

	   	   	  checkMobileAvail(this.value);

		   }

		   else

		   {

		   	 $(this).val('');

		   	 $(this).attr('placeholder', 'Enter valid mobile number');
		   	 
		   	 $(this).focus();

		   }
		   
	   	
	   }); 	

	}

	$(document).on("click", ".view_emp_devices", function() {

		let emp_devices = $(this).closest("td").find(".emp_devices").attr("value");

		// Split the data using '|'
		var parts = emp_devices.split('|');

		let table_devices = "";

		for (var i = 0; i < parts.length; i++) {
			// Split each part using '~~'
			var values = parts[i].split('~~');

			let device_name = "";

			if(values[2] == 1) {

				device_name = getDeviceFromUserAgent(values[5]);

			} else {

				device_name = "IOS";

			}

			table_devices += "<tr><td>"+values[0]+"</td><td>"+values[1]+"</td><td>"+(values[2] == 1 ? "Android" : (values[2] == 2 ? "IOS" : ""))+"</td><td>"+values[3]+"</td><td>"+(values[4] == 1 ? "Collection App" : (values[4] == 2 ? "Estimation App" : ""))+"</td><td>"+device_name+"</td><td>"+(values[6] == 1 ? "Active" : "Inactive")+"</td></tr>";

		}

		$(".devices_table tbody").html(table_devices);

		$("#view_devices_modal").modal("show");

	});



	if(ctrl_page[1]=='employee_settings')
	{       
	    	set_emp_set_table();
			get_ActiveEmpSet();
			get_empSelLst();
			
			$('#add_empset').prop('disabled',true);
				$(window).scroll(function() {   
				var height = $(window).scrollTop();
				if(height  > 300) {
				$(".stickyBlk").css({"position": "fixed"});
				} else{
				$(".stickyBlk").css({"position": "static"});
				}
			});
						
	    	$('#empset_date').daterangepicker(
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
        				$('#empset1').text(moment().startOf('month').format('YYYY-MM-DD'));
        				$('#empset2').text(moment().endOf('month').format('YYYY-MM-DD'));
        				set_emp_set_table($("#emp_filter").val(),start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));
        		  }
        	);
	}

        

});
function checkempcode(emp_code)

{  



	id_employee=$('#id_employee').val();

	$('.overlay').css('display','block');

	$.ajax({

	  type: 'POST',

	  data:{'emp_code':emp_code, 'id_employee':(id_employee != "" ? id_employee : "") },

	  url:  base_url+'index.php/admin_employee/checkempcode',

	   dataType: 'json',

	   success: function(available) {

	    if(available)

		{     $('#emp_code').val('');

			 $('#emp_code').attr("placeholder", "Employee code already exists.");

		}	

	    $('.overlay').css('display','none');

	  },

	  	 error:function(error)  

					  {

						 $(".overlay").css("display", "none"); 

					  }

	});

}
/* -- Coded by ARVK -- */
function checkMobileAvail(mobile)

{ 

$("div.overlay").css("display", "block");

	
	$.ajax({

	  type: 'POST',

	  data:{'mobile':mobile, 'id_employee': (emp_id != "" ? emp_id : "") },

	  url:  base_url+'index.php/employee/check_mobile',

	  dataType: 'json',

	  success: function(avail) {

	  		console.log(avail);

	   		 if(avail==1)

		   	 {

		   	 	$('#mobile').val('');

			 	$('#mobile').attr('placeholder', 'mobile already exists');
			 	
			 	$('#mobile').focus();

			 }

			  $("div.overlay").css("display", "none");  

	  	},

	  	 error:function(error)  

					  {

						 $("div.overlay").css("display", "none"); 

					  }

	  	

	  });	

}
/* -- / Coded by ARVK -- */

function validateImage()

 {

				if(arguments[0].id == 'emp_image')

				{

					var preview = $('#emp_img_preview');

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

			

function get_country()

{

$('.overlay').css('display','block');

	$.ajax({

	  type: 'GET',

	  url:  base_url+'index.php/settings/company/getcountry',

	  dataType: 'json',

	  success: function(country) {

	  //	console.log(country);

		$.each(country, function (key, country) {

				  	

		  

			$('#country').append(

				$("<option></option>")

				  .attr("value", country.id)

				  .text(country.name)

				  

			);

			

		});

				$("#country").select2("val", ($('#countryval').val()!=null?$('#countryval').val():''));

			var selectid=$('#countryval').val();

			if(selectid!=null && selectid > 0)

			{

				console.log(selectid);

				$('#country').val(selectid);

				$('.overlay').css('display','block');

				get_state(selectid);

			}

		$('.overlay').css('display','none');

		},

	  	 error:function(error)  

					  {

						 $("div.overlay").css("display", "none"); 

					  }

	});

	}



function get_usertype()

{

  

	$.ajax({

	  type: 'GET',

	  url:  base_url+'index.php/settings/profile/ajax_list',

	  dataType: 'json',

	  success: function(data) {

		  

	  //	 profiles.data.shift();

	  //	 profiles.data.unshift({'id_profile':0,'profile_name':' -- Select --'});

	  	$('#profile').empty().append($('<option></option>'));
		 var usertypes = data.profile;

		$.each(usertypes, function (key, profile) {

				  	

		  console.log(profile);

			$('#profile').append(

				$("<option></option>")

				  .attr("value", profile.id_profile)

				  .text(profile.profile_name)

				  

			);

		

			

		});

			

			var selectid=$('#usertypeval').val();

			console.log(selectid);

			if(selectid!=null && selectid >0 )

			{

				$('#profile').val(selectid).trigger('change');

			}

			$('.overlay').css('display','none');

		},

	  	 error:function(error)  

					  {

						 $("div.overlay").css("display", "none"); 

					  }

	});

}

//Branch selection mandatory for all User type other than Super admin & admin(based set)//hh
$('#profile').on('change',function(){
    
  var id_profile=this.value;
  
  if(id_profile==1 || id_profile==2)
  {
      $('#branch_select').prop('required',false);
  }
  else
  { 
      $('#branch_select').prop('required',true);
  }
    
});
//Branch selection mandatory for all User type//hh


function get_state(id)

{

	$('.overlay').css('display','block');

	$('#state option').remove();



	$.ajax({

	  type: 'POST',

	   data:{'id_country':id },

	  url:  base_url+'index.php/settings/company/getstate',

	  dataType: 'json',

	  success: function(state) {

	  	

		$.each(state, function (key, state) {

				   

		  

			$('#state').append(

				$("<option></option>")

				  .attr("value", state.id)

				  .text(state.name)

			);

		});

			

				

		$("#state").select2("val", ($('#stateval').val()!=null?$('#stateval').val():''));

		

		var selectid=$('#stateval').val();

		console.log(selectid);

		if(selectid!=null && selectid>0)

		{

			

			$('#state').val(selectid);

			

		    get_city(selectid);

	    }

		$('.overlay').css('display','none');

	

	  },

	  	 error:function(error)  

					  {

						 $("div.overlay").css("display", "none"); 

					  }

	});

}



function get_city(id)

{  

	$('.overlay').css('display','block');

	$('#city option').remove();



	$.ajax({

	  type: 'POST',

	  data:{'id_state':id },

	  url:  base_url+'index.php/settings/company/getcity',

	  dataType: 'json',

	  success: function(city) {

	  

		$.each(city, function (key, city) {

				  	

		  

			$('#city').append(

				$("<option></option>")

				  .attr("value", city.id)

				  .text(city.name)

			);

		});

		$("#city").select2("val", ($('#cityval').val()!=null?$('#cityval').val():''));

		var selectid=$('#cityval').val();

		if(selectid!=null && selectid>0)

		{

			$('#city').val(selectid);

			

		   

	    }

	    $('.overlay').css('display','none');

	  },

	  	 error:function(error)  

					  {

						 $("div.overlay").css("display", "none"); 

					  }

	});



}



function checkuser(username)

{  



	id_employee=$('#id_employee').val();

	$('.overlay').css('display','block');

	$.ajax({

	  type: 'POST',

	  data:{'username':username, 'id_employee':(id_employee != "" ? id_employee : "") },

	  url:  base_url+'index.php/employee/checkuser',

	   dataType: 'json',

	   success: function(available) {

	    if(available)

		{     $('#username').val('');

			 $('#username').attr("placeholder", "User already exists.");

		}	

	    $('.overlay').css('display','none');

	  },

	  	 error:function(error)  

					  {

						 $(".overlay").css("display", "none"); 

					  }

	});

}



function get_dept()

{

    $('div.overlay').css('display','block');

	$.ajax({

	  type: 'GET',

	  url:  base_url+'index.php/employee/dept',

	  dataType: 'json',

	  success: function(dept) {

	  //	console.log(dept);

		$.each(dept, function (key, dept) {

				  	

		  

			$('#dept').append(

				$("<option></option>")

				  .attr("value", dept.id)

				  .text(dept.name)

				  

			);

			

		});

			$("#dept").select2("val", ($('#deptval').val()!=null?$('#deptval').val():''));

		var selectid=($('#deptval').val()!=null?$('#deptval').val():0);

		$('#dept').val(selectid);

	  $('div.overlay').css('display','none');

	  },

	  error:function(error)  

	  {

		 $("div.overlay").css("display", "none"); 

	  }	 

	});

}



function get_designation()

{

   $('div.overlay').css('display','block');

	$.ajax({

	  type: 'GET',

	  url:  base_url+'index.php/employee/designation',

	  dataType: 'json',

	  success: function(designation) {

	  	//console.log(designation);

		$.each(designation, function (key, designation) {

				  	

		  

			$('#designation').append(

				$("<option></option>")

				  .attr("value", designation.id)

				  .text(designation.name)

			);

		});

			$("#designation").select2("val", ($('#designval').val()!=null?$('#designval').val():''));

		var selectid=($('#designval').val()!=null?$('#designval').val():0);

		$('#designation').val(selectid);

		$('div.overlay').css('display','none');

	  },

	  error:function(error)  

	  {

		 $("div.overlay").css("display", "none"); 

	  }	 

	});

	

	

}

$('#branch_select').on('change',function(event){

$('#id_branch').val(event.target.value);

console.log($('#id_branch').val());

});

function get_emp_list()

	{

	my_Date = new Date();

	 

	$.ajax({

			  url:base_url+ "index.php/employee/ajax_emp_list?nocache=" + my_Date.getUTCSeconds(),

			 dataType:"JSON",

			 type:"POST",

			 success:function(data){

			   			set_emp_list(data);

			   			  

					  },

					  error:function(error)  

					  {

						 $("overlay").css("display", "none"); 

					  }	 

			      });

}

function set_emp_list(data)

{



	 var employee 	= data.data;

	 var access		= data.access;	

	 $('#total_employees').text(employee.length);

	 if(access.add == '0')

	 {

		$('#add_employee').attr('disabled','disabled');

	 }

	

	 var oTable = $('#emp_list').DataTable();

	     oTable.clear().draw();

			  	 if (employee!= null && employee.length > 0)

			  	  {  	

					  	oTable = $('#emp_list').dataTable({

				                "bDestroy": true,

				                "bInfo": true,

				                "bFilter": true,

				                "bSort": true,

				                 "order": [[ 0, "desc" ]],
				                 "dom": 'lBfrtip',
				                 "buttons" : ['excel','print'],
						        "tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },
						

				                "aaData": employee,

				                "aoColumns": [{ "mDataProp": "id_employee" },

					                { "mDataProp": function ( row, type, val, meta ){

										 var name=(row.firstname?__capitalizeString(row.firstname):"")+" "+(row.lastname?__capitalizeString(row.lastname):"");

										 return name;

										}

					                },
					                
					                { "mDataProp": "branch_name" },

					                { "mDataProp": function ( row, type, val, meta ){

										  var dept=(row.dept?row.dept:"-");

										  return dept;

										}

					                },

									{ "mDataProp": function ( row, type, val, meta ){

										  var username=(row.username?row.username:"-");

										  return username;

										}

					                },
					                
					                { "mDataProp": function ( row, type, val, meta ){

										  var mobile=(row.mobile?row.mobile:"-");

										  return mobile;

										}

					                },
					                
					                { "mDataProp": function ( row, type, val, meta ){

										  var emp_code=(row.emp_code?row.emp_code:"-");

										  return emp_code;

										}

					                },

					                { "mDataProp": function ( row, type, val, meta ){

										  var usertype=(row.usertype?row.usertype:"-");

										  return usertype;

										}

					                },

									{ "mDataProp": function ( row, type, val, meta ){

										if(row.emp_devices != "") {

											return "<button type='button' class='btn btn-success view_emp_devices' aria-hidden='true'>View</button><div class='emp_devices' value='"+row.emp_devices+"'></div>"; 

										} else {

											return "";

										}

										}

									},

									{ "mDataProp": function ( row, type, val, meta ){

										active_url =base_url+"index.php/admin_employee/employee_status/"+(row.active==1?0:1)+"/"+row.id_employee; 

					                	return "<a href='"+active_url+"'><i class='fa "+(row.active==1?'fa-check':'fa-remove')+"' style='color:"+(row.active==1?'green':'red')+"'></i></a>"

									  }

								  	},

									 { "mDataProp": function ( row, type, val, meta ) {

					                	 id= row.id_employee;

					                	 edit_url=(access.edit=='1' ? base_url+'index.php/employee/edit/'+id : '#' );

										 delete_url=(access.delete=='1' ? base_url+'index.php/employee/delete/'+id : '#' );

					                	 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

					                	 action_content='<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button><ul class="dropdown-menu">'+

											(access.edit =='1' ? '<li><a href="'+edit_url+'" class="btn-edit"><i class="fa fa-edit" ></i> Edit</a></li>':"")+

											(access.delete == '1' ? '<li><a href="#" class="btn-del" data-href="'+delete_url+'"  data-toggle="modal" data-target="'+delete_confirm+'"  ><i class="fa fa-trash"></i> Delete</a></li>':"");

													  

					                	return action_content;

					                	}

									 }]



				            });	

				           		  	 	

					  	 }	

					  	

}

//Pw validation emp login  in the employee master//hh

$('#passwd').on('change',function() {



	



		if($.trim($("#passwd").val()).length < 8)



			{



				$("#passwd").val("");



				$("#passwd").attr('placeholder','Password must be of minimum 8 characters.');



				$("#passwd").focus();



				return false;



			}



	});



 function get_branchnames(){	
         	//$(".overlay").css('display','block');	
         	$.ajax({		
             	type: 'GET',		
             	url: base_url+'index.php/admin_employee/branchname_list',		
             	dataType:'json',		
             	success:function(data){				 
            	 	var id_branch =  $('#login_branch').val();	
            	 	
            	 	
        	 	        	$("#login_branch_select").append(						
                    	 	$("<option></option>")						
                    	 	.attr("value", 0)						  						  
                    	 	.text('All' )
                    	 	);
            	 	
            	 	$.each(data.branch, function (key, item) {					  				  			   		
                	 	$("#login_branch_select").append(						
                	 	$("<option></option>")						
                	 	.attr("value", item.id_branch)						  						  
                	 	.text(item.name )						  					
                	 	);			   											
                 	});						
                 	$("#login_branch_select").select2({			    
                	 	placeholder: "Select Branch",			    
                	 	allowClear: true		    
                 	});

					var ar = $('#sel_br').data('sel_br');
					console.log(ar);
					
					if($('#login_branch_select').length)
					{
					    $('#login_branch_select').select2('val',ar);				
					}
					
							
                 	//$("#login_branch_select").select2("val",(id_branch!='' && id_branch>0?id_branch:''));	 
                 	$(".overlay").css("display", "none");
            	 		
             	}	
            }); 
        }
        
 $("#login_branch_select").change(function() {
			 var data = $("#login_branch_select").select2('data');		
			 selectedValue = $(this).val(); 		
			 $("#login_branch").val(selectedValue);
		}) ;	
				

//Employee settings
	$('#update_access_time').on('click',function(){
		$("div.overlay").css('display','block');
		if( $("#access_time_from").val() != '' &&  $("#access_time_to").val() != ''){
			$.ajax({
				type: 'POST',
				url: base_url+'index.php/admin_employee/employee_settings/updateAccessTimeAll',
				dataType:'json',
				data: { "access_time_from" : $("#access_time_from").val(), "access_time_to" : $("#access_time_to").val()	},
				success:function(data){ 
					$("div.overlay").css('display','none');	
					var html = '<div class="alert alert-'+data.class+' alert-dismissable">'
						+'<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'
						+'<h4><i class="icon fa fa-check"></i>'+ data.title+'!</h4>' 
						+data.message+'</div>'; 
					
					$('.alert-msg').delay(500).fadeIn('normal', function() {
				   	 	  $(".alert-msg").html(html);
					      $(this).delay(10000).fadeOut();
					});  
					set_emp_set_table($("#emp_filter").val(),$("#empset1").val(),$("#empset2").val());
				}
			});
		}
	});
	

	
	$('#add_empset').on( 'click', function () {
		$('#add_empset').prop('disabled',true);
		if(validateemplmtRow()){
		var a = $("#tot_limit").val();
		var i = ++a;
		$("#tot_limit").val(i); 
        var html=''; 
        var emp_name = $(".add_employeeset option:selected").text();
        
		html+='<tr id="empset'+i+'"><td><input type="checkbox" class="id_emp_sett" id="id_emp_sett" name="id_emp_sett[]" checked required /></td><td><input class="create_new" name="create_new" id="create_new"  type="hidden" value="1" required/><input class="id_employee" name="id_employee" id="id_employee"  type="hidden" value="'+$("#add_employeeset").val()+'" required/>'+emp_name+'</td><td><select  class="disc_limit_type form-control" name="empset["+i+"][disc_limit_type]" id="disc_limit_type'+i+'" class="form-control"><option value="">None</option><option value="1">Amount</option><option value="2">Percent</option></select></td><td><input class="disc_limit form-control" name="disc_limit" id="disc_limit" type="number" required/></td><td><input class="max_gold_tol form-control" name="max_gold_tol" id="max_gold_tol" type="number" required/></td><td><input class="min_gold_tol form-control" name="min_gold_tol" id="min_gold_tol" type="number" required/></td><td><input class="max_silver_tol form-control" name="max_silver_tol" id="max_silver_tol" type="number" required/></td><td><input class="min_silver_tol form-control" name="min_silver_tol" id="min_silver_tol" type="number" required/></td><td><select  class="allowed_old_met_pur form-control" name="empset["+i+"][allowed_old_met_pur]" id="allowed_old_met_pur'+i+'" class="form-control"><option value="">None</option><option value="1">All Metal</option><option value="2">Gold</option><option value="3">Silver</option></select></td><td><input type="checkbox" class="allow_day_close" name="allow_day_close" id="allow_day_close'+i+'" value="0" required/></td><td><input type="checkbox" class="allow_manual_rate" name="allow_manual_rate" id="allow_manual_rate'+i+'" value="0" required/></td><td><input type="checkbox" class="otp_dis_approval" name="otp_dis_approval" id="otp_dis_approval'+i+'" value="0" required/></td><td><input type="time"  class="form-control access_time_from" name=" + "timeFrom_".concat(i+1) + " id=" + "timeFrom_".concat(i+1)><br><input type="time"  class="form-control access_time_to" name=" + "timeTo_".concat(i+1) + " id=" + "timeTo_".concat(i+1)></td><td><button type="button" class="btn btn-danger btn-del" onclick="empset_remove('+i+')"><i class="fa fa-trash" ></i></button></td></tr>';		
		$('#emp_set_list').append(html); 	
		}
		else{
			alert("Please fill disc limit field");
		}
        var id_employee=$('#'+a+"id_employee").val();
        $('#add_empset').prop('disabled',false);
    } ); 

	$('#emp_filter').select2().on('change', function() {
		if(this.value!='' && this.value>0)
		{
			var id_employee = $('#emp_filter').val();
			set_emp_set_table(id_employee);  
		}
	});
	
	$('#add_employeeset').select2().on('change', function() {
		if(this.value!='' && this.value>0)
		{
			$('#add_empset').prop('disabled',false);
		}
	});
	
	
	$('#empset_submit').on('click',function(){
	    if($("input[name='id_emp_sett[]']:checked").val())
	    {
			if(validateemplmtRow()){	
				$("#empset_submit").prop('disabled',true);
			    $(".overlay").css('display','none');
			    var selected = [];
			    var allow_update=true;
			    
				$("#emp_set_list tbody tr").each(function(index, value){
				    $(this).prop('disabled',true);
				    curRow = $(this);
				    var allow_day_close=0;
				    var otp_dis_approval=0;
					var allow_manual_rate=0;
				    var access_time_from = (curRow.find('.access_time_from').val() == '')  ? 0 : curRow.find('.access_time_from').val();
				    var access_time_to = (curRow.find('.access_time_to').val() == '')  ? 0 : curRow.find('.access_time_to').val();
					if($(value).find("input[name='id_emp_sett[]']:checked").is(":checked") )
					{   
					    if($(value).find("input[name='allow_day_close']:checked").is(":checked"))
					    {
					        allow_day_close=1;
					    }
					    
					    if($(value).find("input[name='otp_dis_approval']:checked").is(":checked"))
					    {
					        otp_dis_approval=1; 
                        }

						if($(value).find("input[name='allow_manual_rate']:checked").is(":checked"))
					    {
					        allow_manual_rate=1;
					    }
						transData = { 
							'id_emp_sett'               : $(value).find(".id_emp_sett").val(),
							'create_new'                : $(value).find(".create_new").val(),
							'disc_limit_type'           : $(value).find(".disc_limit_type").val(),
							'disc_limit'                : $(value).find(".disc_limit").val(),
							'max_silver_tol'            : $(value).find(".max_silver_tol").val(),
							'min_silver_tol'            : $(value).find(".min_silver_tol").val(),
							'max_gold_tol'              : $(value).find(".max_gold_tol").val(),
							'min_gold_tol'              : $(value).find(".min_gold_tol").val(),
							'allowed_old_met_pur'       : $(value).find(".allowed_old_met_pur").val(),
							'allow_day_close'           : allow_day_close,
							'allow_manual_rate'         : allow_manual_rate,
							'otp_dis_approval'          : otp_dis_approval,
							'access_time_from'          : access_time_from,
							'access_time_to'            : access_time_to,
							'id_employee'               : $(value).find(".id_employee").val(),
							'blk_wast_disc'             : $(value).find(".blk_wast_disc").val(),
							'dia_disc_lmt'              : $(value).find(".dia_disc").val()
						}
						selected.push(transData);
					}
				})	
			}else{
				alert("Please fill disc limit field");
			}
			req_data = selected;
			if(allow_update)
			{
				update_emp_data(req_data);
			}
		}
		else
		{
			alert('Select employee to proceed');
			$("#empset_submit").prop('disabled',false);
		}
		
	});
 
	
function get_ActiveEmpSet()
{
	$.ajax({
	type: 'GET',
	url: base_url+'index.php/admin_employee/employee_settings/active_employee',
	dataType:'json',
	success:function(data){
		
		var id =  $("#id_employee").val();
		$.each(data, function (key, item) {   
		    $("#emp_filter").append(
		    $("<option></option>")
		    .attr("value",item.id_employee)    
		    .text(item.username)  
		    );
		});
		   
		$("#emp_filter").select2(
		{
			placeholder:"Select Employee",
			allowClear: true		    
		});
		if($('#id_employee').data('employee'))
		{
			   var ar = $('#id_employee').data('employee');
               $("#emp_filter").select2('val',ar);
		}
		else
		{
			  $("#emp_filter").select2('val','');
		}
		}
	});
}

function get_empSelLst(){
	$.ajax({
	type: 'GET',
	url: base_url+'index.php/admin_employee/employee_settings/empset_list',
	dataType:'json',
	success:function(data){
		mat_det = data;
		$.each(data, function (key, item) {   
		    $("#add_employeeset").append(
		    $("<option></option>")
		    .attr("value", item.id_employee)    
		    .text(item.username)  
		    );
		});
		$("#add_employeeset").select2(
		{
			placeholder:"Select username",
			allowClear: true		    
		});
		 $("#add_employeeset").select2("val",'');
		$(".overlay").css("display", "none");
		}
		
	});
}



function set_emp_set_table(id_employee="",from_date="",to_date="")
{
	my_Date = new Date();
	$.ajax({
			 url:base_url+ "index.php/admin_employee/employee_settings?nocache=" + my_Date.getUTCSeconds(),
			 data:({'from_date':from_date,'to_date':to_date,'id_employee':id_employee}),
			 dataType:"JSON",
			 type:"POST",
			 cache:false,
			 success:function(data){  
				var emp_set 	= data.emp_set;
				var access	    = data.access;	
				$('#total_empset').text(emp_set.length);
				$('#tot_limit').val(emp_set.length);
				 $("div.overlay").css("display","add_newstone");
			 var oTable = $('#emp_set_list').DataTable();
			 oTable.clear().draw();
			 if (emp_set!= null && emp_set.length > 0)
			 {  	
				oTable = $('#emp_set_list').dataTable({
						"bDestroy": true,
						"bInfo": true,
						"bFilter": true,
						"bSort": true,
						"order": [[ 0, "desc" ]],
						"dom": 'lBfrtip',
						"buttons" : ['excel','print'],
						"tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },
						"aaData"  : emp_set,
						"aoColumns": [
						{"mDataProp": function ( row, type, val, meta )
                        { 
                        chekbox='<input type="checkbox" class="id_emp_sett" name="id_emp_sett[]" value="'+(row.id_emp_sett ==''? '':row.id_emp_sett)+'"/> <input type="hidden" class="create_new" id="create_new" value="0" />' 
                         return chekbox+" "+(row.id_emp_sett==undefined ? '':row.id_emp_sett);
                        }}, 
						{ "mDataProp": "username" },
						{ "mDataProp": function ( row, type, val, meta ){
						blk_disc = '<input value="'+row.blk_wast_disc_lmt+'" class="blk_wast_disc form-control" name="blk_disc_wast_lmt" type="number"/>';
						return blk_disc;
						}
						},
						{ "mDataProp": function ( row, type, val, meta ){
						dia_disc = '<input value="'+row.dia_disc_lmt+'" class="dia_disc form-control" name="dia_disc_lmt" type="number"/>';
						return dia_disc;
						}
						},
						{ "mDataProp": function ( row, type, val, meta ){
						disc = '<select value="'+row.disc_limit_type+'" class="disc_limit_type form-control" name="disc_limit_type" value="row.disc_limit_type"><option value="1">Amount</option><option value="2">Percent</option></select>';
						return disc;
						}
						},
						{"mDataProp": function ( row, type, val, meta ){
						if(row.disc_limit!='' && row.disc_limit!=undefined)
						{
						var disc_limit=row.disc_limit;
						}
						else
						{
						var disc_limit='';
						}
						return '<input class="disc_limit form-control" name="disc_limit"  value="'+disc_limit+'"  id="disc_limit"  type="number" tabindex="'+row.disc_limit+'"/>'
						}
						},
						{"mDataProp": function ( row, type, val, meta ){
							if(row.max_gold_tol!='' && row.max_gold_tol!=undefined)
							{
							var max_gold_tol=row.max_gold_tol;
							}
							else
							{
							var max_gold_tol='';
							}
							return '<input class="max_gold_tol form-control" name="max_gold_tol"  value="'+max_gold_tol+'"  id="max_gold_tol"  type="number" tabindex="'+row.max_gold_tol+'"/>'
							}
							},

							{"mDataProp": function ( row, type, val, meta ){
								if(row.min_gold_tol!='' && row.min_gold_tol!=undefined)
								{
								var min_gold_tol=row.min_gold_tol;
								}
								else
								{
								var min_gold_tol='';
								}
								return '<input class="min_gold_tol form-control" name="min_gold_tol"  value="'+min_gold_tol+'"  id="min_gold_tol"  type="number" tabindex="'+row.min_gold_tol+'"/>'
								}
								},
							{"mDataProp": function ( row, type, val, meta ){
								if(row.max_silver_tol!='' && row.max_silver_tol!=undefined)
								{
								var max_silver_tol=row.max_silver_tol;
								}
								else
								{
								var max_silver_tol='';
								}
								return '<input class="max_silver_tol form-control" name="max_silver_tol"  value="'+max_silver_tol+'"  id="min_gold_tol"  type="number" tabindex="'+row.max_silver_tol+'"/>'
								}
								},

								{"mDataProp": function ( row, type, val, meta ){
									if(row.min_silver_tol!='' && row.min_silver_tol!=undefined)
									{
									var min_silver_tol=row.min_silver_tol;
									}
									else
									{
									var min_silver_tol='';
									}
									return '<input class="min_silver_tol form-control" name="min_silver_tol"  value="'+min_silver_tol+'"  id="min_silver_tol"  type="number" tabindex="'+row.min_silver_tol+'"/>'
									}
									},

							
		
						{ "mDataProp": function ( row, type, val, meta ){
						disc = '<select value="'+row.allowed_old_met_pur+'" class="allowed_old_met_pur form-control" name="allowed_old_met_pur" value="row.allowed_old_met_pur"><option value="">None</option><option value="1">All Metal</option><option value="2">Gold</option><option value="3">Silver</option></select>';
						return disc;
						}
						},
						
						{"mDataProp": function ( row, type, val, meta )
                        { 
                            $checked=' checked="checked"';
                            chekbox= '<input type="checkbox" class="allow_day_close" name="allow_day_close"  value="'+row.allow_day_close+'" "'+(row.allow_day_close==1 ? $checked :'')+'"  id="allow_day_close"/>'
                            return chekbox;
                        }}, 

						{"mDataProp": function ( row, type, val, meta )
                        { 
                            $checked=' checked="checked"';
                            chekbox= '<input type="checkbox" class="allow_manual_rate" name="allow_manual_rate"  value="'+row.allow_manual_rate+'" "'+(row.allow_manual_rate==1 ? $checked :'')+'"  id="allow_manual_rate"/>'
                            return chekbox;
                        }},
					
                        
                        {"mDataProp": function ( row, type, val, meta )
                        { 
                            $checked=' checked="checked"';
                            chekbox= '<input type="checkbox" class="otp_dis_approval" name="otp_dis_approval"  value="'+row.otp_dis_approval+'" "'+(row.otp_dis_approval==1 ? $checked :'')+'"  id="otp_dis_approval"/>'
                      
                        
                         return chekbox;
                        }},
                        
                        { "mDataProp": function ( row, type, val, meta ){
    						$checked=' checked="checked"';
                            time = '<input type="time"  class="form-control access_time_from" name="access_time_from" value="'+row.access_time_from+'" "'+(row.access_time_from!='' ? $checked :'')+'"  id="access_time_from"><br><input type="time"  class="form-control access_time_to" name="access_time_to" value="'+row.access_time_to+'" "'+(row.access_time_to!='' ? $checked :'')+'"  id="access_time_to">';
    						return time;
                        }},
                      
                        { "mDataProp": function ( row, type, val, meta ) {
											id= row.id_emp_sett;
											delete_confirm=(access.delete=='1' ?'#confirm-delete':'');
											delete_url=(access.delete=='1' ? base_url+'index.php/admin_employee/employee_settings/delete/'+id : '#' );
											action_content='<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"> </i></a>'
											return action_content;
											}
						}],
						
					"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                        $('td .disc_limit_type', nRow).val(aData['disc_limit_type'])
                        $('td .allowed_old_met_pur', nRow).val(aData['allowed_old_met_pur'])
                        $('td .allow_day_close', nRow).val(aData['allow_day_close'])
						$('td .allow_manual_rate', nRow).val(aData['allow_manual_rate'])
                        $('td .access_time_from', nRow).val(aData['access_time_from'])
                        $('td .access_time_to', nRow).val(aData['access_time_to'])
                        $('td .otp_dis_approval', nRow).val(aData['otp_dis_approval'])
					}
					});			  	 	
				} 
		  },
		  error:function(error)  
		  {
			 $("div.overlay").css("display","none"); 
		  }	 
	});
	
} 

function validateemplmtRow(){
	var emp_validate = true;
	/*$('#emp_set_list > tbody >tr').each(function(index, tr) {
		if($(this).find('td:eq(2) .disc_limit_type').val() == ""|| $(this).find('td:eq(3) .disc_limit').val() == ""){
			emp_validate = false;
		}
	});*/
	return emp_validate;
}

function empset_remove(i,id = ""){
		var	rowId= "empset"+i;
		$('#'+rowId+'').remove();
		if(id){
			deleteempset(id);
		}
}
function deleteempset(id){
	my_Date = new Date();
		$("div.overlay").css("display", "block"); 
		$.ajax({
			 url:base_url+"index.php/admin_employee/employee_settings/delete/"+id+"?nocache=" + my_Date.getUTCSeconds(),
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

function update_emp_data(req_data)
{
	my_Date = new Date();
	var id_employee = $('#add_employeeset').val();
	$.ajax({
			 url:base_url+ "index.php/admin_employee/update_emp_data?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),
			 data:  {'req_data':req_data,'id_employee':id_employee},
			 type:"POST",
			 dataType:'json',
			 async:false,
			 	  success:function(data){ 
			        $("#empset_submit").prop('disabled',false);
			        set_emp_set_table($("#emp_filter").val(),$("#empset1").val(),$("#empset2").val());
			 	  	var html = '<div class="alert alert-'+data.class+' alert-dismissable">'
						+'<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'
						+'<h4><i class="icon fa fa-check"></i>'+ data.title+'!</h4>' 
						+data.message+'</div>'; 
					
					$('.alert-msg').delay(500).fadeIn('normal', function() {
				   	 	  $(".alert-msg").html(html);
					      $(this).delay(10000).fadeOut();
					 });  
				  },
				  error:function(error)  
				  {
					 $("div.overlay").css("display", "none"); 
				  }	 
		  });
}

//Employee settings
//lines added by Durga starts here 18.07.2023

$("#employee_select").select2({
	placeholder: "Select Employee",
	dropdownParent: $("#enable_device_modal"),
	allowClear: true
	});	
$("#device_select").select2({
	placeholder: "Select Device",
	dropdownParent: $("#enable_device_modal"),
	allowClear: true
	});	
function get_employee_name_list()
{
	$.ajax({
			 url:base_url+ "index.php/admin_employee/get_emp_name_list",
			 type:"POST",
			 dataType:'json',
			 async:false,
			 	  success:function(data)
				  {
					if(data.length>0)
					{
						$.each(data, function (key, emp_data) 
						{
							var name;
							if(emp_data.emp_code!='-' && emp_data.emp_code!='' )
							name=emp_data.firstname+" "+emp_data.lastname+"-"+emp_data.emp_code;
							else
							name=emp_data.firstname+" "+emp_data.lastname;
							$('#employee_select').append(
							$("<option></option>")
				 			  .attr("value", emp_data.id_employee)
							  .text(name)
							);
					  			
						});
						$("#employee_select").select2("val",'');
					}
				  },
				  error:function(error)  
				  {
					 $("div.overlay").css("display", "none"); 
				  }	 

			});
}
function get_device_list()
{
	$.ajax({
			 url:base_url+ "index.php/admin_employee/get_device_list",
			 type:"POST",
			 dataType:'json',
			 async:false,
			 	  success:function(data)
				  {
					if(data.length>0)
					{
						$.each(data, function (key, device_data) 
						{
							
							$('#device_select').append(
							$("<option></option>")
				 			  .attr("value", device_data.id_collection_device)
							  .text(device_data.device_uuid)
							);
					  			
						});
						$("#device_select").select2("val",'');
					}
				  },
				  error:function(error)  
				  {
					 $("div.overlay").css("display", "none"); 
				  }	 

			});
}

$("#chit_cancel").on('click',function()
{
	$('.close').trigger('click');
});
/*$("#employee_select").on('change',function()
{
	if(this.value!='' && this.value!=null)
	{
		$("#employee_id").val(this.value);
		get_employee_name_byid(this.value);
	}
	else
	{
		$("#employee_id").val('');
		$("#device_uuid").val('');
	}
	
});*/

$("#employee_select, #app_type").on('change', function() {
	
	let employee_select = $("#employee_select").val();
	
	let app_type = $("#app_type").val();
    
	if (employee_select !== '' && employee_select !== null && app_type !== '' && app_type !== null) {
     
		$("#employee_id").val(employee_select);

		get_employee_devices(employee_select, app_type)

	} else {
        
		$("#employee_id").val('');
        
		$("#device_uuid").val('');

	}

});

$("#device_select").on('change',function() {

	if(this.value!='' && this.value!=null)
	{
		
		$("#device_uuid").val(this.value);
		
		let device_status = $(this).find('option:selected').attr('device_status');

		$("#enable_device").val(device_status);
		
	}
	else
	{
		$("#device_uuid").val('');
	
		$("#enable_device").val(0);
						}

});

function get_employee_devices(id, app_type) {

	$("#device_select").empty();

	$("#device_select").select2("val",'');
    
	$("#device_uuid").val('');

	$("#enable_device").val(0);

	$.ajax({

		url:base_url+ "index.php/admin_employee/get_employee_devices",

		type:"POST",

		dataType:'json',

		data:{'id_employee':id, 'app_type' : app_type},

		async:false,

		success:function(data) {

			if(data.length > 0) {

				$.each(data, function (key, device_data)  {

					$('#device_select').append(

						$("<option></option>").attr("value", device_data.id_collection_device).text(device_data.device_uuid + "-(" +device_data.device_name+")").attr("device_status", device_data.device_status)
					);

				});

				$("#device_select").select2("val",'');

                    }

				}

	});

}

$("#device_uuid").on('click keydown',function()
{
	if($(employee_select).val()==null || $(employee_select).val()=='')
	{
		$('#device_uuid').val('');
		$('#device_uuid').attr('placeholder', 'Select Employee');
			  
		$.toaster({ priority : 'warning', title : 'warning!', message : ''+"</br>Select Any Employee"}); 
		return false;
}
});
$("#enable_device_modal_button").on("click",function()
{
	$('#device_uuid').val('');
	$('#enable_device').val(0);
	$("#app_type").val(1);
	$("#employee_id").val('');
	$("#employee_select").select2("val",'');
	$("#device_select").select2("val",'');
});

//lines added by Durga starts here 18.07.2023

$("#date_of_birth").change(function(){
    
    console.log(this.value)
                	calculateAge(this.value);

})





function calculateAge(selectedDate)

			{



				if(selectedDate)

				{



					var today = new Date();
					    var selectedDateTime = parseDate(selectedDate, 'dd/mm/yyyy');
                      console.log(selectedDateTime)
				// 	var selectedDateTime = new Date(selectedDate);

                    if (selectedDateTime > today) {
                          alert("Invalid date ");
                          $("#emp_age").val('');
                           $('#date_of_birth').val('')
                          // You can choose to handle this error in a different way if needed
                          return;
                    }
					var dateParts = selectedDate.split('-');

					var dob_year=dateParts[0];

  					var age = today.getFullYear() - dob_year;

					$("#emp_age").val(age);

				}



			  }
			  
 //Parsing string type date to original date format			  
 function parseDate(dateString, format) {
   var parts = dateString.split('-');
var year = parseInt(parts[0], 10);
var month = parseInt(parts[1], 10);

// Check if the month is in the range 01 to 09
if (month >= 1 && month <= 9) {
    month -= 1;  // Subtract 1 from the month
}

var day = parseInt(parts[2], 10);
return new Date(year, month, day);
}

function getDeviceFromUserAgent(userAgentString) {
   
	var device = "";
  
	var pattern = /\bAndroid \d+; ([^;]+) Build/;

    var matches = userAgentString.match(pattern);

    if (matches) {
    
		device = matches[1];
    
	}

    return device;
}
$('#emp_set_list').on('change', '.blk_wast_disc', function() {
    let currow = $(this);
    let blk_wast_disc_per = parseFloat($('#blk_wast_disc_per').val()).toFixed(2);

    if ((parseFloat(currow.val()) > parseFloat(blk_wast_disc_per)) || parseFloat(currow.val()) < 0) {
        $.toaster({
            priority: 'danger',
            title: 'Warning!',
            settings: { 'timeout': 4000 },
            message: '<br>The bulk discount percentage should not exceed ' + blk_wast_disc_per + '.'
        });

        currow.val('').focus();
    }
});
$('#emp_set_list').on('change', '.dia_disc ', function() {
    let currow = $(this);
    let dia_disc_min = parseFloat($('#dia_disc_min').val()).toFixed(2);
    let dia_disc_max = parseFloat($('#dia_disc_max').val()).toFixed(2);

    if ((parseFloat(currow.val()) > parseFloat(dia_disc_max) )|| (parseFloat(currow.val()) < parseFloat(dia_disc_min) )) {
        $.toaster({
            priority: 'danger',
            title: 'Warning!',
            settings: { 'timeout': 4000 },
            message: '<br>' + 'Enter the discount percentage between ' + dia_disc_min + ' to ' + dia_disc_max + '.' 
        });

        currow.val('').focus();
    }
});

