$(document).ready(function() {
    if($('#company_settings').val() == 0){
        get_branchnames();
    }else{
        get_companynames();
    }
	$("#log_submit").on('submit',function(event) {
		event.preventDefault();
		$("#submit_login").prop("disabled","true");
		genOTP();
	});
	
   $('#resendotp').on('click',function(){
        var username = $("#username").val();
        var password = $("#password").val();
        $.ajax({
            url:base_url+ "index.php/chit_admin/resendotp",
            data: {'username':username,'password':password},
            dataType : 'json',
            type: 'POST',
            success : function(data){
                if(data.result==3)
                {
                    alert(data.msg); 
                    $('#otp').val('');
                  //  verify_otp(data.uid,data.otp);
                }
            }
        });	
    });
	
	$('#close_model').on('click',function(){
        window.location.href= base_url+'index.php/admin/login';
    });
    
    $('#verify_otp').on('click',function(){ 
        /*var uid = 10;
        var otp = 111111;
        var input_otp = $('#otp').val(); 
        console.log('Session OTP : '+input_otp);
        if(input_otp == otp)
        {
        	update_otp(uid,otp);
        }
        else{
        	alert('Invalid OTP');
        	 $('#otp').val('');
        	 return false;
        }*/
		var input_otp = $('#otp').val(); 
		update_otp(input_otp);
    });
    
});		

function genOTP()
{
	var username = $("#username").val();
	var password = $("#password").val();
	var id_branch = $("#id_branch").val();
	var id_company = $("#company_select").val();
	$("div.overlay").css("display", "none"); 

            	$.ajax({
                	url:base_url+ "index.php/chit_admin/authenticate",
                	data: {'password':password,'username':username,'id_branch':id_branch,'token_id':DeviceId,'id_company':id_company},
                	type : "POST",
                	dataType: 'json',
                	success : function(data) 
            		{	
            		  if(data.result == 1)
            		  {
            			$("div.overlay").css("display", "block"); 
            			$.toaster({ priority : 'success', title : 'Success!', message : ''+"</br> Login Success"});
            			window.location.href= base_url+'index.php/admin/dashboard';
            		  }
            		   else if(data.result==2)
            		  {
            			//alert(data.msg);
            			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+data.msg});
            			$("#submit_login").removeAttr('disabled');	
            		  }
            		  else if(data.result==5)
                    	{
                    	     $.toaster({ priority : 'danger', title : 'Warning!', message : ''+data.msg});
                    	     $("#submit_login").removeAttr('disabled');
                    	}
            		  else if(data.result==4)
            		  {
            			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+data.msg});
            			$("#submit_login").removeAttr('disabled');	
            		  }
            		 else  if(data.result==0)
            		  {
            				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+data.msg});
            				$("#submit_login").removeAttr('disabled');	
            		  }
            		 else if(data.result==3)
            		 {
            			$('#otp_modal').modal({
            			backdrop: 'static',
            			keyboard: false
            			}); 
            		  }
            		}
            	});
	
}

function update_otp(input_otp)
{
	var username = $("#username").val();
	var password = $("#password").val();
    $.ajax({
         url:base_url+ "index.php/chit_admin/update_otp",
        data: {'username':username,'password':password,'input_otp':input_otp},
        dataType : 'json',
        type: 'POST',
        success : function(data){
         if(data.result==2)
         {
         	window.location.href= base_url+'index.php/admin/dashboard';
         }
         else
         {
         	alert(data.msg);
         	$('#otp').val('');
        	$("#resendotp").attr("disabled", false); 
         }
        
        }
    });
}

function get_branchnames(id_company=""){  
   
    $('#id_branch').val(0);
            $.ajax({    
              type: 'POST', 
              data:{'id_company':id_company},
              url: base_url+'index.php/chit_admin/branchname_list', //?id_company='+id_company  
              dataType:'json',    
              success:function(data){
                 
              var id_branch =  $('#id_branch').val();  
              $("#branch_select").empty().append(            
                        $("<option></option>")            
                        .attr("value", 0)                           
                        .text('All' )
                        );  
               $.each(data.branch, function (key, item) {  
                    $("#branch_select").append(           
                    $("<option></option>")            
                    .attr("value", item.id_branch)                            
                    .text(item.name )                       
                    );                              
                  }); 

              $("#branch_select").select2("val",(id_branch!='' && id_branch>0?id_branch:''));  
              $("#branch_select").select2({         
                    placeholder: "Select Branch",         
                    allowClear: true        
                  });    

       
              } 

            }); 
        }

    $('#branch_select').on('change',function(){
       if(this.value!='')
       {
          $('#id_branch').val(this.value);
       }
       else
       {
          $('#id_branch').val('');
       }
    }); 
     
        
function get_companynames()
{
    $('#id_company').val(0);
            $.ajax({    
              type: 'GET',    
              url: base_url+'index.php/chit_admin/companyname_list',   
              dataType:'json',    
              success:function(data){     
              var id_company =  $('#id_company').val();  
              /*$("#company_select").append(            
                        $("<option></option>")            
                        .attr("value", 0)                           
                        .text('All' )
                        );  
                        $("#company_select").select2({         
                    placeholder: "Select Company",         
                    allowClear: true        
                  });    */

               $.each(data.company, function (key, item) {  
                    $("#company_select").append(           
                    $("<option></option>")            
                    .attr("value", item.id_company)                            
                    .text(item.company_name)                       
                    );                              
                  }); 

              $("#company_select").select2("val",(id_company!='' && id_company>0?id_company:''));  
              
       
              } 

            }); 
}

$('#company_select').on('change',function(){

       if(this.value!='')
       {
          var id_company = $('#company_select').val();

          get_branchnames(id_company);
       }
       else
       {
          $('#id_company').val('');
       }
});  
    




