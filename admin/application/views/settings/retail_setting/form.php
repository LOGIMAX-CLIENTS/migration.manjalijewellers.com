

<script type="text/javascript">

	

function insertValueQuery() {

    //var url_field = document.getElementById("module_msg");

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

            Retail

            <small>Retail settings</small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Settings</a></li>

            

			<li>

                <a href="#"> Retail</a>

		</li>

		<li class="active">Retail settings</li>

            

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">



          <!-- Default box -->

         

		<div class="box" >

            <div class="box-header with-border" >

			 <h3 class="box-title">Retail Settings</h3>

            </div>

            <div class="box-body">

                <!-- put your content here -->

				<div class="col-md-12">

					<?php

						$attributes 		=	array('role'=>'form');

						 echo form_open(( $retail_setting['id_ret_settings']!=NULL && $retail_setting['id_ret_settings']>0 ?'settings/retail_setting/update/'.$retail_setting['id_ret_settings']:'settings/retail_setting/save')); ?>

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

										<label class="control-label col-sm-2 "> Settings </label>

										<div class="col-sm-4">

											<input class="form-control" type="text" name="retail_setting[name]" id="name" required="true" value="<?php echo set_value('retail_setting[name]',$retail_setting['name']); ?>"  />

											<input class="form-control" type="hidden" name="retail_setting[id_ret_settings]" id="id_ret_settings" value="<?php echo set_value('retail_setting[id_ret_settings]',$retail_setting['id_ret_settings']); ?>" required />

											<span class="help-block">Settings name.</span>

										</div>

										

									</div>

								</div>
								
								
										<div class="row">

									<div class="form-group">

										<label class="control-label col-sm-2 ">Value  </label>

										<div class="col-sm-4">

											<input class="form-control" type="text" name="retail_setting[value]" id="value" required="true" value="<?php echo set_value('retail_setting[value]',$retail_setting['value']); ?>"  />

											<input class="form-control" type="hidden" name="retail_setting[id_ret_settings]" id="id_ret_settings" value="<?php echo set_value('retail_setting[id_ret_settings]',$retail_setting['id_ret_settings']); ?>" required />

											<span class="help-block"> value.</span>

										</div>

										

									</div>

								</div>
								
								
									<div class="row">

									<div class="form-group">

										<label class="control-label col-sm-2 ">Description  </label>

										<div class="col-sm-4">

											<input class="form-control" type="textarea" name="retail_setting[description]" id="description" required="true" value="<?php echo set_value('retail_setting[description]',$retail_setting['description']); ?>"/>

											<input class="form-control" type="hidden" name="retail_setting[id_ret_settings]" id="id_ret_settings" value="<?php echo set_value('retail_setting[id_ret_settings]',$retail_setting['id_ret_settings']); ?>" required />

											<span class="help-block"> description.</span>

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





