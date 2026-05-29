

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

            Module

            <small>Module settings</small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Settings</a></li>

            

			<li>

                <a href="#"> Module</a>

		</li>

		<li class="active">Module settings</li>

            

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">



          <!-- Default box -->

         

		<div class="box" >

            <div class="box-header with-border" >

			 <h3 class="box-title">Module Settings</h3>

            </div>

            <div class="box-body">

                <!-- put your content here -->

				<div class="col-md-12">

					<?php

						$attributes 		=	array('role'=>'form');

						 echo form_open(( $module['id_module']!=NULL && $module['id_module']>0 ?'settings/module/update/'.$module['id_module']:'settings/module/save')); ?>

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

										<label class="control-label col-sm-2 ">Module * </label>

										<div class="col-sm-4">

											<input class="form-control" type="text" name="module[m_name]" id="m_name" value="<?php echo set_value('module[m_name]',$module['m_name']); ?>"  required  <?php if($type == 2){?>readonly <?php }?>/>

											<input class="form-control" type="hidden" name="module[id_module]" id="id_module" value="<?php echo set_value('module[id_module]',$module['id_module']); ?>" required />

											<span class="help-block">Module name.</span>

										</div>

										

									</div>

								</div>
								
								
										<div class="row">

									<div class="form-group">

										<label class="control-label col-sm-2 ">Code * </label>

										<div class="col-sm-4">

											<input class="form-control" type="text" name="module[m_code]" id="m_code" value="<?php echo set_value('module[m_code]',$module['m_code']); ?>"  required  <?php if($type == 2){?>readonly <?php }?>/>

											<input class="form-control" type="hidden" name="module[id_module]" id="id_module" value="<?php echo set_value('module[id_module]',$module['id_module']); ?>" required />

											<span class="help-block">Code name.</span>

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





