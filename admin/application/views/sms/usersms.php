
<script type="text/javascript">
function sendsms() {		document.getElementById(smms.id).disabled = true;
	var mobno=document.getElementById('serv_group_number').value;
	var mobno_length=document.getElementById('serv_group_number').value.length;
	var desc=document.getElementById('serv_group_desc').value;
	var desc_length=document.getElementById('serv_group_desc').value.length;
	if(mobno!="" && mobno_length < 110 && desc!="" && desc_length <= 160) {
	sendsmsText();
	}
	else if(mobno=="")
	{			document.getElementById(smms.id).disabled = false;			
		  msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Please Enter Mobile Number.</div>';
		  $('#error-msg').html(msg);
		return false;
	}
	else if(desc=="")
	{			document.getElementById(smms.id).disabled = false;
		  msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Please Enter Description</div>';
		  $('#error-msg').html(msg);
		return false;
	}
	
	else if(desc_length > 160 ) 
	{		document.getElementById(smms.id).disabled = false;
		  msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Description Below 160 Characters</div>';
			  $('#error-msg').html(msg);
			return false;
	}
}
function sendsmsText() {
	var mob_no		= document.getElementById('serv_group_number').value;
	var description	= document.getElementById('serv_group_desc').value;
	
		$.ajax({						
		type: "POST",					   		
		url: "<?php echo $this->config->item('base_url'); ?>/index.php/admin_usersms/send_sms",
		data: "group_id="+mob_no+"&send_type="+description,
		success: function(data) {
			
			//client_sendsms(data);
			document.getElementById('serv_group_number').value="";
			document.getElementById('serv_group_desc').value="";												if(data=='success'){				document.getElementById(smms.id).disabled = false;				
				 msg='<div class = "alert alert-success"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Success!</strong> SMS sent successfully.</div>';
			  $('#error-msg').html(msg);
			 
			}
			else{					document.getElementById(smms.id).disabled = false;
				 msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Unable to proceed your request.</div>';
			  $('#error-msg').html(msg);				
			}		
		},
		error: function(request,error) {
			 msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Unable to proceed your request.</div>';
			  $('#error-msg').html(msg);
			return false;
		}
	});
}

function cleartext() {
document.getElementById('serv_group_number').value="";
document.getElementById('serv_group_desc').value="";
}
	</script>

      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            SMS
            <small>Send SMS</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Settings</a></li>
            
			<li>
                <a href="#">SMS</a>
		</li>
		<li class="active">Quick SMS</li>
            
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

          <!-- Default box -->
         
		<div class="box" >
            <div class="box-header with-border" >
			 <h3 class="box-title">Quick SMS</h3>
            </div>
            <div class="box-body">
                <!-- put your content here -->
				<div class="col-md-12">
					<?php
						$attributes 		=	array('role'=>'form');
						//echo form_open('c_customersms/DB_Controller/usersms_settings_model/',$attributes); ?>		
							 <div class="row">
								    <?php 
										if(isset($db_error_msg) && $db_error_msg != '')
										{
											echo '<div class="alert alert-danger alert-dismissable">
														<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
														<h4><i class="icon fa fa-warning"></i> Warning!</h4>    <strong>'.$db_error_msg.'</strong>              
												</div>';
										}	
									?>
								</div>
								
						
                      
							    
								<div class="row">
									<div class="col-sm-10 col-sm-offset-1">
									<div id="error-msg"></div>
									 
									</div>
								</div>
								<div class="row">
									<div class="form-group">
										<label class="control-label col-sm-2 col-xs-offset-2">Mobile Number  * </label>
										<div class="col-sm-4">
											<input class="form-control" type="text" name="fv[serv_group_number]" id="serv_group_number" value="" required />
											<span class="help-block">Can send upto 10 mobiles, use comma(,) after each number</span>
										</div>
										
									</div>
								</div>
								<div class="row">
									<div class="form-group">
										<label class="control-label col-sm-2 col-xs-offset-2">Message </label>
										<div class="col-sm-4 ">
											<textarea class="form-control" required name="fv[serv_group_desc]" id="serv_group_desc" cols="35" rows="5" tabindex="4" ></textarea>
											<span class="help-block">Maximum 160 characters allowed </span>
										</div>										
									</div>
								</div>
								
								
							 <div class="row">
							   <div class="box box-default"><br/>
								  <div class="col-xs-offset-5">
									<button  onclick="sendsms();" type="submit" id="smms" class="btn btn-primary">Send</button>
									<button   type="submit" class="btn btn-default btn-cancel"> Cancel</button>
								  </div> <br/>
							   </div>
							</div>
					</form>
				</div>
            </div><!-- .box-body End -->
          </div>
  </section><!-- /.content -->
      </div><!-- /.content-wrapper -->


