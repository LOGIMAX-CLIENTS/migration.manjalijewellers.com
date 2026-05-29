

<script type="text/javascript">

	

function insertValueQuery() {

    var url_field = document.getElementById("sms_msg");

    var selected_field = document.getElementById("field_list").value;	



	//IE support

	if (document.selection) {

		url_field.focus();

		sel = document.selection.createRange();

		sel.text = selected_field;

		document.sqlform.insert.focus();

	}

	//MOZILLA/NETSCAPE support

	else if (url_field.selectionStart || url_field.selectionStart == "0") {

		//alert(url_field.selectionStart);

		var startPos = url_field.selectionStart;

		var endPos = url_field.selectionEnd;

		var chaineSql = url_field.value;



		url_field.value = chaineSql.substring(0, startPos) + selected_field + chaineSql.substring(endPos, chaineSql.length);

	} else {

		url_field.value += selected_field;

	}

}



</script>



      <!-- Content Wrapper. Contains page content -->

      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

            SMS Services

            <small>SMS service settings</small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Settings</a></li>

            

			<li>

                <a href="#"> SMS Services</a>

		</li>

		<li class="active">SMS service settings</li>

            

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">



          <!-- Default box -->

         

		<div class="box" >

            <div class="box-header with-border" >

			 <h3 class="box-title">SMS Settings</h3>

            </div>

            <div class="box-body">

                <!-- put your content here -->

				<div class="col-md-12">

					<?php

						$attributes 		=	array('role'=>'form');

						 echo form_open(( $sms['id_services']!=NULL && $sms['id_services']>0 ?'sms/service/update/'.$sms['id_services']:'sms/service/save')); ?>

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

										<label class="control-label col-sm-2 ">Service * </label>

										<div class="col-sm-4">

											<input class="form-control" type="text" name="sms[serv_name]" id="serv_name" value="<?php echo set_value('sms[serv_name]',$sms['serv_name']); ?>"  required  <?php if($type == 2){?>readonly <?php }?>/>

											<input class="form-control" type="hidden" name="sms[id_services]" id="id_services" value="<?php echo set_value('sms[id_services]',$sms['id_services']); ?>" required />

											<span class="help-block">Service name.</span>

										</div>

										

									</div>

								</div>
								
								
								
							<div class="row">

									<div class="form-group">

											<label class="control-label col-sm-2 ">SMS DLT ID</label>

											<div class="col-sm-4">

												<input class="form-control" type="text" name="sms[dlt_te_id]" id="dlt_te_id" value="<?php echo set_value('sms[dlt_te_id]',$sms['dlt_te_id']); ?>"  placeholder="Enter DLT ID" maxlength="50"/>

												<span class="help-block">SMS DLT Template Id.</span>

											</div>
                                           </div>
                                 </div>
                                 <div class="row">

									<div class="form-group">

											<label class="control-label col-sm-2 ">WhatsApp Template Name</label>

											<div class="col-sm-4">

												<input class="form-control" type="text" name="sms[template_name]" id="template_name" value="<?php echo set_value('sms[template_name]',$sms['template_name']); ?>"  placeholder="WhatsApp Template Name" maxlength="50"/>

												<span class="help-block">WhatsApp Template Name Registered in Qikchat.</span>

											</div>
                                           </div>
                                 </div>

								<div class="row">

									<div class="form-group">

										<label class="control-label col-sm-2 ">SMS Content </label>

										<div class="col-sm-4 ">

											<textarea class="form-control" required name="sms[sms_msg]" id="sms_msg" cols="35" rows="5" tabindex="4" ><?php echo set_value('sms[sms_msg]',$sms['sms_msg']); ?></textarea>

											<span class="help-block">Enter or choose the SMS content.</span>

											</div>

											<div  class="col-sm-1">

											<div  class="btn-group" data-toggle="buttons">

												<label onclick="insertValueQuery()" style="margin-top: 85px;" class="btn btn-primary">

													<a style="color:#FFFFFF; text-decoration:none" href="" id="move_button" name="move_button" title="click"> << </a>

												</label>

												</div>

										</div>

										<div class="col-sm-3">

											<select style="height: 150px;" class="form-control" size="11" id="field_list" name="field_list">

											

		<?php foreach($service_list as $service){ ?>

			<option value="@@<?php echo $service['value']?>@@"><?php echo $service['text']?></option>

			

		   <?php } ?>

		</select>	

										  <span class="help-block">Select  the value.</span>										</div>

																		

									</div>

								</div>

								<div class="row">

									<div class="form-group">

											<label class="control-label col-sm-2 ">Footer * </label>

											<div class="col-sm-4">

												<input class="form-control" type="text" name="sms[sms_footer]" id="sms_footer" value="<?php echo set_value('sms[sms_footer]',$sms['sms_footer']); ?>"  />

												<span class="help-block">Enter the footer text.</span>

											</div>

										

									</div>

								</div>

							
							<div class="row">

									<div class="form-group">

											<label class="control-label col-sm-2 ">Send Sms On</label>

											<div class="col-sm-4">

												<input class="form-control" type="text" name="sms[send_sms_on]" id="send_sms_on" value="<?php echo set_value('sms[send_sms_on]',$sms['send_sms_on']); ?>"  placeholder="Enter date to send sms" maxlength="14"/>

												<span class="help-block">Eg : 2 (Sms will be sent on 2nd of every month )</span>

											</div>
                                           </div>
                                 </div>


	                                    <div class="row">

									<div class="form-group">

											<label class="control-label col-sm-2 ">Send sms everyday after</label>

											<div class="col-sm-4">



												<input class="form-control" type="text" name="sms[send_daily_from]" id="send_daily_from" value="<?php echo set_value('sms[send_daily_from]',$sms['send_daily_from']); ?>"  placeholder="Enter date to send sms" maxlength="2"/>



												<span class="help-block">Eg : (Sms will be sent to user after 25th till the end of the month )</span>

											</div>

									</div>

								</div>
							
							
								<div class="row">

								   <div class="box box-default"><br/>

									  <div class="col-xs-offset-5">

										<button type="submit" class="btn btn-primary">Save</button> 

										<button type="button" class="btn btn-default btn-cancel">Cancel</button>

										

									  </div> <br/>

									</div>

				 				 </div> 

							

					 <?php echo form_close();?>

	             <!-- /form -->

	         

				</div>

            </div><!-- .box-body End -->

          </div>

  </section><!-- /.content -->

      </div><!-- /.content-wrapper -->





